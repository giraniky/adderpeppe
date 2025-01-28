<?php

  if(!empty($_GET["file"])) {

    require "../dist/php/start.php";

    $dati = file_get_contents("../bot/dati/".$_SESSION["id"]." ".$_GET["file"].".json");
    $dati = json_decode($dati, true);

	  header('Content-Type: application/excel');
    header('Content-Disposition: attachment; filename="'.$_GET["file"].'.csv";');


    $file_ptr = fopen('php://output', 'w');

    $header = [];
    foreach($dati[0] as $colonna => $dato)
        array_push($header, $colonna);

    fputcsv($file_ptr, $header, ';');

    unset($header);

    foreach($dati as $membro)
       fputcsv($file_ptr, $membro, ';');


    fclose($file_ptr);

  }
  else {
    require "../dist/php/template/header.php";
    $files = glob("../bot/dati/".$_SESSION["id"]." *");  
    if(count($files) < 1) {
    ?>
    Nel database non ci sono membri salvati
    <?php
    }
    else {
    ?>
    <form method="GET">
        <div class="form-group">
            <label for="file">Seleziona il riepilogo dei membri che vuoi scaricare</label>
            <select name="file">
              <?php foreach($files as $file) { 
                $file_name = explode(" ",pathinfo($file,PATHINFO_FILENAME),2)[1];
                ?>
                <option><?php echo $file_name;?></option>
                <?php
              }
              ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Vai</button>
    </form>
    <?php 
      }

    require "../dist/php/template/footer.php";

  }

?>