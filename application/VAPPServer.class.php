<?


	/**
	 * Classe do Servidor do VirtexAdmin.
	 * - Utilizado para acesso à informaçõs distribuídas.
	 */
	class VAPPServer extends VirtexApplication {
	
		protected $fork;
		
		protected $serverConfig;
		protected $userbase;
	
		public function __construct() {
			parent::__construct();
			
			$cfg = new MConfig("etc/comm.server.ini");
			$this->serverConfig = $cfg->config;
			
			$cfg = new MConfig("etc/comm.users.ini");
			$this->userbase = $cfg->config;
			
		}
	
		protected function selfConfig() {
			parent::selfConfig();
			
			// Este aplicativo não conecta ao banco de dados.
			$this->_startdb 	= false;
			
			$this->fork = 1;
			
		}
		
		public function daemon() {
		
			/**
			$pid = pcntl_fork();
			
			if( $pid == -1 ) {
				die("O sistema não pode operar em modo 'daemon'");
			} elseif($pid) {
				// Parent
				// pcntl_wait($signal);
			} else {
				//
				while(true) {
					$this->executa();
				}
			}
			*/
			
			
			// Escuta em uma porta pra cada tipo de serviço. (evita lags e ganha em paralelismo)
			$this->startServerDaemon(VirtexComm::$INC_MONITORAMENTO);	// Monitoramento
			$this->startServerDaemon(VirtexComm::$INC_GRAFICOS);		// Gráficos
			$this->startServerDaemon(VirtexComm::$INC_ARP);				// Tabela ARP
			$this->startServerDaemon(VirtexComm::$INC_PING);			// Tabela PING
			
		}
		
		protected function startServerDaemon($incremento) {

			$pid = pcntl_fork();
			
			if( $pid == -1 ) {
				die("O sistema não pode operar em modo 'daemon'");
			} elseif($pid) {
				// Parent
				// pcntl_wait($signal);
			} else {
				//
				while(true) {
					$srv = new VirtexCommServer(@$this->serverConfig["geral"]["chave"],@$this->serverConfig["geral"]["host"],@$this->serverConfig["geral"]["port"]+((int)$incremento),$this->userbase);
					$srv->start();
				}
			}



		}
		
		public function executa() {
		}
	
	}

