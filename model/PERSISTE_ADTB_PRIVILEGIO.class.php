<?php

	/**
	 * adtb_admin
	 * Cadastro de usuários do sistema
	 */
	class PERSISTE_ADTB_PRIVILEGIO extends VirtexPersiste {
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos          = array("id_priv", "cod_priv", "nome","tem_leitura","tem_gravacao","descricao_leitura","descricao_gravacao");
			$this->_chave           = "id_priv";
			$this->_ordem           = "nome";
			$this->_tabela          = "adtb_privilegio";
			$this->_sequence        = "adsq_id_priv";
			
			$this->_filtro			= array("id_priv" => "number", "tem_leitura" => "bool", "tem_gravacao" => "bool");
			
			
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