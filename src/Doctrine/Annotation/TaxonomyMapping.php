<?php

namespace BiteCodes\TaxonomyBundle\Doctrine\Annotation;

use BiteCodes\TaxonomyBundle\Entity\Taxonomy;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @Annotation
 */
class TaxonomyMapping
{
    const TYPE_ONE = 'ONE';
    const TYPE_MANY = 'MANY';
    const FETCH_STRATEGIES = [
        'LAZY'       => ClassMetadata::FETCH_LAZY,
        'EAGER'      => ClassMetadata::FETCH_EAGER,
        'EXTRA_LAZY' => ClassMetadata::FETCH_EXTRA_LAZY,
    ];

    /**
     * @var string
     */
    public $root;

    /**
     * Use 'one' for OneToMany and 'many' for ManyToMany associations
     *
     * @var string
     *
     * @Enum({"ONE", "MANY"})
     */
    public $assoc;

    /**
     * @var string
     */
    public $targetEntity = Taxonomy::class;

    /**
     * The fetching strategy to use for the association.
     *
     * @var string
     *
     * @Enum({"LAZY", "EAGER", "EXTRA_LAZY"})
     */
    public $fetch = 'LAZY';
}
