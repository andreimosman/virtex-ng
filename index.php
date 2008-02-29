<?
	include("autoload.php");

	/**
	 * Identifica��o do script chamado.
	 */
	
	$script = $_SERVER["SCRIPT_FILENAME"];
	$tmp = explode("/", $script );
	$tmp = explode(".",$tmp[ count($tmp) - 1]);
	$script = $tmp[0];
	unset($tmp);

	// Algum arquivo PHP pode ter definido esta op��o	
	if( !isset($tipo_interface) ) {
		$tipo_interface = $script == "admin" ? "admin" : "usuario";
	}
	
	// Algum arquivo PHP pode ter definido esta op��o
	if( !isset($sessao) ) {
		$sessao = @$_REQUEST["sessao"] ? $_REQUEST["sessao"] : "index";
	}

	try {
		$controler = VirtexController::factory($tipo_interface, $sessao);
	} catch(ExcecaoController $e) {
		/**
		 * CONTROLLER N�O ENCONTRADO.
		 *
		 * Provavelmente o usu�rio est� testando as possibilidades na URL.
		 *
		 * Executar alguma a��o para que o erro n�o apare�a para o usu�rio final.
		 */
		
		/**print_r($e->getTrace());*/
		
		echo "CONTROLLER INEXISTENTE ;): $tipo_interface - $sessao";		
		
	}
	
?>
