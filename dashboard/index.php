<?php
session_start();

function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

if (!empty($_POST['clearsession'])) {
	session_destroy();
	Redirect('/');
}

if (empty($_SESSION['klantnaam']) || empty($_GET['omgeving'])) {
	Redirect('/');
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

	<form action="/account" method="post">
		<input type="hidden" name="verwijderOmgevingNaam" id="" value="<?php echo $_GET['omgeving'] ?>">

		<input type="submit" name="verwijderOmgeving" id="" value="Verwijder omgeving" style="background: red">
	</form>

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
</body>

</html>