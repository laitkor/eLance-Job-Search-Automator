<?php

require_once('elance-auth-lib.php');
 
error_reporting(E_ALL);
 
$elance_auth = new ElanceAuthentication();
$url = $elance_auth->RequestAccessCode("ENTER_YOUR_API_KEY", "http://localhost:8080/callback/" /* ENTER_YOUR_CALLBACK_URL */ );
 
header("Location: " . $url);
