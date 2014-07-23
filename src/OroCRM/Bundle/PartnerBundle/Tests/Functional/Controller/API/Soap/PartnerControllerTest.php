<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Functional\Controller\API\Soap;

use Oro\Bundle\UserBundle\Entity\User;
use OroCRM\Bundle\PartnerBundle\Entity\PartnerStatus;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class PartnerControllerTest extends WebTestCase
{
    /**
     * @var array
     */
    protected $partnerCreateData = [
        'owner' => '1',
        'status' => PartnerStatus::STATUS_ACTIVE,
        'partnerCondition' => 'Test Condition',
        'account' => null,
    ];

    /**
     * @var User
     */
    protected $adminUser;

    protected function setUp()
    {
        $this->initClient(array(), $this->generateWsseAuthHeader());
        $this->initSoapClient();
        $this->loadFixtures(['OroCRM\Bundle\PartnerBundle\Tests\Functional\DataFixtures\LoadAccountData']);

        $this->partnerCreateData['account'] = $this->getReference('orocrm_partner:test_account_1')->getId();

        $this->adminUser = $this->getContainer()->get('doctrine')
            ->getRepository('OroUserBundle:User')->findOneByUsername('admin');
        $this->assertNotEmpty($this->adminUser);
    }

    /**
     * @return integer
     */
    public function testCreate()
    {
        $result = $this->soapClient->createPartner($this->partnerCreateData);
        $this->assertGreaterThan(0, $result, $this->soapClient->__getLastResponse());

        return $result;
    }

    /**
     * @depends testCreate
     * @param integer $id
     */
    public function testCget($id)
    {
        $result = $this->soapClient->getPartners();
        $result = $this->valueToArray($result);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('item', $result);

        $partner = $result['item'];

        $this->assertArrayIntersectEquals(
            array(
                'id' => $id,
                'partnerCondition' => $this->partnerCreateData['partnerCondition'],
                'status' => $this->partnerCreateData['status'],
                'account' => $this->getReference('orocrm_partner:test_account_1')->getId(),
                'owner' => $this->adminUser->getId(),
            ),
            $partner
        );

        $this->assertArrayHasKey('startDate', $partner);
        $this->assertNotEmpty($partner['startDate']);
    }

    /**
     * @depends testCreate
     * @param integer $id
     * @return array
     */
    public function testGet($id)
    {
        $result = $this->soapClient->getPartner($id);
        $partner = $this->valueToArray($result);

        $this->assertArrayIntersectEquals(
            array(
                'id' => $id,
                'partnerCondition' => $this->partnerCreateData['partnerCondition'],
                'status' => $this->partnerCreateData['status'],
                'account' => $this->getReference('orocrm_partner:test_account_1')->getId(),
                'owner' => $this->adminUser->getId(),
            ),
            $partner
        );

        $this->assertArrayHasKey('startDate', $partner);
        $this->assertNotEmpty($partner['startDate']);

        return $partner;
    }
    /**
     * @depends testGet
     * @param array $originalPartner
     * @return integer
     */
    public function testUpdate(array $originalPartner)
    {
        $id = $originalPartner['id'];

        $updateData = [
            'status' => PartnerStatus::STATUS_DISABLE,
            'partnerCondition' => 'Updated Condition',
            'account' => $this->getReference('orocrm_partner:test_account_2')->getId(),
        ];

        $result = $this->soapClient->updatePartner($id, $updateData);
        $this->assertTrue($result, $this->soapClient->__getLastResponse());

        $updatedPartner = $this->soapClient->getPartner($id);
        $updatedPartner = $this->valueToArray($updatedPartner);

        $this->assertArrayHasKey('startDate', $updatedPartner);
        $this->assertNotEmpty($updatedPartner['startDate']);

        $expectedPartner = array_merge($originalPartner, $updateData);

        $this->assertArrayIntersectEquals($expectedPartner, $updatedPartner);

        return $id;
    }

    /**
     * @param integer $id
     * @depends testCreate
     */
    public function testDelete($id)
    {
        $result = $this->soapClient->deletePartner($id);
        $this->assertTrue($result);

        $this->setExpectedException('\SoapFault', 'Record with ID "' . $id . '" can not be found');
        $this->soapClient->getPartner($id);
    }
}
