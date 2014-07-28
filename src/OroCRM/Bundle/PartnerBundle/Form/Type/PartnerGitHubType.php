<?php

namespace OroCRM\Bundle\PartnerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PartnerGitHubType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                'text',
                array('required' => true)
            )->add(
                'email',
                'email',
                array('required' => true)
            )->add(
                'name',
                'text',
                array('required' => true)
            );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'OroCRM\Bundle\PartnerBundle\Entity\PartnerGitHub'
            )
        );
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'oro_partner_git_hub';
    }
}
