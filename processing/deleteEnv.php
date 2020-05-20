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
} elseif (empty($_GET['env'])) {
	// No new environment needs to be created, send to dash
	Redirect('/dashboard/index.php');
} else {
	// An environment needs to be deleted
	DeleteDir($BASEDIR . 'klanten/' . $_SESSION['customerName'] . '/' . $_GET['env'] . '/');
}

// Environment deleted, send to dash
Redirect('/dashboard/index.php');
