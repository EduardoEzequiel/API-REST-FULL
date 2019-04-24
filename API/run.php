<?php
 
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);
$conexao = require_once __DIR__ . "/db/database.php";
$servidor = $conexao['mysql']['host'];
$usuario = $conexao['mysql']['user'];
$senha = $conexao['mysql']['pass'];
$banco = $conexao['mysql']['database'];
$link = mysqli_connect($servidor, $usuario, $senha, $banco);
mysqli_set_charset($link,'utf8');


$table = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$key = array_shift($request)+0;

switch ($method) {
  case 'GET':
    $sql = "select * from `$table`"; break;
  case 'PUT':
    $sql = "update `$table` set $set where id=$key"; break;
  case 'POST':
    $sql = "insert into `$table` set $set"; break;
  case 'DELETE':
    $sql = "delete `$table` where id=$key"; break;
}
 
$result = mysqli_query($link,$sql);
 
if (!$result) {
  http_response_code(404);
  die(mysqli_error());
}
 

if ($method == 'GET') {
  if (!$key) echo '[';
  for ($i=0;$i<mysqli_num_rows($result);$i++) {
    echo ($i>0?',':'').json_encode(mysqli_fetch_object($result));
  }
  if (!$key) echo ']';
} elseif ($method == 'POST') {
  echo mysqli_insert_id($link);
} else {
  echo mysqli_affected_rows($link);
}
 
mysqli_close($link);