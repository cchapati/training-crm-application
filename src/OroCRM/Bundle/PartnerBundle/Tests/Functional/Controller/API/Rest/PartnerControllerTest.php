<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Functional\Controller\API\Rest;

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
    protected $partnerPostData = [
        'owner' => null,
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
        $this->loadFixtures(['OroCRM\Bundle\PartnerBundle\Tests\Functional\DataFixtures\LoadAccountData']);

        $this->partnerPostData['account'] = $this->getReference('orocrm_partner:test_account_1')->getId();

        $this->adminUser = $this->getContainer()->get('doctrine')
            ->getRepository('OroUserBundle:User')->findOneByUsername('admin');
        $this->assertNotEmpty($this->adminUser);

        $this->partnerPostData['owner'] = $this->adminUser->getId();
    }

    /**
     * @return array
     */
    public function testPost()
    {
        $request = [
            'partner' => $this->partnerPostData
        ];

        $this->client->request(
            'POST',
            $this->getUrl('orocrm_partner_api_post_partner'),
            $request
        );

        $response = $this->getJsonResponseContent($this->client->getResponse(), 201);
        $this->assertArrayHasKey('id', $response);

        return $response['id'];
    }

    /**
     * @depends testPost
     */
    public function testCget($id)
    {
        $this->client->request(
            'GET',
            $this->getUrl('orocrm_partner_api_get_partners'),
            [],
            [],
            $this->generateWsseAuthHeader()
        );

        $partners = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertCount(1, $partners);

        $this->assertArrayIntersectEquals(
            [
                'id' => $id,
                'partnerCondition' => $this->partnerPostData['partnerCondition'],
                'status' => $this->partnerPostData['status'],
                'account' => $this->partnerPostData['account'],
                'owner' => $this->adminUser->getId(),
            ],
            $partners[0]
        );

        $this->assertArrayHasKey('startDate', $partners[0]);
        $this->assertNotEmpty($partners[0]['startDate']);
    }

    /**
     * @depends testPost
     * @param integer $id
     * @return array
     */
    public function testGet($id)
    {
        $this->client->request(
            'GET',
            $this->getUrl('orocrm_partner_api_get_partner', ['id' => $id]),
            [],
            [],
            $this->generateWsseAuthHeader()
        );

        $partner = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertArrayIntersectEquals(
            [
                'partnerCondition' => $this->partnerPostData['partnerCondition'],
                'status' => $this->partnerPostData['status'],
                'account' => $this->partnerPostData['account'],
                'owner' => $this->adminUser->getId(),
            ],
            $partner
        );

        $this->assertArrayHasKey('startDate', $partner);
        $this->assertNotEmpty($partner['startDate']);

        $this->assertArrayHasKey('id', $partner);
        $this->assertGreaterThan(0, $partner['id']);

        return $partner;
    }

    /**
     * @param array $originalPartner
     * @depends testGet
     */
    public function testPut(array $originalPartner)
    {
        $id = $originalPartner['id'];

        $putData = [
            'status' => PartnerStatus::STATUS_DISABLE,
            'partnerCondition' => 'Updated Condition',
            'account' => $this->getReference('orocrm_partner:test_account_2')->getId(),
        ];

        $this->client->request(
            'PUT',
            $this->getUrl('orocrm_partner_api_put_partner', ['id' => $id]),
            ['partner' => $putData],
            [],
            $this->generateWsseAuthHeader()
        );

        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 204);

        $this->client->request(
            'GET',
            $this->getUrl('orocrm_partner_api_get_partner', ['id' => $id])
        );

        $updatedPartner = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $expectedPartner = array_merge($originalPartner, $putData);

        $this->assertArrayIntersectEquals($expectedPartner, $updatedPartner);

        return $id;
    }

    /**
     * @param int $id
     * @depends testPut
     */
    public function testDelete($id)
    {
        $this->client->request(
            'DELETE',
            $this->getUrl('orocrm_partner_api_delete_partner', ['id' => $id])
        );
        $result = $this->client->getResponse();

        $this->assertJsonResponseStatusCodeEquals($result, 204);

        $this->client->request(
            'GET',
            $this->getUrl('orocrm_partner_api_delete_partner', ['id' => $id])
        );
        $result = $this->client->getResponse();

        $this->assertJsonResponseStatusCodeEquals($result, 404);
    }
}
