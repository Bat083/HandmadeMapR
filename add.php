<?php
include_once "header.php";

// This is used to submit new markers for review.
// Markers won't appear on the map until they are approved.

$owner_name = mysql_real_escape_string(parseInput($_POST['owner_name']));
$owner_email = mysql_real_escape_string(parseInput($_POST['owner_email']));
// HandmadeMap2.0
//$title = mysql_real_escape_string(parseInput($_POST['title']));
$type = mysql_real_escape_string(parseInput($_POST['type']));
$address = mysql_real_escape_string(parseInput($_POST['address']));
$uri = mysql_real_escape_string(parseInput($_POST['uri']));
$description = mysql_real_escape_string(parseInput($_POST['description']));
$especialidad = $_POST['sub_type'];

// validate fields
// HandmadeMap 2.0
//if(empty($title) || empty($type) || empty($address) || empty($uri) || empty($description) || empty($owner_name) || empty($owner_email)) {
if(empty($address) || empty($uri) || empty($description) || empty($owner_name) || empty($owner_email)) {
//   echo "All fields are required - please try again.";
  echo "Todos los campos deben estar informados, por favor inténtalo de nuevo";
  exit;
} else if(!isset($especialidad)){
  echo "Por favor, selecciona al menos una especialidad";
  exit; 
} else if(is_array($especialidad) && count($especialidad) > 10){
  echo "Por favor, selecciona un máximo de diez especialidades ";
  exit; 
} else {
  
  // if startup genome mode enabled, post new data to API
  if($sg_enabled) {
    
    try {
      @$r = $http->doPost("/organization", $_POST);
      $response = json_decode($r, 1);
      if ($response['response'] == 'success') {
        include_once("startupgenome_get.php");
        echo "success"; 
        exit;
      }
    } catch (Exception $e) {
      echo "<pre>";
      print_r($e);
    }
    
    
  // normal mode enabled, save new data to local db
  } else {

    // insert into db, wait for approval
    //HandmadeMap 2.0 -INI
    //$insert = mysql_query("INSERT INTO places (approved, title, type, address, uri, description, owner_name, owner_email) VALUES (null, '$title', '$type', '$address', '$uri', '$description', '$owner_name', '$owner_email')") or die(mysql_error());
    //HandmadeMap 2.0 -FIN
    if(!empty($_POST['sub_type'])) {
       foreach($_POST['sub_type'] as $sub_type) {
    $insert = mysql_query("INSERT INTO places (approved, title, type, subtype, lat, lng, address, uri, description, sector, owner_name, owner_email, sg_organization_id) "
            . "VALUES (null, '$owner_name', '$type', '$sub_type', 0.00, 0.00, '$address', '$uri', '$description', 'sector', '$owner_name', '$owner_email', 0)") or die(mysql_error());
       }
    }
//    $insert = mysql_query("INSERT INTO places (approved, title, type, lat, lng, address, uri, description, sector, owner_name, owner_email, sg_organization_id) "
//            . "VALUES (null, null, '$type', 0.00, 0.00, '$address', '$uri', '$description', 'prueba', '$owner_name', '$owner_email', 0)") or die(mysql_error());
    // geocode new submission

// HandmadeMap 2.0 control de errores
    $hide_geocode_output = true;
    $is_edit = false;
    include "geocode.php";
    
    echo "success";
    exit;
  
  }

  
}


?>
