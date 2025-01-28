<?php

    $id = (int) $argv[1];
    $current_riavvio = (int) $argv[2];

    if(empty($id))
        exit("Impossibile avviare il bot: parametro id mancante");
    
    require "../dist/php/database.php";
    try {
        $query=$pdo->prepare("Select `bot` from `utenti` where id =?");
        $query->execute([$id]);
    }
    catch(Exception $e) {
        exit("Impossibile reperire le impostazioni dal database: ".$e->getMessage());
    }

    unset($pdo);


    $bot = json_decode($query->fetch(PDO::FETCH_ASSOC)["bot"],true);

    if(empty($bot))
        exit("Non ci sono impostazioni nel database");

    unset($query);

    $destinazione = ltrim(strtolower(trim($bot["destinazione"])),"@");
    $sorgenti = explode(",",$bot["sorgenti"]);
    foreach($sorgenti as $i => $sorgente) {
        $sorgenti[$i] = ltrim(strtolower(trim($sorgente)),"@");
    }
    
    $massima_aggiunta_per_account = (int) $bot["massima_aggiunta_per_account"];

    $account_da_non_usare = $bot["account_da_non_usare"] ?? [];

    $attesa_min = (int) $bot["attesa_min"];
    $attesa_max = (int) $bot["attesa_max"];

    $data_online_minima = empty($bot["data_online_minima"]) ? 0 : strtotime($bot["data_online_minima"]);
    $data_online_massima = strtotime($bot["data_online_massima"]);

    $status_consentiti = $bot["status_consentiti"];
    $salta_membri_senza_status = array_key_exists("salta_membri_senza_status", $bot) && $bot["salta_membri_senza_status"] === "on";

    if(empty($bot["nomi_bannati"]))
        $nomi_bannati = [];
    else
        $nomi_bannati = explode(",",$bot["nomi_bannati"]);

    $proxy = $bot["proxy"];
    $metodo = $bot["metodo"];

    $evita_caratteri_non_latini = array_key_exists("evita_caratteri_non_latini", $bot) && $bot["evita_caratteri_non_latini"] === "on";
    $aggiungi_solo_membri_con_username = array_key_exists("aggiungi_solo_membri_con_username", $bot) && $bot["aggiungi_solo_membri_con_username"] === "on";
    $controlla_gruppi_in_comune = array_key_exists("controlla_gruppi_in_comune", $bot) && $bot["controlla_gruppi_in_comune"] === "on";
    $salta_controllo_membro_faceva_gia_parte_del_gruppo = array_key_exists("salta_controllo_membro_faceva_gia_parte_del_gruppo", $bot) && $bot["salta_controllo_membro_faceva_gia_parte_del_gruppo"] === "on";

    $riavvia = $bot["riavvia"];

    $max_riavvii = $bot["max_riavvii"];


    unset($bot);


    require "../dist/php/funzioni/ottieni_account.php";
    $numeri = ottieni_account($id);


    if(count($account_da_non_usare) > 0) {
        $numeri_nuovo = [];

        foreach($numeri as $i => $numero) {
            foreach($account_da_non_usare as $account)
                if($numero === $account)
                    continue 2;
            $numeri_nuovo[] = $numeri[$i];
        }

        $numeri = $numeri_nuovo;

    }

    unset($numeri_nuovo);
    unset($account_da_non_usare);


    require "../vendor/autoload.php";
    

    
    $logger = new \danog\MadelineProto\Settings\Logger;
    $logger->setLevel(\danog\MadelineProto\Logger::NOTICE);
    $logger->setType(\danog\MadelineProto\Logger::LOGGER_ECHO);
  
    shell_exec("ps aux | grep 'localhost:1808".$id."' | awk '{print $2}' | xargs kill -9"); //l'ho messo anche alla fine

    shell_exec("../proxy/opera-proxy.linux-amd64 -bind-address localhost:1808".$id." -country ".$proxy." >/dev/null 2>&1 &");

    $settings = (new danog\MadelineProto\Settings);


    $logger = new \danog\MadelineProto\Settings\Logger;
    $logger->setLevel(\danog\MadelineProto\Logger::NOTICE);
    $logger->setType(\danog\MadelineProto\Logger::LOGGER_ECHO);
    $settings->setLogger($logger);


    $connection = new \danog\MadelineProto\Settings\Connection;
    $connection->addProxy(
        \danog\MadelineProto\Stream\Proxy\HttpProxy::class, 
        [
            'address'  => 'localhost',
            'port'     =>  "1808".$id,
        ]
    );
    $settings->setConnection($connection);

    umask(002); //cambio i permessi dei file che crea www-data per farli accedere a tutto il gruppo e non solo il proprietario

    $account = [];
    for($i=0;$i<count($numeri);$i++) {
        $account[$i] = new \danog\MadelineProto\API("../dist/sessions/".$id.$numeri[$i].".madeline", $settings);
        $account[$i]->updateSettings($settings); //nel caso fossero nella cache le aggiorno
        // $account[$i]->async(true);

        $account[$i]->setNoop();

    }

    $successi = [];
    $errori = [];

    function shutdown() {
        global $account;
        global $id;

        // $account[0]->loop(static function () use ($account, $id) {
            global $successi;
            global $errori;
            global $sorgenti;
            global $destinazione;

            foreach($successi as $id_sorgente => $successo) {
                file_put_contents(__DIR__."/dati/successo/".$id." ".$sorgenti[$id_sorgente]." ".$destinazione.".json", json_encode($successo));
            }
            foreach($errori as $id_sorgente => $errore) {
                file_put_contents(__DIR__."/dati/errore/".$id." ".$sorgenti[$id_sorgente].".json", json_encode($errore));
            }

        // });
        
        // $account[0]->loop(static function () use($account, $id) {
            global $riavvia;
            global $max_riavvii;
            global $current_riavvio;

            //ricorda di rimuovere www-data dal file /etc/at.deny
            if($riavvia > 0 && $current_riavvio < $max_riavvii) {
                /*yield*/ $account[0]->logger("Il bot si riavvierà automaticamente tra ".$riavvia." minuti");
                shell_exec("echo \"(cd ../pages/; php ../bot/add.php ".$id." ". $current_riavvio+1 ." > '../bot/logs/".$id." ".date("d-m-Y H:i",strtotime("+".$riavvia." minutes Europe/Rome")).".txt' 2>&1 &)\" | at now +".$riavvia." minutes");
            }
            else
                /*yield*/ $account[0]->logger("Numero massimo di riavvi raggiunto");


        // });


        shell_exec("ps aux | grep 'localhost:1808".$id."' | awk '{print $2}' | xargs kill -9");
        
        //provo a killare tutto all'uscita //copiati da bot_stop.php
        shell_exec("ps aux | grep 'dist/sessions/".$id."+' | awk '{print $2}' | xargs kill -9"); //killo i worker
        shell_exec("ps aux | grep 'php ../bot/add.php ".$id."' | awk '{print $2}' | xargs kill -9"); //visto che si sta uccidendo da solo posso forzare aggiungendo -9
    }

    register_shutdown_function('shutdown');
    pcntl_signal(SIGTERM, "shutdown");


    // $account[0]->loop(static function () use ($id, $account, $numeri, $sorgenti, $destinazione, $attesa_min, $attesa_max, $massima_aggiunta_per_account, $data_online_minima, $data_online_massima, $status_consentiti, $salta_membri_senza_status, $nomi_bannati, $evita_caratteri_non_latini, $aggiungi_solo_membri_con_username, $proxy, $metodo, $controlla_gruppi_in_comune, $salta_controllo_membro_faceva_gia_parte_del_gruppo) {
        /*yield*/ $account[0]->logger("Sessione avviata con le seguenti impostazioni: ");
        /*yield*/ $account[0]->logger("Account: ");
        for($i=0;$i<count($account);$i++)
            /*yield*/ $account[0]->logger($numeri[$i]);
        /*yield*/ $account[0]->logger("Canali da cui prendere i membri: ");
        for($i=0;$i<count($sorgenti);$i++)
            /*yield*/ $account[0]->logger($sorgenti[$i]);
        /*yield*/ $account[0]->logger("Canale in cui aggiungere i membri: ".$destinazione);
        /*yield*/ $account[0]->logger("Attesa minima: ".$attesa_min." Attesa massima: ".$attesa_max);
        /*yield*/ $account[0]->logger("Numero massimo di membri da aggiungere con successo: ".$massima_aggiunta_per_account);
        /*yield*/ $account[0]->logger("Data online minima: ".$data_online_minima." Data online massima: ".$data_online_massima);
        /*yield*/ $account[0]->logger("Stati membro consentiti: ");
        for($i=0;$i<count($status_consentiti);$i++)
            /*yield*/ $account[0]->logger($status_consentiti[$i]);
            /*yield*/ $account[0]->logger("Salta membri senza status: ".($salta_membri_senza_status ? "Sì" : "No"));

        /*yield*/ $account[0]->logger("Nomi bannati: ");
        for($i=0;$i<count($nomi_bannati);$i++)
            /*yield*/ $account[0]->logger($nomi_bannati[$i]);
        /*yield*/ $account[0]->logger("Paese proxy: ". $proxy);
        /*yield*/ $account[0]->logger("Indirizzo ip in uso: ". shell_exec("curl --proxy http://localhost:1808".$id." ifconfig.me/ip"));

        /*yield*/ $account[0]->logger("Metodo: ". $metodo);
        /*yield*/ $account[0]->logger("Evita caratteri non latini: ".($evita_caratteri_non_latini ? "Sì" : "No"));
        /*yield*/ $account[0]->logger("Aggiungi solo membri con username: ". ($aggiungi_solo_membri_con_username ? "Sì" : "No"));
        /*yield*/ $account[0]->logger("Controlla gruppi in comune: ". ($controlla_gruppi_in_comune ? "Sì" : "No"));
        /*yield*/ $account[0]->logger("Salta controllo membro faceva già parte del gruppo: ". ($salta_controllo_membro_faceva_gia_parte_del_gruppo ? "Sì" : "No"));

    // });

    require $metodo.".php";

?>