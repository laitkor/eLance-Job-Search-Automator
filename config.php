<?php

$base_url = 'http://localhost:8080/callback'; // Base URL of the working repository

define( 'ELANCE_API_KEY', 'enter_your_elance_api_key' );
define( 'ELANCE_CONSUMER_SECRET_CODE', 'elance_consumer_secret_code' );

// Add / Remove Email addresses, that are supposed to receive the job notifications
$to_email_addresses = array(
	"email_1@domain.com" => "Name 1",
        "email_2@domain.com" => "Name 2"
);

$email_settings = array(
	"smtp_username" => "enter_your_smtp_username",
        "smtp_password" => "enter_your_smtp_password",
        "smtp_host" => "enter_your_smtp_host",
        "smtp_port" => "587", // SMTP Port Number
        "email_subject" => "Elance New Job Posts - Laitkor",
	"from_name" => "Administrator",
	"from_email_address" => "admin@domain.com",
        "reply_to_name" => "No Reply",
        "reply_to_email_address" => "noreply@domain.com"
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
