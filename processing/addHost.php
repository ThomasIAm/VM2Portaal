<?php
session_start();

$BASEDIR = '/home/vagrant/VM2/';

// Redirect to url
function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

// Get existing hosts from vagrant_hosts.yml
function GetHosts()
{
	global $BASEDIR;
	global $CUSTOMERNAME;
	global $ENVIRONMENT;

	// Set the hosts file path
	$file = "${BASEDIR}klanten/${CUSTOMERNAME}/${ENVIRONMENT}/vagrant_hosts.yml";
	// Parse the YAML file to a PHP array
	return yaml_parse_file($file);
}

function EmitYaml($hosts)
{
	global $BASEDIR;
	global $CUSTOMERNAME;
	global $ENVIRONMENT;

	// Set the hosts file path
	$file = "${BASEDIR}klanten/${CUSTOMERNAME}/${ENVIRONMENT}/vagrant_hosts.yml";
	yaml_emit_file($file, $hosts);
}

if (empty($_SESSION['customerName'])) {
	// User is not signed in, send to signin
	Redirect('/account/signin.php');
} elseif (empty($_POST['hostname'])) {
	// No new machine needs to be created, send to dash
	Redirect('/dashboard/index.php');
} else {
	// A new machine must be created
	$ENVIRONMENT = $_GET['env'];
	$CUSTOMERNAME = $_SESSION['customerName'];
	$hosts = GetHosts();

	//Create an array with the new host
	$newhost = array(array('name' => "${CUSTOMERNAME}-${ENVIRONMENT}-${_POST['hostname']}", 'os' => $_POST['os'], 'ip' => $_POST['ip'], 'ram' => $_POST['ram'], 'env' => $ENVIRONMENT));

	if (empty($hosts)) {
		// If there are no hosts already, set the hosts to this new host
		$hosts = $newhost;
	} else {
		// If there are hosts already, merge the arrays
		$hosts = array_merge($hosts, $newhost);
	}

	// Write the new YAML file out
	EmitYaml($hosts);
}

// Host was created, send to dash
Redirect('/dashboard/index.php');
