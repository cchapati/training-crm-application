<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Entity;

use OroCRM\Bundle\PartnerBundle\Entity\PartnerStatus;

class PartnerStatusTest extends \PHPUnit_Framework_TestCase
{
    const STATUS_EXPECTED = PartnerStatus::STATUS_ACTIVE;

    /**
     * @var Partner
     */
    protected $partnerStatus;

    protected function setUp()
    {
        $this->partnerStatus = new PartnerStatus(self::STATUS_EXPECTED);
    }

    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value, $expected)
    {
        call_user_func_array(array($this->partnerStatus, 'set' . ucfirst($property)), array($value));
        $this->assertSame(
            $expected,
            call_user_func_array(array($this->partnerStatus, 'get' . ucfirst($property)), array())
        );
    }

    public function settersAndGettersDataProvider()
    {
        return [
            'order' => ['order', 1, 1],
            'label' => ['label', 'Active', 'Active'],
        ];
    }

    public function testGetName()
    {
        $this->assertEquals(self::STATUS_EXPECTED, $this->partnerStatus->getName());
    }

    public function testToString()
    {
        $this->assertEquals(self::STATUS_EXPECTED, $this->partnerStatus->__toString());
    }
}
