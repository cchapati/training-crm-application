<?php

namespace OroCRM\Bundle\PartnerBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

use Oro\Bundle\SoapBundle\Form\EventListener\PatchSubscriber;

class PartnerApiType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new PatchSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection'      => false,
                'cascade_validation'   => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'orocrm_partner';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orocrm_partner_api';
    }
}
