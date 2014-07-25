<?php

namespace OroCRM\Bundle\PartnerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniquePartnerAccount extends Constraint
{
    public $message = 'This account is already used as partner.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'unique_partner_account';
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
