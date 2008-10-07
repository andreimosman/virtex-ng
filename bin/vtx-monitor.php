<?
	$script = $_SERVER["SCRIPT_FILENAME"] ? $_SERVER["SCRIPT_FILENAME"] : $_SERVER["PHP_SELF"];
	if( $script[0] != "/" ) $script = $_SERVER["PWD"] . "/" . $script;
	$tmp = explode("/",$script);
	array_pop($tmp);
	$path = implode("/",$tmp);
	chdir($path."/..");
	
	// Carrega o autoload de classes.
	require_once("autoload.php");
	
	$app = new VAPPMonitor();
	
	// print_r($app);
	exit($app->executa());

