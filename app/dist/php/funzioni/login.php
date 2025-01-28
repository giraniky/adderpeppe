<?php
    function login($token) {
        global $pdo;
        if(!isset($pdo))
            require __DIR__."/../database.php";
        
        try {
            $query = $pdo->prepare("SELECT id FROM utenti where token = ?");
            $query->execute([$token]);
            if($query->rowCount() > 0)
                return $query->fetch(PDO::FETCH_ASSOC)["id"];
            else
                return 0;
        }
        catch(Exception $e) {
            return 0;
        }

    }
?>