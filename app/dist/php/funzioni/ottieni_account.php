<?php
    function ottieni_account($id) {
        $array = [];
        foreach(glob(__DIR__."/../../sessions/".$id."+*.madeline") as $file)
            array_push($array,ltrim(pathinfo($file, PATHINFO_FILENAME),$id));
        return $array;
    }
?>