<?
	/**
	 * Função para include/require automático de classes
	 */
	function __autoload($class_name) {
	
		// echo "GET: " . ini_get("include_path") . "<br>\n";
		
		$inc_path = ini_get("include_path");
		if( !strstr($inc_path, "smarty" ) && !strstr($inc_path,"Smarty") ) {
			if( file_exists("/usr/local/share/smarty") ) {
				// PATH BSD
				// echo "EXISTE /usr/local/share/smarty<br>\n";
				ini_set("include_path", $inc_path.":/usr/local/share/smarty:/usr/local/share/pear:/usr/local/share/jpgraph");
			} else if( file_exists("/usr/share/Smarty") ) {
				// echo "EXISTE /usr/share/Smarty<br>\n";
				// PATH LINUX
				ini_set("include_path", $inc_path.":/usr/share/Smarty:/usr/local/share/jpgraph");
				if( !strstr($inc_path, "/pear") ) {
					$ini_set("include_path", ini_get("include_path") . ":/usr/local/pear");
				}
			}
		} else {
			// "INC OK!!!<br>\n";
		}
		
		
	
		// echo "CURRENT: " . posix_getcwd() . "<br>\n";
		
		//echo "<pre>";
		//print_r($_SERVER);
		//echo "</pre>";
		
		$tmp = explode("/", $_SERVER["SCRIPT_FILENAME"]);
		array_pop($tmp);
		array_pop($tmp);
		$tmp[] = "framework";
		
		$frameworkPath = implode("/",$tmp);

		// Possibilidades dentro do projeto
		$possibilidades = array();
		$possibilidades[] = $frameworkPath ."/" . $class_name . ".class.php";	// Caminho padrão para o framework.
		$possibilidades[] = "application/" . $class_name . ".class.php";
		$possibilidades[] = "lib/" . $class_name . ".class.php";
		$possibilidades[] = "controller/" . $class_name . ".class.php";
		$possibilidades[] = "model/" . $class_name . ".class.php";
		$possibilidades[] = "view/" . $class_name . ".class.php";
		
		for($i=0;$i<count($possibilidades);$i++) {
			// echo "AL: " . $possibilidades[$i] . " - " . file_exists( $possibilidades[$i] ) . "<br>\n";
			if( file_exists( $possibilidades[$i] ) ) {
				if( include_once($possibilidades[$i]) ) return;
			}
		}
		
		$possibilidades = array();
		$possibilidades[] = $class_name . ".class.php";
		$possibilidades[] = $class_name . ".php";
		$possibilidades[] = str_replace("_","/",$class_name) . ".php";
		$possibilidades[] = "pear/".str_replace("_","/",$class_name) . ".php";
		
		for($i=0;$i<count($possibilidades);$i++) {
			if( @include_once($possibilidades[$i]) ) return;
		}

		// Não encontrou
		die("Classe nao encontrada: $class_name \n");
	 
	}
?>