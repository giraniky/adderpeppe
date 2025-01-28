<?php
  require "../dist/php/template/header.php";
  if(!empty($_GET["file"])) {
    unlink("../bot/dati/".$_SESSION["id"]." ".$_GET["file"].".json");
   
    echo "File eliminato con successo";

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
        <label for="file">Seleziona la lista dei membri che vuoi cancellare</label>
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