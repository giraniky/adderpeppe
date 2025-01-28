<?php

    $id = (int) $argv[1];
    $current_riavvio = (int) $argv[2];

    if(empty($id))
        exit("Impossibile avviare il bot: parametro id mancante");

    require "../dist/php/database.php";

    try {
        $query=$pdo->prepare("Select `membri` from `utenti` where id =?");
        $query->execute([$id]);
    }
    catch(Exception $e) {
        exit("Impossibile reperire le impostazioni dal database: ".$e->getMessage());
    }

    unset($pdo);


    $bot = json_decode($query->fetch(PDO::FETCH_ASSOC)["membri"],true);
    if(empty($bot))
        exit("Non ci sono impostazioni nel database");

    unset($query);

    $sorgente = ltrim(strtolower(trim($bot["sorgente"])),"@");


    $account_da_non_usare = $bot["account_da_non_usare"] ?? [];

    $attesa_min = (int) $bot["attesa_min"];
    $attesa_max = (int) $bot["attesa_max"];

    $proxy = $bot["proxy"];
 
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
    
    $settings = new \danog\MadelineProto\Settings\Logger;
    $settings->setLevel(\danog\MadelineProto\Logger::NOTICE);
    $settings->setType(\danog\MadelineProto\Logger::LOGGER_ECHO);
  
    shell_exec("ps aux | grep 'localhost:1809".$id."' | awk '{print $2}' | xargs kill -9"); //l'ho messo anche alla fine

    shell_exec("../proxyopera-proxy.linux-amd64 -bind-address localhost:1809".$id." -country ".$proxy." >/dev/null 2>&1 &");



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
            'port'     =>  "1809".$id,
        ]
    );
    $settings->setConnection($connection);

    umask(002); //cambio i permessi dei file che crea www-data per farli accedere a tutto il gruppo e non solo il proprietario

    $account = [];
    for($i=0;$i<count($numeri);$i++) {
        $account[$i] = new \danog\MadelineProto\API("../dist/sessions/".$id.$numeri[$i].".madeline", $settings);
        // $account[$i]->async(true);
        $account[$i]->updateSettings($settings); //nel caso fossero nella cache le aggiorno

        $account[$i]->setNoop();
    }


    $membri_select="";
    $membri_insert=[];


    function membri_insert() {
        global $account;

        // $account[0]->loop(static function () use ($account) {
            global $membri_select;
            global $membri_insert;
            global $id;
            global $sorgente;

            if(count($membri_insert) > 0) {
               file_put_contents(__DIR__."/dati/".$id." ".$sorgente.".json", json_encode( array_merge(json_decode($membri_select, true) ?? [], $membri_insert) ));
               /*yield*/ $account[0]->logger("Membri salvati con successo nel database");

               $membri_select = "";
               $membri_insert = [];
            }


        // });
    }


    $q="";

    function shutdown() {
        global $account;
        global $id;

        // $account[0]->loop(static function () use($account, $id) {

            global $sorgente;
            global $q;
            global $riavvia;

            require "../dist/php/database.php";

            /*yield*/ $account[0]->logger("Elimino eventuali query già salvate");
            $query = $pdo->prepare("DELETE FROM query_salvate WHERE id = ? AND gruppo LIKE ?");
            $query->execute([$id, $sorgente]);

            if($q !== "1") {
                /*yield*/ $account[0]->logger("Salvo la query per poter riprendere in seguito");

                $query = $pdo->prepare("Insert into query_salvate VALUES(?,?,?)");
                $query->execute([$id, $sorgente, $q]);


                if($riavvia > 0 && $current_riavvio < $max_riavvii) {
                    /*yield*/ $account[0]->logger("Il bot si riavvierà automaticamente tra ".$riavvia." minuti");
                    shell_exec("echo \"(cd ../pages/; php ../bot/membri.php ".$id." ". $current_riavvio+1 ." > '../bot/membri/".$id." ".date("d-m-Y H:i",strtotime("+".$riavvia." minutes Europe/Rome")).".txt' 2>&1 &)\" | at now +".$riavvia." minutes");
                }
                else
                    /*yield*/ $account[0]->logger("Numero massimo di riavvi raggiunto");
            }
 
            unset($pdo);
        // });

        membri_insert();

        shell_exec("ps aux | grep 'localhost:1809".$id."' | awk '{print $2}' | xargs kill -9");

        //provo a killare tutto all'uscita //copiati da membri_stop.php
        shell_exec("ps aux | grep 'dist/sessions/".$id."+' | awk '{print $2}' | xargs kill -9"); //killo i worker
        shell_exec("ps aux | grep 'php ../bot/membri.php ".$id."' | awk '{print $2}' | xargs kill -9"); //visto che si sta uccidendo da solo posso forzare aggiungendo -9
    }

    register_shutdown_function('shutdown');
    pcntl_signal(SIGTERM, "shutdown");


    // $account[0]->loop(static function () use ($id, $numeri, $sorgente, $attesa_max, $attesa_min, $proxy) {
        global $q;
        global $account;
        global $membri_select;
        global $membri_insert;

        /*yield*/ $account[0]->logger("Sessione avviata con le seguenti impostazioni: ");
        /*yield*/ $account[0]->logger("Account: ");
        for($i=0;$i<count($account);$i++)
            /*yield*/ $account[0]->logger($numeri[$i]);
        /*yield*/ $account[0]->logger("Gruppo da cui prendere i membri: ".$sorgente);
      
        /*yield*/ $account[0]->logger("Attesa minima: ".$attesa_min." Attesa massima: ".$attesa_max);

        /*yield*/ $account[0]->logger("Paese proxy: ". $proxy);
        /*yield*/ $account[0]->logger("Indirizzo ip in uso: ". shell_exec("curl --proxy http://localhost:1809".$id." ifconfig.me/ip"));

        
        require "../dist/php/database.php";
        //Selezione un'eventuale $q già salvata
        $query = $pdo->prepare("SELECT q FROM query_salvate WHERE id = ? AND gruppo LIKE ?");
        $query->execute([$id, $sorgente]);
        unset($pdo);
        if($query->rowCount() > 0) {
            $q = $query->fetch(PDO::FETCH_ASSOC)["q"];
            /*yield*/ $account[0]->logger("Query già salvata trovata: ".$q);

        }
        else
            $q="a";

        $i=0;

        do {
        
            try{
                $membri = /*yield*/ $account[$i]->channels->getParticipants([
                    'channel' => $sorgente, 
                    'filter' => [
                        '_' => 'channelParticipantsSearch',
                        'q' => $q
                    ], 
                    'limit' => 200
                ]);
                /*yield*/ $account[0]->logger($numeri[$i].": Lista di ".$sorgente." con la query ".$q." ottenuta con successo");

            }
            catch(Exception $e) {
                /*yield*/ $account[0]->logger($numeri[$i].": Lista di ".$sorgente." con la query ".$q." non ottenuta per il seguente errore ".$e->getMessage().". Rimuovo l'account");

                if(count($account) === 1) 
                    break;
                else {
                    array_splice($account,$i,1);
                    array_splice($numeri,$i,1);
                }

                continue;
            }
            finally {
                $attesa = rand($attesa_min,$attesa_max);
                /*yield*/ $account[0]->logger($numeri[$i].":Attendo ".$attesa." secondi");
                /*yield*/ $account[0]->sleep($attesa);
            }


            try {
                $membri_select = file_get_contents(__DIR__."/dati/".$id." ".$sorgente.".json");
            }
            catch(Exception $e) {
                if(strpos($e->getMessage(), "Failed to open stream: No such file or directory") !== false)
                    $membri_select = "";
                else
                    throw $e;
            }

            foreach(/*yield*/ $membri["participants"] as $participant) {
                try {
                    $peer = $participant['user_id'];

                    //controlla se il membro è gia nel database così da evitare un getPwrChat inutile
                    if(strpos($membri_select, '"user_id":'.$peer.',') !== false) {
                        /*yield*/ $account[0]->logger($numeri[$i].": ".$peer." era già nel database, lo salto");
                        continue;
                    }


                    $membro = /*yield*/ $account[$i]->getPwrChat($peer, false, true);
                    /*yield*/ $account[0]->logger($numeri[$i].": ".$peer." informazioni ottenute con successo");
                   
                }
                catch(Exception $e) {
                    /*yield*/ $account[0]->logger($numeri[$i].": ".$peer." errore ".$e->getMessage().". Rimuovo l'account");

                    if(count($account) === 1) 
                        break;
                    else {
                        array_splice($account,$i,1);
                        array_splice($numeri,$i,1);
                    }

                    $attesa = rand($attesa_min,$attesa_max);
                    /*yield*/ $account[0]->logger($numeri[$i].": Attendo ".$attesa." secondi");
                    /*yield*/ $account[0]->sleep($attesa);

                    continue;
                }

                //Salva il membro nel database
   
                switch ($participant['_']) {
                    case 'channelParticipantSelf':
                        $role = 'user';
                        break;
                    case 'channelParticipant':
                        $role = 'user';
                        break;
                    case 'channelParticipantCreator':
                        $role = 'creator';
                        break;
                    case 'channelParticipantAdmin':
                        $role = 'admin';
                        break;
                    case 'channelParticipantBanned':
                        $role = 'banned';
                        break;
                }

                
                array_push($membri_insert, [
                    //"id" => $id, 
                    //"gruppo" => $sorgente,
                    "account" => $numeri[$i],
                    "user_id" => $peer, 
                    "type" => /*yield*/ $membro["type"], 
                    "role" => $role, 
                    "first_name" => /*yield*/ $membro["first_name"],
                    "last_name" => /*yield*/ $membro["last_name"] ?? null,
                    "username" => /*yield*/ $membro["username"] ?? null,
                    "status" => /*yield*/ $membro["status"]["_"] ?? null,
                    "was_online" => /*yield*/ $membro["status"]["was_online"] ?? null
                ]);
                /*yield*/ $account[0]->logger($numeri[$i].": ".$peer." inserito nella lista dei membri da salvare");

                unset($peer);
                unset($role);
                unset($membro);

                $attesa = rand($attesa_min,$attesa_max);
                /*yield*/ $account[0]->logger($numeri[$i].": Attendo ".$attesa." secondi");
                /*yield*/ $account[0]->sleep($attesa);

            }

            membri_insert();

            if(count(/*yield*/ $membri["participants"]) === 200)
                $q = $q."a";
            elseif($q[strlen($q)-1] === "z") {
                $q =  substr($q, 0, -1);
                $q++;
            }
            else
                $q++;
            
            if($i === count($account)-1)
                $i=0;
            else
                $i++;

        }
        while($q !== "1");

    // });

?>