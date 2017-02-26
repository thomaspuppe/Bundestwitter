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

    public function indexAction()
    {
        $personRepository = new PersonRepository();
        $persons = $personRepository->findAll();

        $view = new TemplateService('Admin/Person/index');
        $view->assign(array(
            'persons' => $persons
        ));

        $this->responseService->write($view->render());
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

        $countPersons = count($persons);
        $countDirectCandidates = 0;
        $countListCandidates = array();
        $countDirectCandidateSlots = count($electoraldistricts) * count($parties);

        foreach ($persons as $person) {
            if ($person->electoraldistrict_id > 0) {
                $candidateArray[$person->electoraldistrict_id][$person->party_slug] = $person->getName();
                $countDirectCandidates++;
            } else {
                if (isset($countListCandidates[$person->party_slug])) {
                    $countListCandidates[$person->party_slug]++;
                } else {
                    $countListCandidates[$person->party_slug] = 1;
                }
            }
        }

        $view = new TemplateService('Admin/Person/candidate');
        $view->assign(array(
            'parties' => $parties,
            'persons' => $persons,
            'electoraldistricts' => $electoraldistricts,
            'candidateArray' => $candidateArray,
            'countPersons' => $countPersons,
            'countDirectCandidates' => $countDirectCandidates,
            'countListCandidates' => $countListCandidates,
            'countDirectCandidateSlots' => $countDirectCandidateSlots
        ));

        $this->responseService->write($view->render());
    }
}
