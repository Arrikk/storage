<?php

$jwt = function($data = '{user}', $type = 'enc'){
    $result = '';

    $encMethod  = 'AES-256-CBC';
    $secretKey  = '{$%}$59_+-<|+=4GBJynB,15^';
    $secretIv   = substr($secretKey, 0, 14);

    $key = hash('sha256', $secretKey);
    $vector = substr(hash('sha256', $secretIv), 0, 16);

    if($type == 'enc'){
        $result = openssl_encrypt($data, $encMethod, $key, 0, $vector);
        $result = base64_encode($result);
    }
    if($type == 'dec'){
        $result = base64_decode($data);
        $result = openssl_decrypt($result, $encMethod, $key, 0, $vector);
    }

    return $result;
};  





$data = [
    'id' => 6,
    'expiry' => true,
    'date' => 'tomorrow'
];

$hash = 'N0dIMG0xL3NUeFRVUFdHM3FrUkM4R0FOcE9ZbDQva1pRa3gwVGJqVWRtZ0FOSzg5T2E4ckYvcldkcjZyOWVHNg==';

$data = json_encode($data);

echo $jwt($hash, 'dec');