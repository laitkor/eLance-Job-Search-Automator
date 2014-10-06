<?php

require_once('config.php');
require_once('elance-auth-lib.php');
 
error_reporting(E_ALL);
 
$elance_auth = new ElanceAuthentication();
$url = $elance_auth->RequestAccessCode( ELANCE_API_KEY , $base_url."/index.php");

header("Location: " . $url);
//shell_exec( 'xdg-open "'.$url.'"' );
//shell_exec( 'gnome-open "'.$url.'"' );
