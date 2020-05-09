<?php
session_start();

$BASEDIR = '/home/vagrant/VM2/';

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

if (!empty($_POST['verwijderKlant'])) {
	DeleteDir($BASEDIR . 'klanten/' . $_SESSION['klantnaam'] . '/');
	session_destroy();
}
?>

<html>

<head>
	<title>Verwijder Klant - VM2 Portaal</title>
</head>

<body>
	<a href="/">Home</a>

	<br><br>
	<?php echo "Klant <em>" . $_SESSION['klantnaam'] . "</em> succesvol verwijderd!" ?>
</body>

</html>