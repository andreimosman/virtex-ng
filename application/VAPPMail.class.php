<?


	class VAPPMail extends VirtexApplication {
	
		public function __construct() {
			parent::__construct();			
			$this->arquivoEstrutura = "var/update/struct.xml";
		}
		
		protected function selfConfig() {
			$this->_startdb 	= true;
			$this->_shortopts 	= "d";
			$this->_longopts	= array("domains");
		}

		protected function obtemOpcao($opcao) {
			for($i=0;$i<count($this->options);$i++) {			
				if( @$this->options[$i][0] == $opcao) {
					return($this->options[$i]);
				}
			}
			
			return(null);
		}
		
		public function usage() {
			echo "USAGE:\n\n";
			echo "\tphp vtx-mail.php\n";
			echo "\t\t-d|--domains	Lista de dominios do sistema";
			echo "\n\n";
		}
		
		public function executa() {
			if( $this->obtemOpcao("d") || $this->obtemOpcao("--domains") ) {
				$this->executaDominios();
			}
						
		}
		
		public function executaDominios() {
			$preferencias = VirtexModelo::factory("preferencias");
			$dominios = $preferencias->obtemListaDominios();
			for($i=0;$i<count($dominios);$i++) {
				if( trim($dominios[$i]["dominio"]) ) {
					echo $dominios[$i]["dominio"] . "\n";
				}
			}
		
		}


	}
