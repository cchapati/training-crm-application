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
        PartnerStatus::STATUS_ACTIVE,
        PartnerStatus::STATUS_DISABLE,
    ];

    /**
     * Load statuses with translation to DB
     *
     * @param ObjectManager $objectManager
     */
    protected function loadEntities(ObjectManager $objectManager)
    {
        $statusRepository = $objectManager->getRepository('OroCRMPartnerBundle:PartnerStatus');

        $translationLocales = $this->getTranslationLocales();

        foreach ($translationLocales as $locale) {
            foreach ($this->statusNames as $order => $statusName) {
                /** @var PartnerStatus $partnerStatus */
                $partnerStatus = $statusRepository->findOneBy(['name' => $statusName]);
                if (!$partnerStatus) {
                    $partnerStatus = new PartnerStatus($statusName);
                    $partnerStatus->setOrder($order);
                }

                $statusTranslated = $this->translate($statusName, static::PARTNER_STATUS_PREFIX, $locale);
                $partnerStatus->setLocale($locale)
                    ->setLabel($statusTranslated);

                $objectManager->persist($partnerStatus);
            }

            $objectManager->flush();
        }
    }
}
