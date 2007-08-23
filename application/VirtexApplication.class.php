<?

	/**
	 * Classe base para aplicativos de backend (linha de comando/agendados).
	 */
	
	class VirtexApplication extends VirtexAdmin {
		
		/** configuração das opções longas (--qualquercoisa) do getopt */
		protected $_longopts;
		
		/** configurações das opções pequenas (-x) do getopt */
		protected $_shortopts;
		
		/** cache dos valores encontrados */
		protected $options;


		/**
		 * Construtor.
		 */
		public function __construct() {
			parent::__construct();
			$this->options = array();
			
			$this->getopt();
			
		}
		
		protected function getopt() {
			$go = new Console_Getopt();
			$args = $go->readPHPArgv();
			if(!count($args)) {
				// Não foi chamado no console.
				throw new VirtexExcecao(255,"Aplicação não foi chamada no modo console.");
			}


			if( $this->_shortopts || $this->_longopts ) {
				$this->options = array();
				$options = $go->getopt($args,$this->_shortopts,$this->_longopts);
				
				if(PEAR::isError($options) || !count($this->options[1])) {
					$this->usage();
					exit(-1);
				}
				
				$this->options = $options;
			}
		}
		
		protected function selfConfig() {
			
		}
		
		/**
		 * Imprime a sintaxe pra execução do aplicativo.
		 */
		protected function usage() {
		
		}
		
		public function executa() {
		
		}

	}


?>
