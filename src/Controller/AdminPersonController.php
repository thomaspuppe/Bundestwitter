<?php
namespace BT\Controller;

use BT\Service\TemplateService;
use BT\Model\ElectoraldistrictRepository;
use BT\Model\PartyRepository;
use BT\Model\PersonRepository;

class AdminPersonController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->renderHeader('adminPerson');
    }

    public function candidateAction()
    {
        $partyRepository = new PartyRepository();
        $parties = $partyRepository->findAll();

        $electoraldistrictRepository = new ElectoraldistrictRepository();
        $electoraldistricts = $electoraldistrictRepository->findAll();

        $personRepository = new PersonRepository();
        $persons = $personRepository->findAll();

        $candidateArray = array();
        foreach ($electoraldistricts as $electoraldistrict) {
            if (!array_key_exists($electoraldistrict->id, $candidateArray)) {
                $candidateArray[$electoraldistrict->id] = array();
            }
        }

        foreach ($persons as $person) {
            $candidateArray[$person->electoraldistrict_id][$person->party_slug] = $person->getName();
        }

        $view = new TemplateService('Admin/Person/candidate');
        $view->assign(array(
            'parties' => $parties,
            'persons' => $persons,
            'electoraldistricts' => $electoraldistricts,
            'candidateArray' => $candidateArray
        ));

        $this->responseService->write($view->render());
    }
}
