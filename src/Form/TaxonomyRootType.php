<?php

namespace BiteCodes\TaxonomyBundle\Form;

use BiteCodes\TaxonomyBundle\Entity\Taxonomy;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxonomyRootType extends FormType
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
            $event->setData($options['empty_data']);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class'              => Taxonomy::class,
            'title'              => null,
            'allow_extra_fields' => true,
        ]);

        $resolver->setRequired('title');

        $resolver->setDefault('root', function (Options $options) {
            return $this->em->getRepository($options['class'])->findRoot($options['title']);
        });

        $resolver->setDefault('choices', function (Options $options) {
            return [$options['root']];
        });

        $resolver->setDefault('empty_data', function (Options $options) {
            return (string)$options['root']->getId();
        });
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
