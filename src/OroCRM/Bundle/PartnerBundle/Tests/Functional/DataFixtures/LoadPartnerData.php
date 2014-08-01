<?php

namespace OroCRM\Bundle\PartnerBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use OroCRM\Bundle\PartnerBundle\Entity\Partner;

class LoadPartnerData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $adminUser = $manager->getRepository('OroUserBundle:User')->findOneByUsername('admin');

        $partner = new Partner();
        $partner->setAccount($this->getReference('orocrm_partner:test_account_2'));
        $partner->setOwner($adminUser);
        $manager->persist($partner);
        $manager->flush();
        $this->addReference('orocrm_partner:test_partner_1', $partner);

        $partner = new Partner();
        $partner->setAccount($this->getReference('orocrm_partner:test_account_3'));
        $partner->setOwner($adminUser);
        $manager->persist($partner);
        $manager->flush();
        $this->addReference('orocrm_partner:test_partner_2', $partner);
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return array(
            'OroCRM\\Bundle\\PartnerBundle\\Tests\\Functional\\DataFixtures\\LoadAccountData'
        );
    }
}
