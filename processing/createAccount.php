<?php
session_start();

$BASEDIR = '/home/vagrant/VM2/';

// Redirect to url
function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

if (!empty($_SESSION['customerName'])) {
	// User is already signed in, send to dash
	Redirect('/dashboard/index.php');
} elseif (!empty($_POST['inputCustomerName'])) {
	// User is new here
	$customerName = $_POST['inputCustomerName'];
	// Create folder for user
	@mkdir("${BASEDIR}klanten/${customerName}");
	// Log user in and send to dash
	$_SESSION['customerName'] = $customerName;
	Redirect('/dashboard/index.php');
}
