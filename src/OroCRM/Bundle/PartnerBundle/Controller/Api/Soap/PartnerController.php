<?php

namespace OroCRM\Bundle\PartnerBundle\Controller\Api\Soap;

use Symfony\Component\Form\FormInterface;
use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;

use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;
use Oro\Bundle\SoapBundle\Controller\Api\Soap\SoapController;
use Oro\Bundle\SoapBundle\Form\Handler\ApiFormHandler;

class PartnerController extends SoapController
{
    /**
     * @Soap\Method("getPartners")
     * @Soap\Param("page", phpType="int")
     * @Soap\Param("limit", phpType="int")
     * @Soap\Result(phpType = "OroCRM\Bundle\PartnerBundle\Entity\Partner[]")
     */
    public function cgetAction($page = 1, $limit = 10)
    {
        return $this->handleGetListRequest($page, $limit);
    }

    /**
     * @Soap\Method("getPartner")
     * @Soap\Param("id", phpType = "int")
     * @Soap\Result(phpType = "OroCRM\Bundle\PartnerBundle\Entity\Partner")
     */
    public function getAction($id)
    {
        return $this->handleGetRequest($id);
    }

    /**
     * @Soap\Method("createPartner")
     * @Soap\Param("partner", phpType = "OroCRM\Bundle\PartnerBundle\Entity\Partner")
     * @Soap\Result(phpType = "int")
     */
    public function createAction($partner)
    {
        return $this->handleCreateRequest();
    }

    /**
     * @Soap\Method("updatePartner")
     * @Soap\Param("id", phpType = "int")
     * @Soap\Param("partner", phpType = "OroCRM\Bundle\PartnerBundle\Entity\Partner")
     * @Soap\Result(phpType = "boolean")
     */
    public function updateAction($id, $partner)
    {
        return $this->handleUpdateRequest($id);
    }

    /**
     * @Soap\Method("deletePartner")
     * @Soap\Param("id", phpType = "int")
     * @Soap\Result(phpType = "boolean")
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * @return ApiEntityManager
     */
    public function getManager()
    {
        return $this->container->get('orocrm_partner.partner.manager.api');
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->container->get('orocrm_partner.form.partner.api');
    }

    /**
     * @return ApiFormHandler
     */
    public function getFormHandler()
    {
        return $this->container->get('orocrm_partner.form.handler.partner.api');
    }
}
