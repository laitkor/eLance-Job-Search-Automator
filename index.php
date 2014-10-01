<?php

require_once('elance-auth-lib.php');
 
if (!isset($_GET["code"])) {
    die("Require the code parameter to validate!");
}
 
$code = $_GET["code"];
$elance_auth = new ElanceAuthentication();
$json = $elance_auth->GetAccessToken("ENTER_YOUR_CLIENT_ID", "ENTER_YOUR_SECRET_KEY", $code);
 
print_r( $json );
//Output code
echo "<p>Access token is " . $json->data->access_token . "<p/>";
