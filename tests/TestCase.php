<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $om;

    public function setUp()
    {
        AnnotationRegistry::registerLoader('class_exists');
        $this->loadDoctrine();
    }

    protected function loadDoctrine()
    {
        $paths = [__DIR__ . "/Entity"];
        $isDevMode = false;
        $dbParams = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);
        $this->om = EntityManager::create($dbParams, $config);

        $tool = new SchemaTool($this->om);
        $classes = array(
            $this->om->getClassMetadata('AokComponent\AddressBundle\Entity\Street'),
            $this->om->getClassMetadata('AokComponent\AddressBundle\Entity\City')
        );
        $tool->createSchema($classes);
    }
}
