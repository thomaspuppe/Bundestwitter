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

    public function indexAction()
    {
        $view = new TemplateService('Admin/Status/index');

        // TOTO: Use Memcached Singleton Service
        $memcached_ok = false;
        $memcached_message = 'Memcached not available';

        $mc = new \Memcached();
        $mc->addServer('127.0.0.1', 11211);
        $result = $mc->get("status_page_check") or $mc->set("status_page_check", date('Y-m-d H:i:s'));
        if ($result) {
            $memcached_ok = true;
            $memcached_message = $result;
        }

        $view->assign(array(
            'memcached_ok' => $memcached_ok,
            'memcached_message' => $memcached_message,
        ));

        $this->responseService->write($view->render());
    }

    public function phpinfoAction()
    {
        $view = new TemplateService('Admin/Status/phpinfo');
        $this->responseService->write($view->render());
    }
}
