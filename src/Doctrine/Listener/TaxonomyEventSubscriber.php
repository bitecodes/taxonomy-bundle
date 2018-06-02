<?php

namespace BiteCodes\TaxonomyBundle\Doctrine\Listener;

use BiteCodes\TaxonomyBundle\Doctrine\Annotation\TaxonomyMapping;
use BiteCodes\TaxonomyBundle\Entity\Taxonomy;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

class TaxonomyEventSubscriber implements EventSubscriber
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
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->validateTaxonomies($args);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->validateTaxonomies($args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    protected function validateTaxonomies(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        $refl = new \ReflectionClass($entity);

        foreach ($refl->getProperties() as $property) {
            foreach ($this->reader->getPropertyAnnotations($property) as $configuration) {
                if ($configuration instanceof TaxonomyMapping) {
                    $property->setAccessible(true);
                    /** @var Taxonomy[] $taxonomies */
                    $taxonomies = $property->getValue($entity);

                    foreach ($taxonomies as $taxonomy) {
                        if (!$taxonomy instanceof $configuration->targetEntity) {
                            throw new \Exception("");
                        }

                        if (!$taxonomy->getParent()) {
                            throw new \Exception("A root taxonomy '{$taxonomy->getTitle()}' was given. Entites should not be directly linked to roots.");
                        }

                        if ($configuration->root !== $taxonomy->getRoot()->getTitle()) {
                            throw new \Exception("The root of this taxonomy has to be '$configuration->root'. A Taxonomy of type '{$root->getTitle()}' was given.");
                        }
                    }
                }
            }
        }
    }
}
