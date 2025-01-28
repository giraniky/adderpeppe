<?php
    require "../dist/php/database.php";
    require "../dist/php/template/header.php";

    if(count($_POST) > 0) {
        try {
            $query = $pdo->prepare("UPDATE utenti set membri = ? where id = ?");
            $query->execute([json_encode($_POST),$_SESSION["id"]]);
        }
        catch(Exception $e) {
            $errore = $e->getMessage();
        }
        
        
        exec('for i in $(atq | cut -f 1); do at -c "$i" | grep "membri.php '.$_SESSION["id"].'"; done', $output);
        if(count($output) > 0)
            $errore = "C'è almeno un riavvio programmato del bot. Aspetta che si esegua oppure <a href='../dist/php/membri_at_remove.php?redirect=membri.php'>premi qui per annullarlo</a>. Potrai vederne lo stato nella pagina Logs.";
        else {
            
            exec("ps aux | grep 'php ../bot/membri.php ".$_SESSION["id"]."'", $output);
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
                shell_exec("php ../bot/membri.php ".$_SESSION["id"]." > '../bot/membri/".$_SESSION["id"]." ".$logs."' 2>&1 &");

                ?>
                <script>
                    window.location.href = "membri_logs.php?file=<?php echo urlencode($logs);?>";
                </script>
                <?php
            }
            else
                $errore = "Hai già avviato il bot. Aspetta che finisca oppure <a href='../dist/php/membri_stop.php?redirect=membri.php'>premi qui per terminarlo</a>. Puoi vedere lo stato nella pagina Logs.";
        }
    
    }

    try {
        $query = $pdo->prepare("SELECT membri from utenti where id = ?");
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
        <label for="sorgenti">Gruppo da cui prendere i membri (chat id oppure username)</label>
        <input type="text" class="form-control" name="sorgente" required></input>
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
        <label for="riavvia">Numero massimo di riavvii (lascia vuoto o metti 0 se non vuoi che il bot si riavvii automaticamente)</label>
        <input type="number" min="0" step="1" class="form-control" name="max_riavvii"></input>
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
        <label for="riavvia">Tempo dopo il quale riavviare automaticamente di nuovo il bot (in minuti) (lascia vuoto o metti 0 se non vuoi che il bot si riavvii automaticamente)</label>
        <input type="number" min="0" step="1" class="form-control" name="riavvia"></input>
    </div>
    <br><button type="submit" class="btn btn-primary">Avvia</button>
</form>
<script>
    let json = '<?php echo $query->fetch(PDO::FETCH_ASSOC)["membri"]; ?>';
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