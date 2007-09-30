<?

	/**
	 * Classe base do protocolo de comunicações do VirtexAdmin.
	 */
	class VirtexComm {
		// Conexão (cliente ou servidor)
		protected $conn;
		
		// Shared key.
		protected $chave;
		
		// Challenge da sessão.
		protected $challenge;
		
		// indica se está conectado.
		protected $conectado;
		
		public function __construct() {
			$this->conectado = false;
		}
		
		protected function puts($texto) {
			return($this->write($texto));
		}
		
		protected function write($texto) {
			$ret = @fputs($this->conn,$texto);
			if( !$ret ) {
				$this->conectado = false;
			}
			return($ret);
		}
		
		public function estaConectado() {
			return($this->conectado);
		}
		
		function criptografa($texto,$chave) {
			srand();
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

			return(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $chave, $texto, MCRYPT_MODE_ECB, $iv));
		}

		function decriptografa($texto,$chave) {
			srand();
			$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

			return(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $chave, $texto, MCRYPT_MODE_ECB, $iv));
		}

		// Monta string para envio
		function talk($op,$conteudo,$chave) {
			$cnt=base64_encode($this->criptografa($conteudo,$chave));
			return($op."-".$cnt."\n");
		}

		// Analisa recebimento de comando
		function listen($linha,$chave) {
			$comando        = substr($linha,0,4);
			$parametros     = trim(substr($linha,5));

			return(array("comando" => $comando,"parametros" => $this->decriptografa(base64_decode($parametros),$chave)));
		}
	
	}

?>
