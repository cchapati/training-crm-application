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
    const STATUS_ACTIVE = 'active';
    const STATUS_DISABLE = 'disable';

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="name", type="string", length=16)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     * @Gedmo\Translatable
     */
    protected $label;

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
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return integer
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $status
     *
     * @return PartnerStatus
     */
    public function setLabel($status)
    {
        $this->label = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
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
     * @return PartnerStatus
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }
}
