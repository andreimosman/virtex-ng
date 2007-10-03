<?php

	/**
	 * adtb_admin
	 * Cadastro de usuários do sistema
	 */
	class PERSISTE_ADTB_PRIVILEGIO extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id_priv", "cod_priv", "nome");
			$this->_chave           = "id_priv";
			$this->_ordem           = "nome";
			$this->_tabela          = "adtb_privilegio";
			$this->_sequence        = "adsq_id_priv";
		}

		public function obtemPrivilegios(){
			return( $this->obtem() );		
		}
		
		public function enumAcessos(){
    		return array(
    						"0"=> "Desabilitado", 
                    		"f" => "Somente Leitura",
                    		"t" => "Ler e Gravar"
    				);
		}
	
	
	}
	
	
	
	
?>