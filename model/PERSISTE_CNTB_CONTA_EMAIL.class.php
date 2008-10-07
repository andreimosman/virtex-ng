<?

	class PERSISTE_CNTB_CONTA_EMAIL extends PERSISTE_CNTB_CONTA {

		public function __construct() {
			parent::__construct();
			
			$this->_campos = array_merge($this->_campos, array("quota","email","redirecionar_para"));
			$this->_tabela = "cntb_conta_email";
		}

	
	}
