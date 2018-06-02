<?php

namespace BiteCodes\TaxonomyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Taxonomy
 *
 * @ORM\Entity(repositoryClass="BiteCodes\TaxonomyBundle\Repository\TaxonomyRepository")
 * @ORM\Table(name="taxonomy", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="unique_idx", columns={"title", "parent_id"})
 * }))
 * @UniqueEntity({"parent", "title"})
 */
class Taxonomy extends BaseTaxonomy
{
}
