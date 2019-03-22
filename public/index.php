<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = 'localhost';
$config['db']['user']   = 'root';
$config['db']['pass']   = 'root';
$config['db']['dbname'] = 'mortalfight';

require '/../vendor/autoload.php';

$app = new \Slim\App(['settings'=>$config]);
$container=$app->getContainer();
$container['logger']=function($c){
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};
$container['db']=function($c){
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
    $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;       
};
$container['view'] = new \Slim\Views\PhpRenderer('../templates/');

$app->get('/hash/{pass}', function (Request $request, Response $response, array $args) {
    $pass = password_hash($args['pass'],PASSWORD_DEFAULT);
    $response->getBody()->write("hash= $pass");
    $this->logger->addInfo("hash sent with name = $name");
    return $response;
});

$app->get('/users', function (Request $request, Response $response) {
    $this->logger->addInfo("getting user list");
    $mapper = new UserMapper($this->db);
    $users = $mapper->getUsers();
    

    $response = $this->view->render($response, 'userslist.phtml', ['users' => $users]);
    //$response->getBody()->write(var_export($users, true));
    return $response;
});
$app->get('/user/{email}', function (Request $request, Response $response, $args) {
    //$request->getQueryParams()
    $email = $args['email'];
    $mapper = new UserMapper($this->db);
    $user = $mapper->getUserByEmail($email);
    $response->getBody()->write(var_export($user, true));
    return $response;
});
$app->get('/login', function (Request $request, Response $response, $args) {
    //$request->getQueryParams()
    
    $response = $this->view->render($response, 'login.phtml', ['title' => "Login page"]);
    return $response;
});
$app->post('/login', function (Request $request, Response $response, $args) {
    //$paramas = $request->getQueryParams();
    $data = $request->getParsedBody();
    $login= filter_var($data['login'], FILTER_SANITIZE_STRING);
    $password = filter_var($data['password'], FILTER_SANITIZE_STRING);
    $mapper = new UserMapper($this->db);
    $user = $mapper->getUserByEmail($login);
    //$response->getBody()->write($login." ".$password);
    $result = password_verify($password,$user->getPassword());
    $data=[];
    if($result){
        $data= ['title' => "Login page",'result'=>'success'];
    }else{
        $data=['title' => "Login page",'result'=>'faild'];
    }
    $response = $this->view->render($response, 'login.phtml', $data);
    return $response;
});


//$email = $args['email'];

// $app->post('/login', function (Request $request, Response $response, $args) {
//     $params=$request->getQueryParams()
//     $response = $this->redi
// });


$app->run();

