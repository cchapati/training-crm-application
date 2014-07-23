<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Functional\Controller;

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
     * @var User
     */
    protected $adminUser;

    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());

        $this->loadFixtures(['OroCRM\Bundle\PartnerBundle\Tests\Functional\DataFixtures\LoadAccountData']);

        $this->adminUser = $this->getContainer()->get('doctrine')
            ->getRepository('OroUserBundle:User')->findOneByUsername('admin');
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', $this->getUrl('orocrm_partner_create'));
        $form = $crawler->selectButton('Save and Close')->form();
        $form['orocrm_partner_form[status]'] = PartnerStatus::STATUS_ACTIVE;
        $form['orocrm_partner_form[account]'] = $this->getReference('orocrm_partner:test_account_1')->getId();
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
    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('orocrm_partner_index'));
        $response = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($response, 200);
        $this->assertContains('Test condition', $response->getContent());
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

        $response = $this->client->getResponse();
        file_put_contents('/tmp/test.html', $response->getContent());
        $this->assertHtmlResponseStatusCodeEquals($response, 200);
        $this->assertContains(
            $this->getReference('orocrm_partner:test_account_1')->getName()
            . " - View - Partners",
            $crawler->html()
        );
    }
}
