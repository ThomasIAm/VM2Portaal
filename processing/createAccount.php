<?php
session_start();

$BASEDIR = '/home/vagrant/VM2/';

// Redirect to url
function Redirect(string $url)
{
	header('Location: ' . $url);
	die();
}

if (!empty($_SESSION['customerName']) || $_POST['inputCustomerName'] != "demo" || $_SESSION['customerName'] != "demo") {
	// User is already signed in, send to dash
	Redirect('/dashboard/index.php');
} elseif (!empty($_POST['inputCustomerName'])) {
	// User is new here
	$customerName = $_POST['inputCustomerName'];
	// Create array of customers
	$ENVIRONMENTS = array_diff(scandir("${BASEDIR}klanten"), array('..', '.'));
	// Check if user doesn't exist already
	if (in_array($customerName, $ENVIRONMENTS)) {
		// User exists, show modal
		$MODAL = true;
	} else {
		// Create folder for user
		@mkdir("${BASEDIR}klanten/${customerName}");
		// Log user in and send to dash
		$_SESSION['customerName'] = $customerName;
		Redirect('/dashboard/index.php');
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
	<title>Create Account - VM2 Portaal</title>

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

	<!-- Custom styles -->
	<link href="/styles/dashboard.css" rel="stylesheet">
</head>

<body>
	<?php
	// Show message when user already exists
	if ($MODAL) {
		echo ("
			<div class=\"modal\" data-backdrop=\"static\" data-keyboard=\"false\" tabindex=\"-1\" role=\"dialog\" style=\"position: relative; display: block\">
				<div class=\"modal-dialog\">
					<div class=\"modal-content\">
						<div class=\"modal-header\">
							<h5 class=\"modal-title\">Customer already exists</h5>
							<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
								<span aria-hidden=\"true\">&times;</span>
							</button>
						</div>
						<div class=\"modal-body\">
							<p>The customer name you gave corresponds with an already existing customer.</p>
						</div>
						<div class=\"modal-footer\">
							<a class=\"btn btn-secondary\" href=\"/account/create.php\" role=\"button\">Try again</a>
							<a class=\"btn btn-primary\" href=\"/account/signin.php\" role=\"button\">Sign in</a>
						</div>
					</div>
				</div>
			</div>
		");
	}
	?>
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>
</body>

</html>