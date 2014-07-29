<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Unit\Entity;

use OroCRM\Bundle\PartnerBundle\Entity\GitHubAccount;

class GitHubAccountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GitHubAccount
     */
    protected $target;

    public function setUp()
    {
        $this->target = new GitHubAccount();
    }

    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $method = 'set' . ucfirst($property);
        $result = $this->target->$method($value);

        $this->assertInstanceOf(get_class($this->target), $result);
        $this->assertEquals($value, $this->target->{'get' . $property}());
    }

    public function testPrePersist()
    {
        $this->assertNull($this->target->getCreatedAt());

        $this->target->prePersist();

        $this->assertInstanceOf('\DateTime', $this->target->getCreatedAt());
        $expectedCreated = $this->target->getCreatedAt();

        $this->target->prePersist();

        $this->assertSame($expectedCreated, $this->target->getCreatedAt());
    }

    public function testToString()
    {
        $expected = 'username';
        $this->target->setUsername($expected);
        $this->assertEquals($expected, (string)$this->target);
    }

    /**
     * @return array
     */
    public function settersAndGettersDataProvider()
    {
        $partner = $this->getMock('OroCRM\Bundle\PartnerBundle\Entity\Partner');

        return array(
            array('partner', $partner),
            array('username', 'test_username'),
            array('name', 'test_name'),
            array('email', 'test@mail.com')
        );
    }
}
