<?php
include "header.php";


if(isset($_GET['place_id'])) {
  $place_id = htmlspecialchars($_GET['place_id']); 
} else if(isset($_POST['place_id'])) {
  $place_id = htmlspecialchars($_POST['place_id']);
} else {
  exit; 
}

if(isset($_GET['table'])) { 
    $table = $_GET['table']; 
} else if(isset($_POST['table'])) { 
    $table = $_POST['table']; 
} else { 
    exit; 
}

// get place info
$place_query = mysql_query("SELECT * FROM $table WHERE id='$place_id' LIMIT 1");
if(mysql_num_rows($place_query) != 1) { exit; }
$place = mysql_fetch_assoc($place_query);


// do place edit if requested
if($task == "doedit") {  
  $title = utf8_decode(str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['title'] ) ));
  $type = $_POST['type'];
  $address = str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['address'] ) );
  $address_db = utf8_encode($place[address]);
  $uri = $_POST['uri'];
  $description = utf8_decode(str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['description'] ) ));  
  $owner_name = utf8_decode(str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['owner_name'] ) ));  
  $owner_email = utf8_decode($_POST['owner_email']);

  $b_geocode = false;
  
  if ($address_db != $address){      
     $lat = 0.00;
     $lng = 0.00;
     $b_geocode = true;
     $address2 = utf8_decode($address);
  } else {
    $lat = (float) $_POST['lat'];
    $lng = (float) $_POST['lng'];      
  }
  
  if ($table == 'events'){
      $fecini_prev = $_POST['fecini'];
      $fecfin_prev = $_POST['fecfin'];
      if (substr((string)$fecini_prev, 2, 1) == "/") {
        $fecini = date ("Y-m-d", strtotime(str_replace('/','-',$fecini_prev)));
        $fecfin = date ("Y-m-d", strtotime(str_replace('/','-',$fecfin_prev)));
      } else {
        $fecini = $fecini_prev;
        $fecfin = $fecfin_prev;
      }      
      mysql_query("UPDATE $table SET title='$title', type='$type', address='$address2', uri='$uri', lat='$lat', lng='$lng', fecini='$fecini', fecfin='$fecfin', description='$description', owner_name='$owner_name', owner_email='$owner_email' WHERE id='$place_id' LIMIT 1") or die(mysql_error());
  } else {
      mysql_query("UPDATE $table SET title='$title', type='$type', address='$address2', uri='$uri', lat='$lat', lng='$lng', description='$description', owner_name='$owner_name', owner_email='$owner_email' WHERE id='$place_id' LIMIT 1") or die(mysql_error());      
  }
  
  // geocode
  if ($b_geocode){
     $hide_geocode_output = true;
     $is_edit = true;
     include "../geocode.php";
  }
  
  header("Location: index.php?view=$view&search=$search&p=$p&table=$table");
  exit;
}

?>


<? echo $admin_head; ?>

<form id="admin" class="form-horizontal" action="edit.php" method="post">
  <h1>
    Edit <? echo "$table" ?>
  </h1>
  <fieldset>
    <div class="control-group">
      <label class="control-label" for="">Nombre</label>
      <div class="controls">
        <? $title_utf8 = utf8_encode($place[title]); ?>   
        <input type="text" class="input input-xlarge" name="title" value="<?=$title_utf8?>" id="">
      </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="">Tipo</label>
      <div class="controls">
        <select class="input input-xlarge" name="type">
          <? if ($table == 'events') { ?>
                <option<? if($place[type] == "talleres") {?> selected="selected"<? } ?>>talleres</option>
                <option<? if($place[type] == "markets") {?> selected="selected"<? } ?>>markets</option>
          <? } else { ?> 
                <option<? if($place[type] == "handmakers") {?> selected="selected"<? } ?>>handmakers</option>
                <option<? if($place[type] == "espacios") {?> selected="selected"<? } ?>>espacios</option>
          <? } ?>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Dirección</label>
      <div class="controls">
        <? $address_utf8 = utf8_encode($place[address]); ?> 
        <input type="text" class="input input-xlarge" name="address" value="<?=$address_utf8?>" id="">          
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">URL</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="uri" value="<?=$place[uri]?>" id="">
      </div>
    </div>
    <div class="control-group">
    <label class="control-label" for="">Descripción</label>
      <div class="controls">
        <? $description_utf8 = utf8_encode($place[description]); ?>           
        <textarea class="input input-xlarge" name="description"><?=$description_utf8?></textarea>          
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Nombre asignado</label>
      <div class="controls">
        <? $owner_name_utf8 = utf8_encode($place[owner_name]); ?>           
      <input type="text" class="input input-xlarge" name="owner_name" value="<?=$owner_name_utf8?>" id="">          
      </div>
    </div>
    <div class="control-group">
     <label class="control-label" for="">Email asignado</label> 
      <div class="controls">
        <? $owner_email_utf8 = utf8_encode($place[owner_email]); ?>           
        <input type="text" class="input input-xlarge" name="owner_email" value="<?=$owner_email_utf8?>" id="">
      </div>
    </div>
    <? if ($table == 'events') { ?>
            <div class="control-group">
              <label class="control-label" for="">Fecha de inicio</label>
              <div class="controls">
                <input type="date" name="fecini" value="<?=$place[fecini]?>" id="" maxlength="100">            
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="">Fecha de fin</label>
              <div class="controls">
                   <input type="date" name="fecfin" value="<?=$place[fecfin]?>" id="" maxlength="100">  
              </div>
            </div>             
    <? } ?>  
    <div class="control-group">
      <label class="control-label" for="">Localización</label>
      <div class="controls">
        <input type="hidden" name="lat" id="mylat" value="<?=$place[lat]?>"/>
        <input type="hidden" name="lng" id="mylng" value="<?=$place[lng]?>"/>
        <div id="map" style="width:80%;height:300px;">
        </div>
        <script type="text/javascript">
          var map = new google.maps.Map( document.getElementById('map'), {
            zoom: 17,
            center: new google.maps.LatLng( <?=$place[lat]?>, <?=$place[lng]?> ),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl: false
          });
          var marker = new google.maps.Marker({
            position: new google.maps.LatLng( <?=$place[lat]?>, <?=$place[lng]?> ),
            map: map,
            draggable: true
          });
          google.maps.event.addListener(marker, 'dragend', function(e){
            document.getElementById('mylat').value = e.latLng.lat().toFixed(6);
            document.getElementById('mylng').value = e.latLng.lng().toFixed(6);
          });
        </script>
      </div>
    </div>    
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Guardar cambios</button>
      <input type="hidden" name="task" value="doedit" />
      <input type="hidden" name="place_id" value="<?=$place[id]?>" />
      <input type="hidden" name="view" value="<?=$view?>" />
      <input type="hidden" name="search" value="<?=$search?>" />
      <input type="hidden" name="p" value="<?=$p?>" />
      <input type="hidden" name="table" value="<?=$table?>" />
      <a href="index.php" class="btn" style="float: right;">Cancelar</a>
    </div>
  </fieldset>
</form>



<? echo $admin_foot; ?>
