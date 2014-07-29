<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Entity;

use OroCRM\Bundle\PartnerBundle\Entity\Partner;

class PartnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Partner
     */
    protected $partner;

    protected function setUp()
    {
        $this->partner = new Partner();
    }

    public function testBeforeSave()
    {
        $this->assertNull($this->partner->getStartDate());
        $this->partner->beforeSave();
        $this->assertInstanceOf('DateTime', $this->partner->getStartDate());
    }

    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value, $expected)
    {
        call_user_func_array(array($this->partner, 'set' . ucfirst($property)), array($value));
        $this->assertSame($expected, call_user_func_array(array($this->partner, 'get' . ucfirst($property)), array()));
    }

    public function settersAndGettersDataProvider()
    {
        $now = new \DateTime('now');
        $user = $this->getMock('Oro\Bundle\UserBundle\Entity\User');
        $account = $this->getMock('OroCRM\Bundle\AccountBundle\Entity\Account');
        $status = $this->getMockBuilder('OroCRM\Bundle\PartnerBundle\Entity\PartnerStatus')
            ->disableOriginalConstructor()
            ->getMock();

        return array(
            'partnerCondition' => array('partnerCondition', 'test', 'test'),
            'account' => array('account', $account, $account),
            'owner' => array('owner', $user, $user),
            'startDate' => array('startDate', $now, $now),
            'status' => array('status', $status, $status),
        );
    }

    public function testGetEmail()
    {
        $expectedEmail = 'mail@example.com';
        $account = $this->getMock('OroCRM\Bundle\AccountBundle\Entity\Account');
        $account->expects($this->once())
            ->method('getEmail')
            ->will($this->returnValue($expectedEmail));

        $this->partner->setAccount($account);
        $this->assertEquals($expectedEmail, $this->partner->getEmail());
    }

    public function testGetEmailWithEmptyAccount()
    {
        $this->assertNull($this->partner->getEmail());
    }

    public function testToString()
    {
        $expected = 'test';
        $account = $this->getMock('OroCRM\Bundle\AccountBundle\Entity\Account');
        $account->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($expected));

        $this->partner->setAccount($account);
        $this->assertEquals($expected, $this->partner->__toString());
    }

    public function testToStringEmpty()
    {
        $this->assertEquals('', $this->partner->__toString());
    }

    public function testAddGitHubAccount()
    {
        $this->partner->addGitHubAccount(null);
        $actual = $this->partner->getGitHubAccounts();
        $this->assertCount(0, $actual);
        $account = $this->getMock('OroCRM\Bundle\PartnerBundle\Entity\GitHubAccount');
        $account->expects($this->once())
            ->method('setPartner')
            ->with($this->partner);
        $this->partner->addGitHubAccount($account);
        $actual = $this->partner->getGitHubAccounts();
        $this->assertCount(1, $actual);
        $this->assertSame($account, $actual->get(0));
    }

    public function testRemoveGitHubAccount()
    {
        $account = $this->getMock('OroCRM\Bundle\PartnerBundle\Entity\GitHubAccount');
        $account->expects($this->once())
            ->method('setPartner')
            ->with($this->partner);
        $this->partner->addGitHubAccount($account);
        $this->partner->removeGitHubAccount(null);
        $actual = $this->partner->getGitHubAccounts();
        $this->assertCount(1, $actual);
        $this->partner->removeGitHubAccount($account);
        $this->assertCount(0, $actual);
    }
}
