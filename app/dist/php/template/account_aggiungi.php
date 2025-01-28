%s
<form method="POST">
    %s
    <br><button type="submit" class="btn btn-primary">%s</button>
    <button type="button" class="btn btn-danger" onclick="window.location.href = '../../pages/account_elimina.php?telefono=' + encodeURIComponent((new URLSearchParams(window.location.search)).get('telefono').replace(/\s/g, '')) + '&redirect=account_aggiungi.php';"> Annulla</button>
</form>

<script>
    /*document.querySelector("form > div").childNodes.forEach(function(e) {
        e.classList.add("form-control");
        e.outerHTML = "<div class='form-group'>" + e.outerHTML + "</div>";
        
    })*/

    let type = document.querySelector("form > select[name=type]");
    if(type) {
        let automatic = type.querySelector("option[value=automatic]");
        if(automatic)
            automatic.selected = true;

        let phone = type.querySelector("option[value=phone]");
        if(phone)
            phone.selected = true;
        document.getElementsByTagName("form")[0].submit();
    }
    let telefono = (new URLSearchParams(window.location.search)).get("telefono").replace(/\s/g, "");
    if(telefono) {
        let phone_number = document.querySelector("form > input[name=phone_number]");
        if(phone_number) {
            phone_number.value = telefono;
            // document.getElementsByTagName("form")[0].submit();
        }

        // let app_title = document.querySelector("form > input[name=app_title]");
        // let app_shortname = document.querySelector("form > input[name=app_shortname]");
        // let app_url = document.querySelector("form > input[name=app_url]");
        // let app_platform=document.querySelector("form > label > input[name=app_platform][value=web]");
        // let app_desc = document.querySelector("form > textarea[name=app_desc]");

        // if(app_title && app_shortname && app_url && app_platform && app_desc) {
        //     app_title.value = telefono.replace("+","");
        //     app_shortname.value = telefono.replace("+","");
        //     app_url.value = "https://t.me/" + telefono;
        //     app_platform.click();
        //     app_desc.required = false;
        //     document.getElementsByTagName("form")[0].submit();
        // }
    }
</script>