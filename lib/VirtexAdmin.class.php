<?

	/**
	 * Base do sistema VirtexAdmin.
	 */
	
	class VirtexAdmin {
		protected $_cfg;
		protected $_config;

		protected $_startdb;
		
		protected $licenca;
				
		// Sistema Operacional
		protected $SO;
	
		protected function __construct() {
			$this->licenca		= new VirtexLicenca();
			$this->_config		= "etc/virtex.ini";
			$this->_startdb 	= true;
			$this->selfConfig();

			$this->_cfg = MConfig::getInstance($this->_config);

			// Inicializações
			if( $this->_startdb && @$this->_cfg->config["DB"]["dsn"] ) {
				// TODO: Try/Catch
				MDatabase::getInstance(@$this->_cfg->config["DB"]["dsn"],@$this->_cfg->config["geral"]["dsn"]);
				VirtexPersiste::init();
			}
			
			$this->SO = new SOFreeBSD();

			$this->init();
		
		}
	
		protected function selfConfig() {
		
		}

		protected function init() {
		
		}
	
	}

