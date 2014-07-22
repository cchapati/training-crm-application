<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Functional;

use Symfony\Component\DomCrawler\Form;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class PartnerControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(
            [],
            array_merge($this->generateBasicAuthHeader(), array('HTTP_X-CSRF-Header' => 1))
        );
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('orocrm_partner_index'));
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
    }

    public function testCreate()
    {
        $this->loadFixtures(
            [
                'OroCRM\Bundle\PartnerBundle\Tests\Functional\DataFixtures\LoadAccountData',
            ],
            true
        );
        $crawler = $this->client->request('GET', $this->getUrl('orocrm_partner_create'));
        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['orocrm_partner_form[account]'] = $this->getContainer()
            ->get('doctrine')
            ->getRepository('OroCRMAccountBundle:Account')
            ->findOneByName('Test Account')
            ->getId();
        $form['orocrm_partner_form[partnerCondition]'] = 'Test condition';
        $form['orocrm_partner_form[owner]'] = 1;

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains("Partner saved", $crawler->html());
    }

    /**
     * @depends testCreate
     */
    public function testUpdate()
    {
        $response = $this->client->requestGrid(
            'partner-accounts-grid',
            array('partner-accounts-grid[_filter][condition][value]' => 'Test condition')
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);

        $id = $result['id'];
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('orocrm_partner_update', array('id' => $result['id']))
        );
        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['orocrm_partner_form[partnerCondition]'] = 'Condition update';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains("Partner saved", $crawler->html());

        return $id;
    }

    /**
     * @depends testUpdate
     */
    public function testView($id)
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('orocrm_partner_view', array('id' => $id))
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains("Test Account - View", $crawler->html());
    }


    /**
     * @depends testUpdate
     */
    public function testDelete($id)
    {
        $this->client->request(
            'DELETE',
            $this->getUrl('oro_api_delete_partner', array('id' => $id))
        );
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 204);
        $this->client->request('GET', $this->getUrl('orocrm_partner_view', array('id' => $id)));
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 404);
    }
}
