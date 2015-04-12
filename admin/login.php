<?
$page = "login";
include "header.php";

$is_loggedin = false;
$alert = "";

// logout
if($task == "logout") {
  setcookie("handmademap_user", "", time()+3600000);
  setcookie("handmademap_pass", "", time()+3600000);
  header("Localización: login.php");
  exit;
}

// attempt login
if($task == "dologin") {
  $input_user = htmlspecialchars($_POST['user']);
  $input_pass = htmlspecialchars($_POST['pass']);
  if(trim($input_user) == "" || trim($input_pass) == "") {
    $alert = utf8_decode(utf8_encode('No. ¿Intentar de nuevo?'));
  } else {
    if(crypt($input_user, $admin_user) == crypt($admin_user, $admin_user) && crypt($input_pass, $admin_pass) == crypt($admin_pass,$admin_pass)) {
      setcookie("handmademap_user", crypt($input_user, $admin_user), time()+3600000);
      setcookie("handmademap_pass", crypt($input_pass, $admin_pass), time()+3600000);
      header("Localización: index.php");
      exit;
    } else {
      $alert = utf8_decode(utf8_encode('La información que enviaste es inválida. :('));
    }
  }
}

?>

<? echo $admin_head; ?>

<form class="well form-inline" action="login.php" id="login" method="post">
  <h1>
    HandmadeMap Admin
  </h1>
  <?
    if($alert != "") {
      echo "
        <div class='alert alert-danger'>
          $alert
        </div>
      ";
    }
  ?>
  <input type="text" name="user" class="input-large" placeholder="Usuario">
  <input type="password" name="pass" class="input-large" placeholder="Password">
  <button type="submit" class="btn btn-info">Conectarse</button>
  <input type="hidden" name="task" value="dologin" />
</form>

<? echo $admin_foot; ?>