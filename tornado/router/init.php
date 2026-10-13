<?php
    //MiddleWares By TwisterSystems 
    //inicializacion de router
    $router=new \Zaphpa\Router();
    //se añaden middlewares
    $router->attach('\Tornado\Mustache');
    $router->attach('\Tornado\SessionLogin');
    $router->attach('\Tornado\Migrator');
    $router->attach('\Tornado\AutoDocumentator','/apidocs',$details=true);
    $router->attach('\Tornado\OAuth');
    require_once (__TOR__.'/tornado/router/routes.php');
    //controladores y modelos se cargan de forma dinamica via autoload de composer
    //se define slug inicial
    global $BASE;
    $BASE="";
    try {
        if($BASE!=""){
            $tokens= parse_url('http://twister.com'.str_replace($BASE,"",$_SERVER["REQUEST_URI"]));
        }else{
            $tokens= parse_url('http://twister.com'.$_SERVER["REQUEST_URI"]);
        }
        
        $uri= rawurldecode(isset($tokens["path"])?$tokens["path"]:"/");
        $router->route($uri);
    } catch (\Zaphpa\Exceptions\InvalidPathException $ex) {
        header("Content-Type: application/json;",TRUE,404);
        $out=array("error"=>"not found");
        die(json_encode($out));
    }