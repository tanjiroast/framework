<?php
namespace controllers;

use Attribute;
use models\Groupe;
use models\Organization;
use models\User;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\orm\DAO;
use Ubiquity\orm\repositories\ViewRepository;
use Ubiquity\utils\http\URequest;

/**
 * Controller OrgaController
 */
class OrgaController extends ControllerBase{
    #[Route('org')]
    public function index(){
        //$orgas=DAO::getAll(Organization::class, "like '%uni%'",true);
        //$this->loadView("OrgaController/index.html",['orga'=>$orgas]);
    }

    public function initialize()
    {
        parent::initialize();
        $this->repo=new ViewRepository($this, Organization::class);
    }

    #[Route(path: "orga",name: "orga.index2")]
    public function index2(){
        //$orgas=DAO::getAll(Organization::class, "",false);
        //$this->loadView("OrgaController/index.html",['orgas'=>$orgas]);
        $this->repo->all("",false);
        $this->loadView("OrgaController/index.html");
    }


    #[Route(path: "orga/{idOrga}",name: "orga.getOne")]
    public function getOne($idOrga){
        //$orga=DAO::getById(Organization::class, $idOrga,['groupe.users']);
        $this->repo->byId($idOrga,['users.groupes','groupes.users']);
        //$this->loadDefaultView(['orga'=>$orga]);
        $this->loadDefaultView();
    }

    #[Route()]
    public function getOrga($name,$aliases){
        DAO::getOne(Organization::class,"name= ?", parameters:[$name,$aliases]);
    }

    public function testInsert(){
        $groupe=new Groupe();
        URequest::setValuesToObject($groupe);
        $idOrga=URequest::post('idOrga');
        $orga=DAO::getById(Organization::class,$idOrga);
        $groupe->setOrganization($orga);
        DAO::insert($orga);


    }

    public function testUpdate(){
        $groupe=DAO::getByID(Groupe::class,URequest::post('idGroupe'));
        URequest::setValuesToObject($groupe);
        $idOrga=URequest::post('idOrga');
        $orga=DAO::getById(Organization::class,$idOrga);
        $groupe->setOrganization($orga);
        $idUsers=explode(',',URequest::post('users'));
        $users=DAO::getAllByIds(User::class,$idUsers);
        foreach($users as $user){
            $groupe->addUser($user);
        }
        DAO::update($orga,true);


    }

}