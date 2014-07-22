<?php

namespace OroCRM\Bundle\PartnerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use OroCRM\Bundle\PartnerBundle\Entity\Partner;
use OroCRM\Bundle\AccountBundle\Entity\Account;

class PartnerController extends Controller
{
    /**
     * Show accounts list of partners
     *
     * @Route("/", name="orocrm_partner_index")
     * @AclAncestor("orocrm_partner_view")
     * @Template
     */
    public function indexAction()
    {
        return [
            'entity_class' => $this->container->getParameter('orocrm_partner.partner.entity.class')
        ];
    }

    /**
     * Create Partner
     *
     * @Route("/create", name="orocrm_partner_create")
     * @Acl(
     *      id="orocrm_partner_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="OroCRMPartnerBundle:Partner"
     * )
     * @Template("OroCRMPartnerBundle:Partner:update.html.twig")
     */
    public function createAction()
    {
        return $this->update();
    }

    /**
     * Edit Partner
     *
     * @Route("/update/{id}", name="orocrm_partner_update", requirements={"id"="\d+"})
     * @Acl(
     *      id="orocrm_partner_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="OroCRMPartnerBundle:Partner"
     * )
     * @Template()
     */
    public function updateAction(Partner $entity)
    {
        return $this->update($entity);
    }

    /**
     * @Route("/view/{id}", name="orocrm_partner_view", requirements={"id"="\d+"})
     * @Acl(
     *      id="orocrm_partner_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="OroCRMPartnerBundle:Partner"
     * )
     * @Template()
     */
    public function viewAction(Partner $partner)
    {
        return [
            'entity' => $partner
        ];
    }

    /**
     * @param Partner $entity
     * @return array
     */
    private function update(Partner $entity = null)
    {
        if (!$entity) {
            $entity = new Partner();
        }

        return $this->get('oro_form.model.update_handler')->handleUpdate(
            $entity,
            $this->get('orocrm_partner.form.partner'),
            function (Partner $entity) {
                return [
                    'route' => 'orocrm_partner_update',
                    'parameters' => array('id' => $entity->getId())
                ];
            },
            function (Partner $entity) {
                return [
                    'route' => 'orocrm_partner_view',
                    'parameters' => array('id' => $entity->getId())
                ];
            },
            $this->get('translator')->trans('orocrm.partner.controller.partner.saved.message'),
            $this->get('orocrm_partner.form.handler.partner')
        );
    }

    /**
     * @Route("/widget/info/{id}", name="orocrm_partner_widget_info", requirements={"id"="\d+"})
     * @AclAncestor("orocrm_partner_view")
     * @Template()
     */
    public function infoAction(Partner $partner)
    {
        return [
            'partner' => $partner
        ];
    }

    /**
     * @Route("/widget/account/info/{id}", name="orocrm_partner_account_widget_info", requirements={"id"="\d+"})
     * @AclAncestor("orocrm_partner_view")
     * @Template()
     */
    public function accountinfoAction(Account $account)
    {
        return [
            'account' => $account
        ];
    }
}
