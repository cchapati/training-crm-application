<?php

namespace OroCRM\Bundle\PartnerBundle\Autocomplete;

use Doctrine\ORM\EntityManager;

use Oro\Bundle\FormBundle\Autocomplete\SearchHandlerInterface;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

class SearchHandler implements SearchHandlerInterface
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var AclHelper
     */
    protected $aclHelper;

    /**
     * @param EntityManager $manager
     * @param AclHelper     $aclHelper
     */
    public function __construct(EntityManager $manager, AclHelper $aclHelper)
    {
        $this->manager = $manager;
        $this->aclHelper = $aclHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function convertItem($item)
    {
        return array(
            'id' => $item->getId(),
            'name' => $item->getName()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function search($query, $page, $perPage, $searchById = false)
    {
        $query = trim($query);

        $page = (int)$page > 0 ? (int)$page : 1;
        $perPage = (int)$perPage > 0 ? (int)$perPage : 10;
        $firstResult = ($page - 1) * $perPage;
        $perPage += 1;

        $queryBuilder = $this->manager->createQueryBuilder();

        $queryBuilder->from('OroCRMAccountBundle:Account', 'a')
            ->select('a')
            ->where('p IS NULL')
            ->leftJoin('OroCRMPartnerBundle:Partner', 'p', 'WITH', 'a = p.account')
            ->setFirstResult($firstResult)
            ->setMaxResults($perPage)
            ->orderBy('a.name');

        if ($query) {
            if ($searchById) {
                $queryBuilder->andWhere('a.id = :id');
                $queryBuilder->setParameters(array('id' => $query));
            } else {
                $queryBuilder->addSelect('LOCATE(:query, a.name) as HIDDEN entry_position');
                $queryBuilder->andWhere('a.name like :search_expression');
                $queryBuilder->orderBy('entry_position');
                $queryBuilder->addOrderBy('a.name');
                $queryBuilder->setParameters(array('query' => $query, 'search_expression' => "%{$query}%"));
            }

        }

        $items = $this->aclHelper->apply($queryBuilder)->execute();

        $hasMore = count($items) == $perPage;
        if ($hasMore) {
            $items = array_slice($items, 0, $perPage - 1);
        }

        return array('results' => $this->convertItems($items), 'more' => $hasMore);
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return array('name');
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityName()
    {
        return 'OroCRM\Bundle\AccountBundle\Entity\Account';
    }

    /**
     * @param array $items
     * @return array
     */
    protected function convertItems(array $items)
    {
        $convertedItems = array();
        foreach ($items as $item) {
            $convertedItems[] = $this->convertItem($item);
        }
        return $convertedItems;
    }
}
