<?php
    require "start.php";
    shell_exec("ps aux | grep 'php ../bot/add.php ".$_SESSION["id"]."' | awk '{print $2}' | xargs kill"); //killa bot adder //ho tolto -9
    shell_exec("ps aux | grep 'dist/sessions/".$_SESSION["id"]."+' | awk '{print $2}' | xargs kill -9"); //killa worker
    
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