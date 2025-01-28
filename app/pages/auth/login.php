<?php 
session_start();
 
if(!empty($_SESSION["id"]) && !empty($_SESSION["token"])){
    header("location: ../index.php");
    exit;
}

if(!empty($_POST["token"]) || !empty($_COOKIE["token"])) {
    if(!empty($_POST["token"])) {
      $token = $_POST["token"];
      if(isset($_POST["ricordami"]) && $_POST["ricordami"])
        $ricordami = true;
      else 
        $ricordami = false;
    }
    else {
      $token = $_COOKIE["token"];
      $ricordami = true;
    }
    
    require "../../dist/php/funzioni/login.php";
    $id = login($token);

    if($id > 0) {
        $_SESSION["id"] = $id;
        $_SESSION["token"] = $token;

        if($ricordami)
          //https://stackoverflow.com/questions/3290424/set-a-cookie-to-never-expire/22479460#22479460
          setcookie("token", $token, 2147483647);
        else
          //https://stackoverflow.com/questions/686155/remove-a-cookie/686166#686166
          setcookie("token", "", time()-3600);

        header("location: ../index.php");
    }
    else
      $errore = "Non è possibile autenticarsi con il token inserito";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Adder | Log in</title>
  <link rel="icon" type="image/x-icon" href="../../dist/img/favicon.ico">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <img class="img-circle" style="display: block;margin-left: auto; margin-right: auto; width: 30%;" src="../../dist/img/logo.png">
    <a href="#"><b>Adder</b>
  </div>

  <?php if(isset($errore)) { ?>
  <div class="callout callout-danger">
    <h4>Errore!</h4>
    <p><?php echo $errore; ?></p>
  </div>
  <?php } ?>

  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Inserisci il tuo token per accedere al pannello</p>

      <form method="post">
        <div class="input-group mb-3">
          <input type="password" name ="token" class="form-control" placeholder="Token" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" name="ricordami">
              <label for="remember">
                Ricordami
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Log In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
</body>
</html>