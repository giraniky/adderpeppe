<?php
  require "../dist/php/template/header.php";
  if(!empty($_GET["file"])) {
    $dati = file_get_contents("../bot/dati/".$_SESSION["id"]." ".$_GET["file"].".json");
    $dati = json_decode($dati,true);

    echo "Totale membri salvati: ".count($dati);
    echo "<table class='table table-responsive'><thead>";
    foreach($dati[0] as $colonna => $dato) {
      echo "<th>".$colonna."</th>";
    }
    echo "</thead><tbody>";

    foreach($dati as $membro) {
      echo "<tr>";
      foreach($membro as $dato) {
        echo "<td>".$dato."</td>";
      }
      echo "</tr>";
    }
    echo "</tbody></table>";

  }
  else {
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
        <label for="file">Seleziona il riepilogo dei membri che vuoi visualizzare</label>
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
}
  require "../dist/php/template/footer.php";
?>