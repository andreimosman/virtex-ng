<?


	/**
	 * Classe para objetos de login em sessão (Especializada por LoginAdmin e por LoginUsuario).
	 */
	class Login {	

		protected static $instance=null;
		
		protected $username;
		protected $password;
		protected $variaveis;
		protected $tipo;
		
		protected $listaPodeLer;
		protected $listaPodeGravar;
		
	
		protected function __construct($tipo='admin') {
			$this->tipo = $tipo;
			$this->init();			
		}
		
		public function init() {
		
			$this->username = "";
			$this->password = "";
			$this->variaveis = array();
			
			$this->listaPodeLer = array();
			$this->listaPodeGravar = array();
		}
		
		public static function &getInstance($tipo='admin') {
			@session_start();
			
			if( self::$instance != null ) {
				// Objeto já instanciado.
				return(self::$instance);
			} else {
				// Procurar na sessão.
				if( @$_SESSION[$tipo.'vaLOGIN'] != null ) {
					// Está na sessão
					self::$instance = @$_SESSION[$tipo.'vaLOGIN'];
				} else {
					self::$instance = new Login($tipo);
				}
			}

			return self::$instance;
			
		}
		
		public function persisteSessao() {
			@$_SESSION[$this->tipo.'vaLOGIN'] = $this;
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
		
		public function atribuiPrivilegios($listaPrivilegios,$chave="cod_priv",$write="pode_gravar") {
			if( !is_array($listaPrivilegios) ) {
				$this->privilegios = array();
				return;
			}

			foreach($listaPrivilegios as $privilegio) {
				// print_r($privilegio);
				
				$this->listaPodeLer[$privilegio[$chave]] = true;
				
				if( $privilegio[$write] == 't' ) {
					$this->listaPodeGravar[$privilegio[$chave]] = true;
				}
				
			}
			
		}
		
		public function podeLer($privilegio) {
			return(@$this->listaPodeLer[$privilegio]);
		}

		public function podeGravar($privilegio) {
			return(@$this->listaPodeGravar[$privilegio]);
		}


	}
	
?>
