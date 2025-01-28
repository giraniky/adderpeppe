<?php
// $account[0]->loop(static function () use ($id, $account, $numeri, $sorgenti, $destinazione, $attesa_min, $attesa_max, $massima_aggiunta_per_account, $data_online_minima, $data_online_massima, $status_consentiti, $salta_membri_senza_status, $nomi_bannati, $evita_caratteri_non_latini, $aggiungi_solo_membri_con_username, $controlla_gruppi_in_comune, $proxy, $metodo, $salta_controllo_membro_faceva_gia_parte_del_gruppo) {
    // global $successi;
    // global $errori;

    $membri_aggiunti = [];
    for($i=0;$i<count($account);$i++) {
        $membri_aggiunti[$i] = 0;
        $account[$i]->start();
        try {
            /*yield*/ $account[0]->logger($numeri[$i].": Mi assicuro che l'account sia nel gruppo in cui aggiungere i membri (".$destinazione.")");
            /*yield*/ $account[$i]->channels->joinChannel(channel: $destinazione );
        }
        catch(Exception $e) {
            /*yield*/ $account[0]->logger($numeri[$i].": Impossibile unirsi al gruppo in cui aggiungere i membri (".$destinazione.")");
        }
    }
   

    $account_aggiunta = 0;
    
    foreach($sorgenti as $id_sorgente => $sorgente) {
        /*yield*/ $account[0]->logger("Inizio aggiunta membri di ".$sorgente);

        try {
            $rows = json_decode(file_get_contents(__DIR__."/dati/".$id." ".$sorgente.".json"),true);
        }
        catch(Exception $e) {
            /*yield*/ $account[0]->logger("Nessun membro trovato nel database per il gruppo ".$sorgente);
            continue;
        }

        try {
            $successi[$id_sorgente] = json_decode(file_get_contents(__DIR__."/dati/successo/".$id." ".$sorgente." ".$destinazione.".json"),true);
        }
        catch(Exception $e) {
            $successi[$id_sorgente] = [];
        }
        
        try {
            $errori[$id_sorgente] = json_decode(file_get_contents(__DIR__."/dati/errore/".$id." ".$sorgente.".json"),true);
        }
        catch(Exception $e) {
            $errori[$id_sorgente] = [];
        }

        foreach($rows as $row) {

            if(!is_null($row["username"]))
                $peer = "@".strtolower($row["username"]);
            else {
                if($aggiungi_solo_membri_con_username) {                        
                    /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": "."Siccome l'utente ".$peer." non ha username ed è attiva l'impostazione di aggiungere solo account con username lo salto");
                    continue;
                }


                $peer = $row["user_id"];

                $account_aggiunta_backup = $account_aggiunta;
                $account_aggiunta = array_search($row["account"],$numeri);
                if($account_aggiunta === false) {
                    /*yield*/ $account[0]->logger("Siccome l'utente non ha username volevo provare ad aggiungere usando l'account ".$row["account"]." che ha preso i dettagli di tale utente, ma non è attivo l'utente");

                    continue;
                }
                
                /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": "."Siccome l'utente ".$peer." non ha username provo ad aggiungerlo usando l'account che ha preso i dettagli di tale utente");

            }


            if(in_array($peer,$errori[$id_sorgente])) {
                /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il membro in passato risultava aver dato un errore, lo salto");
                continue;
            }
            /*if(in_array($peer,$successi[$id_sorgente])) {
                /*yield/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il membro risulta già aggiunto con successo in passato, lo salto");
                continue;
            }*/
            

            if($row["type"] === "bot") {
                /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il membro è un bot");
                continue;
            }
            if($row["role"] !== "user") {
                /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il membro non è un utente");
                continue;
            } 
            if(!is_null($row["status"])) {

                if(!is_null($row["was_online"])) {

                    if($row["was_online"] < $data_online_minima) {
                        /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: L'ultimo accesso del membro è minore della data minima");
                        continue;
                    }
                    if($row["was_online"] >  $data_online_massima) {
                        /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: L'ultimo accesso del membro è maggiore della data massima");
                        continue;
                    }
                }

                if(!in_array($row["status"],$status_consentiti)) {
                    /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Lo stato del membro non è tra quelli consentiti");
                    continue;
                }

            }
            else {
                if($salta_membri_senza_status) {
                    /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il membro non aveva uno stato");
                    continue;
                }
            }
            if( (function($row, $nomi_bannati) {
                    foreach($nomi_bannati as $nome)
                        if(
                            (!is_null($row["first_name"]) && stripos($row["first_name"],$nome) !== false) ||
                            (!is_null($row["last_name"]) && stripos($row["last_name"],$nome) !== false) ||
                            (!is_null($row["username"]) && stripos($row["username"],$nome) !== false)
                        )
                            return true;          

                    return false;
                })($row, $nomi_bannati)
            ) {
                /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il nome, il cognome o l'username del membro contengono uno dei nomi bannati");
                continue;
            }
            if($evita_caratteri_non_latini && (function($row) {
                return (
                    ( !is_null($row["first_name"]) && preg_match('/(?=\pL)(?![a-zA-Z])/', $row["first_name"]) ) ||
                    ( !is_null($row["last_name"]) && preg_match('/(?=\pL)(?![a-zA-Z])/', $row["last_name"]) )
                );
                    
            })($row)
            ) {
                /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il nome o il cognome  del membro contengono caratteri non latini");
                continue;
            }


            try {
                $update = /*yield*/ $account[$account_aggiunta]->channels->inviteToChannel(channel: $destinazione, users: [ $peer ] );
                //file_put_contents(__DIR__."/esempi/".$peer.".json",json_encode(/*yield/ $update));
                
                if(count(/*yield*/ $update["updates"]) > 0 || $salta_controllo_membro_faceva_gia_parte_del_gruppo) {
                    
                    if($controlla_gruppi_in_comune) {
                        /*yield*/ $account[$account_aggiunta]->sleep(2);
                        
                        $gruppiincomune = /*yield*/ $account[$account_aggiunta]->messages->getCommonChats(user_id: $peer, max_id: 1000);
                        ///*yield*/ $account[0]->logger(json_encode(/*yield*/ $gruppiincomune));

                        if(count(/*yield*/ $gruppiincomune["chats"]) > 0) {
                            foreach(/*yield*/ $gruppiincomune["chats"] as $gruppoincomune) {
                                if(array_key_exists("username",$gruppoincomune) && $destinazione === strtolower($gruppoincomune["username"]))
                                    $successo = true;
                                elseif(/*yield*/ 
                                    danog\DialogId\DialogId::toSupergroupOrChannelId($gruppoincomune["id"]) === $destinazione)
                                    $successo = true;
                                else
                                    $successo = false;
                            }
                        }
                        else 
                            $successo = false;

                        if(!$successo)
                            throw new Exception($peer." risulta aggiunto con successo, ma controllando i gruppi in comune non risulta il gruppo ".$destinazione.". Potrebbe essere che l'account è stato limitato da telegram");
                    }

                    /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." successo");
                    $membri_aggiunti[$account_aggiunta]++;
                    $salta_controllo_massima_aggiunta_per_account = false;
                    array_push($successi[$id_sorgente],$peer);

                }
                else {
                    /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il membro fa già parte del gruppo");
                    $salta_controllo_massima_aggiunta_per_account = true;
                    array_push($errori[$id_sorgente],$peer);
                }
            }
            catch(Exception $e) {
                /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore:".$e->getMessage());
                $salta_controllo_massima_aggiunta_per_account = true;

                if(strpos($e->getMessage(),"FLOOD_WAIT_") !== false /*|| $e->getMessage() === "PEER_FLOOD"*/ || $e->getMessage() === "CHAT_WRITE_FORBIDDEN" || $e->getMessage() === "USER_DEACTIVATED_BAN" || strpos($e->getMessage(),"gruppi in comune") !== false) {
                    /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": account bloccato per flood, errore: ".$e->getMessage().", lo rimuovo da questa sessione");
                    if(count($account) === 1) {
                        /*yield*/ $account[0]->logger("Ho terminato gli account a disposizione. Sessione terminata.");
                        break 2;
                    }
                    else {

                        array_splice($account, $account_aggiunta, 1);
                        array_splice($numeri, $account_aggiunta, 1);
                        array_splice($membri_aggiunti, $account_aggiunta, 1);

                        if($account_aggiunta > 0)
                            $account_aggiunta--;

                    }
                }
                else
                    array_push($errori[$id_sorgente],$peer);

            }
            
            $attesa = rand($attesa_min,$attesa_max);
            /*yield*/ $account[0]->logger("Attendo ".$attesa." secondi");
            /*yield*/ $account[0]->sleep($attesa);



            if(!$salta_controllo_massima_aggiunta_per_account && $membri_aggiunti[$account_aggiunta] === $massima_aggiunta_per_account) {
                /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ha aggiunto con successo ".$massima_aggiunta_per_account." membri. Lo rimuovo da questa sessione.");
                if(count($account) === 1) {
                    /*yield*/ $account[0]->logger("Ho terminato gli account a disposizione. Sessione terminata.");
                    break 2;
                }
                else {
                    array_splice($account, $account_aggiunta, 1);
                    array_splice($numeri, $account_aggiunta, 1);
                    array_splice($membri_aggiunti, $account_aggiunta, 1);

                    if($account_aggiunta > 0)
                        $account_aggiunta--;

                }
            }

            if(isset($account_aggiunta_backup)) {
                $account_aggiunta = $account_aggiunta_backup;
                unset($account_aggiunta_backup);
            }
            if($account_aggiunta >= count($account)-1)
                $account_aggiunta = 0;
            else
                $account_aggiunta++;
   

   
        }
   
        /*yield*/ $account[0]->logger("Fine aggiunta membri di ".$sorgente);
    }

// });