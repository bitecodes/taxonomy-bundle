<?php

namespace BiteCodes\TaxonomyBundle\Doctrine\Listener;

use BiteCodes\TaxonomyBundle\Doctrine\Annotation\TaxonomyMapping;
use BiteCodes\TaxonomyBundle\Entity\BaseTaxonomy;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\Id;

class TaxonomyListener implements EventSubscriber
{
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();

        $refl = $metadata->getReflectionClass();

        if ($refl->isSubclassOf(BaseTaxonomy::class)) {
            $this->addSelfReferentialMetadata($metadata);
        } else {
            foreach ($refl->getProperties() as $property) {
                if ($configuration = $this->reader->getPropertyAnnotation($property, TaxonomyMapping::class)) {
                    switch (strtoupper($configuration->assoc)) {
                        case TaxonomyMapping::TYPE_ONE:
                            $mapping = $this->getManyToOneMapping($property, $configuration);
                            $metadata->mapManyToOne($mapping);
                            break;
                        case TaxonomyMapping::TYPE_MANY:
                            $mapping = $this->getManyToManyMapping($property, $configuration);
                            $metadata->mapManyToMany($mapping);
                            break;
                        default:
                            throw new \Exception('Invalid type');
                            break;
                    }
                }
            }
        }
    }

    private function addSelfReferentialMetadata(ClassMetadata $metadata)
    {
        // Root
        $fieldMapping = [
            'targetEntity' => $metadata->name,
            'fieldName'    => 'root',
            'inversedBy'   => null,
            'JoinColumn'   => [
                'name'                 => 'root_id',
                'referencedColumnName' => 'id',
                'nullable'             => false,
                'onDelete'             => 'SET NULL',
            ],
        ];

        $metadata->mapManyToOne($fieldMapping);

        // Parent
        $fieldMapping = [
            'targetEntity' => $metadata->name,
            'fieldName'    => 'parent',
            'inversedBy'   => 'children',
            'JoinColumn'   => [
                'name'                 => 'parent_id',
                'referencedColumnName' => 'id',
                'nullable'             => true,
                'onDelete'             => 'SET NULL',
            ],
        ];

        $metadata->mapManyToOne($fieldMapping);

        // Children
        $fieldMapping = [
            'fieldName'    => 'children',
            'targetEntity' => $metadata->name,
            'mappedBy'     => 'parent',
        ];
        $metadata->mapOneToMany($fieldMapping);
    }

    /**
     * @param $name
     * @param bool $pluralize
     * @return string
     */
    protected function getNormalizedName($name, $pluralize = false)
    {
        $method = $pluralize ? 'pluralize' : 'singularize';

        $className = Inflector::$method($name);

        return Inflector::tableize($className);
    }

    /**
     * @param $property
     * @param $configuration
     * @return array
     */
    protected function getManyToOneMapping(\ReflectionProperty $property, TaxonomyMapping $configuration)
    {
        $name = $this->getNormalizedName($property->getName());

        if ($idAnnot = $this->reader->getPropertyAnnotation($property, Id::class)) {
            $mapping['id'] = true;
        }

        $mapping['fieldName'] = $name;
        $mapping['joinColumns'] = [
            [
                'name'                 => $name . '_id',
                'referencedColumnName' => 'id',
            ],
        ];
        $mapping['cascade'] = [];
        $mapping['inversedBy'] = 'entites';
        $mapping['targetEntity'] = $configuration->targetEntity;
        $mapping['fetch'] = TaxonomyMapping::FETCH_STRATEGIES[$configuration->fetch];

        return $mapping;
    }

    /**
     * @param $property
     * @param $configuration
     * @return mixed
     */
    protected function getManyToManyMapping(\ReflectionProperty $property, TaxonomyMapping $configuration)
    {
        $name = $this->getNormalizedName($property->getName(), true);
        $mapping['fieldName'] = $name;

        $joinTable = [
            'name'   => 'taxonomy_' . $name . '_' . $this->getNormalizedName($property->getDeclaringClass()->getShortName(), true),
            'schema' => null,
        ];

        $mapping['joinTable'] = $joinTable;
        $mapping['targetEntity'] = $configuration->targetEntity;
        $mapping['mappedBy'] = null;
        $mapping['inversedBy'] = 'entities';
        $mapping['cascade'] = [];
        $mapping['indexBy'] = 'id';
        $mapping['orphanRemoval'] = false;
        $mapping['fetch'] = TaxonomyMapping::FETCH_STRATEGIES[$configuration->fetch];
        $mapping['type'] = ClassMetadataInfo::MANY_TO_MANY;
        $mapping['isOwningSide'] = true;

        return $mapping;
    }
}
