<?php
if(!file_exists('include/db.php')) require_once('installer.php');
include_once "header.php";
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>HandmadeMap - Mapa de handmakers de España</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="main.css">
    <link rel="stylesheet" type="text/css" href="responsive.css">
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <script src="./scripts/jquery-1.7.1.js" type="text/javascript" charset="utf-8"></script>
    <script src="./scripts/bootstrap-typeahead.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="./scripts/label.js"></script>
    <script type="text/javascript" src="./scripts/oms.min.js"></script> 
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-55002519-1', 'auto');
      ga('send', 'pageview');
    </script>   
  </head>
  <body>

    <header class="header">
      <div class="header__inner">
        <a href="./">
            <img class="header__logo" src="images/handmademap-icono-web.png"/>
        </a>
        <img id="menu" class="header__menu" src="./images/hamburger.png"> 
      </div>
    </header>

    <nav id="drawer" class="nav">
      <ul class="nav__list">
        <li class="nav__item"><a href="#openModal_add">Inscríbete</a>
        </li>
        <li class="nav__item"><a href="#openModal_add_ev">Eventos</a>
        </li>
        <li class="nav__item"><a href="#openModal_info">Info</a>
        </li> 
        <li class="nav__item"><a href="http://www.handmademap.com/blog" target="_blank">Blog</a></li>
      </ul>
    </nav>

    <main>
       <section class="container content">
       <nav id="filter" class="nav_left">
          <ul class="nav_left__list">
          <?php
            $fecha_hoy = date("Y-m-d");
            $types = Array(
                        Array('handmakers', 'Handmakers'),
                        Array('espacios','Espacios'),              
                        Array('talleres','Talleres'),
                        Array('markets','Markets')              
                      );     
            $subtipos = Array(
                        Array("Amigurimi","Amigurimi"),                
                        Array("Bebe","Bebé: Ropa y complementos"), 
                        Array("Bolsos","Bolsos y mochilas"),              
                        Array("Caligrafia","Caligrafía"),
                        Array("Carvado","Carvado de sellos"),
                        Array("Ceramica","Cerámica"),
                        Array("Chapas","Chapas,imanes y merchandising"),                    
                        Array("Chuches","Chuches/candy"),                   
                        Array("Costura","Costura y Patchwork"),
                        Array("Cuadros","Cuadros"),                       
                        Array("Cupcakes","Cupcakes, galletas y tartas"), 
                        Array("Decoracion","Decoración"),                   
                        Array("Encuadernacion","Encuadernación"),      
                        Array("Escultura","Escultura"),                    
                        Array("Esparto","Esparto"), 
                        Array("Fieltro","Fieltro"),                    
                        Array("Fimo","Fimo"),                    
                        Array("Fofuchas","Fofuchas"),                    
                        Array("Ganchillo","Ganchillo"),
                        Array("Ilustracion","Ilustración"),
                        Array("Joyas","Joyas y bisutería"),
                        Array("Markets","Markets"), 
                        Array("Merceria","Mercería creativa"), 
                        Array("Muñecos","Muñecos, peluches y muñecas"),             
                        Array("Organizacion","Organización de eventos"),                                 
                        Array("Piel","Piel y cuero"),                
                        Array("Pintura","Pintura mural y Graffitis"),              
                        Array("Reciclaje","Reciclaje de materiales"),              
                        Array("Ropa","Ropa"),                    
                        Array("Scrapbook","Scrapbook"),
                        Array("Tocados","Tocados"),
                        Array("Trapillo","Trapillo"),                    
                        Array("Vidrio","Vidrio"), 
                        Array("Zapatos","Zapatos")
                    );                   
          $marker_id = 0;
          foreach($types as $type) {
            if($type[0] == "handmakers" || $type[0] == "espacios") {
              $markers = mysql_query("SELECT * FROM places WHERE approved='1' AND type='$type[0]' GROUP BY title");
            } else {               
              $markers = mysql_query("SELECT * FROM events WHERE approved='1' AND type='$type[0]' AND fecfin >= '$fecha_hoy' GROUP BY title");                
            }
            $markers_total = mysql_num_rows($markers);       
            if ($type[0] == 'markets'){            
                echo "
                  <li class='category'>
                    <div class='category_item'>
                      <div class='category_toggle' onClick=\"toggle('$type[0]')\" id='filter_$type[0]'></div>
                      <a href='#' onClick=\"toggleList('$type[0]');\" class='category_info'><img src='./images/icons/$type[0].png' alt='' />$type[1]<span class='total'> ($markers_total)</span></a>
                    </div>
                ";
            } else {              
                echo "
                  <li class='category'>
                    <div class='category_item'>
                      <div class='category_toggle' onClick=\"toggle('$type[0]')\" id='filter_$type[0]'></div>
                      <a href='#' onClick=\"toggleList('$type[0]');\" class='category_info'><img src='./images/icons/$type[0].png' alt='' />$type[1]<span class='total'> ($markers_total)</span></a>
                    </div>
                    <ul class='list-items list-$type[0]'>
                ";                                              
            }
            if ($type[0] != 'markets')
            {              
                foreach($subtipos as $subtipo) {
                    if($type[0] == "handmakers" || $type[0] == "espacios") {                  
                        $markers_subtipo = mysql_query("SELECT * FROM places WHERE approved='1' AND type='$type[0]' AND subtype='$subtipo[0]' ORDER BY subtype");
                    } else {    
                        $markers_subtipo = mysql_query("SELECT * FROM events WHERE approved='1' AND type='$type[0]' AND subtype='$subtipo[0]' AND fecfin >= '$fecha_hoy' ORDER BY subtype");                    
                    }
                    $markers_total_subtipo = mysql_num_rows($markers_subtipo);
                    echo "
                    <li class='category'>
                      <div class='category_item'>
                        <div class='category_toggle' onClick=\"toggles('$type[0]','$subtipo[0]')\" id='filter_$type[0]_$subtipo[0]'></div>
                        <a href='#' class='category_info'>$subtipo[1]<span class='total'> ($markers_total_subtipo)</span></a>
                      </div>
                    </li>
                    ";
                }           
            }           
            if ($type[0] == 'markets'){  
                echo "
                  </li>
                ";              
            } else {               
                echo "
                    </ul>
                  </li>
                ";                 
            }            
          }
          ?>
          <li>
            <div class="search">
              <input type="text" name="search" id="search" placeholder="Busca handmakers..." data-provide="typeahead" autocomplete="off" />
            </div>
          </li>  
          <li class="blurb"><?= $blurb ?></li>
        </ul>
        </nav> 
        <section>
          <button type="button" id="filter_button" class="filter__menu"> Filtros </button>
        </section>
        <section id="id_map" class="map">
        </section>
        <section>
          <button type="button" id="social_button" class="social__menu"> Contacta </button>
        </section>
        <footer>
          <section id="id_social" class="social">
            <a href="https://twitter.com/Handmade_Map_" target="_blank">
            <img title="Twitter" alt="Twitter" src="https://socialmediawidgets.files.wordpress.com/2014/03/twitter.png"/>
            </a>
            <a href="https://www.facebook.com/pages/HandMade-Map/1487264921506025?fref=ts">
            <img title="Facebook" alt="Facebook" src="https://socialmediawidgets.files.wordpress.com/2014/03/facebook.png"/>
            </a>
            <a href="http://es.pinterest.com/handmademap/">
            <img title="Pinterest" alt="Pinterest" src="https://socialmediawidgets.files.wordpress.com/2014/03/pinterest.png"/>
            </a>
            <a href="mailto:hello@handmademap.com">
            <img title="Email" alt="hello@handmademap.com" src="https://socialmediawidgets.files.wordpress.com/2014/03/email.png"/>
            </a>       
          </section>
        </footer>
      </section>
      <div class="cookiesms" id="cookie1">
          Esta web utiliza cookies. 
          <a href="#openModal_priv" data-toggle="modal">Política.</a> 
          Si continuas navegando estás aceptándola
          <button onclick="controlcookies()"> Aceptar </button>
          <div  class="cookies2" onmouseover="document.getElementById('cookie1').style.bottom = '0px';">Política de cookies + </div>
      </div>
      <script type="text/javascript">
        if (localStorage.controlcookie>0){ 
        document.getElementById('cookie1').style.bottom = '-50px';
        }
      </script>  
    </main>

    <!-- AÑADIR HANDMAKER -->
    <div id="openModal_add" class="modalDialog">
      <div>
        <form action="add.php" id="modal_addform" name="addForm">
        <div class="modal-header">
            <h2>Añade algo!</h2>
            <a href="#close" title="Close" class="close btn" id="closeX">X</a>
        </div>
        <div class="modal-body" id="modal_body">
          <div id="result"></div> 
          <fieldset>
            <div class="control-group">
              <label class="control-label" for="input01">Selecciona Tipo</label>
              <div class="controls">
                <select name="type" id="add_type" class="input-xlarge">
                  <option value="handmakers">Handmakers</option>
                  <option value="espacios">Espacios</option>
                </select>   
                <p class="help-block">
                    ¿Quieres publicar un Handmaker o un Espacio?
                </p>     
              </div>
            </div>              
            <div class="control-group">
              <label class="control-label" for="add_owner_name">Tu nombre</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="owner_name" id="add_owner_name" maxlength="100" required>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_owner_email">Tu email</label>
              <div class="controls">
                <input type="email" class="input-xlarge" name="owner_email" id="add_owner_email" maxlength="100" required>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_address">Dirección</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="address" id="add_address" required>
                <p class="help-block">
                  Deberías poner tu calle (incluyendo la ciudad y el Código Postal).
                  Pon la misma dirección que pondrías en Google Maps,
                  ¡funciona igual!
                </p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_uri">Web / FanPage</label>
              <div class="controls">
                <input type="url" class="input-xlarge" id="add_uri" name="uri" placeholder="http://" required>
                <p class="help-block">
                  Pon tu web completa (ej: http://handmademap.com) y si no tienes aún,
                  puedes poner tu FanPage de Facebook (ej: https://www.facebook.com/Atutichapas)
                </p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_description">Descripción</label>
              <div class="controls">
                <input type="text" class="input-xlarge" id="add_description" name="description" maxlength="150" required>
                <p class="help-block">
                  Explícanos brevemente qué es lo que haces. 150 letras máximo.
                </p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_especialidad2">Tu especialidad
                 <p class="help-block">
                    Puedes poner máximo 
                    diez especialidades.
                </p>
            </label>
              <div class="controls">                 
                <ul class="checkboxes" for="add_especialidad"> 
                    <li><input type="checkbox" name="sub_type[]" value="Amigurimi"> Amigurimi </li>                
                    <li><input type="checkbox" name="sub_type[]" value="Bebe"> Bebé: Ropa y complementos </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Bolsos"> Bolsos y mochilas </li>
                    <li><input type="checkbox" name="sub_type[]" value="Caligrafia"> Caligrafía </li>
                    <li><input type="checkbox" name="sub_type[]" value="Carvado"> Carvado de sellos </li>
                    <li><input type="checkbox" name="sub_type[]" value="Ceramica"> Cerámica </li>
                    <li><input type="checkbox" name="sub_type[]" value="Chapas"> Chapas, imanes y merchandising </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Chuches"> Chuches/candy </li>                   
                    <li><input type="checkbox" name="sub_type[]" value="Costura"> Costura y Patchwork </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Cuadros"> Cuadros </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Cupcakes"> Cupcakes, galletas y tartas </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Decoracion"> Decoración </li>                   
                    <li><input type="checkbox" name="sub_type[]" value="Encuadernacion"> Encuadernación </li>      
                    <li><input type="checkbox" name="sub_type[]" value="Escultura"> Escultura </li>  
                    <li><input type="checkbox" name="sub_type[]" value="Esparto"> Esparto </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Fieltro"> Fieltro </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Fimo"> Fimo </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Fofuchas"> Fofuchas </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Ganchillo"> Ganchillo </li>
                    <li><input type="checkbox" name="sub_type[]" value="Ilustracion"> Ilustración </li>
                    <li><input type="checkbox" name="sub_type[]" value="Joyas"> Joyas y bisutería </li>
                    <li><input type="checkbox" name="sub_type[]" value="Merceria"> Mercería creativa </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Muñecos"> Muñecos, peluches y muñecas </li>
                    <li><input type="checkbox" name="sub_type[]" value="Organizacion"> Organización de eventos </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Piel"> Piel y cuero </li>                
                    <li><input type="checkbox" name="sub_type[]" value="Pintura"> Pintura mural y Graffitis </li>
                    <li><input type="checkbox" name="sub_type[]" value="Reciclaje"> Reciclaje de materiales </li>
                    <li><input type="checkbox" name="sub_type[]" value="Ropa"> Ropa </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Scrapbook"> Scrapbook </li>
                    <li><input type="checkbox" name="sub_type[]" value="Tocados"> Tocados </li>
                    <li><input type="checkbox" name="sub_type[]" value="Trapillo"> Trapillo </li>                    
                    <li><input type="checkbox" name="sub_type[]" value="Vidrio"> Vidrio </li>                                                                                              
                    <li><input type="checkbox" name="sub_type[]" value="Zapatos"> Zapatos </li>   
                </ul>             
              </div>
            </div>
          </fieldset>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="sub_form" style="float: left;">Enviar</button>
            <a href="#" class="btn" id="close_form" data-dismiss="modal" style="float: right;">Cerrar</a>
        </div>
        </form>
      </div>
    </div>
    <script>
      //VACIAR FORMULARIO AL CERRAR LA PANTALLA
      var close_form = document.querySelector('#close_form');
      var closeX     = document.querySelector('#closeX');
      var sub_form   = document.querySelector('#sub_form');

      close_form.addEventListener('click', function() {
        document.getElementById("modal_addform").reset();
      });
      closeX.addEventListener('click', function() {
        document.getElementById("modal_addform").reset();
      });
      $('body').css('overflow','hidden');
      $('body').css('position','fixed');
      $('#modal_addform').css('overflow','scroll');
      // AÑADIR HANDMAKER O ESPACIO
      $("#modal_addform").submit(function(event) {
        event.preventDefault();
        // get values
        var allVals=[];
        $('input[type="checkbox"]:checked').each(function () {
         allVals.push($(this).val());
        });
        var $form       = $( this ),
            owner_name  = $form.find( '#add_owner_name' ).val(),
            owner_email = $form.find( '#add_owner_email' ).val(),
            type        = $form.find( '#add_type' ).val(),
            address     = $form.find( '#add_address' ).val(),
            uri         = $form.find( '#add_uri' ).val(),
            description = $form.find( '#add_description' ).val(),     
            sub_type    = allVals, 
            url         = $form.attr( 'action' );

        // send data and get results
        var posting = $.post( url, { owner_name: owner_name, owner_email: owner_email, type: type, address: address, uri: uri, description: description, sub_type: sub_type});        

        posting.done(function( data ) {
            var content = $( data ).find( '#content' );

            // if submission was successful, show info alert            
            if(data == "success") {
              $("#modal_addform #result").html("Hemos recibido su formulario y lo validaremos pronto. Gracias!");
              $("#modal_addform #result").addClass("alert alert-info");
              $("#modal_addform p").css("display", "none");
              $("#modal_addform fieldset").css("display", "none");
              $("#modal_addform .btn-primary").css("display", "none");

            // if submission failed, show error
            } else {
              $("#modal_addform #result").html(data);
              $("#modal_addform #result").addClass("alert alert-danger");
            }
        });
      });
    </script>

    <!-- AÑADIR EVENTO -->
    <div id="openModal_add_ev" class="modalDialog">
      <div>
        <form action="add_event.php" id="modal_addform_ev" name="addForm_ev">
        <div class="modal-header">
            <h2>Añade un evento</h2>
            <a href="#close" title="Close" class="close btn" id="closeX_ev">X</a>
        </div>
        <div class="modal-body" id="modal_body">
          <div id="result_ev"></div>
          <fieldset>
            <div class="control-group">
              <label class="control-label" for="input01">Selecciona Tipo</label>
              <div class="controls">
                <select name="ev_type" id="add_type_ev" class="input-xlarge">
                  <option value="markets">Markets</option>
                  <option value="talleres">Talleres</option>
                </select>
                <p class="help-block">
                    ¿Quieres publicar un Market o un Taller?
                </p>                   
              </div>
            </div>               
            <div class="control-group">
              <label class="control-label" for="add_owner_name_ev">Nombre del organizador</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="ev_owner_name" id="add_owner_name_ev" maxlength="100" required>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_owner_email_ev">Email del organizador</label>
              <div class="controls">
                <input type="email" class="input-xlarge" name="ev_owner_email" id="add_owner_email_ev" maxlength="100" required>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_title_ev">Título del taller</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="ev_title" id="add_title_ev" maxlength="100" required>
              </div>
            </div>         
            <div class="control-group">
              <label class="control-label" for="add_fecini_ev">Fecha de inicio</label>
              <div class="controls">
                <input type="date" name="ev_fecini" id="add_fecini_ev" maxlength="100" required>            
                   <p class="help-block">
                        Por favor, usar DD/MM/AAAA
                   </p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_fecfin_ev">Fecha de fin</label>
              <div class="controls">
                   <input type="date" name="ev_fecfin" id="add_fecfin_ev" maxlength="100" required>  
                   <p class="help-block">
                        Por favor, usar DD/MM/AAAA
                   </p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_address_ev">Dirección</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="ev_address" id="add_address_ev" required>
                <p class="help-block">
                  Deberías poner tu calle (incluyendo la ciudad y el Código Postal).
                  Pon la misma dirección que pondrías en Google Maps,
                  ¡funciona igual!
                </p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_uri_ev">Web / FanPage</label>
              <div class="controls">
                <input type="url" class="input-xlarge" id="add_uri_ev" name="ev_uri" placeholder="http://" reuired>
                <p class="help-block">
                  Pon tu web completa (ej: http://handmademap.com) y si no tienes aún,
                  puedes poner tu FanPage de Facebook (ej: https://www.facebook.com/Atutichapas)
                </p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_description_ev">Descripción</label>
              <div class="controls">
                <input type="text" class="input-xlarge" id="add_description_ev" name="ev_description" maxlength="150" required>
                <p class="help-block">
                  Explícanos brevemente cómo está enfocado el taller. 150 letras máximo.
                </p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_especialidad2_ev">Especialidad del evento
                 <p class="help-block">
                    Indica una única especialidad para los talleres o un máximo de 10 para markets.
                </p>
            </label>
              <div class="controls">                 
                <ul class="checkboxes" for="add_especialidad_ev"> 
                    <li><input type="checkbox" name="ev_sub_type[]" value="Amigurimi"> Amigurimi </li>                
                    <li><input type="checkbox" name="ev_sub_type[]" value="Bebe"> Bebé: Ropa y complementos </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Bolsos"> Bolsos y mochilas </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Caligrafia"> Caligrafía </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Carvado"> Carvado de sellos </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Ceramica"> Cerámica </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Chapas"> Chapas, imanes y merchandising </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Chuches"> Chuches/candy </li>                   
                    <li><input type="checkbox" name="ev_sub_type[]" value="Costura"> Costura y Patchwork </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Cuadros"> Cuadros </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Cupcakes"> Cupcakes, galletas y tartas </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Decoracion"> Decoración </li>                   
                    <li><input type="checkbox" name="ev_sub_type[]" value="Encuadernacion"> Encuadernación </li>      
                    <li><input type="checkbox" name="ev_sub_type[]" value="Escultura"> Escultura </li> 
                    <li><input type="checkbox" name="ev_sub_type[]" value="Esparto"> Esparto </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Fieltro"> Fieltro </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Fimo"> Fimo </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Fofuchas"> Fofuchas </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Ganchillo"> Ganchillo </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Ilustracion"> Ilustración </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Joyas"> Joyas y bisutería </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Market"> Market </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Merceria"> Merecería creativa </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Muñecos"> Muñecos, peluches y muñecas </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Organizacion"> Organización de eventos </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Piel"> Piel y cuero </li>                
                    <li><input type="checkbox" name="ev_sub_type[]" value="Pintura"> Pintura mural y Graffitis </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Reciclaje"> Reciclaje de materiales </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Ropa"> Ropa </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Scrapbook"> Scrapbook </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Tocados"> Tocados </li>
                    <li><input type="checkbox" name="ev_sub_type[]" value="Trapillo"> Trapillo </li>                    
                    <li><input type="checkbox" name="ev_sub_type[]" value="Vidrio"> Vidrio </li>                                                                                              
                    <li><input type="checkbox" name="ev_sub_type[]" value="Zapatos"> Zapatos </li>   
                </ul>             
              </div>
            </div>
          </fieldset>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="sub_form_ev" style="float: left;">Enviar</button>
            <a href="#" class="btn" id="close_form_ev" data-dismiss="modal" style="float: right;">Cerrar</a>
        </div>
        </form>
      </div>
    </div>
    <script>
      //VACIAR FORMULARIO AL CERRAR LA PANTALLA
      var close_form_ev = document.querySelector('#close_form_ev');
      var closeX_ev     = document.querySelector('#closeX_ev');
      var sub_form_ev   = document.querySelector('#sub_form_ev');

      close_form_ev.addEventListener('click', function() {
        document.getElementById("modal_addform_ev").reset();
      });
      closeX_ev.addEventListener('click', function() {
        document.getElementById("modal_addform_ev").reset();
      });

      // add modal form submit
      $("#modal_addform_ev").submit(function(event) {
        event.preventDefault();
        // get values
        var allVals=[];
        $('input[type="checkbox"]:checked').each(function () {
         allVals.push($(this).val());
        });
        var $form = $( this ),
            ev_owner_name  = $form.find( '#add_owner_name_ev' ).val(),
            ev_owner_email = $form.find( '#add_owner_email_ev' ).val(),
            ev_title       = $form.find( '#add_title_ev' ).val(),
            ev_type        = $form.find( '#add_type_ev' ).val(),            
            ev_fecini      = $form.find( '#add_fecini_ev' ).val(),
            ev_fecfin      = $form.find( '#add_fecfin_ev' ).val(),
            ev_address     = $form.find( '#add_address_ev' ).val(),
            ev_uri         = $form.find( '#add_uri_ev' ).val(),
            ev_description = $form.find( '#add_description_ev' ).val(),
            ev_sub_type    = allVals, 
            ev_url         = $form.attr( 'action' );

        // send data and get results
        var posting = $.post( ev_url, { ev_owner_name: ev_owner_name, ev_owner_email: ev_owner_email, ev_title: ev_title, ev_type: ev_type, ev_fecini: ev_fecini, ev_fecfin: ev_fecfin, ev_address: ev_address, ev_uri: ev_uri, ev_description: ev_description, ev_sub_type: ev_sub_type} );        
        posting.done(function( data ) {
            var content = $( data ).find( '#content' );

            // if submission was successful, show info alert
            if(data == "success") {
              $("#modal_addform_ev #result_ev").html("Hemos recibido su formulario y lo validaremos pronto. Gracias!");
              $("#modal_addform_ev #result_ev").addClass("alert alert-info");
              $("#modal_addform_ev p").css("display", "none");
              $("#modal_addform_ev fieldset").css("display", "none");
              $("#modal_addform_ev .btn-primary").css("display", "none");

            // if submission failed, show error
            } else {
              $("#modal_addform_ev #result_ev").html(data);
              $("#modal_addform_ev #result_ev").addClass("alert alert-danger");
            }
          }
        );
      });
    </script>

    <!-- INFO -->
    <div id="openModal_info" class="modalDialog">
      <div>
        <div class="modal-header">
            <h2>F.A.Q.</h2>
            <a href="#close" title="Close" class="close btn">X</a>
        </div>
        <div class="modal-body">
                  <p> 
                      <b>
                      1. ¿Qué es HandMade Map? 
                      </b>
                  </p>
                  <p>
                      HandMade Map es un mapa en donde queremos geoposicionar a todas las personas relacionadas con el mundo de los objetos hechos a mano.
                  </p>
                  <p> 
                      <b>
                      2. ¿Por qué lo hemos creado?  
                      </b>
                  </p>
                  <p>
                      Como usuarios/compradores de objetos hechos a mano es muy difícil encontrar estos espacios o hacedores por búsquedas de proximidad. Con este mapa queremos visualizar donde están estas personas, ya que por cercanía es mas fácil contactar con ellos.
                  </p>
                  <p> 
                      <b>
                      3. ¿En qué me beneficia como maker o hacedor estar en el mapa?
                      </b>
                  </p>
                  <p>
                      Visibilidad.<br/>
                      Queremos deis a conocer vuestros talleres, en donde la geolocalización es muy importante, y en el caso de que la proximidad sea un problema pueda acudir a vuestra web o fan page para adquirir vuestras obras.
                  </p>
                  <p> 
                      <b>
                      4. ¿Es gratis geoposicinarse en este mapa? 
                      </b>
                  </p>
                  <p>
                      Totalmente gratis, no posee coste ninguno.<br/> 
                      El único fin  de esta herramienta es  aglutinar todo el movimiento handcraft para geoposicionarlo en un mapa para que las personas puedan identificar fácilmente los espacios, makers y puedan adquirir información de los servicios/poductos de las personas/espacios registrados en la web.
                  </p>
                  <p> 
                      <b>
                      5. ¿Qué diferencia hay entre Makers, Espacios, Markets y Talleres? 
                      </b>
                  </p>
                  <p>
                      Maker es lo que conocemos como el hacedor, aquella persona que haga productos hechos a manos.<br/>
                      Espacio es el lugar en donde se produce la venta de productos, a modo tienda o que ofrezca servicios como talleres.<br/>
                      Taller es un evento que puede organizar un determinado espacio para enseñar y mostrar una determinada habilidad.<br/>
                      Market hace referencia a los eventos itinerantes que aglutinan handmakers de todas partes. 
                  </p>
                  <p> 
                      <b>
                      6. Soy un maker ¿pero no quiero ubicar donde estoy exactamente ?
                      </b>
                  </p>
                  <p>
                      No hace falta que pongas tu lugar exacto donde te ubicas, solamente colocando la calle y la ciudad podemos incluirte en el mapa.
                  </p>
                  <p> 
                      <b>
                      7. ¿Como espacio o maker, qué tipologia de etiquetas hay para clasificarme?
                      </b>
                  </p>
                  <p>
                      <ul> 
                          <li>Amigurimi </li>                
                          <li>Bebé: Ropa y complementos </li>                    
                          <li>Bolsos y mochilas </li>
                          <li>Caligrafía </li>
                          <li>Carvado de sellos </li>
                          <li>Cerámica </li>
                          <li>Chapas, imanes y merchandising </li>                    
                          <li>Chuches/candy </li>                   
                          <li>Costura y Patchwork </li>                    
                          <li>Cuadros </li>                    
                          <li>Cupcakes, galletas y tartas </li>                    
                          <li>Decoración </li>                   
                          <li>Encuadernación </li>      
                          <li>Escultura </li> 
                          <li>Esparto </li>                    
                          <li>Fieltro </li>                    
                          <li>Fimo </li>                    
                          <li>Fofuchas </li>                    
                          <li>Ganchillo </li>
                          <li>Ilustración </li>
                          <li>Joyas y bisutería </li>             
                          <li>Mercería creativa </li>
                          <li>Muñecos, peluches y muñecas </li>
                          <li>Organización de eventos </li>                    
                          <li>Piel y cuero </li>                
                          <li>Pintura mural y Graffitis </li>
                          <li>Reciclaje de materiales </li>
                          <li>Ropa </li>                    
                          <li>Scrapbook </li>
                          <li>Tocados </li>
                          <li>Trapillo </li>                    
                          <li>Vidrio </li>                                                                                              
                          <li>Zapatos </li>   
                      </ul>  
                  </p>
                  <p> 
                      <b>
                      8. ¿Cuántos tipos de etiquetas puedo ponermer?
                      </b>
                  </p>
                  <p>
                      Puedes ponerte hasta 10 etiquets de una sola vez como máximo.
                  </p>
                  <p> 
                      <b>
                      9. ¿Y si cuando me registré me faltó una etiqueta por poner?
                      </b>
                  </p>
                  <p>
                      Puedes volver a registrarte pero OJO, registrate esta vez con la etiqueta que te falta, no vuelva a activar las que activaste anteriormente.
                  </p>
                  <p> 
                      <b>
                      10. Me he añadido en el mapa, pero no aparezco…
                      </b>
                  </p>
                  <p>
                      Danos 1-2 dias para procesar la información.
                  </p>
                  <p> 
                      <b>
                      11. ¿Y si me he equivocado con los datos que subí?
                      </b>
                  </p>
                  <p>
                      No pasa nada, puedes escribirnos a <a href="mailto:hello@handmademap.com">hello@handmademap.com</a> y nosotros haremos el cambio.
                  </p>
                  <p> 
                      <b>
                      12. Tengo otra pregunta que no aparece en el listado
                      </b>
                  </p>
                  <p>
                      Escríbenos a <a href="mailto:hello@handmademap.com">hello@handmademap.com</a> y te contestaremos con mucho gusto.
                  </p>
          </div>
          <div class="modal-footer">
            <a href="#" class="btn">Cerrar</a>
          </div>     
        </div>
      </div>


 <!-- Info privacidad -->
    <div id="openModal_priv" class="modalDialog">
      <div>
        <div class="modal-header">
            <h2>Política de Privacidad</h2>
            <a href="#close" title="Close" class="close btn">X</a>
        </div>
        <div class="modal-body">
          <p>
              Nuestra política de privacidad describe como recogemos, 
              guardamos o utilizamos la información que recabamos a través de los diferentes servicios 
              o páginas disponibles en este sitio. Es importante que entienda que información recogemos 
              y como la utilizamos ya que el acceso a este sitio implica la aceptación nuestra política de privacidad.
          </p>
          <p> 
            <b>
            Cookies
            </b>
          </p>
          <p>
              El acceso a este puede implicar la utilización de cookies. 
              Las cookies son pequeñas cantidades de información que se almacenan 
              en el navegador utilizado por cada usuario para que el servidor recuerde cierta 
              información que posteriormente pueda utilizar. 
              Esta información permite identificarle a usted como un usuario concreto y 
              permite guardar sus preferencias personales, así como información técnica 
              como pueden ser visitas o páginas concretas que visite.
          </p>
          <p>
              Aquellos usuarios que no deseen recibir cookies o quieran ser informados antes 
              de que se almacenen en su computadora, pueden configurar su navegador a tal efecto.
          </p>
          <p>
              La mayor parte de los navegadores de hoy en día permiten la gestión de las cookies de 3 
              formas diferentes:
          </p>
            <ol> 
            <li>Las cookies no se aceptan nunca.</li> 
            <li>El navegador pregunta al usuario si se debe aceptar cada cookie.</li> 
            <li>Las cookies se aceptan siempre.</li> 
            </ol> 
          <p>
             El navegador también puede incluir la posibilidad de especificar mejor qué cookies tienen que ser aceptadas y cuáles no. 
             En concreto, el usuario puede normalmente aceptar alguna de las siguientes opciones:
          </p>
            <ol> 
            <li>Rechazar las cookies de determinados dominios;</li> 
            <li>Rechazar las cookies de terceros;</li> 
            <li>Aceptar cookies como no persistentes (se eliminan cuando el navegador se cierra);</li> 
            <li>Permitir al servidor crear cookies para un dominio diferente.</li> 
            </ol> 
          <p> 
            <b>
            Terceros
            </b>
          </p>
          <p>
            En algunos casos, compartimos información sobre los visitantes de este sitio de forma anónima 
            o agregada con terceros como puedan ser anunciantes, patrocinadores o auditores con el único fin de 
            mejorar nuestros servicios. Todas estas tareas de procesamiento serán reguladas según las normas legales y 
            se respetarán todos sus derechos en materia de protección de datos conforme a la regulación vigente
          </p>
          <p>
             Este sitio mide el tráfico con diferentes soluciones que pueden utilizar cookies o web beacons para analizar
             lo que sucede en nuestras páginas. Actualmente utilizamos las siguientes soluciones para la medición del tráfico 
             de este sitio. Puede ver más información sobre la política de privacidad de cada una de las soluciones utilizadas 
             para tal efecto:
          </p>
          <p>
            <li>Google (Analytics): <a href="http://www.google.com/intl/es_ALL/privacypolicy.html">Política de Privacidad de Google Analytics</a></li> 
          </p>
          <p> 
            <b>
            Otras condiciones de uso exigidas de este sito web
            </b>
          </p>
          <p>
            El usuario se compromete a hacer un uso diligente del sitio web y de los servicios accesibles desde el mismo, 
            con total sujeción a la Ley, a las buenas costumbres y al presente aviso legal. Asimismo, se compromete, salvo autorización 
            previa, expresa y escrita de HandMadeMap.com a utilizar la información contenida en el sitio web, exclusivamente para su 
            información, no pudiendo realizar ni directa ni indirectamente una explotación comercial de los contenidos a los que tiene acceso.
          </p>
          <p>
            Este sitio web contiene hiperenlaces que conducen a otras páginas web gestionadas por terceros ajenos a nuestra organización. 
            HandMadeMap.com no garantiza ni se hace responsable del contenido que se recoja en dichas paginas web.
          </p>
          <p>
            Salvo autorización expresa, previa y por escrito de HandMadeMap.com , queda terminantemente prohibida la reproducción, 
            excepto para uso privado, la transformación, y en general cualquier otra forma de explotación, por cualquier procedimiento, 
            de todo o parte de los contenidos de este sitio web.
          </p>   
          <p>
            Queda terminantemente prohibido realizar, sin el previo consentimiento de HandMadeMap.com cualquier manipulación o alteración 
            de este sitio web. Consecuentemente, HandMadeMap.com no asumirá ninguna responsabilidad derivada, o que pudiera derivarse, de 
            dicha alteración o manipulación por terceros.
          </p>            
         
        </div>
        <div class="modal-footer">
          <a href="#" class="btn">Cerrar</a>
        </div>   
      </div>
    </div>
 <!-- fin privacidad -->

    <script>
      //DEFINICION DE VARIABLES
      var gmarkers = [];       
      var markerTitles =[];    
      var infowindow = null; 
      var agent = "default";
      /*
       * Open the drawer when the menu ison is clicked.
       */     
      var menu = document.querySelector('#menu');
      var main = document.querySelector('main');
      var drawer = document.querySelector('.nav');

      menu.addEventListener('click', function(e) {
        drawer.classList.toggle('open');
        menu.classList.toggle('open')
        e.stopPropagation();
      });
      main.addEventListener('click', function() {
        drawer.classList.remove('open');
        menu.classList.remove('open');
      });

      var filter_button = document.querySelector('#filter_button');
      var filter = document.querySelector('.nav_left');

      filter_button.addEventListener('click', function(m) {
        filter.classList.toggle('open');
        filter_button.classList.toggle('open');
        m.stopPropagation();
      });
      filter_button.addEventListener('onclick', function() {
        filter.classList.remove('open');
        filter_button.classList.remove('open');
      });

      var social_button = document.querySelector('#social_button');
      var social = document.querySelector('.social');

      social_button.addEventListener('click', function(c) {
        social.classList.toggle('open');
        social_button.classList.toggle('open');
        c.stopPropagation();
      });
      social_button.addEventListener('onclick', function() {
        social.classList.remove('open');
        social_button.classList.remove('open');
      });      

      // initialize map
      function initialize() {
        // set map styles
        var mapStyles = [
         {
            featureType: "road",
            elementType: "geometry",
            stylers: [
              { hue: "#8800ff" },
              { lightness: 100 }
            ]
          },{
            featureType: "road",
            stylers: [
              { visibility: "on" },
              { hue: "#91ff00" },
              { saturation: -62 },
              { gamma: 1.98 },
              { lightness: 45 }
            ]
          },{
            featureType: "water",
            stylers: [
              { hue: "#005eff" },
              { gamma: 0.72 },
              { lightness: 42 }
            ]
          },{
            featureType: "transit.line",
            stylers: [
              { visibility: "off" }
            ]
          },{
            featureType: "administrative.locality",
            stylers: [
              { visibility: "on" }
            ]
          },{
            featureType: "administrative.neighborhood",
            elementType: "geometry",
            stylers: [
              { visibility: "simplified" }
            ]
          },{
            featureType: "landscape",
            stylers: [
              { visibility: "on" },
              { gamma: 0.41 },
              { lightness: 46 }
            ]
          },{
            featureType: "administrative.neighborhood",
            elementType: "labels.text",
            stylers: [
              { visibility: "on" },
              { saturation: 33 },
              { lightness: 20 }
            ]
          }
        ];

        // set map options
      var zoomControl = true;
      var myOptions = {
          zoom: 6,
          center: new google.maps.LatLng(39.7217793,-3.5655605,7),
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          streetViewControl: false,
          mapTypeControl: false,
          panControl: false,
          zoomControl: zoomControl,
          styles: mapStyles,
          zoomControlOptions: {
            style: google.maps.ZoomControlStyle.SMALL,
            position: google.maps.ControlPosition.LEFT_CENTER
          }
        };

        map = new google.maps.Map(document.getElementById('id_map'), myOptions);
        zoomLevel = map.getZoom();

        // only show marker labels if zoomed in
        google.maps.event.addListener(map, 'zoom_changed', function() {
          zoomLevel = map.getZoom();
          if(zoomLevel <= 15) {
            $(".marker_label").css("display", "none");
          } else {
            $(".marker_label").css("display", "inline");
          }
        });

        // markers array: name, type (icon), lat, long, description, uri, address
        markers = new Array();
        <?php
          $types = Array(
              Array('handmakers', 'Handmakers'),
              Array('espacios','Espacios'),
              Array('talleres', 'Talleres'),              
              Array('markets', 'Markets') 
              );

          $marker_id = 0;
          foreach($types as $type) {            
            if ($type[0] == 'talleres' || $type[0] == 'markets'){
                $fecha_hoy = date("Y-m-d");
                $events = mysql_query("SELECT * FROM events WHERE approved='1' AND type='$type[0]' AND fecfin >= '$fecha_hoy' ORDER BY title");                
                $events_total = mysql_num_rows($events);
                while($event = mysql_fetch_assoc($events)) {
                  $event[title] = htmlspecialchars_decode(addslashes(htmlspecialchars($event[title])));
                  $event[description] = str_replace(array("\n", "\t", "\r"), "", htmlspecialchars_decode(addslashes(htmlspecialchars($event[description]))));
                  $event[uri] = addslashes(htmlspecialchars($event[uri]));
                  $event[address] = htmlspecialchars_decode(addslashes(htmlspecialchars($event[address])));
                  $event[subtype] = htmlspecialchars_decode(addslashes(htmlspecialchars($event[subtype])));               
                  $event[fecini] = htmlspecialchars_decode(addslashes(htmlspecialchars($event[fecini])));                  
                  $fecini_marker = date("d-m-Y",strtotime($event[fecini]));                                  
                  $event[fecfin] = htmlspecialchars_decode(addslashes(htmlspecialchars($event[fecfin])));  
                  $fecfin_marker = date("d-m-Y",strtotime($event[fecfin]));

                  echo "
                    markers.push(['".$event[title]."','".$event[type]."', '".$event[lat]."', '".$event[lng]."', '".$event[description]."', '".$event[uri]."', '".$event[address]."', '".$event[subtype]."', '".$fecini_marker."', '".$fecfin_marker."']);
                    markerTitles[".$marker_id."] = '".$event[title]."';
                  ";       
                  $count[$event[type]]++;
                  $marker_id++;
                }
            } else {
                $places = mysql_query("SELECT * FROM places WHERE approved='1' AND type='$type[0]' ORDER BY title");
                $places_total = mysql_num_rows($places);
                while($place = mysql_fetch_assoc($places)) {
                  $place[title] = htmlspecialchars_decode(addslashes(htmlspecialchars($place[title])));
                  $place[description] = str_replace(array("\n", "\t", "\r"), "", htmlspecialchars_decode(addslashes(htmlspecialchars($place[description]))));
                  $place[uri] = addslashes(htmlspecialchars($place[uri]));
                  $place[address] = htmlspecialchars_decode(addslashes(htmlspecialchars($place[address])));             
                  $place[subtype] = htmlspecialchars_decode(addslashes(htmlspecialchars($place[subtype])));
                  echo "
                    markers.push(['".$place[title]."', '".$place[type]."', '".$place[lat]."', '".$place[lng]."', '".$place[description]."', '".$place[uri]."', '".$place[address]."', '".$place[subtype]."']);
                    markerTitles[".$marker_id."] = '".$place[title]."';
                  ";
                  $count[$place[type]]++;
                  $marker_id++;
                }
            }
          }
        ?>

        var oms = new OverlappingMarkerSpiderfier(map);

        // ADD MARKERS
        jQuery.each(markers, function(i, val) {
          infowindow = new google.maps.InfoWindow({
            content: ""
          });
          var iconSize = null;
          // build this marker
          var markerImage = new google.maps.MarkerImage("./images/icons/"+val[1]+".png", null, null, null, iconSize);
          var marker = new google.maps.Marker({
            position: new google.maps.LatLng(val[2],val[3]),
            map: map,
            title: '',
            clickable: true,
            infoWindowHtml: '',
            zIndex: 10 + i,
            icon: markerImage
          });
          marker.type = val[1];
          marker.subtype = val[7];
       
          if (val[1] == 'markets' || val[1] == 'talleres'){
             marker.fecini = val[8];
             marker.fecfin = val[9];
          }
          gmarkers.push(marker);

          oms.addMarker(marker);

          // add marker hover events (if not viewing on mobile)
          if(agent == "default") {
            google.maps.event.addListener(marker, "mouseover", function() {
              this.old_ZIndex = this.getZIndex();
              this.setZIndex(9999);
              $("#marker"+i).css("display", "inline");
              $("#marker"+i).css("z-index", "99999");
            });
            google.maps.event.addListener(marker, "mouseout", function() {
              if (this.old_ZIndex && zoomLevel <= 15) {
                this.setZIndex(this.old_ZIndex);
                $("#marker"+i).css("display", "none");
              }
            });
          }

          // format marker URI for display and linking
          var markerURI = val[5];
          if(markerURI.substr(0,7) != "http://") {
            markerURI = "http://" + markerURI;
          }
          var markerURI_short = markerURI.replace("http://", "");
          var markerURI_short = markerURI_short.replace("www.", "");

          // add marker click effects (open infowindow)
          google.maps.event.addListener(marker, 'click', function () {

            if (val[1] == 'markets' || val[1] == 'talleres') { 
                var markerFecha = "Del " + val[8] + " al " + val[9];
                infowindow.setContent(
                  "<div class='marker_title'>"+val[0]+"</div>"
                  + "<div class='marker_fecini'>"+markerFecha+"</div>"            
                  + "<div class='marker_uri'><a target='_blank' href='"+markerURI+"'>"+markerURI_short+"</a></div>"
                  + "<div class='marker_desc'>"+val[4]+"</div>"
                  + "<div class='marker_address'>"+val[6]+"</div>" 
                );
            } else {
                infowindow.setContent(
                  "<div class='marker_title'>"+val[0]+"</div>"
                  + "<div class='marker_uri'><a target='_blank' href='"+markerURI+"'>"+markerURI_short+"</a></div>"
                  + "<div class='marker_desc'>"+val[4]+"</div>"
                  + "<div class='marker_address'>"+val[6]+"</div>"                 
                );
            }
            infowindow.open(map, this);
          });

          // add marker label
          var latLng = new google.maps.LatLng(val[2], val[3]);
          var label = new Label({
            map: map,
            id: i
          });
          label.bindTo('position', marker);
          label.set("text", val[0]);
          label.bindTo('visible', marker);
          label.bindTo('clickable', marker);
          label.bindTo('zIndex', marker);

        }) //FIN ADD MARKERS 
      } //FIN INITIALIZE

        //FUNCIONES PARA LA BARRA LATAREAL

        // hide all markers of a given type
        function hide(type) {
          for (var i=0; i<gmarkers.length; i++) {
            if (gmarkers[i].type == type) {
              gmarkers[i].setVisible(false);
            }
          }
          $("#filter_"+type).addClass("inactive");
        }

        // show all markers of a given type
        function show(type) {
          for (var i=0; i<gmarkers.length; i++) {
            if (gmarkers[i].type == type) {
              gmarkers[i].setVisible(true);
            }
          }
          $("#filter_"+type).removeClass("inactive");
        }        

        // hide all markers of a given type
        function hides(type,subtype) {          
           for (var i=0; i<gmarkers.length; i++) {
             if (gmarkers[i].type == type && gmarkers[i].subtype == subtype) {
                 gmarkers[i].setVisible(false);
              }
           }
           $("#filter_"+type+'_'+subtype).addClass("inactive");      
        }

        // show all markers of a given type
        function shows(type,subtype) {  
           for (var i=0; i<gmarkers.length; i++) {
             if (gmarkers[i].type == type && gmarkers[i].subtype == subtype) {
                  gmarkers[i].setVisible(true);
               }
           }
           $("#filter_"+type+'_'+subtype).removeClass("inactive");         
        }

        // toggle (hide/show) markers of a given type (on the map)
        function toggle(type) {
            var index;
            var subtipos = ["Amigurimi","Bebe","Bolsos","Caligrafia","Carvado","Ceramica","Chapas","Chuches","Costura","Cuadros","Cupcakes"
            ,"Decoracion","Encuadernacion","Escultura","Esparto","Fieltro","Fimo","Fofuchas","Ganchillo","Ilustracion","Joyas","Markets"
            ,"Merceria","Muñecos","Organizacion","Piel","Pintura","Reciclaje","Ropa","Scrapbook","Tocados","Trapillo","Vidrio","Zapatos"];

          if($('#filter_'+type).is('.inactive')) {
            show(type);
            if (String(type) != "markets"){
               for (index = 0; index < subtipos.length; index++) {
                  shows(type,subtipos[index]); 
               }
            }         
          } else {
            hide(type);  
            if (String(type) != "markets"){
               for (index = 0; index < subtipos.length; index++) {
                  hides(type,subtipos[index]); 
               }
            }           
          }
        }
        
      // toggle (hide/show) markers of a given type (on the map)
      function toggles(type,subtype) {
        if($('#filter_'+type+'_'+subtype).is('.inactive')) {
          shows(type,subtype);
        } else {
          hides(type,subtype);
        }
      }
        // toggle (hide/show) marker list of a given type
        function toggleList(type) {
          $(".nav_left__list .list-"+type).toggle();
        }

        // toggle (hide/show) marker list of a given type
        function toggleLists(subtype) {
          $(".nav_left__list .list-"+subtype).toggles();
        }        
      google.maps.event.addDomListener(window, 'load', initialize); 

        // zoom to marker if selected in search typeahead list
        $('#search').typeahead({
          source: markerTitles,
          onselect: function(obj) {
            marker_id = jQuery.inArray(obj, markerTitles);
            if(marker_id > -1) {
              map.panTo(gmarkers[marker_id].getPosition());
              map.setZoom(15);
              google.maps.event.trigger(gmarkers[marker_id], 'click');
            }
            $("#search").val("");
          }
        });
        function controlcookies() {
          // si variable no existe se crea (al clicar en Aceptar)
          localStorage.controlcookie = (localStorage.controlcookie || 0);
          localStorage.controlcookie++; // incrementamos cuenta de la cookie
          cookie1.style.display='none'; // Esconde la política de cookies
        }
    </script>
  </body>
</html>
