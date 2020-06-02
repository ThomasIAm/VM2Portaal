<?php
session_start();

$BASEDIR = '/home/vagrant/VM2/';
$RES = '';

// Redirect to url
function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

// Execute command
function ShellExec(string $cmd)
{
	global $BASEDIR;
	global $RES;

	chdir("${BASEDIR}klanten/${_SESSION['customerName']}/${_POST['env']}/");
	$RES = shell_exec('export VAGRANT_HOME=/home/vagrant/.vagrant.d && export HOME=/home/vagrant && ' . $cmd);
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
} elseif (empty($_POST['cmd'])) {
	// No action was given, send to dash
	Redirect('/dashboard/index.php');
} elseif (!empty($_POST['vmName'])) {
	$CUSTOMERNAME = $_SESSION['customerName'];

	switch ($_POST['cmd']) {
		case 'Up':
			ShellExec("vagrant up ${_POST['vmName']}");
			break;

		case 'Down':
			ShellExec("vagrant halt ${_POST['vmName']}");
			break;

		case 'Delete':
			ShellExec("vagrant destroy ${_POST['vmName']} --force");

			$vFile = "${BASEDIR}klanten/${CUSTOMERNAME}/${_POST['env']}/vagrant_hosts.yml";
			$vHosts = yaml_parse_file($vFile);
			$vVmKey = FindVVmKey($vHosts, $_POST['vmName']);
			if ($vVmKey !== null) {
				array_splice($vHosts, $vVmKey, 1);
				yaml_emit_file($vFile, $vHosts);
			}

			$aFile = "${BASEDIR}klanten/${CUSTOMERNAME}/${_POST['env']}/hosts.yml";
			$aHosts = yaml_parse_file($aFile);
			switch ($_POST['type']) {
				case 'db':
					$group = "databaseservers";
					break;
				case 'lb':
					$group = "loadbalancers";
					break;
				case 'web':
					$group = 'webservers';
					break;
			}
			unset($aHosts['all']['children'][$group]['hosts'][$_POST['vmName']]);
			yaml_emit_file($aFile, $aHosts);
			break;
		case 'Run Ansible':
			ShellExec("ansible-playbook playbook.yml -l ${_POST['vmName']}");
			break;

		default:
			// No correct action was given, send to dash
			Redirect('/dashboard/index.php');
			break;
	}
} else {
	$CUSTOMERNAME = $_SESSION['customerName'];

	switch ($_POST['cmd']) {
		case 'Up':
			ShellExec("vagrant up");
			break;

		case 'Down':
			ShellExec("vagrant halt");
			break;

		case 'Delete':
			ShellExec("vagrant destroy --force");
			file_put_contents("${BASEDIR}klanten/${CUSTOMERNAME}/${_POST['env']}/vagrant_hosts.yml", "");
			file_put_contents("${BASEDIR}klanten/${CUSTOMERNAME}/${_POST['env']}/hosts.yml", "");
			break;

		case 'Run Ansible':
			ShellExec("ansible-playbook playbook.yml");
			break;

		default:
			// No correct action was given, send to dash
			Redirect('/dashboard/index.php');
			break;
	}
}
?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Very simple Self-Service Portal for school project for Virtualization Methods 2">
	<meta name="author" content="Thomas van den Nieuwenhoff">
	<title>Machine Action - VM2 Portaal</title>

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

	<!-- Custom styles -->
	<link href="/styles/dashboard.css" rel="stylesheet">
</head>

<body>
	<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
		<a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#"><?php echo $CUSTOMERNAME ?></a>
		<button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<ul class="navbar-nav px-3">
			<li class="nav-item text-nowrap">
				<a class="nav-link" href="/processing/signout.php">Sign out</a>
			</li>
		</ul>
	</nav>

	<div class="container-fluid">
		<div class="row">
			<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
				<div class="sidebar-sticky pt-3">
					<ul class="nav flex-column">
						<li class="nav-item">
							<a class="nav-link" href="/account/index.php">
								<span data-feather="user"></span>
								Account
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="/dashboard/index.php">
								<span data-feather="cloud"></span>
								Dashboard
							</a>
						</li>
					</ul>
				</div>
			</nav>

			<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
				<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
					<h1 class="h2">Result</h1>
				</div>

				<pre><?php echo $RES ?></pre>
			</main>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>
	<script src="/scripts/dashboard.js"></script>
</body>

</html>