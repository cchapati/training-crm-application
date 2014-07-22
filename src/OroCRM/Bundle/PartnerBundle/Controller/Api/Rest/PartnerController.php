<?php

namespace OroCRM\Bundle\PartnerBundle\Controller\Api\Rest;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormInterface;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Routing\ClassResourceInterface;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Oro\Bundle\SoapBundle\Form\Handler\ApiFormHandler;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;

/**
 * @RouteResource("partner")
 * @NamePrefix("oro_api_")
 */
class PartnerController extends RestController implements ClassResourceInterface
{
    /**
     * REST GET LIST PARTNER
     *
     * @QueryParam(
     *      name="page",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Page number, starting from 1. Defaults to 1."
     * )
     * @QueryParam(
     *      name="limit",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Number of items per page. defaults to 10."
     * )
     * @ApiDoc(
     *      description="Get all partners",
     *      resource=true
     * )
     * @AclAncestor("orocrm_partner_view")
     *
     * @return Response
     */
    public function cgetAction()
    {
        return $this->handleGetListRequest();
    }

    /**
     * REST GET PARTNER
     *
     * @param integer $id
     *
     * @ApiDoc(
     *      description="Get partner",
     *      resource=true
     * )
     * @AclAncestor("orocrm_partner_view")
     *
     * @return Response
     */
    public function getAction($id)
    {
        return $this->handleGetRequest($id);
    }

    /**
     * REST PUT PARTNER
     *
     * @param integer $id
     *
     * @ApiDoc(
     *      description="Update partner",
     *      resource=true
     * )
     * @AclAncestor("orocrm_partner_update")
     *
     * @return Response
     */
    public function putAction($id)
    {
        return $this->handleUpdateRequest($id);
    }

    /**
     * REST POST PARTNER
     *
     * @ApiDoc(
     *      description="Create new partner",
     *      resource=true
     * )
     * @AclAncestor("orocrm_partner_create")
     */
    public function postAction()
    {
        return $this->handleCreateRequest();
    }

    /**
     * REST DELETE PARTNER
     *
     * @param int $id
     *
     * @ApiDoc(
     *      description="Delete Partner",
     *      resource=true
     * )
     * @Acl(
     *      id="orocrm_partner_delete",
     *      type="entity",
     *      permission="DELETE",
     *      class="OroCRMPartnerBundle:Partner"
     * )
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * Get entity Manager
     *
     * @return ApiEntityManager
     */
    public function getManager()
    {
        return $this->get('orocrm_partner.partner.manager.api');
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->get('orocrm_partner.form.type.partner.api');
    }

    /**
     * @return ApiFormHandler
     */
    public function getFormHandler()
    {
        return $this->get('orocrm_partner.form.handler.partner.api');
    }
}
