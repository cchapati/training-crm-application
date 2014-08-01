<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use OroCRM\Bundle\AccountBundle\Entity\Account;

class LoadAccountData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $adminUser = $manager->getRepository('OroUserBundle:User')->findOneByUsername('admin');

        $account = new Account();
        $account->setName('Test Account #1');
        $account->setOwner($adminUser);
        $manager->persist($account);
        $manager->flush();

        $this->addReference('orocrm_partner:test_account_1', $account);

        $account = new Account();
        $account->setName('Test Account #2');
        $account->setOwner($adminUser);
        $manager->persist($account);
        $manager->flush();

        $this->addReference('orocrm_partner:test_account_2', $account);

        $account = new Account();
        $account->setName('Third Test Account');
        $account->setOwner($adminUser);
        $manager->persist($account);
        $manager->flush();

        $this->addReference('orocrm_partner:test_account_3', $account);

        $account = new Account();
        $account->setName('Fourth Account');
        $account->setOwner($adminUser);
        $manager->persist($account);
        $manager->flush();

        $this->addReference('orocrm_partner:test_account_4', $account);

        $account = new Account();
        $account->setName('Fifth Account');
        $account->setOwner($adminUser);
        $manager->persist($account);
        $manager->flush();

        $this->addReference('orocrm_partner:test_account_5', $account);
    }
}
