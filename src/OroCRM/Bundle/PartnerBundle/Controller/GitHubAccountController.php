<?php

namespace OroCRM\Bundle\PartnerBundle\Controller;

use Doctrine\ORM\EntityManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use OroCRM\Bundle\PartnerBundle\Entity\GitHubAccount;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use OroCRM\Bundle\PartnerBundle\Entity\Partner;

/**
 * @Route("/github-account")
 */
class GitHubAccountController extends Controller
{

    /**
     * @Route("/", name="orocrm_github_account_index")
     * @AclAncestor("orocrm_partner_view")
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/widget/partner-github-accounts/{id}",
     *      name="orocrm_partner_git_hub_accounts_widget",
     *      requirements={"id"="\d+"}
     * )
     * @AclAncestor("orocrm_partner_view")
     * @Template
     */
    public function partnerGitHubAccountsAction(Partner $partner)
    {
        return array('partner' => $partner);
    }

    /**
     * @Route("/delete/{id}", name="orocrm_github_account_request_delete", requirements={"id"="\d+"})
     * @AclAncestor("orocrm_partner_delete")
     */
    public function deleteAction(GitHubAccount $entity)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        $em->remove($entity);
        $em->flush();

        return new JsonResponse();
    }
}
