<?

	/**
	 * Classe base para aplicativos de backend (linha de comando/agendados).
	 */
	
	class VirtexApplication extends VirtexAdmin {
		
		/** configura��o das op��es longas (--qualquercoisa) do getopt */
		protected $_longopts;
		
		/** configura��es das op��es pequenas (-x) do getopt */
		protected $_shortopts;
		
		/** console/argumentos */
		protected $_console;
		protected $_args;
		
		/** cache dos valores encontrados */
		protected $options;
		protected $params;

		// tcpip.ini - Arquivo de configura��es de NAS tcpip.
		protected $nasConfig;
		protected $netConfig;

		// Cache de informa��es
		protected $tcpip;
		protected $pppoe;
		protected $pptp;
		
		protected $fator;
		
		protected $network;
		protected $ext_iface;	// Interface externa
		
		protected $infoNAS;
		
		/**
		 * Construtor.
		 */
		public function __construct() {
			parent::__construct();
			
			$this->options = array();
			$this->params = array();
			
			$this->_shortopts = NULL;
			$this->_longopts  = NULL;

			$this->selfConfig();

			$this->getopt();

			// Configura��es dos NAS
			$this->nasConfig = MConfig::getInstance("etc/nas.ini");
			
			// Configura��es de Rede
			$this->netConfig = MConfig::getInstance("etc/network.ini");

			$equipamentos = null;


			$this->tcpip 	= array();
			$this->pppoe 	= array();
			$this->pptp		= array();
			$this->infoNAS 	= array();
			$this->fator 	= array();

			
			$this->network = $this->netConfig->config;
			
			foreach($this->network as $iface => $dados) {
				if( $dados["status"] == "up" ) {
					if( $dados["type"] == "external" ) {
						$this->ext_iface = $iface;
					}
				}
			}
			
			
			$equipamentos = null;
			
			if( $this->_startdb && count($this->nasConfig->config) ) {
				$equipamentos = VirtexModelo::factory("equipamentos");
			}

			foreach($this->nasConfig->config as $nas => $dados) {
				@list($tipo,$id_nas) = explode(":",$nas);
				
				if( ((int)$dados["enabled"]) && trim($dados["interface"]) ) {
					if( $equipamentos ) {
						$this->infoNAS[$id_nas] = $equipamentos->obtemNAS($id_nas);
					}
				
					// Adicionar no cache
					
					if( $tipo == "pppoe" ) {
						// 
						$this->pppoe[$id_nas] = trim($dados["interface"]);
					}
					
					if( $tipo == "pptp" ) {
						//
						$this->pptp[$id_nas] = trim($dados["interface"]);
					}

					if( $tipo == "tcpip" ) {
						//
						$this->tcpip[$id_nas] = trim($dados["interface"]);
					}
					
					$this->fator[$id_nas] = trim($dados["fator"]);

				}
				
			}
			
		}
		
		protected function getopt() {
			$go = new Console_Getopt();
			$args = $go->readPHPArgv();
			if(!count($args)) {
				// N�o foi chamado no console.
				throw new VirtexExcecao(255,"Aplica��o n�o foi chamada no modo console.");
			}


			if( $this->_shortopts || $this->_longopts ) {
				$this->options = array();
				$options = $go->getopt($args,$this->_shortopts,$this->_longopts);
				
				if(PEAR::isError($options) || count($this->options[1])) {
					$this->usage();
					exit(-1);
				}
				
				$this->options 	= @$options[0];
				
				if( !is_array($this->options) ) $this->options = array();
			}
		}
		
		protected function selfConfig() {

		}
		
		/**
		 * Imprime a sintaxe pra execu��o do aplicativo.
		 */
		protected function usage() {
			
		}
		
		public function executa() {
		
		}

		protected function obtemOpcao($opcao) {
			for($i=0;$i<count($this->options);$i++) {			
				if( @$this->options[$i][0] == $opcao) {
					return($this->options[$i]);
				}
			}
			
			return(null);
		}


	}


?>
