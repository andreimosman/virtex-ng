<?

	class PERSISTE_CLTB_CLIENTE extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_cliente", "data_cadastro", "nome_razao", "tipo_pessoa", "rg_inscr", "rg_expedicao", "cpf_cnpj", 
										"email", "endereco", "complemento", "id_cidade", "estado", "cep", "bairro", "fone_comercial",
										"fone_residencial", "fone_celular", "contato", "banco", "conta_corrente", "agencia", "dia_pagamento", 
										"ativo", "obs", "provedor", "excluido", "info_cobranca"
									);
			$this->_chave 		= "id_cliente";
			$this->_ordem 		= "nome_razao";
			$this->_tabela		= "cltb_cliente";
			$this->_sequence	= "clsq_id_cliente";
		}
		
		public static function listaTipoPessoa() {
			return( array(
                  "F" => "Física",
                  "J" => "Jurídica"
                 ));
		}
		
		public static function listaStatusCliente() {
			return( array(
                      "t" => "Ativo",
                      "f" => "Inativo"
                   ));
		
		}
		
		public static function listaDiaPagamento() {
			return( array(
                    "5" => "05",
                    "10" => "10",
                    "15" => "15",
                    "20" => "20",
                    "25" => "25",
                    "30" => "30"
                   ));		
		}


	}

?>
