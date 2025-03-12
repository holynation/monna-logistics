<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Generates and signs a JWT for User
function generateJwtToken($payload){
    $key = getenv('jwtKey');
    $expiration = getenv('tokenExpiration');
    // Make an array for the JWT Payload
    $payload = array(
        "iss"   => base_url(),
        "iat"   => time(),
        "nbf"   => time() - 5,
        "exp"   => time() + (60 * $expiration),
        "data"  => $payload
    );
    // encode the payload using our secretkey and return the token
    return JWT::encode($payload, $key, 'HS256');
}

function decodeJwtToken($payload) {
    $key = getenv('jwtKey');
    JWT::$leeway = 60; // $leeway in seconds
    // decode the payload using our secretkey and return the token
    return JWT::decode($payload, new Key($key, 'HS256'));
}

function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    }else {
        $urlPath = apache_request_headers();
        $headers = array_key_exists('Authorization', $urlPath)?$urlPath['Authorization']:(array_key_exists('authorization',$urlPath)?$urlPath['authorization']:false);
    }
    return $headers;
}

function getBearerToken() {
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    throw new \Exception('Access Token Not found');
}
