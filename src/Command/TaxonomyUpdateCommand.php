<?php

namespace BiteCodes\TaxonomyBundle\Command;

use BiteCodes\TaxonomyBundle\Doctrine\Annotation\TaxonomyMapping;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaxonomyUpdateCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var Reader
     */
    private $reader;

    public function __construct(EntityManager $em, Reader $reader)
    {
        parent::__construct();

        $this->em = $em;
        $this->reader = $reader;
    }

    protected function configure()
    {
        $this
            ->setName('taxonomy:update')
            ->setDescription('Will add all registered taxonomy roots');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
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
