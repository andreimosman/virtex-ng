<?
	require_once("MUtils.class.php");
	// Entra no diret�rio anterior ao do arquivo execut�vel. (VAHOME)
	chdir(MUtils::getPwd() . "/..");
	
	// Carrega o autoload de classes.
	require_once("autoload.php");
	
	$app = new VAPPServer();
	
	exit($app->daemon());

?>
