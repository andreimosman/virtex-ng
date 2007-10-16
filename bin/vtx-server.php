<?
	require_once("MUtils.class.php");
	// Entra no diretório anterior ao do arquivo executável. (VAHOME)
	chdir(MUtils::getPwd() . "/..");
	
	// Carrega o autoload de classes.
	require_once("autoload.php");
	
	$app = new VAPPServer();
	
	exit($app->daemon());

?>
