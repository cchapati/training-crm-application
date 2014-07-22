<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Entity;

use OroCRM\Bundle\PartnerBundle\Entity\Partner;

class PartnerTest extends \PHPUnit_Framework_TestCase
{
    public function testBeforeSave()
    {
        $partner = new Partner();
        $partner->beforeSave();
        $this->assertInstanceOf('DateTime', $partner->getStartDate());
    }

    /**
     * @dataProvider flatPropertiesDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new Partner();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertSame($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function flatPropertiesDataProvider()
    {
        $now = new \DateTime('now');
        $user = $this->getMockBuilder('Oro\Bundle\UserBundle\Entity\User');
        $account = $this->getMockBuilder('OroCRM\Bundle\AccountBundle\Entity\Account');
        return array(
            'partnerCondition' => array('partnerCondition', 'test', 'test'),
            'account' => array('account', $account, $account),
            'owner' => array('owner', $user, $user),
            'startDate' => array('startDate', $now, $now),
        );
    }
}
