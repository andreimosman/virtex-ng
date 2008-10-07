<?

	/**
	 * Base para classes de persistencia
	 */
	
	class VirtexPersiste extends MPersiste {
	
		public function __construct() {
			parent::__construct();
		}

		public static final function init() {
			self::$_prefix = "PERSISTE";
		}
	}

