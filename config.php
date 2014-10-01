<?php
 
$base_url = 'http://localhost:8080/elance'; // Base URL of your directory

// Add / Remove Email addresses, that are supposed to receive the job notifications
$to_email_addresses = array(
	"email_1@domain.com" => "Name 1",
        "email_2@domain.com" => "Name 2"
);

$email_settings = array(
	"smtp_username" => "ENTER_YOUR_SMTP_USERNAME",
        "smtp_password" => "ENTER_YOUR_SMTP_PASSWORD",
        "smtp_host" => "ENTER_YOUT_SMTP_HOST",
        "smtp_port" => "587", // SMTP Port Number
        "email_subject" => "Elance New Job Posts - Laitkor",
	"from_name" => "ENTER_FROM_NAME",
	"from_email_address" => "ENTER_FROM_EMAIL_ADDRESS",
        "reply_to_name" => "ENTER_REPLY_TO_NAME",
        "reply_to_email_address" => "ENTER_REPLY_TO_EMAIL_ADDRESS"
);

$page = (isset($_GET['page']) && $_GET['page'])?$_GET['page']:1;

$filter_arr = array(
	'keywords' => 'php mysql wordpress', // Enter Keywords to be searched for jobs
	'sortCol' => 'budget', // Order By column. Possible Values :  budget, numProposals, startDate and endDate
	'sortOrder' => 'desc', // Sort Order. Possible Values :  asc / desc
	'page' => $page, // **Not to be changed
	'rpp' => 25  // **Not to be changed
);

$hourly_min_rate = 10;
$min_budget = 500;
