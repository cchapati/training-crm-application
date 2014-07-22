<?php

namespace OroCRM\Bundle\PartnerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;

use Oro\Bundle\DataAuditBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\UserBundle\Entity\User;

use OroCRM\Bundle\PartnerBundle\Model\ExtendPartner;
use OroCRM\Bundle\PartnerBundle\Entity\PartnerStatus;
use OroCRM\Bundle\AccountBundle\Entity\Account;

/**
 * @ORM\Entity()
 * @ORM\Table(name="orocrm_partner")
 * @Oro\Loggable
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      routeName="orocrm_account_index",
 *      routeView="orocrm_account_view",
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-suitcase"
 *          },
 *          "ownership"={
 *              "owner_type"="USER",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="user_owner_id"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"=""
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          }
 *      }
 * )
 */
class Partner extends ExtendPartner
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Soap\ComplexType("int", nillable=true)
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date")
     * @Soap\ComplexType("dateTime", nillable=true)
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $startDate;

    /**
     * @var string
     *
     * @ORM\Column(name="partner_condition", type="text", nullable=true)
     * @Soap\ComplexType("string", nillable=true)
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $partnerCondition;

    /**
     * @var PartnerStatus
     *
     * @ORM\ManyToOne(targetEntity="PartnerStatus", cascade={"persist"})
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     * @Soap\ComplexType("string", nillable=false)
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $status;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_owner_id", referencedColumnName="id", onDelete="SET NULL")
     * @Soap\ComplexType("string", nillable=false)
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $owner;

    /**
     * @var Account
     *
     * @ORM\OneToOne(targetEntity="OroCRM\Bundle\AccountBundle\Entity\Account")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @Soap\ComplexType("string", nillable=false)
     * @Oro\Versioned
     * @ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $account;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $condition
     * @return Partner
     */
    public function setPartnerCondition($condition)
    {
        $this->partnerCondition = $condition;

        return $this;
    }

    /**
     * @return string
     */
    public function getPartnerCondition()
    {
        return $this->partnerCondition;
    }

    /**
     * @param \Oro\Bundle\UserBundle\Entity\User $owner
     * @return Partner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return \Oro\Bundle\UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param \DateTime $startDate
     * @return Partner
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \OroCRM\Bundle\AccountBundle\Entity\Account $account
     * @return Partner
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return \OroCRM\Bundle\AccountBundle\Entity\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param PartnerStatus $status
     *
     * @return Partner
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return PartnerStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Pre persist event listener
     *
     * @ORM\PrePersist
     */
    public function beforeSave()
    {
        $this->startDate = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    public function __toString()
    {
        return (string) $this->getAccount()?$this->getAccount()->getName():'';
    }

    /**
     * Get email from account
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getAccount()?$this->getAccount()->getEmail():null;
    }
}
