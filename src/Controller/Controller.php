<?php
namespace BT\Controller;

class Controller
{

    protected $databaseService;
    protected $responseService;
    protected $isJsonResponse = false;
    protected $isAjaxResponse = false;
    protected $useNewTemplates = false;

    public function __construct()
    {

        $this->databaseService = \BT\Service\DatabaseService::getInstance();
        $this->requestService = \BT\Service\RequestService::getInstance();
        $this->responseService = \BT\Service\ResponseService::getInstance();
    }

    protected function renderHeader($mainNavItem = null, $useNewTemplates = false)
    {
        // Header einbinden/anzeigen, wenn Seite nicht per Ajax aufgerufen wurde
        if (!$this->isAjaxResponse && !$this->isJsonResponse && !$this->requestService->isAjaxRequest()) {
            $headerTemplateName = 'Common/header';

            if ($useNewTemplates == true) {
                $headerTemplateName = 'Common/headerNew';
            }

            if (strpos(get_class($this), 'Admin')) {
                $headerTemplateName = 'Admin/Common/header';
            }

            $headerView = new \BT\Service\TemplateService($headerTemplateName);

            $headerView->assign(array(
                'bodyclass' => 'TODO_BODYCLASS',
                'title' => 'TODO_TITLE',
                // OPTIMIZE: If this is not enough, then create a "renderHeader($menuItem)" class that is called from each controller's constructor and does these lines.
                'mainNavItem' => $mainNavItem
            ));

            $this->responseService->write($headerView->render());
        }

    }


#****f* Controller/ControllerObject/__destruct()
# FUNCTION
# Destruktor. Rendert Quelltext für den Footer und sendet ihn an den Browser,
# wenn die aktuelle Anfrage nicht per Ajax-Request kam.
#
# RESULT
# * [output] Ausgabe von Footer-Quelltext an den Browser, wenn Request nicht per Ajax kam.
#***
    public function __destruct()
    {

        if ($this->isJsonResponse) {
            $this->responseService->addHeader('content-type', 'application/json');
            $this->responseService->flush();
            return;
        }

        if ($this->isAjaxResponse) {
            $this->responseService->flush();
            return;
        }


        // Footer einbinden/anzeigen, wennn Seite nicht per Ajax aufgerufen wurde
        if (!$this->isAjaxResponse && !$this->isJsonResponse && !$this->requestService->isAjaxRequest()) {
            $footerTemplateName = 'Common/footer';

            if ($this->useNewTemplates == true) {
                $footerTemplateName = 'Common/footerNew';
            }

            if (strpos(get_class($this), 'Admin')) {
                $footerTemplateName = 'Admin/Common/footer';
            }

            $footerView = new \BT\Service\TemplateService($footerTemplateName);
            $this->responseService->write($footerView->render())->flush();

            if (isset($GLOBALS['PROFILING']) && isset($GLOBALS['QUERY_COUNTER'])) {
                $GLOBALS['PROFILING'][] = array(intval($GLOBALS['QUERY_COUNTER']) . ' Queries', microtime(true));
            }
            if (isset($GLOBALS['PROFILING']) && isset($GLOBALS['TWITTER_API_COUNTER'])) {
                $GLOBALS['PROFILING'][] = array(intval($GLOBALS['TWITTER_API_COUNTER']) . ' Twitter API Calls', microtime(true));
            }
        }

    }



    /* **************************************************************************
     * Should only be called from Controller-Constructors or -Actions where
     * needed (but these seem to be most ones.)
     * *********************************************************************** */
    protected function provideGlobalAccountData()
    {
        $GLOBALS['globalAccountData'] = unserialize(file_get_contents(ROOT . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . APP . DIRECTORY_SEPARATOR . 'globalAccountData.txt'));
    }


    /* **************************************************************************
     * Default 404 Behaviour that can be called from any Controller.
     * TODO: custom 404 page content fpr any controller (e.g. account page)
     * *********************************************************************** */
    protected function error404Action($args)
    {
        $this->responseService->setStatus(404);
        $view = new \BT\Service\TemplateService('Page/error404');
        $this->responseService->write($view->render());
        die();
    }
}
