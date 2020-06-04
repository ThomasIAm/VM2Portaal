<?php
session_start();

$BASEDIR = '/home/vagrant/VM2/';

// Redirect to url
function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
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

function FindVVmKey($hosts, $vmName)
{
	foreach ($hosts as $key => $host) {
		if ($host['name'] === $vmName) {
			return $key;
		}
	}
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
	$HOSTNAME = $_POST['hostname'];
	$CUSTOMERNAME = $_SESSION['customerName'];

	// Remove host from current inventories
	$vFile = "${BASEDIR}klanten/${CUSTOMERNAME}/${ENVIRONMENT}/vagrant_hosts.yml";
	$vHosts = yaml_parse_file($vFile);
	$vVmKey = FindVVmKey($vHosts, $HOSTNAME);
	if ($vVmKey !== null) {
		array_splice($vHosts, $vVmKey, 1);
	}

	$aFile = "${BASEDIR}klanten/${CUSTOMERNAME}/${ENVIRONMENT}/hosts.yml";
	$aHosts = yaml_parse_file($aFile);
	foreach ($aHosts['all']['children'] as $key => $group) {
		unset($group['hosts'][$HOSTNAME]);
	}

	//Create an array with the new host
	$newVHost = array(array(
		'name' => $HOSTNAME,
		'os' => $_POST['os'],
		'ip' => $_POST['ip'],
		'ram' => $_POST['ram'],
		'env' => $ENVIRONMENT,
		'type' => $_POST['type']
	));

	// Merge the arrays
	$vHosts = array_merge($vHosts, $newVHost);

	// Write the new YAML file out
	yaml_emit_file($vFile, $vHosts);


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
	$newAHost = array($HOSTNAME => array('ansible_host' => $_POST['ip']));

	// Merge the arrays
	$aHosts['all']['children'][$newHostGroup]['hosts'] = array_merge($aHosts['all']['children'][$newHostGroup]['hosts'], $newAHost);

	// Write the new YAML file out
	yaml_emit_file($aFile, $aHosts);
}

// Host was created, send to dash
Redirect("/dashboard/index.php?env=${ENVIRONMENT}");
