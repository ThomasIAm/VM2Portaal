<?php
session_start();

function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

if (empty($_SESSION['klantnaam']) || empty($_POST['omgeving']) || empty($_POST['cmd'])) {
	Redirect('/');
} else {
	switch ($_POST['cmd']) {
		case 'v_up':
			$cmd = 'vagrant/up.sh testklant test';
			break;

		case 'v_halt':
			// $cmd = 'vagrant halt';
			$cmd = 'vagrant/halt.sh testklant test';
			break;

		case 'v_destroy':
			$cmd = 'vagrant destroy --force';
			break;

		case 'a-p_p':
			$cmd = 'ansible-playbook playbook.yml';
			break;

		default:
			echo "There was an error while processing your request";
			break;
	}

	// chdir("/home/vagrant/VM2/klanten/testklant/test/");
	chdir("../scripts");
	// $cmdRes = shell_exec('export VAGRANT_HOME=/home/vagrant/.vagrant.d/ && export HOME=/home/vagrant/ && VAGRANT_CWD=/home/vagrant/VM2/klanten/testklant/test/ && ' . $cmd);
	$cmdRes = shell_exec($cmd);
}
?>

<html>

<head>
	<title>Execute Command - <?php echo $_POST['omgeving'] ?> - VM2 Portaal</title>
</head>

<body>
	<p>Console output:</p>

	<div style="border: .25em solid black; margin: 1em; max-height: 100%; overflow: auto">
		<?php echo "<pre>$cmdRes</pre>" ?>
	</div>

	<form action="/dashboard" method="get">
		<input type="hidden" name="omgeving" id="" value="<?php echo $_POST['omgeving'] ?>">

		<input type="submit" name="" id="" value="Terug naar dashboard">
	</form>
</body>

</html>
