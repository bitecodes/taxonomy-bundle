<?php

namespace BiteCodes\TaxonomyBundle\Test\Doctrine\Listener;

use BiteCodes\TaxonomyBundle\Entity\Taxonomy;
use BiteCodes\TaxonomyBundle\Test\TestEntity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaxonomyListenerTest extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function setUp()
    {
        static::bootKernel();

        $this->em = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        $taxonomyManager = static::$kernel->getContainer()->get('bite_codes_taxonomy.services.taxonomy_manager');

        $schema = new SchemaTool($this->em);
        $schema->createSchema($this->em->getMetadataFactory()->getAllMetadata());
        $taxonomyManager->updateRootTaxonomies();
    }

    /** @test */
    public function it_allows_to_use_taxonomies()
    {
        $tag1 = new Taxonomy();
        $tag1->setTitle('Lifestyle');

        $tag2 = new Taxonomy();
        $tag2->setTitle('Holiday');

        $post = new Post();
        $post
            ->setTitle('Test Post')
            ->addTag($tag1)
            ->addTag($tag2);

        $this->em->persist($post);
        $this->em->flush();
        $this->em->clear();

        $post = $this->em->getRepository(Post::class)->findOneBy([]);

        $this->assertEquals('Lifestyle', $post->getTags()->first()->getTitle());
        $this->assertEquals('Holiday', $post->getTags()->next()->getTitle());
    }
}
