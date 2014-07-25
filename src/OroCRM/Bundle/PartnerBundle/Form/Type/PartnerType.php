<?php

namespace OroCRM\Bundle\PartnerBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ORM\EntityRepository;

class PartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'account',
                'orocrm_account_select',
                [
                    'required' => true,
                    'label' => 'orocrm.partner.account.label',
                ]
            )
            ->add(
                'contract',
                'oro_file',
                [
                    'required' => false,
                    'label' => 'orocrm.partner.contract.label',
                ]
            )
            ->add(
                'partnerCondition',
                'textarea',
                [
                    'required' => false,
                    'label' => 'orocrm.partner.partner_condition.label',
                ]
            )
            ->add(
                'status',
                'entity',
                [
                    'label'         => 'orocrm.partner.status.label',
                    'class'         => 'OroCRMPartnerBundle:PartnerStatus',
                    'query_builder' => function (EntityRepository $entityRepository) {
                        return $entityRepository->createQueryBuilder('partnerStatus')
                            ->orderBy('partnerStatus.order', 'ASC');
                    },
                    'property'      => 'label'
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'OroCRM\Bundle\PartnerBundle\Entity\Partner',
                'intention' => 'partner',
                'extra_fields_message' => 'This form should not contain extra fields: "{{ extra_fields }}"',
                'cascade_validation' => true
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'orocrm_partner';
    }
}
