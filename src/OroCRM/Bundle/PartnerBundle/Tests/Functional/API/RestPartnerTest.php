<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Functional\API;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class RestPartnerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(array(), $this->generateWsseAuthHeader());
        $this->loadFixtures(
            [
                'OroCRM\Bundle\PartnerBundle\Tests\Functional\DataFixtures\LoadAccountData',
            ],
            true
        );
    }

    /**
     * @return array
     */
    public function testCreate()
    {
        $account = $this->getContainer()
            ->get('doctrine')
            ->getRepository('OroCRMAccountBundle:Account')
            ->findOneByName('Test Account');

        $this->assertNotEmpty($account);

        $request = [
            'partner' => [
                'owner'     => '1',
                'status'    => '1',
                'partnerCondition' => 'Test Condition',
            ]
        ];

        if (!$account) {
            $request['account'] = 0;
            $request['id'] = 0;
            return $request;
        } else {
            $request['account'] = $account->getId();
        }

        $this->client->request(
            'POST',
            $this->getUrl('oro_api_post_partner'),
            $request
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 201);
        $this->assertArrayHasKey('id', $result);

        $request['id'] = $result['id'];
        return $request;
    }

    /**
     * @param array $request
     * @depends testCreate
     */
    public function testGet(array $request)
    {
        $this->client->request(
            'GET',
            $this->getUrl('oro_api_get_partners')
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $id = $request['id'];
        $result = array_filter(
            $result,
            function ($a) use ($id) {
                return $a['id'] == $id;
            }
        );

        $this->assertNotEmpty($result);
        $this->assertEquals($request['partner']['partnerCondition'], reset($result)['partnerCondition']);

        $this->client->request(
            'GET',
            $this->getUrl('oro_api_get_partner', ['id' => $request['id']])
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals($request['partner']['partnerCondition'], $result['partnerCondition']);
    }

    /**
     * @param array $request
     * @depends testCreate
     */
    public function testUpdate(array $request)
    {
        $request['partner']['partnerCondition'] .= "_Updated";
        $this->client->request(
            'PUT',
            $this->getUrl('oro_api_put_partner', ['id' => $request['id']]),
            $request
        );

        $result = $this->client->getResponse();

        $this->assertJsonResponseStatusCodeEquals($result, 204);

        $this->client->request(
            'GET',
            $this->getUrl('oro_api_get_partner', ['id' => $request['id']])
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals($request['partner']['partnerCondition'], $result['partnerCondition']);
    }

    /**
     * @param array $request
     * @depends testCreate
     */
    public function testDelete(array $request)
    {
        $this->client->request(
            'DELETE',
            $this->getUrl('oro_api_delete_partner', ['id' => $request['id']])
        );
        $result = $this->client->getResponse();

        $this->assertJsonResponseStatusCodeEquals($result, 204);

        $this->client->request(
            'GET',
            $this->getUrl('oro_api_get_partner', ['id' => $request['id']])
        );
        $result = $this->client->getResponse();

        $this->assertJsonResponseStatusCodeEquals($result, 404);
    }
}
