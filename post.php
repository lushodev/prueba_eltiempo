<?php

header( 'Content-Type: application/json' );

if ( !array_key_exists( 'HTTP_X_TOKEN', $_SERVER ) ) {
	die;
}

$url = 'http://'.$_SERVER['HTTP_HOST'].'/auth';

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_HTTPHEADER, [
	"X-Token: {$_SERVER['HTTP_X_TOKEN']}",
]);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$ret = curl_exec( $ch );

if ( curl_errno($ch) != 0 ) {
	die ( curl_error($ch) );
}

if ( $ret !== 'true' ) {
	http_response_code( 403 );

	die;
}



include "config.php";
include "utils.php";

$cnxDb =  connect($db);

// listado de productos
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
  //Muestra producto por id
    if (isset($_GET['id']))
    {
      $sql = $cnxDb->prepare("SELECT * FROM productos where id=:id");
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
      exit();
	  }
    else {
      $sql = $cnxDb->prepare("SELECT * FROM productos");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll()  );
      exit();
	}
}

// Creación de producto
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $input = $_POST;
    $sql = "INSERT INTO productos
          (nombre, marca, categoria, precio, disponible)
          VALUES
          (:nombre, :marca, :categoria, :precio, :disponible)";
    $statement = $cnxDb->prepare($sql);
    bindAllValues($statement, $input);
    $statement->execute();
    $postId = $cnxDb->lastInsertId();
    if($postId)
    {
      $input['id'] = $postId;
      header("HTTP/1.1 200 OK");
      echo json_encode($input);
      exit();
	 }
}

// Borra producto
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
	$id = $_GET['id'];
  $statement = $cnxDb->prepare("DELETE FROM productos where id=:id");
  $statement->bindValue(':id', $id);
  $statement->execute();
	header("HTTP/1.1 200 OK");
	exit();
}

// Actualiza productos
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $input = $_GET;
    $postId = $input['id'];
    $fields = getParams($input);

    $sql = "
          UPDATE productos
          SET $fields
          WHERE id='$postId'
           ";

    $statement = $cnxDb->prepare($sql);
    bindAllValues($statement, $input);

    $statement->execute();
    header("HTTP/1.1 200 OK");
    exit();
}


// Se muestra un mensaje de error si no se ejecuta ninguna de las anteriores
header("HTTP/1.1 400 Bad Request");