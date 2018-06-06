<?php

namespace BiteCodes\TaxonomyBundle\Command;

use BiteCodes\TaxonomyBundle\Services\TaxonomyManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TaxonomyUpdateCommand extends Command
{
    /**
     * @var TaxonomyManager
     */
    private $taxonomyManager;

    public function __construct(TaxonomyManager $taxonomyManager)
    {
        parent::__construct();

        $this->taxonomyManager = $taxonomyManager;
    }

    protected function configure()
    {
        $this
            ->setName('taxonomy:update')
            ->setDescription('Will add all registered taxonomy roots');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->taxonomyManager->updateRootTaxonomies();
    }
}
