<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use OroCRM\Bundle\AccountBundle\Entity\Account;

class LoadAccountData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setOwner($manager->getRepository('OroUserBundle:User')->findOneById(1));
        $manager->persist($account);
        $manager->flush();
    }
}
