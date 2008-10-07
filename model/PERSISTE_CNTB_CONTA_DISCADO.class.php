<?

	class PERSISTE_CNTB_CONTA_DISCADO extends PERSISTE_CNTB_CONTA {

		public function __construct() {
			parent::__construct();
			
			$this->_campos = array_merge($this->_campos, array("foneinfo"));
			$this->_tabela = "cntb_conta_discado";
		}

	
	}
