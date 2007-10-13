<?


	class MMenu {
		protected $itens;
		
		public function __construct($espacos=3) {
			$itens = array();
			$this->espacos = $espacos;
		}
		
		/**
		 * Adiciona um Item de Menu
		 */
		public function addItem($text,$uri,$target) {
		
		
			$item = array("type" => "ITEM", "text" => $text . str_repeat(" &nbsp; ",$this->espacos), "uri" => $uri, "target" => $target);
			$this->itens[] = $item;
		}
		
		/**
		 * Adiciona um separador
		 */
		public function addSeparator() {
			$this->itens[] = array("type" => "SEPARATOR");
		}
		
		/**
		 * Adiciona um submenu.
		 */
		public function addSubmenu($text, MMenu $submenu) {
			$this->itens[] = array("type" => "SUBMENU", "text" => $text, "data" => $submenu);
		}
		
		
		
		public function getItens() {
			return($this->itens);
		}
		
		public function printRecursive($level=0) {
			foreach($this->itens as $item) {
				echo str_repeat(" ", $level*5);
				switch($item["type"]) {
					case 'ITEM':
						 echo $item["text"] . " (" . $item["uri"] . ")\n";
						break;
					case 'SEPARATOR':
						echo "---------------\n";
						break;
					case 'SUBMENU':
						 echo $item["text"] . "\n";
						 $item["data"]->printRecursive($level+1);
						 break;
				}
			}
		}
		
		protected function getJsItemHash($item) {
			$jsHash = "new Hash('contents', '" . $item["text"] . "', 'uri', '" . $item["uri"] . "', 'target', '" . $item["target"] . "')";
			return($jsHash);
		}
		
		protected function getJsSeparatorHash() {
			return("new Hash('contents', '<hr class=separador>')");
		}
		
		protected function getJsSubmenuHash($item) {
			return("'contents', '" . $item["text"] . "', 'uri', '', 'statusText', '', " . $item["data"]->jsOutput() );
		}
		
		public function jsOutput() {
			$idx=1;
			
			
			$retorno = "";
			$jsElementos = array();


			foreach($this->itens as $item) {
				switch($item["type"]) {
					case 'ITEM':
						$jsElementos[] = $idx . ", " . $this->getJsItemHash($item) . "\n";
						break;
					case 'SEPARATOR':
						if( $idx > 1 ) {
							$jsElementos[] = $idx . ", " . $this->getJsSeparatorHash() . "\n";
						}
						break;
					case 'SUBMENU':
						//$jsElementos[] = $idx . ", " . $this->getJsSubmenuHash($item) . "\n";
						$jsElementos[]  = $idx . ", new Hash('contents', '" . $item["text"] . "', 'uri', '', 'statusText', '', " . $item["data"]->jsOutput().")";
						
						break;
				
				}
						
				$idx++;
			}
			if( count($jsElementos) ) {
				$retorno = implode(", \n",$jsElementos);
			}
			
			return($retorno);
		}
		
		
	
	}
	
	//print_r($menu);


?>
