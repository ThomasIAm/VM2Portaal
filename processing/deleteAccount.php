<?php
session_start();

$BASEDIR = '/home/vagrant/VM2/';

// Redirect to url
function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

// Delete directory recursively
function DeleteDir(string $dir)
{
	if (is_dir($dir)) {
		$dir_handle = opendir($dir);
	}
	if (!$dir_handle) {
		return false;
	}
	while ($file = readdir($dir_handle)) {
		if ($file != "." && $file != "..") {
			if (!is_dir($dir . "/" . $file)) {
				// Delete files
				unlink($dir . "/" . $file);
			} else {
				// Delete subdirectories
				DeleteDir($dir . "/" . $file);
			}
		}
	}
	closedir($dir_handle);
	rmdir($dir);
}

if (empty($_SESSION['customerName'])) {
	// User is not signed in, send to signin
	Redirect('/account/signin.php');
} else {
	$CUSTOMERNAME = $_SESSION['customerName'];
}

// Delete customer's directory
DeleteDir("${BASEDIR}klanten/${CUSTOMERNAME}/");
// Sign the user out
Redirect('/processing/signout.php');
