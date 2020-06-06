<?php
session_start();

// Redirect to url
function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

// Destroy the (past) user's session and send to signin
session_destroy();
Redirect('/account/signin.php');
