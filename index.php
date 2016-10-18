<?php
header('Access-Control-Allow-Origin: *');  

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$application = new \Slim\Slim();

$application->response->headers->set('Content-Type', 'application/json');

$application->get('/',function (){
   
     echo json_encode("it's alive!");
});


$application->get('/clientes', 'get_clientes');

$application->post('/clientes', 'post_http');

$application->put('/clientes/:id/', 'put_http');


function    get_clientes()
{
    $sql = "select * from clientes";
    global $application;
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $clientes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        if ($clientes == null) {
            http_response_code(404);
            $tab = array('status' => 404, 'message' => 'not found');
            echo json_encode($tab, JSON_PRETTY_PRINT);
            exit;
        }

        echo json_encode($clientes, JSON_PRETTY_PRINT);
    } catch (PDOException $e) {
        $application->response->setStatus(500);
        echo('{"status": 500,"message": "' . $e->getMessage() . '"}');
    }
};

function    post_http()
{
    global $application;
    
    $body = $application->request->getBody();
    
    $params_str = urldecode($body);
    
    parse_str($params_str, $params_arr);
    
    $dominio = $params_arr['dominio'];
    $email = $params_arr['email'];
    $senhaMysql = sha1($params_arr['senhaMysql']);
    $senhaRoot = sha1($params_arr['$senhaRoot']);
    $senhaFtp = sha1($params_arr['$senhaFtp']);
    $senhaSenha = sha1($params_arr['$senhaSenha']);
    $plano = $params_arr['plano'];
    
    $uuid = getUUID();

    try {
        $sql = "INSERT INTO clientes(uuid, host, email, senhamysql, senharoot, senhaftp, senhasenha, plano) VALUES('$uuid', '$dominio','$email','$senhaMysql','$senhaRoot','$senhaFtp', '$senhaSenha', '$plano') ";
        
        $dbCon = getConnection();
        
        $stmt = $dbCon->prepare($sql);
        
        $stmt->execute();
        
        $dbCon = null;
        
        echo('{"status": 200,"message": "' . $uuid . '"}');
    } catch (PDOException $e) {
        $application->response->setStatus(500);
        
        echo('{"status": 500,"message": "' . $e->getMessage() . '"}');
    }
};

function    put_http($uuid)
{
    $sql0 = "select uuid FROM clientes WHERE id='$uuid[0]' ";
    
    global $application;
    
    try {
        $db = getConnection();
        
        $stmt = $db->query($sql0);
        
        $info_clientes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        
        if ($info_clientes == null) {
            http_response_code(404);
            $tab = array('status' => 404, 'message' => 'not found');
            echo json_encode($tab);
            exit;
        }
    } catch (PDOException $e) {
        $application->response->setStatus(500);
        echo('{"status": 500,"message": "' . $e->getMessage() . '"}');
    }
    
    $body = $application->request->getBody();
    $json = json_decode($body);
    
    if (array_key_exists('pago', $json)) {
        $email = $json->{'pago'};
    }

    if (!isset($pago))
        $pago = $info_users[0]->pago;

    $sql = "UPDATE clientes SET pago='$pago' where uuid='$uuid' ";
    
    try {
        $db = getConnection();
        $s = $db->prepare($sql);
        
        $s->bindParam("uuid", $uuid);
        $s->bindParam("pago", $pago);
        
        $s->execute();
        
        echo('{"status": 200,"message": "ok"}');
    } catch (PDOException $e) {
        $application->response->setStatus(500);
        
        echo('{"status": 500,"message": "' . $e->getMessage() . '"}');
    }

};

function getUUID(){
    try {
        $db = getConnection();
     
        $stmt = $db->query("SELECT UUID() as uuid;");
        
        $uuid = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        return $uuid['uuid'];
    } catch (PDOException $e) {
        return null;
    }
};

function getConnection(){
    $dbhost = getenv('IP');
    $dbuser = getenv('C9_USER');
    $dbpass = "";
    $dbname = "c9";

    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}


$application->run();