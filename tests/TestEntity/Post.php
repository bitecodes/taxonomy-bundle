<?php

namespace BiteCodes\TaxonomyBundle\Test\TestEntity;

use BiteCodes\TaxonomyBundle\Doctrine\Annotation\TaxonomyMapping;
use BiteCodes\TaxonomyBundle\Entity\Taxonomy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="post")
 */
class Post
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var Taxonomy[]|Collection
     *
     * @TaxonomyMapping(root="tags", assoc="MANY")
     */
    protected $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Taxonomy[]|ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Taxonomy $tag
     *
     * @return Post
     */
    public function addTag(Taxonomy $tag)
    {
        $this->tags->add($tag);

        return $this;
    }

    /**
     * @param Taxonomy $tag
     *
     * @return Post
     */
    public function removeTag(Taxonomy $tag)
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
