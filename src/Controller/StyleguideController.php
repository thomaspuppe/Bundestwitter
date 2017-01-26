<?php
namespace BT\Controller;

use \BT\Service\TemplateService;

class StyleguideController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->renderHeader();
    }

    public function indexAction($args)
    {
        $view = new \BT\Service\TemplateService('Public/Styleguide/index');
        $this->responseService->write($view->render());
    }
}
