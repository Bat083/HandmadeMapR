<?php
// HandmadeMap2.0
include_once "header.php";

// This is used to submit new markers of events for review.
// Markers won't appear on the map until they are approved.

$ev_owner_name = mysql_real_escape_string(parseInput($_POST['ev_owner_name']));
$ev_owner_email = mysql_real_escape_string(parseInput($_POST['ev_owner_email']));
$ev_title = mysql_real_escape_string(parseInput($_POST['ev_title']));
$ev_fecini_prev = mysql_real_escape_string(parseInput($_POST['ev_fecini']));
$ev_fecfin_prev = mysql_real_escape_string(parseInput($_POST['ev_fecfin']));
if (substr((string)$ev_fecini_prev, 2, 1) == "/") {
    $ev_fecini = date ("Y-m-d", strtotime(str_replace('/','-',$ev_fecini_prev)));
    $ev_fecfin = date ("Y-m-d", strtotime(str_replace('/','-',$ev_fecfin_prev)));
}
 else {
    $ev_fecini = $ev_fecini_prev;
    $ev_fecfin = $ev_fecfin_prev;
}
//$ev_fecini = mysql_real_escape_string(parseInput($_POST['ev_fecini']));
//$ev_fecfin = mysql_real_escape_string(parseInput($_POST['ev_fecfin']));
$ev_address = mysql_real_escape_string(parseInput($_POST['ev_address']));
$ev_uri = mysql_real_escape_string(parseInput($_POST['ev_uri']));
$ev_description = mysql_real_escape_string(parseInput($_POST['ev_description']));
$ev_type = mysql_real_escape_string(parseInput($_POST['ev_type']));
$ev_sub_type = $_POST['ev_sub_type'];
$fecha_hoy = date("Y-m-d");

if(empty($ev_address) || empty($ev_uri) || empty($ev_description) || empty($ev_owner_name) || empty($ev_owner_email) || empty($ev_title) || empty($ev_fecini) || empty($ev_fecfin)) {
  echo "Todos los campos deben estar informados, por favor inténtalo de nuevo";
  exit;
} else if(!isset($ev_sub_type)){
          echo "Por favor, selecciona al menos una especialidad";
          exit; 
} else if(is_array($ev_sub_type) && count($ev_sub_type) > 1 && ($ev_type == 'talleres')){
          echo "Por favor, selecciona sólo una especialidad para el taller";
          exit; 
} else if(is_array($ev_sub_type) && count($ev_sub_type) > 10 && ($ev_type == 'markets')){
          echo "Por favor, selecciona un máximo de diez especialidades para el market";
          exit; 
} else if($ev_fecini > $ev_fecfin){
          echo "La fecha inicial del evento debe ser menor o igual a la final"; 
          exit;
} else if($ev_fecini < $fecha_hoy){
          echo "La fecha inicial del evento debe ser mayor o igual a la actual"; 
          exit;
} else if($ev_fecfin < $fecha_hoy){
          echo "La fecha final del evento debe ser mayor o igual a la actual"; 
          exit;
} else { if(!empty($_POST['ev_sub_type'])) {
         foreach($_POST['ev_sub_type'] as $ev_sub_type) {
//         $insert = mysql_query("INSERT INTO events (approved, title, subtype, created, lat, lng, fecini, fecfin, address, uri, description, owner_name, owner_email) "
//         . "VALUES (null, '$ev_title', '$ev_sub_type', '$fecha_hoy', 0.00, 0.00, '$ev_fecini', '$ev_fecfin', '$ev_address', '$ev_uri', '$ev_description', '$ev_owner_name', '$ev_owner_email')") or die(mysql_error());
         $insert = mysql_query("INSERT INTO events (approved, title, type, subtype, created, lat, lng, fecini, fecfin, address, uri, description, owner_name, owner_email) "
         . "VALUES (null, '$ev_title', '$ev_type', '$ev_sub_type', '$fecha_hoy', 0.00, 0.00, '$ev_fecini', '$ev_fecfin', '$ev_address', '$ev_uri', '$ev_description', '$ev_owner_name', '$ev_owner_email')") or die(mysql_error());
         }
        }
    // geocode new submission
// HandmadeMap 2.0 control de errores
    $hide_geocode_output = true;
    $is_edit = false;
    include "geocode.php";
    
    echo "success";
    exit;
     }                                          
?>