<?php
namespace BT\Controller;

use BT\Service\TemplateService;
use BT\Model\PartyRepository;

class AdminPartyController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->renderHeader('adminParty');
    }

    public function indexAction()
    {
        $partyRepository = new PartyRepository();
        $parties = $partyRepository->findAll();

        $view = new TemplateService('Admin/Party/index');
        $view->assign(array(
            'parties' => $parties
        ));

        $this->responseService->write($view->render());
    }
}
