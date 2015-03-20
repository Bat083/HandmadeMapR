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
    <script src="./scripts/jquery-1.7.1.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="./scripts/label.js"></script>
    <script type="text/javascript" src="./scripts/oms.min.js"></script>    
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
        <li class="nav__item"><a href="#">Inscribete</a></li>
        <li class="nav__item"><a href="#">Eventos</a></li>
        <li class="nav__item"><a href="#">Info</a></li>
        <li class="nav__item"><a href="#">Blog</a></li>
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
          <li class="blurb"><?= $blurb ?></li>
        </ul>
        </nav> 
        <section>
            <img id="filter_button" class="filter__menu" src="./images/hamburger.png"> 
        </section>
        <section id="id_map" class="map">
        </section>
      
        <footer>
         <ul>
           <li><a href="#">Contact us</a></li>
           <li><a href="#">Follow us on Twitter</a></li>
           <li><a href="#">RSS</a></li>
          </ul>
        </footer>
      </section>
    </main>
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
      var zoomControl = false;
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

    </script>
  </body>
</html>
