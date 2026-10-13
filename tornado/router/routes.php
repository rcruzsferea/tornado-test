<?php
// EJEMPLO
//    "/path"=>[
//      'path'=>'path',
//      'get'=>array('Controller','method'),
//      'post'=>array('Controller','method'),
//    ]  
global $ROUTES;
$ROUTES=[
    "/home"=>[
        'path'=>'/',
        'allow'=>true,
        'get'=>array('Plain','home')
    ],
    /* VISTAS PARA USUARIOS */
    // VISTAS
    '/registrar-usuario' => [
        'path' => '/registrar-usuario',
        'allow' => true,
        'get' => ['User', 'viewRegistrarUsuario']
    ],

    // SERVICIOS
    'registrar-usuario' => [
        'path' => '/registrar-usuario',
        'allow' => true,
        'post' => ['User', 'registrarUsuario'],
    ],
    'usuarios' => [
        'path' => '/usuarios',
        'allow' => true,
        'get' => [],
    ],
];
foreach ($ROUTES as $key => $route) {
    $router->addRoute($route);
}