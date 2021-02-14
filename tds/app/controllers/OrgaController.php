<?php
namespace controllers;

 use models\Organization;
 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\orm\DAO;
 use Ubiquity\orm\repositories\ViewRepository;

 /**
 * Controller OrgaController
 **/
class OrgaController extends ControllerBase{

    private ViewRepository $repo;

    public function initialize(){
        parent::initialize();
        $this->repo = new ViewRepository($this, Organization::class);
    }

    #[Route('orga')]
	public function index(){
        $orgas=DAO::getAll(Organization::class,"", ['groupes.users']);
        $this->loadView("OrgaController/index.html",['orgas'=>$orgas]);
    }

}
