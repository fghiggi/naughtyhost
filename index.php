<?php
header('Access-Control-Allow-Origin: *');  

require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$application = new \Slim\Slim();

$application->response->headers->set('Content-Type', 'application/json');

$application->get('/',function (){
   
     echo json_encode("bienvenue");
});


$application->get('/clientes', 'get_clientes');

$application->post('/clientes', 'post_http');


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
    
   /* $dominio = $json->{'dominio'};
    $email = $json->{'email'};
    $password = sha1($json->{'senhaMysql'});
    $password = sha1($json->{'$senhaRoot'});
    $password = sha1($json->{'$senhaFtp'});
    $password = sha1($json->{'$senhaSenha'});
    $plano = $json->{'plano'};*/

    try {
        $sql = "INSERT INTO clientes(host, email, senhamysql, senharoot, senhaftp, senhasenha, plano) VALUES('$dominio','$email','$senhaMysql','$senhaRoot','$senhaFtp', '$senhaSenha', '$plano') ";
        
        $dbCon = getConnection();
        
        $stmt = $dbCon->prepare($sql);
        
        $stmt->execute();
        
        $dbCon = null;
        
        echo('{"status": 200,"message": "' . $uniqueId . '"}');
    } catch (PDOException $e) {
        $application->response->setStatus(500);
        
        echo('{"status": 500,"message": "' . $e->getMessage() . '"}');
    }
}
/*
$application->get('/user/:user_id/','authorization','get_user_id');
$application->get('/user/:user_id','authorization','get_user_id');
$application->get('/users/:user_id/','authorization','get_user_id');
$application->get('/users/:user_id','authorization','get_user_id');
$application->put('/users/:id/','authorization','put_http');
$application->put('/users/:id','authorization','put_http');
$application->delete('/users/:id/','authorization','delete_http');
$application->delete('/users/:id','authorization','delete_http');
$application->post('/users/','authorization','post_http');
$application->post('/users','authorization','post_http');

function    authorization() {
    global $application;
    global $user_role;

    $test = apache_request_headers();
    $user = null;
    $pass = null;
    if (!array_key_exists('Authorization', $test)) {
        http_response_code(401);
        $tab = array('status' => 401, 'message' => 'Unauthorized');
        echo json_encode($tab);
        exit;
    }
    if (preg_match_all('/(Basic) (.*)/', $test['Authorization'], $return)) {
        $return_decode = base64_decode($return[2][0]);
        preg_match_all('/(.*):(.*)/', $return_decode, $return);
        if (isset($return[1][0]) || isset($return[2][0])) {
            $user = $return[1][0];
            $pass = $return[2][0];
        }
    }
    if (!preg_match_all('/(Basic) (.*)/', $test['Authorization'], $return)) {
        $return_decode = $test['Authorization'];
        preg_match_all('/(.*):(.*)/', $return_decode, $return);
        if (isset($return[1][0]) || isset($return[2][0])) {
            $user = $return[1][0];
            $pass = $return[2][0];
        }
    }
    if ($user == null || $pass == null) {
        http_response_code(401);
        $tab = array('status' => 401, 'message' => 'Unauthorized');
        echo json_encode($tab);
        exit;
    }
    $sql0 = "Select * FROM user WHERE email='$user'";

    $db = getConnection();
    $stmt = $db->query($sql0);
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    $pass_sha1 = sha1($pass);
    if (isset($users[0]->password) && $users[0]->password == $pass_sha1) {
        $user_role = $users[0]->role;
    }
    else {
        http_response_code(401);
        $tab = array('status' => 401, 'message' => 'Unauthorized');
        echo json_encode($tab);
        $application->stop();
    }
}


function    put_http($id)
{
    global $user_role;
    if ($user_role != "admin") {
        http_response_code(401);
        $tab = array('status' => 401, 'message' => 'Unauthorized');
        echo json_encode($tab, JSON_PRETTY_PRINT);
        exit;
    }
    $sql0 = "select * FROM user WHERE id='$id' ";
    global $application;
    try {
        $db = getConnection();
        $stmt = $db->query($sql0);
        $info_users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        if ($info_users == null) {
            http_response_code(404);
            $tab = array('status' => 404, 'message' => 'not found');
            echo json_encode($tab);
            exit;
        }
        if ($info_users[0]->role == "admin") {
            http_response_code(401);
            $tab = array('status' => 401, 'message' => 'it is a administrator');
            echo json_encode($tab);
            exit;
        }
    } catch (PDOException $e) {
        $application->response->setStatus(500);
        echo('{"status": 500,"message": "' . $e->getMessage() . '"}');
    }
    $body = $application->request->getBody();
    $json = json_decode($body);
    if (array_key_exists('email', $json)) {
        $email = $json->{'email'};
    }
    if (array_key_exists('lastname', $json)) {
        $lastname = $json->{'lastname'};
    }
    if (array_key_exists('firstname', $json)) {
        $firstname = $json->{'firstname'};
    }
    if (array_key_exists('password', $json)) {
        $password = sha1($json->{'password'});
    }
    if (array_key_exists('role', $json)) {
        $role = $json->{'role'};
    }

    if (!isset($firstname))
        $firstname = $info_users[0]->firstname;
    if (!isset($lastname))
        $lastname = $info_users[0]->lastname;
    if (!isset($email))
        $email = $info_users[0]->email;
    if (!isset($password))
        $password = $info_users[0]->password;
    if (!isset($role))
        $role = $info_users[0]->role;

    $sql = "UPDATE user SET firstname='$firstname', lastname='$lastname', email='$email', password='$password', role='$role' where id='$id' ";
    try {
        $db = getConnection();
        $s = $db->prepare($sql);
        $s->bindParam("id", $id);
        $s->bindParam("firstname", $firstname);
        $s->bindParam("lastname", $lastname);
        $s->bindParam("email", $email);
        $s->bindParam("password", $password);
        $s->bindParam("role", $role);
        $s->execute();
        echo('{"status": 200,"message": "ok"}');
    } catch (PDOException $e) {
        $application->response->setStatus(500);
        echo('{"status": 500,"message": "' . $e->getMessage() . '"}');
    }

};

function    delete_http($id)
{
    global $user_role;
    if ($user_role != "admin") {
        http_response_code(401);
        $tab = array('status' => 401, 'message' => 'Unauthorized');
        echo json_encode($tab, JSON_PRETTY_PRINT);
        exit;
    }
    $sql = "DELETE FROM user WHERE id=:id";
    global $application;
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo('{"status": 200,"message": "ok"}');
    } catch (PDOException $e) {
        $application->response->setStatus(500);
        echo('{"status": 500,"message": "' . $e->getMessage() . '"}');
    }

};
*/
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