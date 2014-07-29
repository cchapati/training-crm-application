<?php

namespace OroCRM\Bundle\PartnerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use OroCRM\Bundle\PartnerBundle\Entity\Partner;

/**
 * @Route("/partner-github")
 */
class GitHubAccountController extends Controller
{
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
}
