<?php
if(!file_exists('include/db.php')) require_once('installer.php');
include_once "header.php";
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>HandmadeMap - Mapa de handmakers de España</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="main.css">
    <link rel="stylesheet" type="text/css" href="responsive.css">
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
  </head>
  <body>

    <header class="header">
      <div class="header__inner">
        <img class="header__logo" src="images/city.png">
        <h1 class="header__title">
          HandMade Map
        </h1>
        <img id="menu" class="header__menu" src="./images/hamburger.png"> 
      </div>
    </header>

    <nav id="drawer" class="nav">
      <ul class="nav__list">
        <li class="nav__item"><a href="#">Inscribete</a></li>
        <li class="nav__item"><a href="#">Eventos</a></li>
        <li class="nav__item"><a href="#">Info</a></li>
        <li class="nav__item"><a href="#">Blog</a></li>
      </ul>
    </nav>

    <main>
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
        <li class="blurb"><?= $blurb ?></li>
      </ul>
      </nav> 
      <section class="container content">
        <section>
            <img id="filter_button" class="filter__menu" src="./images/hamburger.png"> 
        </section>
        <section id="id_map" class="map">
        </section>
      </section>
      <footer>
        <ul>
          <li><a href="#">Contact us</a></li>
          <li><a href="#">Follow us on Twitter</a></li>
          <li><a href="#">RSS</a></li>
        </ul>
      </footer>
    </main>
    <script>
      /*
       * Open the drawer when the menu ison is clicked.
       */
      var menu = document.querySelector('#menu');
      var main = document.querySelector('main');
      var drawer = document.querySelector('.nav');

      menu.addEventListener('click', function(e) {
        drawer.classList.toggle('open');
        e.stopPropagation();
      });
      main.addEventListener('click', function() {
        drawer.classList.remove('open');
      });

      var filter_button = document.querySelector('#filter_button');
      var filter = document.querySelector('.nav_left');

      filter_button.addEventListener('click', function(m) {
        filter.classList.toggle('open');
        m.stopPropagation();
      });
      main.addEventListener('click', function() {
        filter.classList.remove('open');
      });
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

        map = new google.maps.Map(document.getElementById('id_map'), myOptions);
        zoomLevel = map.getZoom();

    </script>
  </body>
</html>
