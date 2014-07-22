<?php

namespace OroCRM\Bundle\PartnerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * @ORM\Entity
 * @ORM\Table(name="orocrm_partner_status")
 * @Gedmo\TranslationEntity(class="OroCRM\Bundle\PartnerBundle\Entity\PartnerStatusTranslation")
 */
class PartnerStatus implements Translatable
{
    const STATUS_ACTIVE        = 'active';
    const STATUS_DISABLE       = 'disable';

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=64)
     * @Gedmo\Translatable
     */
    protected $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="`order`", type="integer")
     */
    protected $order;

    /**
     * @Gedmo\Locale
     */
    protected $locale;

    /**
     * @param string $status
     */
    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $status
     *
     * @return PartnerStatus
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param integer $order
     *
     * @return PartnerStatus
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Set locale for translation
     *
     * @param string $locale
     *
     * @return PartnerStatus
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->status;
    }
}
