<?

	class PERSISTE_CNTB_CONTA_HOSPEDAGEM extends PERSISTE_CNTB_CONTA {

		public function __construct() {
			parent::__construct();
			
			$this->_campos = array_merge($this->_campos, array("tipo_hospedagem", "uid", "gid", "home", "shell", "dominio_hospedagem"));
			$this->_tabela = "cntb_conta_hospedagem";
			$this->_filtros(array_merge($this->_filtros,array("uid" => "custom", "gid" => "custom", "home" => 
																"custom", "shell" => "custom", "dominio_hospedagem" => "custom")));
		}

	
	}
