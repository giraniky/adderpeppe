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
   
    $account_lista = 0;
    $account_aggiunta = 0;
   
    foreach($sorgenti as $id_sorgente => $sorgente) {
        /*yield*/ $account[0]->logger("Inizio aggiunta membri di ".$sorgente);

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

    
        $letters = [];
        for($i="aa"; $i !== "aaa"; $i++)
            $letters[] = $i;

        shuffle($letters);

        foreach($letters as $x) {
            foreach($nomi_bannati as $nome)
                if(stripos($x,$nome) !== false)
                    continue 2;
   
            /*yield*/ $account[0]->logger($numeri[$account_lista].": "."Prendo la lista dei membri di ".$sorgente." con la lettera ".$x);
            try {
                $membri = /*yield*/ $account[$account_lista]->channels->getParticipants(['channel' => $sorgente, 'filter' => ['_' => 'channelParticipantsSearch', 'q' => $x], 'offset' => 0, 'limit' => 200]);
            }
            catch(Exception $e) {
                /*yield*/ $account[0]->logger($numeri[$account_lista].": "."Lista dei membri di ".$sorgente." con la lettera ".$x." non ottenuta per il seguente errore: ".$e->getMessage());
                break;
            }
   
            /*yield*/ $account[0]->logger($numeri[$account_lista].": "."Lista dei membri di ".$sorgente." con la lettera ".$x." ottenuta con successo");
            
            if(/*yield*/ $membri["count"] < 1)
                continue;
            
   
            foreach(/*yield*/ $membri["participants"] as $participant) {
                switch ($participant['_']) {
                    case 'channelParticipantSelf':
                        $membro['role'] = 'user';
                        break;
                    case 'channelParticipant':
                        $membro['role'] = 'user';
                        break;
                    case 'channelParticipantCreator':
                        $membro['role'] = 'creator';
                        break;
                    case 'channelParticipantAdmin':
                        $membro['role'] = 'admin';
                        break;
                    case 'channelParticipantBanned':
                        $membro['role'] = 'banned';
                        break;
                }
                $peer = $participant['user_id'] ?? $participant['peer'];
                try {
                    $membro['user'] = (/*yield*/ $account[$account_lista]->getPwrChat($peer, false, true));
                }
                catch(Exception $e) {
   
                    if(strpos($e->getMessage(),"FLOOD_WAIT_") !== false /*|| $e->getMessage() === "PEER_FLOOD"*/ || $e->getMessage() === "CHAT_WRITE_FORBIDDEN" || $e->getMessage() === "USER_DEACTIVATED_BAN") {
                        /*yield*/ $account[0]->logger($numeri[$account_lista].": account bloccato per flood, messaggio di errore da parte di telegram: ".$e->getMessage().", lo rimuovo da questa sessione");
                        if(count($account) === 1) {
                            /*yield*/ $account[0]->logger("Ho terminato gli account a disposizione. Sessione terminata.");
                            break 2;
                        }
                        else {
                            /*yield*/ $account[0]->logger($numeri[$account_lista].": questo account bloccato per flood era proprio quello che aveva preso la lista, ho bisogno di prendere un'altra lista di ".$sorgente);
                            array_splice($account, $account_lista, 1);
                            array_splice($numeri, $account_lista, 1);
                            array_splice($membri_aggiunti, $account_lista, 1);
   
                            if($account_lista > 0)
                                $account_lista--;
                            
                            break;
                        }
                    }
                    else
                        /*yield*/ $account[0]->logger($numeri[$account_lista].": impossibile ottenere i dati dell'utente ".$peer.": ".$e->getMessage());
   
                    continue;
                }
   
                if(array_key_exists("username", $membro["user"]))
                    $peer = "@".strtolower($membro["user"]["username"]);
                else {
                    if($aggiungi_solo_membri_con_username) {                        
                        /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": "."Siccome l'utente ".$peer." non ha username ed è attiva l'impostazione di aggiungere solo account con username lo salto");
                        
                        if($account_aggiunta >= count($account)-1)
                            $account_aggiunta = 0;
                        else
                            $account_aggiunta++;

                        continue;
                    }
   
   
                    $peer = $membro["user"]["id"];
   
                    $account_aggiunta_backup = $account_aggiunta;
                    $account_aggiunta = $account_lista;
   
                    
                    /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": "."Siccome l'utente ".$peer." non ha username provo ad aggiungerlo usando l'account che ha preso l'elenco dei membri del gruppo ".$sorgente);
   
                }

                
                if(in_array($peer,$errori[$id_sorgente])) {
                    /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il membro in passato risultava aver dato un errore, lo salto");
                    
                    if($account_aggiunta >= count($account)-1)
                        $account_aggiunta = 0;
                    else
                        $account_aggiunta++;
                    
                    continue;
                }
                /*if(in_array($peer,$successi[$id_sorgente])) {
                    /*yield/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il membro risulta già aggiunto con successo in passato, lo salto");

                    if($account_aggiunta >= count($account)-1)
                        $account_aggiunta = 0;
                    else
                        $account_aggiunta++;
    
                    continue;
                }*/
                

                if($membro["user"]["type"] === "bot") {
                    /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il membro è un bot");

                    if($account_aggiunta >= count($account)-1)
                        $account_aggiunta = 0;
                    else
                        $account_aggiunta++;

                    continue;
                }
                if($membro["role"] !== "user") {
                    /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il membro non è un utente");

                    if($account_aggiunta >= count($account)-1)
                        $account_aggiunta = 0;
                    else
                        $account_aggiunta++;

                    continue;
                } 
                if(array_key_exists("status", $membro["user"])) {
   
                    if(array_key_exists("was_online", $membro["user"]["status"])) {
   
                        if($membro["user"]["status"]["was_online"] <= $data_online_minima) {
                            /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: L'ultimo accesso del membro è minore della data minima");
                            if($account_aggiunta >= count($account)-1)
                                $account_aggiunta = 0;
                            else
                                $account_aggiunta++;
                            
                            continue;
                        }
                        if($membro["user"]["status"]["was_online"] >=  $data_online_massima) {
                            /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: L'ultimo accesso del membro è maggiore della data massima");
                            if($account_aggiunta >= count($account)-1)
                                $account_aggiunta = 0;
                            else
                                $account_aggiunta++;
                           
                            continue;
                        }
                    }
   
                    if(!in_array($membro["user"]["status"]["_"],$status_consentiti)) {
                        /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Lo stato del membro non è tra quelli consentiti");
                        
                        if($account_aggiunta >= count($account)-1)
                            $account_aggiunta = 0;
                        else
                            $account_aggiunta++;
                        
                        continue;
                    }
   
                }
                else {
                    if($salta_membri_senza_status) {
                        /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il membro non aveva uno stato");

                        if($account_aggiunta >= count($account)-1)
                            $account_aggiunta = 0;
                        else
                            $account_aggiunta++;

                        continue;
                    }
                }
                if( (function($membro, $nomi_bannati) {
                        foreach($nomi_bannati as $nome)
                            if(
                                (array_key_exists("first_name",$membro["user"]) && stripos($membro["user"]["first_name"],$nome) !== false) ||
                                (array_key_exists("last_name",$membro["user"]) && stripos($membro["user"]["last_name"],$nome) !== false) ||
                                (array_key_exists("username",$membro["user"]) && stripos($membro["user"]["username"],$nome) !== false)
                            )
                                return true;          
   
                        return false;
                    })($membro, $nomi_bannati)
                ) {
                    /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il nome, il cognome o l'username del membro contengono uno dei nomi bannati");
                    
                    if($account_aggiunta >= count($account)-1)
                        $account_aggiunta = 0;
                    else
                        $account_aggiunta++;
                    
                    continue;
                }
                if($evita_caratteri_non_latini && (function($membro) {
                    return (
                        ( array_key_exists("first_name", $membro["user"]) && preg_match('/(?=\pL)(?![a-zA-Z])/', $membro["user"]["first_name"]) ) ||
                        ( array_key_exists("last_name", $membro["user"]) && preg_match('/(?=\pL)(?![a-zA-Z])/', $membro["user"]["last_name"]) )
                    );
                        
                })($membro)
                ) {
                    /*yield*/ $account[0]->logger($numeri[$account_aggiunta].": ".$peer." errore: Il nome o il cognome  del membro contengono caratteri non latini");
                    
                    if($account_aggiunta >= count($account)-1)
                        $account_aggiunta = 0;
                    else
                        $account_aggiunta++;
                    
                    continue;
                }
   
   
                try {
                    $update = /*yield*/ $account[$account_aggiunta]->channels->inviteToChannel(channel: $destinazione, users: [ $peer ] );
   
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
   
                            if($account_aggiunta>0 ? $account_aggiunta+1 : $account_aggiunta <= $account_lista) 
                                if($account_lista > 0)
                                    $account_lista--;
   
                            if($account_aggiunta>0 ? $account_aggiunta+1 : $account_aggiunta === $account_lista) {
                                /*yield*/ $account[0]->logger("L'account appena rimosso era proprio l'account che aveva preso la lista, devo prendere una nuova lista di ".$sorgente);
                                break;
                            }
   
   
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
   
                        if($account_aggiunta>0 ? $account_aggiunta+1 : $account_aggiunta <= $account_lista)
                            if($account_lista > 0)
                                $account_lista--;
   
                        if($account_aggiunta>0 ? $account_aggiunta+1 : $account_aggiunta === $account_lista) {
                            /*yield*/ $account[0]->logger("L'account appena rimosso era proprio l'account che aveva preso la lista, devo prendere una nuova lista di ".$sorgente);
                            break;
                        }
   
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
   
            if($account_lista >= count($account)-1)
                $account_lista=0;
            else 
                $account_lista++;
   
        }
   
        /*yield*/ $account[0]->logger("Fine aggiunta membri di ".$sorgente);
    }

// });