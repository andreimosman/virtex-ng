<?


	/**
	 * Classe para objetos de login em sess�o (Especializada por LoginAdmin e por LoginUsuario).
	 */
	class Login {	

		protected static $instance=null;
		
		protected $username;
		protected $password;
		protected $variaveis;
	
		protected function __construct() {
			$this->init();			
		}
		
		public function init() {
			$this->username = "";
			$this->password = "";
			$this->variaveis = array();
		}
		
		public static function &getInstance() {
			@session_start();
		
			if( self::$instance != null ) {
				// Objeto j� instanciado.
				return(self::$instance);
			} else {
				// Procurar na sess�o.
				if( @$_SESSION['vaLOGIN'] != null ) {
					// Est� na sess�o
					self::$instance = @$_SESSION['vaLOGIN'];
				} else {
					self::$instance = new Login();
				}
			}
			
			return self::$instance;
			
		}
		
		public function persisteSessao() {
			@$_SESSION['vaLOGIN'] = $this;
		}
		
		public function obtemUsername() {
			return($this->username);
		}
		
		public function obtemPassword() {
			return($this->password);
		}
		
		public function atribuiUsername($username,$password="") {
			$this->username = $username;
			$this->password = $password;
		}
		
		public function atribui($variavel,$valor) {
			$this->variaveis[$variavel] = $valor;
		}
		
		public function obtem($variavel) {
			return(@$this->variaveis[$variavel]);
		}
	
	}
	
	// $teste = Login::getInstance();
	// $teste->atribuiUsername("Andrei","Teste");
	// $teste->atribui("privilegios",array("A","B","C"));
	// print_r($teste);

?>
