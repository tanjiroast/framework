<?php
namespace controllers;
use Ubiquity\orm\DAO;
use Ubiquity\utils\flash\FlashMessage;
use Ubiquity\utils\http\USession;
use Ubiquity\utils\http\URequest;
use controllers\auth\files\MyAuthFiles;
use Ubiquity\controllers\auth\AuthFiles;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\utils\http\UResponse;

#[Route(path: "/login",inherited: true,automated: true)]
class MyAuth extends \Ubiquity\controllers\auth\AuthController{

    protected function onConnect($connected) {
        $urlParts=$this->getOriginalURL();
        USession::set($this->_getUserSessionKey(), $connected);
        if(isset($urlParts)){
            $this->_forward(implode("/",$urlParts));
        }else{
            UResponse::header('location','/');
        }
    }

    protected function _connect() {
        if(URequest::isPost()){
            $email=URequest::post($this->_getLoginInputName());
            $password=URequest::post($this->_getPasswordInputName());
            if($email!=null){
                $user=DAO::getOne(User::class,'email= ?', false,[$email]);
                if(isset($user)){
                    USession::set('idOrga', $user->getOrganization());
                    return $user;
                }
            }
            //TODO
            //Loading from the database the user corresponding to the parameters
            //Checking user creditentials
            //Returning the user
        }
        return;
    }

    /**
     * {@inheritDoc}
     * @see \Ubiquity\controllers\auth\AuthController::isValidUser()
     */
    public function _isValidUser($action=null) {
        return USession::exists($this->_getUserSessionKey());
    }

    public function _getBaseRoute() {
        return '/login';
    }

    protected function getFiles(): AuthFiles{
        return new MyAuthFiles();
    }

    protected function finalizeAuth() {
        if(!URequest::isAjax()){
            $this->loadView('@activeTheme/main/vFooter.html');
        }
    }

    protected function initializeAuth() {
        if(!URequest::isAjax()){
            $this->loadView('@activeTheme/main/vHeader.html');
        }
    }

    public function _getBodySelector() {
        return '#page-container';
    }

    protected function noAccessMessage(FlashMessage $fMessage) {
        $fMessage->setTitle('Acces interdit');
        $fMessage->setContent("vous n'êtes pas autorisé à acceder à cette ressource");
    }

    public function _displayinfoAsString() {
        return true; //affiche dans la page et non apres la page (false)
    }

}