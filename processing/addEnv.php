<?php
session_start();

$BASEDIR = '/home/vagrant/VM2/';

// Redirect to url
function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

// Copy directory from src to dst
function CopyDir(string $src, $dst)
{
	$dir = opendir($src);
	// Create dst dir if not existst
	@mkdir($dst);

	// Copy dirs and files recursively
	while ($file = readdir($dir)) {
		if (($file != '.') && ($file != '..')) {
			if (is_dir($src . '/' . $file)) {
				CopyDir($src . '/' . $file, $dst . '/' . $file);
			} else {
				copy($src . '/' . $file, $dst . '/' . $file);
			}
		}
	}

	closedir($dir);
}

// Replace text in file
function ReplaceText(string $old, string $new, string $file)
{
	$str = file_get_contents($file);
	$str = str_replace($old, $new, $str);
	file_put_contents($file, $str);
}

if (empty($_SESSION['customerName'])) {
	// User is not signed in, send to signin
	Redirect('/account/signin.php');
} elseif (empty($_POST['inputEnvName'])) {
	// No new environment needs to be created, send to dash
	Redirect('/dashboard/index.php');
} else {
	$CUSTOMERNAME = $_SESSION['customerName'];
	$env = $_POST['inputEnvName'];
	// A new environment has to be created
	$omgevingDir = "${BASEDIR}klanten/${CUSTOMERNAME}/${env}";
	// Copy template environment
	CopyDir($BASEDIR . 'templates/voorbeeld_klant/voorbeeld_omgeving/', $omgevingDir);
	shell_exec("ssh-keygen -q -f ${omgevingDir}/${CUSTOMERNAME}-${env}-id_rsa -N \"\"");
	// Replace variables in files
	ReplaceText('%klant%', $CUSTOMERNAME, $omgevingDir . '/ansible.cfg');
	ReplaceText('%omgeving%', $env, $omgevingDir . '/ansible.cfg');
	ReplaceText('%klant%', $CUSTOMERNAME, $omgevingDir . '/Vagrantfile');
	ReplaceText('%omgeving%', $env, $omgevingDir . '/Vagrantfile');
}

// Environment added, send to dash
Redirect("/dashboard/index.php?env=${env}");
