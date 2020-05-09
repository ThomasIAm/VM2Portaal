<?php
session_start();

$BASEDIR = '/home/vagrant/VM2/';

function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

function CopyDir(string $src, $dst)
{
	$dir = opendir($src);
	@mkdir($dst);

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
				unlink($dir . "/" . $file);
			} else {
				DeleteDir($dir . "/" . $file);
			}
		}
	}
	closedir($dir_handle);
	rmdir($dir);
}

if (!empty($_POST['clearsession'])) {
	session_destroy();
	Redirect('/');
}

if (empty($_SESSION['klantnaam']) && empty($_POST['klantnaam'])) {
	if (!empty($_POST['klantnaamNieuw'])) {
		@mkdir($BASEDIR . '/klanten/' . $_POST['klantnaamNieuw']);
		$_SESSION['klantnaam'] = $_POST['klantnaamNieuw'];
		header('Refresh: 0');
	} else {
		Redirect('login.php');
	}
} else if (empty($_SESSION['klantnaam'])) {
	$_SESSION['klantnaam'] = $_POST['klantnaam'];
}

if (!empty($_SESSION['klantnaam'])) {
	if (!empty($_POST['omgeving'])) {
		CopyDir($BASEDIR . 'templates/voorbeeld_klant/voorbeeld_omgeving/', $BASEDIR . 'klanten/' . $_SESSION['klantnaam'] . '/' . $_POST['omgeving']);
	} else if (!empty($_POST['verwijderOmgeving']) && !empty($_POST['verwijderOmgevingNaam'])) {
		DeleteDir($BASEDIR . 'klanten/' . $_SESSION['klantnaam'] . '/' . $_POST['verwijderOmgevingNaam'] . '/');
	}

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