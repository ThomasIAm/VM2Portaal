<?php
session_start();

$BASEDIR = '/home/vagrant/VM2/';

// Redirect to url
function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

// Get existing host from vagrant_hosts.yml
function GetHost(string $hostName)
{
	global $BASEDIR;
	global $CUSTOMERNAME;
	global $ENVIRONMENT;

	// Parse vagrant_hosts.yml to array
	$hosts = yaml_parse_file("${BASEDIR}klanten/${CUSTOMERNAME}/${ENVIRONMENT}/vagrant_hosts.yml");
	// Return requested host
	foreach ($hosts as $host) {
		if ($host['name'] === $hostName) {
			return $host;
		}
	}
}

if (empty($_SESSION['customerName'])) {
	// User is not signed in, send to signin
	Redirect('/account/signin.php');
} else {
	// User is signed in, set global variables
	$CUSTOMERNAME = $_SESSION['customerName'];
	$ENVIRONMENT = $_GET['env'];
	$HOSTNAME = $_GET['hostName'];
}
?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Very simple Self-Service Portal for school project for Virtualization Methods 2">
	<meta name="author" content="Thomas van den Nieuwenhoff">
	<title>Edit Host - VM2 Portaal</title>

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
						<li class="nav-item">
							<a class="nav-link" href="/editor/index.php">
								<span data-feather="edit"></span>
								Editor
							</a>
						</li>
					</ul>
				</div>
			</nav>

			<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
				<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
					<h1 class="h2">Edit Host</h1>
				</div>
				<!-- Gather host information and send to processing -->
				<form action="/processing/editHost.php?env=<?php echo $ENVIRONMENT ?>" method="post" style="max-width: 300px; margin: auto">
					<input type="hidden" name="type" value="<?php echo (GetHost($HOSTNAME)['type']) ?>">
					<div class="form-group">
						<label for="inputTypeHost">Type of host</label>
						<select class="form-control" name="type" id="inputTypeHost" disabled>
							<option value="db" <?php
																	if (GetHost($HOSTNAME)['type'] === "db") {
																		echo ("selected");
																	}
																	?>>Databaseserver</option>
							<option value="lb" <?php
																	if (GetHost($HOSTNAME)['type'] === "lb") {
																		echo ("selected");
																	}
																	?>>Loadbalancer</option>
							<option value="web" <?php
																	if (GetHost($HOSTNAME)['type'] === "web") {
																		echo ("selected");
																	}
																	?>>Webserver</option>
						</select>
					</div>

					<input type="hidden" name="hostname" value="<?php echo (GetHost($HOSTNAME)['name']) ?>">
					<div class="form-group">
						<label for="inputHostname">Hostname</label>
						<input type="text" class="form-control" name="hostname" id="inputHostname" placeholder="web01" value="<?php echo (GetHost($HOSTNAME)['name']) ?>" disabled>
					</div>

					<div class="form-group">
						<label for="inputOs">Operating System</label>
						<input type="text" class="form-control" name="os" id="inputOs" placeholder="ubuntu/bionic64" value="<?php echo (GetHost($HOSTNAME)['os']) ?>" required>
					</div>

					<div class="form-group">
						<!-- When a database is used to store customer data, their id could be used to generate a unique net -->
						<!-- <div class="input-group mb-2">
							<div class="input-group-prepend">
								<div class="input-group-text"><?php //echo "10.{id from db}.0." 
																							?></div>
							</div>
							<input type="number" class="form-control" name="ip" id="inputIp" placeholder="11" required>
						</div> -->
						<label for="inputIp">IP address</label>
						<input type="text" class="form-control" name="ip" id="inputIp" placeholder="10.1.0.11" value="<?php echo (GetHost($HOSTNAME)['ip']) ?>" required>
					</div>

					<div class="form-group">
						<label for="inputRam">Memory amount</label>
						<div class="input-group mb-2">
							<input type="number" class="form-control" name="ram" id="inputRam" placeholder="512" value="<?php echo (GetHost($HOSTNAME)['ram']) ?>" required>
							<div class="input-group-append">
								<div class="input-group-text"><?php echo "MB" ?></div>
							</div>
						</div>
					</div>

					<button type="submit" class="btn btn-primary btn-block">Submit</button>
				</form>
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