<?php
namespace controllers;
use classes\baskets;
use classes\LocalBasket;
use models\Basket;
use models\Basketdetail;
use models\Order;
use models\Product;
use models\Section;
use services\dao\UserRepository;
use Ubiquity\attributes\items\di\Autowired;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\controllers\auth\AuthController;
use Ubiquity\controllers\auth\WithAuthTrait;
use Ubiquity\controllers\Router;
use Ubiquity\orm\DAO;
use Ubiquity\utils\http\URequest;
use Ubiquity\utils\http\UResponse;
use Ubiquity\utils\http\USession;
/**
 * Controller MainController
 */
class MainController extends ControllerBase{

    use WithAuthTrait;

    #[Autowired]
    private UserRepository $repo;

    #[Route('_default',name:'home')]
    public function index(){
        $nbOrders = DAO::count(Order::class, 'idUser= ?', [USession::get("idUser")]); // not count(DAO::getAll
        $listProm = DAO::getAll(Product::class, 'promotion< ?', false, [0]);
        $nbBaskets = DAO::count(Basket::class, 'idUser= ?', [USession::get("idUser")]);
        $this->loadDefaultView(['nbOrders'=>$nbOrders, 'listProm'=>$listProm, 'nbBaskets'=>$nbBaskets]);
    }

    protected function getAuthController(): AuthController{
        return new MyAuth($this);
    }

    public function getRepo(): UserRepository {
        return $this->repo;
    }

    public function setRepo(UserRepository $repo): void {
        $this->repo = $repo;
    }

    #[Route ('order', name:'order')]
    public function orders(){
        $idUser=$this->getAuthController()->_getActiveUser()->getId();
        $orders = DAO::getAll(Order::class, 'idUser= ?', false, [$idUser]);
        $this->loadDefaultView(['orders'=>$orders]);
    }

    #[Route ('store', name:'store')]
    public function store(){
        $store = DAO::getAll(Product::class, false, false);
        $listsections = DAO::getAll(Section::class,'', ['products']);
        $listProm = DAO::getAll(Product::class, 'promotion< ?', false, [0]);
        //$nbprodSection = DAO::count(Product::class, 'section= ?', false?);
        $this->loadDefaultView(['store'=>$store, 'listProm'=>$listProm, 'listSection'=>$listsections]);
    }

    #[Route ('section/{id}', name:'section')]
    public function section($id){
        $product = DAO::getAll(Product::class, 'idSection= '.$id, [USession::get("idSection")]);
        $section = DAO::getById(Section::class,$id,['products']);
        $listsections = DAO::getAll(Section::class,'', ['products']);
        $this->loadDefaultView(['section'=>$section, 'listSection'=>$listsections, 'product'=>$product]);
    }

    #[Route ('product/{idSection}/{idProduct}', name:'product')]
    public function product($idSection,$idProduct){
        $product = DAO::getAll(Product::class, 'idSection= '.$idProduct, [USession::get("idSection")]);
        $productid = DAO::getById(Product::class,$idProduct,['sections']);
        $section = DAO::getById(Section::class,$idSection,['products']);
        $listsections = DAO::getAll(Section::class,'', ['products']);
        $this->loadDefaultView(['section'=>$section, 'listSection'=>$listsections, 'product'=>$product, 'productid'=>$productid]);
    }

    #[Route ('newBasket', name:'newBasket')]
    public function newBasket(){
        $reponse = URequest::post("name");
        if($reponse != null && $reponse != "_default"){
            $currentUser = DAO::getById(User::class, USession::get("idUser"), false);
            $newBaset = new Basket();
            $newBaset->setUser($currentUser);
            $newBaset->setName($reponse);
            UResponse::header('location', '/'.Router::path('basket'));
        }
        $baskets = DAO::getAll(Basket::class, 'idUser= ?', ['basketdetails.product'], [USession::get("idUser")]);
        $this->loadDefaultView(['baskets'=>$baskets]);
    }

    #[Route ('Basket', name:'basket')]
    public function basket(){
        $idUser=$this->getAuthController()->_getActiveUser()->getId();
        $baskets = DAO::getAll(Basket::class, 'idUser= ?', false, [$idUser]);
        $this->loadDefaultView(['basket'=>$baskets]);
    }

    #[Route(path: "basketInfo/{idBasket}",name: "basketInfo")]
    public function basketInfo($idBasket){
        $basket = DAO::getById(Basket::class, $idBasket, false);
        $basket = new LocalBasket($basket);
        USession::set('defaultBasket', $basket);
        UResponse::header('location', '/'.Router::path('store'));
    }

}