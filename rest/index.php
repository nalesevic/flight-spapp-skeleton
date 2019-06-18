<?php

require '../vendor/autoload.php';
require_once 'PersistanceManager.class.php';
require_once 'Config.class.php';
use \Firebase\JWT\JWT;

Flight::register('pm', 'PersistanceManager', [Config::DB]);



/*Flight::route('/', function(){
    echo 'hello world!';
});*/

Flight::route('POST /users/register', function(){
  $request = Flight::request();
  $user = [
    'email' => $request->data->email,
    'password' => $request->data->password,
  ];
  Flight::pm()->register($user);
  //header("Location: index.html");
  // do routing on frontend
 });

Flight::route('POST /users/login', function(){
  $data = Flight::request()->data->getData();
  $user = Flight::pm()->get_user($data);
  if($user["status"] == "success") {
    unset($user["status"]);
    unset($user["password"]);

    $token = array(
      "user" => $user,
      "iat" => time(),
      "exp" => time() + 29312
    );
    $jwt = JWT::encode($token, CONFIG::JWT_SECRET);
    $user["token"] = $jwt;
    Flight::json($user);
  }
});

Flight::route('GET /babies', function($route){
 $header = apache_request_headers();
 if(isset($header["Authorization"]) && $header["Authorization"] != "null") {
   $jwt = $header["Authorization"];

 $decoded = JWT::decode($jwt, CONFIG::JWT_SECRET, array("HS256"));
 $decoded = json_decode(json_encode($decoded), true);
 // if(!isset($decoded["user"]["id"])) {
 //   die;
 // }
 $babies = Flight::pm()->get_babies();
 Flight::json($babies);
 }
}, true);

Flight::route('POST /babies', function(){
  $request = Flight::request()->data->getData();
  $data = apache_request_headers();
  if($data['Authorization'] != 'null') {
    $jwt = $data["Authorization"];
    $decoded = JWT::decode($jwt, CONFIG::JWT_SECRET, array("HS256"));
    $decoded = json_decode(json_encode($decoded), true);
    //$user = $decoded['user']['id'];
    $baby = [
      'firstName' => $request['firstname'],
      'lastName' => $request['lastname'],
      'mother' => $request['mother_name'],
    ];
    Flight::pm()->add_baby($baby);
  }
});

Flight::route('POST /babies/@id', function($id){
  $request = Flight::request()->data->getData();
  Flight::pm()->update_baby($id, $request);
});

Flight::route('DELETE /babies/@id', function($id){
  Flight::pm()->delete_baby($id);
});

Flight::start();
?>
