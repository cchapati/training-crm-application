<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Functional\Autocomplete;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use OroCRM\Bundle\PartnerBundle\Autocomplete\SearchHandler;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class SearchHandlerTest extends WebTestCase
{
    /**
     * @var SearchHandler
     */
    protected $searchHandler;

    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());

        $this->loadFixtures(['OroCRM\Bundle\PartnerBundle\Tests\Functional\DataFixtures\LoadPartnerData']);
        $this->searchHandler = $this->getContainer()->get('orocrm_partner.form.autocomplete.account.search_handler');
    }

    /**
     * @dataProvider searchDataProvider
     */
    public function testSearch($data, $expected)
    {
        $expected = array(
            'results' => array_map(array($this, 'getResultByReference'), $expected['results']),
            'more' => $expected['more']
        );

        $data = $this->searchHandler->search($data['query'], $data['page'], $data['perPage']);
        $this->assertEquals($expected, $data);
    }

    public function searchDataProvider()
    {
        return array(
            'Search returns an alphabetic ordered data' => array(
                'data' => array(
                    'page' => 1,
                    'perPage' => 10,
                    'query' => ''
                ),
                'expected' => array(
                    'results' => array(
                        'orocrm_partner:test_account_5',
                        'orocrm_partner:test_account_4',
                        'orocrm_partner:test_account_1'
                    ),
                    'more' => false
                )
            ),
            'returns data included query string' => array(
                'data' => array(
                    'page' => 1,
                    'perPage' => 10,
                    'query' => 'Test'
                ),
                'expected' => array(
                    'results' => array(
                        'orocrm_partner:test_account_1'
                    ),
                    'more' => false
                )
            ),
            'returns data ordered by entry position' => array(
                'data' => array(
                    'page' => 1,
                    'perPage' => 10,
                    'query' => 't'
                ),
                'expected' => array(
                    'results' => array(
                        'orocrm_partner:test_account_1',
                        'orocrm_partner:test_account_5',
                        'orocrm_partner:test_account_4'
                    ),
                    'more' => false
                )
            ),
            'returns more == true if has more rows' => array(
                'data' => array(
                    'page' => 1,
                    'perPage' => 2,
                    'query' => 't'
                ),
                'expected' => array(
                    'results' => array(
                        'orocrm_partner:test_account_1',
                        'orocrm_partner:test_account_5'
                    ),
                    'more' => true
                )
            ),
            'returns correct page of data' => array(
                'data' => array(
                    'page' => 2,
                    'perPage' => 2,
                    'query' => 't'
                ),
                'expected' => array(
                    'results' => array(
                        'orocrm_partner:test_account_4'
                    ),
                    'more' => false
                )
            ),
        );
    }

    /**
     * @param string $reference
     * @return array
     */
    protected function getResultByReference($reference)
    {
        $account = $this->getReference($reference);
        return array('id' => $account->getId(), 'name' => $account->getName());
    }
}
