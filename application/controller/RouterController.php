<?php

//PÁGINA HOME
$app->get('/', 'Application\actions\acesso:home')->add(Application\middlewares\AuthMiddleware::class)->setName('language');

$app->get('/{lang}', 'Application\actions\acesso:home')->add(Application\middlewares\AuthMiddleware::class)->setName('language');

//PÁGINA PARA CADASTRO
$app->get('/{lang}/cadastro', 'Application\actions\acesso:getNovaturma');
$app->post('/{lang}/cadastro', 'Application\actions\acesso:postNovaturma');
$app->post('/checkturmas', 'Application\actions\acesso:checkNovaturma');

//PÁGINA DE LOGIN
$app->get('/{lang}/login', 'Application\actions\acesso:getLogin')->setName('language');
$app->post('/{lang}/login', 'Application\actions\acesso:postLogin');

//PÁGINA PARA SOLICITAR RESET DE SENHA
$app->get('/{lang}/recovery/password', 'Application\actions\acesso:getRecoverypassword');
$app->post('/{lang}/recovery/password', 'Application\actions\acesso:postRecoverypassword');
//PÁGINA PARA INSERIR NOVA SENHA
$app->get('/{lang}/reset/password/[{email}[/{code}]]', 'Application\actions\acesso:getResetpassword');
$app->post('/reset/password/[{email}[/{code}]]', 'Application\actions\acesso:postResetpassword');

$app->post('/{lang}/upload', 'Application\actions\home:upload')->add(Application\middlewares\AuthMiddleware::class);
$app->post('/{lang}/remove', 'Application\actions\home:remove')->add(Application\middlewares\AuthMiddleware::class);
$app->get('/download/{img}', 'Application\actions\home:download')->add(Application\middlewares\AuthMiddleware::class);
