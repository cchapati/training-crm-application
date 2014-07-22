<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Functional\API;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class SoapPartnerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(array(), $this->generateWsseAuthHeader());
        $this->initSoapClient();
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
            'owner'     => '1',
            'status'    => '1',
            'partnerCondition' => 'Test Condition',
        ];

        if (!$account) {
            $request['account'] = 0;
            $request['id'] = 0;
            return $request;
        } else {
            $request['account'] = (string)$account->getId();
        }

        $id = $this->soapClient->createPartner($request);
        $this->assertTrue((bool) $id, $this->soapClient->__getLastResponse());

        $request['id'] = $id;

        return $request;
    }

    /**
     * @param array $request
     * @depends testCreate
     */
    public function testGet(array $request)
    {
        $result = $this->soapClient->getPartners(1, 1000);
        $result = $this->valueToArray($result);
        $id = $request['id'];
        $partners = $partner = $result['item'];
        if (isset($partners[0])) {
            $partners = array_filter(
                $partners,
                function ($partner) use ($id) {
                    return $partner['id'] == $id;
                }
            );
            $partner = reset($partners);
        }

        $this->assertEquals($request['partnerCondition'], $partner['partnerCondition']);
        $this->assertEquals($request['id'], $partner['id']);
    }

    /**
     * @param array $request
     * @depends testCreate
     */
    public function testUpdate(array $request)
    {
        $id = $request['id'];
        unset($request['id']);
        $request['partnerCondition'] .= '_Updated';

        $result = $this->soapClient->updatePartner($id, $request);
        $this->assertTrue($result);

        $partner = $this->soapClient->getPartner($id);
        $partner = $this->valueToArray($partner);

        $this->assertEquals($request['partnerCondition'], $partner['partnerCondition']);
    }

    /**
     * @param array $request
     * @depends testCreate
     */
    public function testDelete(array $request)
    {
        $result = $this->soapClient->deletePartner($request['id']);
        $this->assertTrue($result);

        $this->setExpectedException('\SoapFault', 'Record with ID "' . $request['id'] . '" can not be found');
        $this->soapClient->getPartner($request['id']);
    }
}
