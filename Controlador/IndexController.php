<?php
require_once('modelo/indexModel.php');
class IndexController{
    private $indexModel;
    public function __construct(){
        $this->indexModel = new IndexModel();
    }
    public static function index(){
        require_once("Vista/index.php");
    }
    public static function Contacto(){
        require_once("Vista/contacto.php");
    }
}
?>