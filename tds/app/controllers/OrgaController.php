<?php
namespace controllers;

 use models\Organization;
 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\orm\DAO;

 /**
 * Controller OrgaController
 **/
class OrgaController extends ControllerBase{
    #[Route('orga')]
	public function index(){
        $orgas=DAO::getAll(Organization::class,"", ['groupes.users']);
        $this->loadView("OrgaController/index.html",['orgas'=>$orgas]);
    }

}
