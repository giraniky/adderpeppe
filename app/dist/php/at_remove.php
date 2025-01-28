<?php
    require "start.php";
    shell_exec('for i in $(atq | cut -f 1); do if at -c "$i" | grep -q "add.php '.$_SESSION["id"].'"; then atrm "$i"; fi done');    
    /*
    var_dump(error_get_last());
    var_dump($_SESSION);
    var_dump($output);
    var_dump($return_value);
    */
?>
<script>
    window.location.href = "../../pages/<?php echo $_GET["redirect"]; ?>";
</script>