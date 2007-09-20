<?
	/**
	 * Funчуo para include/require automсtico de classes
	 */
	function __autoload($class_name) {
		
		// Possibilidades dentro do projeto
		$possibilidades = array();
		
		$possibilidades[] = "application/" . $class_name . ".class.php";
		$possibilidades[] = "lib/" . $class_name . ".class.php";
		$possibilidades[] = "controller/" . $class_name . ".class.php";
		$possibilidades[] = "model/" . $class_name . ".class.php";
		$possibilidades[] = "view/" . $class_name . ".class.php";
		
		for($i=0;$i<count($possibilidades);$i++) {
			// echo "AL: " . $possibilidades[$i] . " - " . file_exists( $possibilidades[$i] ) . "\n";
			if( file_exists( $possibilidades[$i] ) ) {
				if( include_once($possibilidades[$i]) ) return;
			}
		}
		
		$possibilidades = array();
		$possibilidades[] = $class_name . ".class.php";
		$possibilidades[] = $class_name . ".php";
		$possibilidades[] = str_replace("_","/",$class_name) . ".php";

		for($i=0;$i<count($possibilidades);$i++) {
			if( include_once($possibilidades[$i]) ) return;
		}

		// Nуo encontrou
		die("Classe nao encontrada: $class_name \n");
	 
	}
?>