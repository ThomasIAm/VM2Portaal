<?php
session_start();

// Redirect to url
function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

if (empty($_SESSION['customerName']) || $_POST['inputCustomerName'] != "demo") {
	// User is not yet signed in
	if (empty($_POST['inputCustomerName'])) {
		// User is not signing in, send to signin
		Redirect('/account/signin.php');
	} else {
		// User is logging in, log user in and send to dash
		$_SESSION['customerName'] = $_POST['inputCustomerName'];
		Redirect('/dashboard/index.php');
	}
} else {
	// User is already logged in, send to dash
	Redirect('/dashboard/index.php');
}
