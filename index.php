<?
	include("autoload.php");

	/**
	 * Identificação do script chamado.
	 */
	
	$script = $_SERVER["SCRIPT_FILENAME"];
	$tmp = explode("/", $script );
	$tmp = explode(".",$tmp[ count($tmp) - 1]);
	$script = $tmp[0];
	unset($tmp);

	// Algum arquivo PHP pode ter definido esta opção	
	if( !isset($tipo_interface) ) {
		$tipo_interface = $script == "admin" ? "admin" : "usuario";
	}
	
	// Algum arquivo PHP pode ter definido esta opção
	if( !isset($sessao) ) {
		$sessao = @$_REQUEST["sessao"] ? $_REQUEST["sessao"] : "index";
	}

	try {
		$controler = VirtexController::factory($tipo_interface, $sessao);
	} catch(ExcecaoController $e) {
		/**
		 * CONTROLLER NÃO ENCONTRADO.
		 *
		 * Provavelmente o usuário está testando as possibilidades na URL.
		 *
		 * Executar alguma ação para que o erro não apareça para o usuário final.
		 */
		
		/**print_r($e->getTrace());*/
		
		echo "CONTROLLER INEXISTENTE ;): $tipo_interface - $sessao";		
		
	}
	
?>
