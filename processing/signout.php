<?php
session_start();

// Redirect to url
function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

session_destroy();
Redirect('/account/signin.php');
