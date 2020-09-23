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
function GetHosts($file)
{
	global $BASEDIR;
	global $CUSTOMERNAME;
	global $ENVIRONMENT;

	// Set the hosts file path
	$hostsFile = "${BASEDIR}klanten/${CUSTOMERNAME}/${ENVIRONMENT}/${file}";
	// Parse the YAML file to a PHP array
	return yaml_parse_file($hostsFile);
}

function EmitYaml($hosts, $file)
{
	global $BASEDIR;
	global $CUSTOMERNAME;
	global $ENVIRONMENT;

	// Set the hosts file path
	$hostsFile = "${BASEDIR}klanten/${CUSTOMERNAME}/${ENVIRONMENT}/${file}";
	yaml_emit_file($hostsFile, $hosts);
}

if (empty($_SESSION['customerName']) || $_SESSION['customerName'] != "demo") {
	// User is not signed in, send to signin
	Redirect('/account/signin.php');
} elseif (empty($_POST['hostname'])) {
	// No new machine needs to be created, send to dash
	Redirect('/dashboard/index.php');
} else {
	// A new machine must be created
	$ENVIRONMENT = $_GET['env'];
	$CUSTOMERNAME = $_SESSION['customerName'];

	// Gather Vagrant hosts
	$vHosts = GetHosts('vagrant_hosts.yml');

	//Create an array with the new host
	$newHostName = "${CUSTOMERNAME}-${ENVIRONMENT}-${_POST['hostname']}";
	$newVHost = array(array(
		'name' => $newHostName,
		'os' => $_POST['os'],
		'ip' => $_POST['ip'],
		'ram' => $_POST['ram'],
		'env' => $ENVIRONMENT,
		'type' => $_POST['type']
	));

	if (empty($vHosts)) {
		// If there are no hosts already, set the hosts to this new host
		$vHosts = $newVHost;
	} else {
		// If there are hosts already, merge the arrays
		$vHosts = array_merge($vHosts, $newVHost);
	}

	// Write the new YAML file out
	EmitYaml($vHosts, 'vagrant_hosts.yml');


	// Gather Ansible hosts
	$aHosts = GetHosts('hosts.yml');

	// Set correct groups for types
	switch ($_POST['type']) {
		case 'db':
			$newHostGroup = "databaseservers";
			break;
		case 'lb':
			$newHostGroup = "loadbalancers";
			break;
		case 'web':
			$newHostGroup = "webservers";
			break;
		default:
			$newHostGroup = "unknown";
			break;
	}

	// Create an array with the new host
	$newAHost = array($newHostName => array('ansible_host' => $_POST['ip']));

	if (empty($aHosts['all']['children'][$newHostGroup])) {
		// If there are no hosts already in this group, set the hosts to this new host
		$aHosts['all']['children'][$newHostGroup]['hosts'] = $newAHost;
	} else {
		// If there are hosts already, merge the arrays
		$test = $aHosts['all']['children'][$newHostGroup]['hosts'];
		$aHosts['all']['children'][$newHostGroup]['hosts'] = array_merge($aHosts['all']['children'][$newHostGroup]['hosts'], $newAHost);
	}

	// Write the new YAML file out
	EmitYaml($aHosts, 'hosts.yml');
}

// Host was created, send to dash
Redirect("/dashboard/index.php?env=${ENVIRONMENT}");
