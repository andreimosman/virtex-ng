<?

	class VirtexExcecao extends Exception {
		
		public function __construct($codigo,$mensagem) {
			$this->code = $codigo;
			$this->message = $mensagem;
		}
		
		public function obtemMensagem() {
			return $this->getMessage();
		}
		
		public function obtemCodigo() {
			return $this->getCode();
		}	
	}

