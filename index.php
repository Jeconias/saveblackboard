<?php
setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
date_default_timezone_set('America/Sao_Paulo');
session_cache_expire(10);
session_start();

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 'On');

require('vendor/autoload.php');
require('config/config.php');
require('config/define.php');

$app = new \Slim\App(['settings' => $config]);
$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host='.$db['host'].'; dbname='.$db['dbname'], $db['user'], $db['pass'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['views'] = new \Slim\Views\PhpRenderer('resources/views/');

$container['crud'] = function ($c) {
    return new \PlusCrud\Crud\CRUD($c->get('db'));
};

$container['FileUpload'] = function($c){
	$upload = new \Application\classes\upload();
	return $upload;
};

$container['mailer'] = function($c){
	$email = new \PHPMailer\PHPMailer\PHPMailer(true);
	return $email;
};
$container['idioma'] = function($c){
    $lang = new \Application\classes\Language();
    $lang->changeLanguage(explode('/', $c['request']->getUri()->getPath())[0]);
    return $lang;
};

require('application/controller/RouterController.php');
$app->run();
