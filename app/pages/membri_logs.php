<?php
  require "../dist/php/template/header.php";
  if(!empty($_GET["file"])) {
?>
<center><b>Ecco i logs della sessione avviata il <?php echo pathinfo($_GET["file"],PATHINFO_FILENAME);?></b></center>
Se vuoi terminare la sessione <a href='../dist/php/membri_stop.php?redirect=membri_logs.php'>premi qui</a>.
<br><br>
<div class="form-group">
<label for="visualizzazione">Modalità di visualizzazione: </label>
<select class="form-control" id="visualizzazione">
  <option value="semplificata" selected>Semplificata</option>
  <option value="completa">Completa</option>
</select>
</div>
<br><br>

<div id="logs">
  Caricamento...
</div>


<div id="contatore" class="toasts-top-right fixed">
  <div class="toast bg-success fade show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <strong class="mr-auto">Membri aggiunti con successo:</strong>
      <button onclick="$('#contatore').remove()" type="button" class="ml-2 mb-1 close" aria-label="Close"><span aria-hidden="true">×</span></button>
  </div>
  <div class="toast-body">0</div>
  </div>
</div>
<div style="position: fixed; bottom: 20px; right: 20px;">
<button  type="button" class="btn btn-primary btn-floating btn-lg" onclick="window.scrollTo(0, 0);">
  <i class="fas fa-arrow-up"></i>
</button>
<br>
<br>
<button type="button" class="btn btn-primary btn-floating btn-lg" onclick="window.scrollTo(0, document.body.scrollHeight);">
  <i class="fas fa-arrow-down"></i>
</button>
</div>

<?php
  require "../dist/php/template/footer.php";
?>
<script>
  let file = "<?php echo $_SESSION["id"]." ".$_GET["file"];?>";
  
  function logs() {
    $.get( "../bot/membri/"+file, function( data ) {
      $("#contatore > .toast > .toast-body").html( (data.match(/inserito nella lista dei membri da salvare/g) ?? []).length );


      
      if($("#visualizzazione").val() === "semplificata")
        regex = /^(?:membri):.*/gm;
      else
        regex = /^.*/gm;

      $("#logs").html( data.match(regex).join("<br>").replaceAll("membri:","<small><i class='far fa-circle nav-icon'></i></small>") );
    });
  }

  logs();
  setInterval( logs, 3000);
</script>
<?php }
else { 
  
$files = array_reverse(glob("../bot/membri/".$_SESSION["id"]." *"));  
if(count($files) < 1) {
?>
Non hai mai avviato il bot
<?php
}
else {
foreach($files as $i => $file) {
  $file = explode(" ",pathinfo($file,PATHINFO_BASENAME));
  unset($file[0]);
  $file = implode(" ", $file);
  $files[$i] = $file;
}
usort($files, function($time1, $time2) {
    $time1 = pathinfo($time1,PATHINFO_FILENAME);
    $time2 = pathinfo($time2,PATHINFO_FILENAME);

    if (strtotime($time1) < strtotime($time2))
        return 1;
    else if (strtotime($time1) > strtotime($time2)) 
        return -1;
    else
        return 0;
});
?>
<form method="GET">
    <div class="form-group">
        <label for="file">Seleziona i logs che vuoi visualizzare</label>
        <select name="file" onchange="$(this).parent().submit()">
          <?php foreach($files as $file) { ?>
            <option value="<?php echo $file; ?>"><?php echo pathinfo($file,PATHINFO_FILENAME);?></option>
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
} ?>