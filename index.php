<?php

//incluir los archivos requeridos
require_once('Clases/juego.php');
require_once('Clases/ahorcado.php');

//esto mantendrá los datos del juego mientras actualizan la página
session_start();

//si aun no han iniciado un juego vamos a cargar uno
if (!$_SESSION['game']['hangman'])
	$_SESSION['game']['hangman'] = new hangman();

?>
<html>
	<head>
		<title>AHORCADO</title>
		<link rel="stylesheet" type="text/css" href="inc/style.css" />
	</head>
	<body>
		<div id="content">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
		<h2>¡Juguemos al ahorcado!</h2>
		<?php
			$_SESSION['game']['hangman']->playGame($_POST);
		?>
		</form>
		</div>
	</body>
</html>