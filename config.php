<?php
$config = array(
	'site_url' => 'https://app.linkboxy.com',
	//'db_host' => 'localhost',
	'db_host' => 'sql322.main-hosting.eu',
	'db_username' => 'u198566027_linkboxy', //'ux2ncp62fbv23',
	'db_pass' => 'JLtcg6VrQzR6A6R', //'52pcdxpadgt8',
	'db_name' => 'u198566027_linkboxy', //'dbyxcxf48eqvkn',
	'server_dir' =>  __DIR__ . '/',
	'vendor_autoload' =>  __DIR__ . '/' . 'vendor/autoload.php',
);
$admin_email = array('vikashsaini876@gmail.com', 'spatel1981@hotmail.com');
$package_data = array(
	"monthly-freebie" => array(
		"plan-name" => "Monthly Freebie",
		"plan-price" => 0,
		"sites" => 1,
		"monitored-links" => 50,
		"plan-period" => "monthly"
	),
	"monthly-entrepreneur" => array(
		"plan-name" => "Monthly Entrepreneur",
		"plan-price" => 5,
		"sites" => 3,
		"monitored-links" => 500,
		"plan-period" => "monthly"
	),
	"monthly-pro" => array(
		"plan-name" => "Monthly Pro",
		"plan-price" => 15,
		"sites" => 10,
		"monitored-links" => 5000,
		"plan-period" => "monthly"
	),
	"monthly-agency" => array(
		"plan-name" => "Monthly Agency",
		"plan-price" => 40,
		"sites" => 50,
		"monitored-links" => 15000,
		"plan-period" => "monthly"
	),
	"monthly-custom" => array(
		"plan-name" => "Monthly Custom",
		"plan-price" => "?",
		"sites" => "?",
		"monitored-links" => "?",
		"plan-period" => "?"
	),
	"yearly-freebie" => array(
		"plan-name" => "Yearly Freebie",
		"plan-price" => 0,
		"sites" => 1,
		"monitored-links" => 50,
		"plan-period" => "yearly"
	),
	"yearly-entrepreneur" => array(
		"plan-name" => "Yearly Entrepreneur",
		"plan-price" => 30,
		"sites" => 3,
		"monitored-links" => 500,
		"plan-period" => "yearly"
	),
	"yearly-pro" => array(
		"plan-name" => "Yearly Pro",
		"plan-price" => 90,
		"sites" => 10,
		"monitored-links" => 5000,
		"plan-period" => "yearly"
	),
	"yearly-agency" => array(
		"plan-name" => "Yearly Agency",
		"plan-price" => 240,
		"sites" => 50,
		"monitored-links" => 15000,
		"plan-period" => "yearly"
	),
	"yearly-custom" => array(
		"plan-name" => "Yearly Custom",
		"plan-price" => "?",
		"sites" => "?",
		"monitored-links" => "?",
		"plan-period" => "yearly"
	),
	"lifetime-free" => array(
		"plan-name" => "Lifetime Free",
		"plan-price" => 0,
		"sites" => 10000000,
		"monitored-links" => 10000000,
		"plan-period" => "lifetime-free"
	),
);
