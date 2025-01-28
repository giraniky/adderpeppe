<?php
  require "../dist/php/template/header.php";

  if(is_uploaded_file($_FILES['csv']['tmp_name']) ) {
 
    $pathinfo = pathinfo($_FILES['csv']['name']);


    if($pathinfo["extension"] !== "csv")
      echo "Wrong file extension";
    else {

      $dati = file_get_contents($_FILES['csv']['tmp_name']);
      $dati = str_replace("\r\n", "\n", $dati);
      $dati = explode("\n", $dati);

      $result = [];

      foreach($dati as $i => $riga) {


        $riga = explode(";", $riga);


        if($i === 0) {
          $dati[0] = $riga;
          continue;
        }

        if(empty($riga[0]))
          continue;

        //convert excel floating comma to integer
        $riga[0] = "+". ( (int) str_replace(",", ".", $riga[0])  );

        $result_riga = [];

        for($j=0; $j<count($riga); $j++)
          $result_riga[$dati[0][$j]] = $riga[$j];
        
        $result[] = $result_riga;


      }

      $result = json_encode($result, JSON_INVALID_UTF8_IGNORE);

      if($a=json_last_error() !== JSON_ERROR_NONE)
        echo "Il file non è strutturato in maniera corretta: ".json_last_error_msg();
      else {


        if(file_put_contents("../bot/dati/".$_SESSION["id"]." ".ltrim(strtolower(trim($pathinfo["filename"])),"@").".json", $result))
          echo "File caricato con successo";
        else
          echo "Errore nel caricare il file";
      }

    }

  }
  else {

?>
<form method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="csv">File Excel <small>(Assicurati di usare come nome del file l'username oppure chat id del gruppo da cui i membri sono stati presi)</small></label>
        <br>
        <input type="file" name = "csv" accept=".csv" required>
    </div>
    <button type="submit" class="btn btn-primary">Vai</button>
</form>
<?php 

}
  require "../dist/php/template/footer.php";
?>