<?
	require_once("MUtils.class.php");
	// Entra no diretório anterior ao do arquivo executável. (VAHOME)
	chdir(MUtils::getPwd() . "/..");
	
	// Carrega o autoload de classes.
	require_once("autoload.php");
	
	$app = new VAPPMonitor();
	
	// print_r($app);
	exit($app->executa());

?>
