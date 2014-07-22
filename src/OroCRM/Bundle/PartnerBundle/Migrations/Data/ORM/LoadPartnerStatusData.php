<?php

namespace OroCRM\Bundle\PartnerBundle\Migrations\Data\ORM;

use Doctrine\Common\Persistence\ObjectManager;

use OroCRM\Bundle\PartnerBundle\Entity\PartnerStatus;
use Oro\Bundle\TranslationBundle\DataFixtures\AbstractTranslatableEntityFixture;

class LoadPartnerStatusData extends AbstractTranslatableEntityFixture
{
    const PARTNER_STATUS_PREFIX = 'partner_status';

    /**
     * @var array
     */
    protected $statusNames = [
        1 => PartnerStatus::STATUS_ACTIVE,
        2 => PartnerStatus::STATUS_DISABLE,
    ];

    /**
     * Load statuses with translation to DB
     *
     * @param ObjectManager $manager
     */
    protected function loadEntities(ObjectManager $manager)
    {
        $statusRepository = $manager->getRepository('OroCRMPartnerBundle:PartnerStatus');

        $translationLocales = $this->getTranslationLocales();

        foreach ($translationLocales as $locale) {
            foreach ($this->statusNames as $order => $statusName) {
                /** @var PartnerStatus $partnerStatus */
                $partnerStatus = $statusRepository->findOneBy(['status' => $statusName]);
                if (!$partnerStatus) {
                    $partnerStatus = new PartnerStatus($statusName);
                    $partnerStatus->setOrder($order);
                }

                $statusTranslated = $this->translate($statusName, static::PARTNER_STATUS_PREFIX, $locale);
                $partnerStatus->setStatus($statusTranslated)->setTranslatableLocale($locale);

                $manager->persist($partnerStatus);
            }

            $manager->flush();
        }
    }
}
