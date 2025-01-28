<?php
    require "../dist/php/template/header.php";

    if(empty($_GET["telefono"])) { 
        require "../dist/php/template/inserimento_telefono.php";
    }
    else {
        require "../vendor/autoload.php";
        try {
            
            /* Il logout non è supportato
            if(file_exists("../dist/sessions/".$_SESSION["id"].$_GET["telefono"].".madeline")) {
                $settings = new \danog\MadelineProto\Settings;

                $MadelineProto = new \danog\MadelineProto\API("../dist/sessions/".$_SESSION["id"].$_GET["telefono"].".madeline", $settings);
                if($MadelineProto->getAuthorization() === $MadelineProto::LOGGED_IN)
                    $MadelineProto->logout();
                unset($MadelineProto);
            }*/
            shell_exec("ps aux | grep -ie '".$_SESSION["id"].$_GET["telefono"]."' | awk '{print $2}' | xargs kill -9");
            foreach(glob("../dist/sessions/".$_SESSION["id"].$_GET["telefono"]."*") as $file)
                system("rm -rf ".escapeshellarg($file));


            echo $_GET["telefono"]." eliminato con successo";
        }
        catch(Exception $e) {
            echo "Utente non eliminato per il seguente errore: ".$e->getMessage();
        }
        finally {
            echo '<br><br><a href="account_elimina.php"><button type="button" class="btn btn-info">Eliminane un altro</button></a><br><br><a href="index.php"><button type="button" class="btn btn-warning">Torna alla home</button></a>';
        }

        if(!empty($_GET["redirect"])) { ?>
        <script>
            window.location.href = "<?php echo $_GET["redirect"];?>";
        </script>
        <?php }

    }

    require "../dist/php/template/footer.php";
?>