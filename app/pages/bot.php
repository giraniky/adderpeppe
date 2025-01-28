<?php
    require "../dist/php/database.php";
    require "../dist/php/template/header.php";

    if(count($_POST) > 0) {
        try {
            $query = $pdo->prepare("UPDATE utenti set bot = ? where id = ?");
            $query->execute([json_encode($_POST),$_SESSION["id"]]);
        }
        catch(Exception $e) {
            $errore = $e->getMessage();
        }
        
        
        exec('for i in $(atq | cut -f 1); do at -c "$i" | grep "add.php '.$_SESSION["id"].'"; done', $output);
        if(count($output) > 0)
            $errore = "C'è almeno un riavvio programmato del bot. Aspetta che si esegua oppure <a href='../dist/php/at_remove.php?redirect=bot.php'>premi qui per annullarlo</a>. Potrai vederne lo stato nella pagina Logs.";
        else {
            
            exec("ps aux | grep 'php ../bot/add.php ".$_SESSION["id"]."'", $output);
            //var_dump($output);        

            if( (function($output) {
                    foreach($output as $line) {
                        if(strpos($line,"grep") === false)
                            return false;
                        return true;
                    }
                }) ($output)
            ) {
                $logs = date("d-m-Y H:i",time()).".txt";
                shell_exec("php ../bot/add.php ".$_SESSION["id"]." > '../bot/logs/".$_SESSION["id"]." ".$logs."' 2>&1 &");

                ?>
                <script>
                    window.location.href = "logs.php?file=<?php echo urlencode($logs);?>";
                </script>
                <?php
            }
            else
                $errore = "Hai già avviato il bot. Aspetta che finisca oppure <a href='../dist/php/bot_stop.php?redirect=bot.php'>premi qui per terminarlo</a>. Puoi vedere lo stato nella pagina Logs.";
        }
    }

    try {
        $query = $pdo->prepare("SELECT bot from utenti where id = ?");
        $query->execute([$_SESSION["id"]]);
    }
    catch(Exception $e) {
        $errore = "Impossibile selezionare i valore che hai compilato l'ultima volta: ".$e->getMessage();
    }

    if(isset($errore)) { ?>
        <div class="callout callout-danger">
            <h4>Errore!</h4>
            <p><?php echo $errore; ?></p>
        </div>
    <?php }
?>

<form method="POST">
    <div class="form-group">
        <label for="destinazione">Gruppo in cui aggiungere i membri (chat id oppure username)</label>
        <input type="text" class="form-control" name="destinazione" required>
    </div>
    <div class="form-group">
        <label for="sorgenti">Lista di gruppi da cui prendere i membri (chat id oppure username) (separa con la virgola)</label>
        <input type="text" class="form-control" name="sorgenti" required></input>
    </div>
    <div class="form-group">
        <label for="massima_aggiunta_per_account">Massimo numero di membri che ogni account deve aggiungere nel gruppo con successo</label>
        <input type="number" min="1" step="1" class="form-control" name="massima_aggiunta_per_account" required>
    </div>
    <div class="form-group">
        <label for="account_da_non_usare[]">Account da non usare</label>
        <select multiple="" class="form-control" name="account_da_non_usare[]">
            <?php
            require "../dist/php/funzioni/ottieni_account.php";
            foreach(ottieni_account($_SESSION["id"]) as $account) {?>
            <option value="<?php echo $account ?>"><?php echo $account ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group">
        <label for="attesa_min">Minimo numero di secondi da aspettare tra una richiesta ed un'altra</label>
        <input type="number" min="1" step="1" class="form-control" name="attesa_min" required>
    </div>
    <div class="form-group">
        <label for="attesa_max">Massimo numero di secondi da aspettare tra una richiesta ed un'altra</label>
        <input type="number" min="1" step="1" class="form-control" name="attesa_max" required>
    </div>
    <div class="form-group">
        <label for="data_online_minima">Tempo minimo in cui il membro deve essere stato online (se ultimo accesso è visibile) </label>
        <select class="custom-select" name="data_online_minima" required>
            <option value="1 week ago">Una settimana fa</option>
            <option value="1 month ago">Un mese fa</option>
            <option value="1 year ago">Un anno fa</option>
            <option value="">Sempre</option>
        </select>
    </div>
    <div class="form-group">
        <label for="data_online_massima">Tempo massimo in cui il membro deve essere stato online (se ultimo accesso è visibile) </label>
        <select class="form-control" name="data_online_massima" required>
            <option value="30 minutes ago">30 minuti fa</option>
            <option value="1 hour ago">Un'ora fa</option>
            <option value="1 day ago">Un giorno fa</option>
            <option value="+ 50 years">Nessuno</option>
        </select>
    </div>
    <div class="form-group">
        <label for="status_consentiti[]">Stati del membro consentiti ("Offline" dovrebbe essere sempre selezionato)</label>
        <select multiple="" class="form-control" name="status_consentiti[]" required>
            <option value="userStatusOffline">Offline</option>
            <option value="userStatusOnline">Online</option>
            <option value="userStatusRecently">Ultimo accesso di recente</option>
            <option value="userStatusLastWeek">Ultimo accesso entro una settimana</option>
            <option value="userStatusLastMonth">Ultimo accesso entro un mese</option>
            <!-- <option value="userStatusEmpty">Stato non ancora impostato (non a cosa si riferisca?)</option> -->
        </select>
    </div>
    <div class="form-check">
        <input type="checkbox" class="form-check-input" name="salta_membri_senza_status">
        <label class="form-check-label" for="salta_membri_senza_status">Salta membri che non hanno uno stato (Ultimo accesso molto tempo fa)</label>
    </div>
    <div class="form-group">
        <label for="nomi_bannati">Lista dei nomi bannati (separa con la virgola) (Maiuscole o minuscole è indifferente, verranno ignorate)</label>
        <input type="text" class="form-control" name="nomi_bannati"></input>
    </div>
    <div class="form-group">
        <label for="proxy">Paese del proxy: </label>
        <select class="form-control" name="proxy" required>
            <?php 
                exec("../proxy/opera-proxy.linux-amd64 -list-countries", $output);
                unset($output[0]);

                foreach($output as $country) {
                    $country = explode(",", $country);
            ?>
                    <option value="<?php echo $country[0];?>"><?php echo $country[1];?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group">
        <label for="metodo">Metodo</label>
        <select class="form-control" name="metodo" required>
            <option value="lettera">Lettera</option>
            <option value="database">Database</option>
        </select>
    </div>
    <div class="form-check">
        <input type="checkbox" class="form-check-input" name="evita_caratteri_non_latini">
        <label class="form-check-label" for="evita_caratteri_non_latini">Evita membri che hanno caratteri non latini nel nome</label>
    </div>
    <div class="form-check">
        <input type="checkbox" class="form-check-input" name="aggiungi_solo_membri_con_username">
        <label class="form-check-label" for="aggiungi_solo_membri_con_username">Aggiungi solo i membri che hanno un username</label>
    </div>
    <div class="form-check">
        <input type="checkbox" class="form-check-input" name="controlla_gruppi_in_comune">
        <label class="form-check-label" for="controlla_gruppi_in_comune">Dopo aver aggiunto un membro controlla i gruppi in comune per vedere se è stato effettivamente aggiunto</label>
    </div>
    <div class="form-check">
        <input type="checkbox" class="form-check-input" name="salta_controllo_membro_faceva_gia_parte_del_gruppo">
        <label class="form-check-label" for="salta_controllo_membro_faceva_gia_parte_del_gruppo">Salta controllo membro faceva già parte del gruppo (Da attivare se il gruppo in cui si vogliono aggiungere i membri ha più di 10 mila utenti perché in tal caso telegram non manda il messaggio "Utente si è unito al gruppo")</label>
    </div>
    <div class="form-group">
        <label for="riavvia">Tempo dopo il quale riavviare automaticamente di nuovo il bot (in minuti) (lascia vuoto o metti 0 se non vuoi che il bot si riavvii automaticamente)</label>
        <input type="number" min="0" step="1" class="form-control" name="riavvia"></input>
    </div>
    <div class="form-group">
        <label for="riavvia">Numero massimo di riavvii (lascia vuoto o metti 0 se non vuoi che il bot si riavvii automaticamente)</label>
        <input type="number" min="0" step="1" class="form-control" name="max_riavvii"></input>
    </div>
    <br><button type="submit" class="btn btn-primary">Avvia</button>
</form>
<script>
    let json = '<?php echo $query->fetch(PDO::FETCH_ASSOC)["bot"]; ?>';
    if(json) {
        let obj = JSON.parse(json);
        for(let key in obj)
            if(document.querySelector("[name='" + key+ "[]']"))
                for(let i=0; i < obj[key].length; i++)
                    document.querySelector("option[value='"+obj[key][i]+"']").selected=true;
            else if(document.querySelector("[name=" + key+ "]").type === "checkbox")
                document.querySelector("[name=" + key+ "]").checked = obj[key];
            else
                document.querySelector("[name=" + key+ "]").value = obj[key];
    }
</script>
<?php
    require "../dist/php/template/footer.php";
?>