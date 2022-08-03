<?php

return [
    /*  Outbound HTTP TLS requests to Enterprise Hosts */

    /* Set this to false to support self-signed certificates. */
    /* This is not recommended in production environments */

    'verify_certificates' => env('TLS_VERIFY_CERTIFICATES', true),

    /* Set this to the CURLOPT_TLS_VERSION you wish to support self-signed certificates. */
    /* See possible settings here: https://curl.haxx.se/libcurl/c/CURLOPT_SSLVERSION.html
    /* Default value: CURL_SSLVERSION_TLSv1_2 ( TLSv1.2+ )
    */

    // string name of curl constant, passed through constant() so we can use .env
    'protocol_support' => env('TLS_PROTOCOL_SUPPORT', 'CURL_SSLVERSION_MAX_DEFAULT'), //"CURL_SSLVERSION_TLSv1_2"

    /* Modify cipher list for  */
    'cipher_list' => env('TLS_CIPHER_LIST', 'DEFAULT'),
];
