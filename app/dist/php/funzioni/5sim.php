<?php
define("domain", "https://5sim.net/v1/");
define("product", "telegram");
define("token", "eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE2OTg2MTA4NTEsImlhdCI6MTY2NzA3NDg1MSwicmF5IjoiNGRiZjY3NjhlZTUwYjQ2YzFlYzY2YjI3OWNlYzI5YTciLCJzdWIiOjEzMDQ5MDR9.U2QWQ_vqx0k1pUn7p4za6JWR4gsDHPV_67VAQU4BwT0NlG7mui9oAEWGKfhmUhRjpnoX0LdQ5F-L5UoZbeHRXdmZsPHi9r4b8RoOU-jaZBRZ3rKVKSpcB7l_ExL6Qv25cvUh_fopIQkop81ZhXzVAejEQDROJVKTTDmNoFhCKyG4DDucdcN3vpE6HqsGSTtIxGRJKMj7hC6xK3q-tWUa-HPoxxktsFSLAofQU4-KabaoEjvWv_5_PKJ4bVi2InGWLnODGGbur8Um0Z4YTX7rsljQLvW7I5_hsUdPN6GPNh5rxcMfvObPzoZD1gpllz_CyJPARBmIh8g2jOqRcxwvig");


function request($url) {

    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    
    
    $headers = array();
    $headers[] = 'Authorization: Bearer ' . token;
    $headers[] = 'Accept: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $result = curl_exec($ch);
    
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch))
        throw new Exception('Curl Error:' . curl_error($ch));

    curl_close($ch);
    
    if($http_status !== 200)
        throw new Exception('Http Status '.$http_status);
    

    return $result;
}


function getCheapest() {
    $result = request(domain."guest/prices?product=".product);
    $result = json_decode($result,true);

    foreach($result["telegram"] as $country => $operators) 
        foreach($operators as $operator)
            if($operator["count"] > 0)
                $result2[] = ["country" => $country, "count" => $operator["count"], "cost" => $operator["cost"]];


    usort($result2, function ($item1, $item2) {
        return $item1['cost'] <=> $item2['cost'];
    });

    //print_r($result2);
    return $result2[0];
}

function purchase($country, $operator) {
    return request(domain."user/buy/activation/" . $country . '/' . $operator . '/' . product);
}