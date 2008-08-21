<?


	class PERSISTE_LGTB_ALTERACAO_IP extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct();

			$this->_campos 		= array("id_alteracao_ip","datahora","id_conta","rede","ipaddr","tipo");
			$this->_chave 		= "id_alteracao_ip";
			$this->_ordem 		= "id_alteracao_ip DESC";
			$this->_tabela 		= "lgtb_alteracao_ip";
			$this->_sequence	= "lgsq_id_alteracao_ip";
			$this->_filtros		= array("id_alteracao_ip" => "number", "datahora"=> "datetime", "id_conta"=> "number", "rede" => "inet", "ipaddr" => "inet" );

		}
		
	}



?>
