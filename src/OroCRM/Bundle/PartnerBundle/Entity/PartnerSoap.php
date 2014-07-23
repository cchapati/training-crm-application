<?php

namespace OroCRM\Bundle\PartnerBundle\Entity;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;

use Oro\Bundle\SoapBundle\Entity\SoapEntityInterface;

/**
 * @Soap\Alias("OroCRM.Bundle.PartnerBundle.Entity.Partner")
 */
class PartnerSoap extends Partner implements SoapEntityInterface
{
    /**
     * @Soap\ComplexType("int", nillable=true)
     */
    protected $id;

    /**
     * @Soap\ComplexType("dateTime", nillable=true)
     */
    protected $startDate;

    /**
     * @Soap\ComplexType("string", nillable=true)
     */
    protected $partnerCondition;

    /**
     * @Soap\ComplexType("string", nillable=false)
     */
    protected $status;

    /**
     * @Soap\ComplexType("int", nillable=true)
     */
    protected $owner;

    /**
     * @Soap\ComplexType("int", nillable=true)
     */
    protected $account;

    /**
     * @param Partner $partner
     */
    public function soapInit($partner)
    {
        $this->id               = $partner->getId();
        $this->startDate        = $partner->getStartDate();
        $this->partnerCondition = $partner->getPartnerCondition();
        $this->status           = $partner->getStatus() ? $partner->getStatus()->getName() : null;
        $this->owner            = $partner->getOwner() ? $partner->getOwner()->getId() : null;
        $this->account          = $partner->getAccount() ? $partner->getAccount()->getId() : null;
    }
}
