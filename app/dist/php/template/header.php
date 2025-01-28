<?php
  require  __DIR__."/../start.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Adder</title>
  <link rel="icon" type="image/x-icon" href="../dist/img/favicon.ico">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="auth/logout.php" role="button">
          <i class="fa fa-sign-out-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
      <img src="../dist/img/logo.png" alt="logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Adder Bot</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        
        <li class="nav-item">
          <a href="index.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Home</p>
          </a>
        </li>

        <li class="nav-header">Bot aggiunta</li>
        <li class="nav-item">
          <a href="bot.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Avvia il bot</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="logs.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Logs</p>
          </a>
        </li>
  
        <li class="nav-header">Bot lista</li>
        <li class="nav-item">
          <a href="membri.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Avvia il bot</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="membri_logs.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Logs</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="membri_riepilogo.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Riepilogo</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="membri_cancella.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Cancella lista</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="membri_download_excel.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Download Excel</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="membri_upload_excel.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Upload Excel</p>
          </a>
        </li>
        <li class="nav-header">Account</li>
        <li class="nav-item">
          <a href="account_gestisci.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Gestisci</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="account_aggiungi.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Aggiungi</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="account_elimina.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Elimina</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="messaggi.php" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Messaggi</p>
          </a>
        </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Home</a></li>
              <li class="breadcrumb-item"></li>
              <li class="breadcrumb-item active"></li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container">
        <div class="row">
          <div class="col-12">
		    <div class="card">
			  <div class="card-body">