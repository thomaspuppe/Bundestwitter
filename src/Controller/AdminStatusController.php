<?php
namespace BT\Controller;

use BT\Service\TemplateService;

class AdminStatusController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->renderHeader('adminStatus');
    }

    public function phpinfoAction()
    {
        $view = new TemplateService('Admin/Status/phpinfo');
        $this->responseService->write($view->render());
    }
}
