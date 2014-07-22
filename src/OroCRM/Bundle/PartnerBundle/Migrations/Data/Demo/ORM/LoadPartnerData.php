<?php

namespace OroCRM\Bundle\PartnerBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use OroCRM\Bundle\PartnerBundle\Entity\Partner;

class LoadPartnerData extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{
    /**
     * @var array
     */
    static protected $fixtureConditions = array(
        'Lorem ipsum dolor sit amet, consectetuer adipiscing elit',
        'Aenean commodo ligula eget dolor',
        'Aenean massa',
        'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus',
        'Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem',
        'Nulla consequat massa quis enim',
        'Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu',
        'In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo',
        'Nullam dictum felis eu pede mollis pretium',
        'Integer tincidunt',
        'Cras dapibus',
        'Vivamus elementum semper nisi',
        'Aenean vulputate eleifend tellus',
        'Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim',
        'Aliquam lorem ante, dapibus in, viverra quis, feugiat',
        'Aenean imperdiet. Etiam ultricies nisi vel',
        'Praesent adipiscing',
        'Integer ante arcu',
        'Curabitur ligula sapien',
        'Donec posuere vulputate'
    );

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return array(
            'OroCRM\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM\LoadAccountData',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        $numberConditions = count(self::$fixtureConditions) - 1;
        $accounts = $om->getRepository('OroCRMAccountBundle:Account')->findAll();
        $users = $om->getRepository('OroUserBundle:User')->findAll();
        $statuses = $om->getRepository('OroCRMPartnerBundle:PartnerStatus')->findAll();;
        $numberUsers = count($users)-1;
        $numberStatuses = count($statuses) - 1;

        foreach ($accounts as $account) {

            if (!$om->getRepository('OroCRMPartnerBundle:Partner')->findOneBy(['account' => $account])) {
                $partner = new Partner();
                $partner->setAccount($account);
                $partner->setOwner($users[rand(0, $numberUsers)]);
                $partner->setStatus($statuses[rand(0, $numberStatuses)]);
                $partner->setPartnerCondition(self::$fixtureConditions[rand(0, $numberConditions)]);
                $om->persist($partner);
            }
        }

        $om->flush();
    }
}
