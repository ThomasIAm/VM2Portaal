<?php
// configuration
$url = 'http://192.168.1.211/dashboard/simple_editor.php';
$file = '/home/vagrant/VM2/klanten/testklant/dev/hosts.yml';

// check if form has been submitted
if (isset($_POST['text'])) {
	// save the text contents
	file_put_contents($file, $_POST['text']);

	// redirect to form again
	header(sprintf('Location: %s', $url));
	printf('<a href="%s">Moved</a>.', htmlspecialchars($url));
	exit();
}

// read the textfile
$text = file_get_contents($file);

?>
<!-- HTML form -->
<form action="" method="post">
	<textarea name="text"><?php echo htmlspecialchars($text) ?></textarea>
	<input type="submit" />
	<input type="reset" />
</form>