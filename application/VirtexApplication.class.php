<?

	/**
	 * Classe base para aplicativos de backend (linha de comando/agendados).
	 */
	
	class VirtexApplication extends VirtexAdmin {
		
		/** configuração das opções longas (--qualquercoisa) do getopt */
		protected $_longopts;
		
		/** configurações das opções pequenas (-x) do getopt */
		protected $_shortopts;
		
		/** console/argumentos */
		protected $_console;
		protected $_args;
		
		/** cache dos valores encontrados */
		protected $options;


		/**
		 * Construtor.
		 */
		public function __construct() {
			parent::__construct();
			$this->options = array();
			
			$this->_shortopts = NULL;
			$this->_longopts  = NULL;

			$this->selfConfig();

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
				
				if(PEAR::isError($options) || count($this->options[1])) {
					$this->usage();
					exit(-1);
				}
				
				$this->options = @$options[0][0];
				
				if( !is_array($this->options) ) $this->options = array();
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
