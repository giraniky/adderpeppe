<?php
require "../dist/php/template/header.php";

$telefono=str_replace(" ","+",$_GET["telefono"]);

if(empty($telefono)) {
    require "../dist/php/template/inserimento_telefono.php";
}
else {
    echo "Ultimi 100 messaggi per il numero: ".$telefono."<br>";
    try {
        require '../vendor/autoload.php';

        $MadelineProto = new \danog\MadelineProto\API("../dist/sessions/".$_SESSION["id"] .$telefono.".madeline");

        $MadelineProto->start();
        $MadelineProto->setNoop();

        $messages = $MadelineProto->messages->search(
            peer: ['_' => 'inputPeerEmpty'],
            q: "",
            //add_offset: limit*$i,
            limit: 100
        );

        echo "<pre>".json_encode($messages["messages"], JSON_PRETTY_PRINT)."</pre>";


    }
    catch(Exception $e) {
        echo "errore: ".$e->getMessage();
    }
}

require "../dist/php/template/footer.php";
?>