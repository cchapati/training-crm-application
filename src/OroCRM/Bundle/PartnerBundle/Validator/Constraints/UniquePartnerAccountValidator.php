<?php

namespace OroCRM\Bundle\PartnerBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use OroCRM\Bundle\PartnerBundle\Entity\Partner;

class UniquePartnerAccountValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->entityManager = $managerRegistry->getManager();
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof Partner) {
            throw new UnexpectedTypeException($value, 'OroCRM\Bundle\PartnerBundle\Entity\Partner');
        }

        if ($value->getAccount() && $this->isAccountHasPartner($value)) {
            /** @var UniquePartnerAccount $constraint */
            $this->context->addViolation($constraint->message);
        }
    }

    /**
     * @param Partner $partner
     * @return bool
     */
    protected function isAccountHasPartner(Partner $partner)
    {
        $queryBuilder = $this->entityManager->getRepository('OroCRMPartnerBundle:Partner')
            ->createQueryBuilder('partner')
            ->select('COUNT(partner)')
            ->where('partner.account = :account')
            ->setParameter('account', $partner->getAccount())
            ->setMaxResults(1);

        if ($partner->getId()) {
            $queryBuilder->andWhere('partner != :partner')->setParameter('partner', $partner);
        }

        return (bool)$queryBuilder->getQuery()->getSingleScalarResult();
    }
}
