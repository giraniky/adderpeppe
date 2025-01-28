<p>Inserisci un numero di telefono <b>già registrato su Telegram</b> includendo il prefisso internazionale</p>

<form method="GET" onsubmit="return verifica()">
    <div class="form-group">
        <input type="text" name="telefono" placeholder="Numero di telefono" required="" value="+" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Vai</button><br>
    <a href="index.php"><button type="button" class="btn btn-danger">Annulla</button></a>
</form>
<script>
function verifica() {
    let telefono = document.querySelector("form > div > input[name=telefono]").value;
    
    /*if(!telefono) {
        alert("Il telefono non può essere vuoto");
        return false;
    }
    else if(telefono.charAt(0) != "+") {
        alert("Il telefono deve iniziare per +");
        return false;
    }
    if(!telefono.replace("+","")) {
        alert("Il telefono deve contenere qualcos'altro oltre al simbolo +");
        return false;
    }
    else*/
    if(!/^\+[0-9]+$/.test(telefono)){
        alert("Il numero di telefono non è in un formato valido");
        return false;
    }
}
</script>