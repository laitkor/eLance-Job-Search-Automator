<?php

require( 'config.php' );
require( 'elance-auth-lib.php' );
require( 'PHPMailer-master/PHPMailerAutoload.php');

$myFile = dirname( __FILE__ )."/log.txt";
$fh = fopen($myFile, 'a') or die("can't open file");

$access_token = '';

fwrite( $fh, json_encode($_GET) );    

// Get Access Token
if ( isset($_GET["code"]) )
{
    $code = $_GET["code"];
    $elance_auth = new ElanceAuthentication();
    $json = $elance_auth->GetAccessToken("ENTER_YOUR_API_KEY", "ENTER_YOUR_CONSUMER_SECRET_CODE", $code);
 
    if( $json->data->access_token )
    {
	$access_token = $json->data->access_token; 
    }
}
else
if ( isset($_GET["access_token"]) && $_GET["access_token"] )
{
    $access_token = urldecode($_GET["access_token"]);
}

// Check for Access Token
if( !$access_token )
{
    fwrite( $fh, date('Y-m-d H:i:s',time())." >> Could not get an access token !!!\n" );    
    fclose( $fh );
    die();
}

// Start searching for Jobs
$url = "https://api.elance.com/api2/jobs?keywords=php&access_token=".$access_token;

foreach($filter_arr as $key => $fp)
  $url .= "&".$key."=".urlencode($fp);

//echo $url;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$output = curl_exec($ch);\
curl_close($ch); 

$response = json_decode($output);

if( !isset($response->data) )
  die( $page );

$totalResults = $response->data->totalResults;
$totalPages = $response->data->totalPages;
$numResults = $response->data->numResults;
$page = $response->data->page;
$pageResults = $response->data->pageResults;

//echo $page . " = " . $totalResults ." = ". $totalPages . "<br />";
//echo '<pre>'; print_r( $pageResults[0] ); echo '</pre>';

if( $totalPages >= $page ) // Create / Edit file updating new records
{
   $filename = "job_posts.txt";
   $new_content = '';
   $new_content_arr = array();
   $job_counter = 0;
   $new_job_counter = 0;

   $fhandle = fopen( $filename, "r") ;
   $file_content = @fread($fhandle,filesize($filename));

   if( $file_content != null )
   {
	$file_content = json_decode( $file_content );
        $new_content_arr = $file_content;
        $job_counter = count($file_content);
   }

   if( !empty($pageResults) )
   {
	foreach($pageResults as $result)
	{
		if( $result->hourlyRateMin >= $hourly_min_rate && $result->budgetMin >= $min_budget && $result->endDate >= time() && $result->clientVerifiedPayment )
		{
		   $new_content_arr[$job_counter] = $result;
	           $job_counter++;
	           $new_job_counter++;
	        } 
	}
   }

   //fwrite( $fh, "Page : " . $page. " = ". $job_counter . " | Results Found : " . $new_job_counter ."\n" );
   $new_content = json_encode( $new_content_arr );
   
   $handle = fopen($filename, "w+");
   fwrite($handle, $new_content);
   fclose($handle);
}

if( $totalPages == $page ) // Fetch file content and send mails
{
   $filename = "job_posts.txt";
   $fhandle = fopen( $filename, "r") ;
   $file_content = fread($fhandle,filesize($filename));

   if( $file_content != null )
   {
	$file_content = json_decode( $file_content );
	$html = '<html>'.
		'<head>'.
                  //'<link href="'.$base_url.'/style.css" type="text/css" rel="stylesheet" />'.
                '</head>'.
		'<body style="background-color:#efefef; color: #333; font-family: sans-serif; font-size: 12px; min-width: 600px; padding: 10px;">'.
		'<h2 class="heading" style="color: #47a801; margin-bottom:10px; border-bottom:1px dotted #ccc; padding-bottom:10px;">Relevant Job Posts on Elance</h2>'.
		'<ul id="posts" style="list-style: none; margin: 0; padding: 0;">';

	if( !empty($file_content) )
	{
		foreach($file_content as $result)
		{	
			$html .= '<li class="post" style="list-style: none; margin: 0; padding: 0; border-bottom: 1px dotted #999; padding: 10px 0; text-align: justify;">
	                            <div class="left area_1" style="float: left; width: 73%;">
	                              <h3 class="job_title" style="font-size: 15px; margin: 0 0 5px;">
	                                <a href="'.$result->jobURL.'" target="_blank" style="color: #0067b1; text-decoration: none;">'.$result->name.'</a>
	                              </h3>
				      <p class="meta_data" style="color: #000; font-size: 11px; font-style: italic; margin: 0;">
					 <span><b>Budget :</b> '.$result->budgetMin.' to '.$result->budgetMax.'$</span>
					 &nbsp;|&nbsp;
					 <span><b>Hourly Rate :</b> '.$result->budget.'</span>
					 <!-- &nbsp;|&nbsp;
					 <span class="green" style="color: #47a801;">
	                                   Payment Verified
	                                 </span> -->
				      </p>
				      <p class="meta_data" style="color: #000; font-size: 11px; font-style: italic; margin: 0 0 8px;">
					 <span>Posted On : '.date( 'd-m-Y', $result->postedDate ).'</span>
					 &nbsp;|&nbsp;
					 <span>End Date : '.date( 'd-m-Y', $result->endDate ).'</span>
					 &nbsp;|&nbsp;
					 <span>Proposals : '. $result->numProposals .'</span>
				      </p>
				      <p class="description" style="font-size: 11px; margin: 5px 0;">'.nl2br($result->description).'</p>
				      <p class="meta_data" style="color:#000; font-size:11px; font-style:italic; margin: 0;">
	                                 <b>Category : </b>'.$result->category.' ('.$result->subcategory.')
	                              </p>
				    </div>
				    <div class="right area_2" style="float: right; width: 25%; text-align: center;">
				      <img src="'.$result->clientImageURL.'" /><br />
	                              <span class="blue" style="color: #0067b1;">'.(($result->clientName)?$result->clientName:$result->clientUserName).' ('.$result->clientCountryCode.')</span>
				      '.($result->isFeatured?'<br /><br /><b><img src="'.$base_url.'/featured.jpeg" alt="Featured" style="color: #47a801;" /></b>':'').'
				    </div>
				    <div class="clear" style="clear:both;"></div>
	                          </li>';
			
		}
	}

	$html .= '</ul></body></html>';
	//echo $html;

	if( !empty($file_content) )
	{
		$mail = new PHPMailer;

		//$mail->SMTPDebug = 3;                               // Enable verbose debug output
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = $email_settings['smtp_host'];  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = $email_settings['smtp_username'];                 // SMTP username
		$mail->Password = $email_settings['smtp_password'];                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                                    // TCP port to connect to

		$mail->From = $email_settings['from_email_address'];
		$mail->FromName = $email_settings['from_name'];

		if( !empty($to_email_addresses) )
		  foreach($to_email_addresses as $email => $name )
		    $mail->addAddress( $email, $name );

		//$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
		//$mail->addAddress('ellen@example.com');               // Name is optional

		$mail->addReplyTo( $email_settings['reply_to_email_address'] , $email_settings['reply_to_name'] );
		//$mail->addCC('cc@example.com');
		//$mail->addBCC('bcc@example.com');

		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $email_settings['email_subject'];
		$mail->Body    = $html;
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		if(!$mail->send()) {                    
                    fwrite( $fh, "Message could not be sent. Mailer Error: " .  $mail->ErrorInfo ."\n" );
		} else {
		    //echo 'Message has been sent';
		}
	}
   }

   $handle = fopen($filename, "w+");
   fwrite($handle, '');
   fclose($handle);
}
else
{
   $next_url = $base_url."/index.php?page=".( $page + 1 )."&access_token=".urlencode($access_token);

   fwrite( $fh, $next_url."\n" );    
   shell_exec( 'wget "'.$next_url.'"' );

   @unlink( dirname( __FILE__ ).'/index.php?page='.$page.'&access_token='.urlencode($access_token) );
}

fclose($fh);
die();
