<?php

//PÁGINA HOME
$app->get('/', 'Application\actions\acesso:home')->add(Application\middlewares\AuthMiddleware::class);

//PÁGINA PARA CADASTRO
$app->get('/cadastro', 'Application\actions\acesso:getNovaturma');
$app->post('/cadastro', 'Application\actions\acesso:postNovaturma');
$app->post('/checkturmas', 'Application\actions\acesso:checkNovaturma');

//PÁGINA DE LOGIN
$app->get('/login', 'Application\actions\acesso:getLogin');
$app->post('/login', 'Application\actions\acesso:postLogin');

//PÁGINA PARA SOLICITAR RESET DE SENHA
$app->get('/recovery/password', 'Application\actions\acesso:getRecoverypassword');
$app->post('/recovery/password', 'Application\actions\acesso:postRecoverypassword');
//PÁGINA PARA INSERIR NOVA SENHA
$app->get('/reset/password/[{email}[/{code}]]', 'Application\actions\acesso:getResetpassword');
$app->post('/reset/password/[{email}[/{code}]]', 'Application\actions\acesso:postResetpassword');

$app->post('/upload', 'Application\actions\home:upload')->add(Application\middlewares\AuthMiddleware::class);
$app->post('/remove', 'Application\actions\home:remove')->add(Application\middlewares\AuthMiddleware::class);
$app->get('/download/{img}', 'Application\actions\home:download')->add(Application\middlewares\AuthMiddleware::class);
