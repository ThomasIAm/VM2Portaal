<?php
session_start();

// Cloud base folder; where 'klanten/' is located
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

// Replace text in file
function ReplaceText(string $old, string $new, string $file) {
	$str = file_get_contents($file);
	$str = str_replace($old, $new, $str);
	file_put_contents($file, $str);
}

// Log user out and send to root
if (!empty($_POST['clearsession'])) {
	session_destroy();
	Redirect('/');
}

if (empty($_SESSION['klantnaam']) && empty($_POST['klantnaam'])) {
	// User is not logged in and not logging in
	if (!empty($_POST['klantnaamNieuw'])) {
		// User is new here
		// Create folder for new user
		@mkdir($BASEDIR . '/klanten/' . $_POST['klantnaamNieuw']);
		// Log user in and refresh page
		$_SESSION['klantnaam'] = $_POST['klantnaamNieuw'];
		header('Refresh: 0');
	} else {
		// User is just not logged in; so, send to login
		Redirect('login.php');
	}
} else if (empty($_SESSION['klantnaam'])) {
	// User is not logged in, but is logging in; so, log user in
	$_SESSION['klantnaam'] = $_POST['klantnaam'];
}

if (!empty($_SESSION['klantnaam'])) {
	// User is logged in
	if (!empty($_POST['omgeving'])) {
		// A new environment has to be created
		$omgevingDir = $BASEDIR . 'klanten/' . $_SESSION['klantnaam'] . '/' . $_POST['omgeving'];
		// Copy template envirionment
		CopyDir($BASEDIR . 'templates/voorbeeld_klant/voorbeeld_omgeving/', $omgevingDir);
		shell_exec("ssh-keygen -q -f ${omgevingDir}/${_SESSION['klantnaam']}-${_POST['omgeving']}-id_rsa -N \"\"");
		// Replace variables in files
		ReplaceText('%klant%', $_SESSION['klantnaam'], $omgevingDir . '/ansible.cfg');
		ReplaceText('%omgeving%', $_POST['omgeving'], $omgevingDir . '/ansible.cfg');
		ReplaceText('%klant%', $_SESSION['klantnaam'], $omgevingDir . '/Vagrantfile');
		ReplaceText('%omgeving%', $_POST['omgeving'], $omgevingDir . '/Vagrantfile');
	} else if (!empty($_POST['verwijderOmgeving']) && !empty($_POST['verwijderOmgevingNaam'])) {
		// An environment needs to be deleted
		DeleteDir($BASEDIR . 'klanten/' . $_SESSION['klantnaam'] . '/' . $_POST['verwijderOmgevingNaam'] . '/');
	}

	// Create array of environments
	$omgevingen = array_diff(scandir($BASEDIR . 'klanten/' . $_SESSION['klantnaam']), array('..', '.'));
}
?>
<html>

<head>
	<title>Account - VM2 Portaal</title>
</head>

<body>
	<?php echo $_SESSION['klantnaam'] ?>

	<form action="" method="post">
		<input type="submit" name="clearsession" value="Logout">
	</form>

	<form action="/account/delete.php" method="post">
		<input type="submit" name="verwijderKlant" id="" value="Verwijder klant" style="background: red">
	</form>

	<hr>
	<ul>
		<?php
		foreach ($omgevingen as $omgeving) {
			echo "<li><a href='/dashboard?omgeving=$omgeving'>$omgeving</a></li>";
		}
		?>
	</ul>

	<hr>
	<form action="" method="post">
		<label for="omgeving">Nieuwe omgevingnaam:</label>
		<input type="text" name="omgeving" id="omgeving">

		<input type="submit" name="" id="" value="Creëer omgeving">
	</form>
</body>

</html>