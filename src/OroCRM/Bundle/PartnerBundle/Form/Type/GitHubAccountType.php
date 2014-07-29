<?php

namespace OroCRM\Bundle\PartnerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GitHubAccountType extends AbstractType
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
                array(
                    'required' => true,
                    'label' => 'orocrm.partner.githubaccount.username.label'
                )
            )->add(
                'email',
                'email',
                array(
                    'required' => false,
                    'label' => 'orocrm.partner.githubaccount.email.label'
                )
            )->add(
                'name',
                'text',
                array(
                    'required' => false,
                    'label' => 'orocrm.partner.githubaccount.name.label'
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'OroCRM\Bundle\PartnerBundle\Entity\GitHubAccount'
            )
        );
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'oro_partner_git_hub_account';
    }
}
