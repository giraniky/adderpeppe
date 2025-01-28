<?php
    require "../dist/php/template/header.php";
    
    if(empty($_GET["telefono"])) {
        require "../dist/php/template/inserimento_telefono.php";
    }
    else {
        try {
            require "../vendor/autoload.php";

            $settings = new \danog\MadelineProto\Settings;
            
            $logger = new \danog\MadelineProto\Settings\Logger;
            $logger->setLevel(\danog\MadelineProto\Logger::LEVEL_ULTRA_VERBOSE);
            $logger->setType(\danog\MadelineProto\Logger::LOGGER_ECHO);
            $settings->setLogger( $logger );
            unset($logger);

            $settings->setTemplates( 
                (new \danog\MadelineProto\Settings\Templates)->setHtmlTemplate( 
                    file_get_contents("../dist/php/template/account_aggiungi.php").
                    file_get_contents("../dist/php/template/footer.php")
                ) 
            );

            /*$settings->setAppInfo( 
                (new \danog\MadelineProto\Settings\AppInfo)->setApiId(1525495)->setApiHash("afed6a76e480760c081447927d769444")
            );*/

            umask(002); //cambio i permessi dei file che crea www-data per farli accedere a tutto il gruppo e non solo il proprietario

            $MadelineProto = new \danog\MadelineProto\API("../dist/sessions/".$_SESSION["id"].$_GET["telefono"].".madeline", $settings);

            $MadelineProto->start();

            $MadelineProto->setNoop();


            echo "Utente aggiunto con successo";
        }
        catch(Exception $e) {
            echo "Utente non aggiunto per il seguente errore: ".$e->getMessage();
        }
        finally {
            echo '<br><br><a href="account_aggiungi.php"><button type="button" class="btn btn-info">Aggiungine un altro</button></a><br><br><a href="index.php"><button type="button" class="btn btn-warning">Torna alla home</button></a>';
        }
    }

    require "../dist/php/template/footer.php";
?>