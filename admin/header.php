<?php
include "../include/db.php";
// get task
if(isset($_GET['task'])) { $task = $_GET['task']; } 
else if(isset($_POST['task'])) { $task = $_POST['task']; }

// get view
if(isset($_GET['view'])) { $view = $_GET['view']; } 
else if(isset($_POST['view'])) { $view = $_POST['view']; }
else { $view = ""; }

// get page
if(isset($_GET['p'])) { $p = $_GET['p']; } 
else if(isset($_POST['p'])) { $p = $_POST['p']; }
else { $p = 1; }

// get search
if(isset($_GET['search'])) { $search = $_GET['search']; } 
else if(isset($_POST['search'])) { $search = $_POST['search']; }
else { $search = ""; }

//HandmadeMap 2.1
// get table
if(isset($_GET['table'])) { $table = $_GET['table']; } 
else if(isset($_POST['table'])) { $table = $_POST['table']; }
else { $table= "places"; }
//HandmadeMap 2.1

// make sure admin is logged in
if($page != "login") {
  if($_COOKIE["handmademap_user"] != crypt($admin_user, $admin_user) OR $_COOKIE["handmademap_pass"] != crypt($admin_pass, $admin_pass)) {
    header("Location: login.php");
    exit;
  }
}

// connect to db
mysql_connect($db_host, $db_user, $db_pass) or die(mysql_error());
mysql_select_db($db_name) or die(mysql_error());

// get marker totals
if ($table == "events"){
    $total_approved = mysql_num_rows(mysql_query("SELECT id FROM events WHERE approved='1'"));
    $total_rejected = mysql_num_rows(mysql_query("SELECT id FROM events WHERE approved='0'"));
    $total_pending = mysql_num_rows(mysql_query("SELECT id FROM events WHERE approved IS null"));
    $total_all = mysql_num_rows(mysql_query("SELECT id FROM events"));    
} else {
    $total_approved = mysql_num_rows(mysql_query("SELECT id FROM places WHERE approved='1'"));
    $total_rejected = mysql_num_rows(mysql_query("SELECT id FROM places WHERE approved='0'"));
    $total_pending = mysql_num_rows(mysql_query("SELECT id FROM places WHERE approved IS null"));
    $total_all = mysql_num_rows(mysql_query("SELECT id FROM places"));
}
   

// admin header
$admin_head = "
  <html>
  <head>
    <title>HandmadeMap Admin</title>
    <meta charset='UTF-8'>
    <link href='../admin/bootstrap/css/bootstrap.css' rel='stylesheet' type='text/css' />
    <link href='../admin/bootstrap/css/bootstrap-responsive.css' rel='stylesheet' type='text/css' />
    <link rel='stylesheet' href='admin.css' type='text/css' />
    <script src='../admin/bootstrap/js/bootstrap.js' type='text/javascript' charset='utf-8'></script>
    <script src='../scripts/jquery-1.7.1.js' type='text/javascript' charset='utf-8'></script>
    <script src='https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false' type='text/javascript' charset='utf-8'></script>
  </head>
  <body>
";
if($page != "login") {
  $admin_head .= "
    <div class='navbar navbar-fixed-top'>
      <div class='navbar-inner'>
        <div class='container'>
          <a class='brand' href='index.php'>
            HandmadeMap
          </a>
          <ul class='nav'>
            <li"; if($view == "") { $admin_head .= " class='active'"; } $admin_head .= ">
              <a href='index.php'>Todos</a>
            </li>
            <li"; if($view == "approved") { $admin_head .= " class='active'"; } $admin_head .= ">
              <a href='index.php?view=approved&table=$table'>
                Aprobados
              <span class='badge badge-info'>$total_approved</span>
              </a>
            </li>
            <li"; if($view == "pending") { $admin_head .= " class='active'"; } $admin_head .= ">
              <a href='index.php?view=pending&table=$table'>
                Pendientes
              <span class='badge badge-info'>$total_pending</span>
              </a>
            </li>
            <li"; if($view == "rejected") { $admin_head .= " class='active'"; } $admin_head .= ">
              <a href='index.php?view=rejected&table=$table'>
                Rechazados
              <span class='badge badge-info'>$total_rejected</span>                  
              </a>
            </li>          
            <li>
              <a href='index.php?table=places'> Places </a>
            </li>
            <li>
              <a href='index.php?table=events'> Events </a>
            </li>          
          </ul>
          <form class='navbar-search pull-left' action='index.php' method='get'>
            <input type='text' name='search' class='search-query' placeholder='Buscar' autocomplete='off' value='$search'>
          </form>
          <ul class='nav pull-right'>
            <li><a href='login.php?task=logout'>Desconectar</a></li>
          </ul>
        </div>
      </div>
    </div>
  ";
}
$admin_head .= "
  <div id='content'>
";

// admin footer 
$admin_foot = "
    </div>
  </body>
</html>
";


?>