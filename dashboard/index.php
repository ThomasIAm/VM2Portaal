<?php
session_start();

// Cloud base folder; where 'klanten/' is located
$BASEDIR = '/home/vagrant/VM2/';

// Redirect to url
function Redirect(string $url) {
	header('Location: ' . $url);
	die();
}

// Get existing hosts from vagrant_hosts.yml
function GetHosts() {
	global $BASEDIR;
	// Set the hosts file path
	$file = "${BASEDIR}klanten/${_SESSION['klantnaam']}/${_GET['omgeving']}/vagrant_hosts.yml";
	// Parse the YAML file to a PHP array
	return yaml_parse_file($file);
}

function EmitYaml($hosts) {
	global $BASEDIR;	
	// Set the hosts file path
	$file = "${BASEDIR}klanten/${_SESSION['klantnaam']}/${_GET['omgeving']}/vagrant_hosts.yml";
	// Write the new YAML file out
	yaml_emit_file($file, $hosts);
}

function ShellExec(string $cmd) {
	global $BASEDIR;

	chdir("${BASEDIR}${_SESSION['klantnaam']}/${_POST['omgeving']}");
	shell_exec('export VAGRANT_HOME=/home/vagrant/ && ' . $cmd);
}

if (!empty($_POST['clearsession'])) {
	session_destroy();
	Redirect('/');
}

if (empty($_SESSION['klantnaam']) || empty($_GET['omgeving'])) {
	Redirect('/');
} elseif (!empty($_POST['newvm'])) {
	// A new machine must be created
	$hosts = GetHosts();
	// Create an array with the new host
	$newhost = array(array('name' => "${_SESSION['klantnaam']}-${_GET['omgeving']}-${_POST['hostname']}", 'os' => $_POST['os'], 'ip' => $_POST['ip'], 'ram' => $_POST['ram']));
	
	if (empty($hosts)) {
		// If there are no hosts already, set the hosts to this new host
		$hosts = $newhost;
	} else {
		// If there are hosts already, merge the arrays
		$hosts = array_merge($hosts, $newhost);
	}

	// Write the new YAML file out
	EmitYaml($hosts);
} elseif (!empty($_POST['vmname'])) {
	if (!empty($_POST['vmup'])) {
		ShellExec("vagrant/up.sh ${_POST['vmname']}");
	} elseif (!empty($_POST['vmdown'])) {
		ShellExec("vagrant/halt.sh ${_POST['vmname']}");
	} elseif (!empty($_POST['delvm'])) {
		ShellExec("vagrant/destroy.sh ${_POST['vmname']}");
	}
}
?>

<html>

<head>
	<title><?php echo $_GET['omgeving'] ?> - Dashboard - VM2 Portaal</title>
</head>

<body>
	<?php echo $_SESSION['klantnaam'] ?>

	<form action="" method="post">
		<input type="submit" name="clearsession" value="Logout">
	</form>

	<hr>
	<?php echo $_GET['omgeving'] ?>

	<form action="/account">
		<input type="submit" name="" id="" value="Verander omgeving">
	</form>

	<form action="/account/index.php" method="post">
		<input type="hidden" name="verwijderOmgevingNaam" id="" value="<?php echo $_GET['omgeving'] ?>">

		<input type="submit" name="verwijderOmgeving" id="" value="Verwijder omgeving" style="background: red">
	</form>

	<?php
	$hosts = GetHosts();
	foreach ($hosts as $host) {
		echo   "<div style=\"border: .25em solid black\">
					<h4>${host['name']}</h4>
					<p>OS: ${host['os']}</p>
					<p>IP: ${host['ip']}</p>
					<p>RAM: ${host['ram']} MB</p>
					<form action=\"?omgeving=${_GET['omgeving']}\" method=\"post\">
						<input type=\"hidden\" name=\"vmname\" value=\"${host['name']}\">

						<input type=\"submit\" name=\"vmup\" value=\"Up\" style=\"background: green\">
						<input type=\"submit\" name=\"vmdown\" value=\"Down\">
						<input type=\"submit\" name=\"delvm\" value=\"Delete\" style=\"background: red\">
					</form>
				</div>";
	}
	?>

	<hr>
	<form action="/dashboard/shell_exec.php" method="post">
		<input type="hidden" name="omgeving" id="" value="<?php echo $_GET['omgeving'] ?>">

		<label for="">Kies een commando:</label><br>
		<input type="radio" name="cmd" id="v_up" value="v_up">vagrant up<br>
		<input type="radio" name="cmd" id="v_halt" value="v_halt">vagrant halt<br>
		<input type="radio" name="cmd" id="v_destroy" value="v_destroy">vagrant destroy<br>
		<input type="radio" name="cmd" id="a-p_p" value="a-p_p">ansible-playbook playbook.yml<br>

		<br>
		<input type="submit" name="" id="" value="Voer uit">
	</form>

	<hr>
	<h3>Nieuwe machine</h3>
	<form action="?omgeving=<?php echo $_GET['omgeving'] ?>" method="post">
		<label for="type">Type:</label>
		<select id="type" name="type">
			<option value="db">Databaseserver</option>
			<option value="lb">Loadbalancer</option>
			<option value="web">Webserver</option>
		</select>

		<br>
		<label for="hostname">Hostname: <?php echo "${_SESSION['klantnaam']}-${_GET['omgeving']}-" ?></label>
		<input type="text" name="hostname" id="hostname" placeholder="web01">

		<br>
		<label for="os">OS:</label>
		<input type="text" name="os" id="os" placeholder="ubuntu/bionic64">

		<!-- When a database is used to store customer data, their id could be used to generate a unique net -->
		<!-- <br>
		<label for="ip">IP: <?php //echo "10.{id from database}.0." ?></label>
		<input type="number" name="ip" id="ip" placeholder="10"> -->
		<br>
		<label for="ip">IP:</label>
		<input type="text" name="ip" id="ip" placeholder="10.1.0.11">

		<br>
		<label for="ram">RAM:</label>
		<input type="number" name="ram" id="ram" placeholder="512">
		<label>MB</label>

		<br><br>
		<input type="submit" name="newvm" value="Rol uit">
	</form>
</body>

</html>