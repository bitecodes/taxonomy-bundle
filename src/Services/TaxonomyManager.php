<?php

namespace BiteCodes\TaxonomyBundle\Services;

use BiteCodes\TaxonomyBundle\Doctrine\Annotation\TaxonomyMapping;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class TaxonomyManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Reader
     */
    private $reader;

    public function __construct(EntityManagerInterface $em, Reader $reader)
    {
        $this->em = $em;
        $this->reader = $reader;
    }

    public function updateRootTaxonomies()
    {
        /** @var ClassMetadata[] $metadata */
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();

        foreach ($metadata as $meta) {
            $properties = $meta->getReflectionClass()->getProperties();

            foreach ($properties as $property) {
                /** @var TaxonomyMapping $configuration */
                if ($configuration = $this->reader->getPropertyAnnotation($property, TaxonomyMapping::class)) {
                    $root = $configuration->root;

                    $taxonomyRepo = $this->em->getRepository($configuration->targetEntity);

                    if (!$taxonomyRepo->findOneBy(['title' => $root])) {
                        $rootTaxonomy = new $configuration->targetEntity($root);

                        $this->em->persist($rootTaxonomy);
                    }
                }
            }
        }

        $this->em->flush();
    }
}