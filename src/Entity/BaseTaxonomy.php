<?php

namespace BiteCodes\TaxonomyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\MappedSuperclass()
 * @Gedmo\Tree(type="nested")
 */
class BaseTaxonomy
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    protected $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    protected $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    protected $rgt;

    /**
     * @Gedmo\TreeRoot
     */
    protected $root;

    /**
     * @Gedmo\TreeParent
     */
    protected $parent;

    /**
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    public function __construct($title = null)
    {
        $this->title = $title;
        $this->children = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $title
     *
     * @return Taxonomy
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * @return null|Taxonomy
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param BaseTaxonomy $parent
     */
    public function setParent(BaseTaxonomy $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return Taxonomy
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return ArrayCollection|Taxonomy[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param BaseTaxonomy $taxonomy
     * @return BaseTaxonomy
     */
    public function addChild(BaseTaxonomy $taxonomy)
    {
        $this->children->add($taxonomy);
        $taxonomy->setParent($this);

        return $this;
    }
}