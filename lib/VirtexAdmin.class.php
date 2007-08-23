<?


	/**
	 * Base do sistema VirtexAdmin.
	 */
	
	class VirtexAdmin {
		protected $_cfg;
		protected $_config;

		protected $_startdb;
	
		protected function __construct() {
			$this->_config		= "etc/virtex.ini";
			$this->_startdb 	= true;
			$this->selfConfig();

			$this->_cfg = MConfig::getInstance($this->_config);

			// Inicializações
			if( $this->_startdb && @$this->_cfg->config["DB"]["dsn"] ) {
				// TODO: Try/Catch
				$bd = MDatabase::getInstance(@$this->_cfg->config["DB"]["dsn"],@$this->_cfg->config["geral"]["dsn"]);
			}

			$this->init();
		
		}
	
		protected function selfConfig() {
		
		}

		protected function init() {
		
		}
	
	}



?>
