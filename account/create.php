<?php
session_start();

// Redirect to url
function Redirect(string $url)
{
  header('Location: ' . $url);
  die();
}

if (!empty($_SESSION['customerName'])) {
  // User is already logged in, send to dash
  Redirect('/dashboard/index.php');
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

  <!-- Custom styles for this template -->
  <link href="/styles/create.css" rel="stylesheet">
</head>

<body class="text-center">
  <!-- Gather account information and send to processing -->
  <form class="form-signin" action="/processing/createAccount.php" method="post">
    <h1 class="h3 mb-3 font-weight-normal">Please fill in the details</h1>
    <label for="inputCustomerName" class="sr-only">Customer name</label>
    <input type="text" id="inputCustomerName" name="inputCustomerName" class="form-control" placeholder="Customer name" required autofocus readonly value="demo">
    <label for="inputPassword" class="sr-only">Password</label>
    <!-- <input type="password" id="inputPassword" class="form-control" placeholder="Password" required> -->
    <input type="password" id="inputPassword" class="form-control" placeholder="Password" disabled>
    <small id="inputCustomerNameDemoNotification" class="form-text text-muted">
      These fields have been disabled for demo purposes.
    </small>
    <a href="/account/signin.php">Already have an account? Sign in!</a>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Create</button>
    <p class="mt-5 mb-3 text-muted">&copy; Thomas van den Nieuwenhoff - 2020</p>
  </form>
</body>

</html>