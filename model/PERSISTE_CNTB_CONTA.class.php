<?

	class PERSISTE_CNTB_CONTA extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("username","dominio","tipo_conta","senha","id_cliente","id_cliente_produto","id_conta",
										"senha_cript", "conta_mestre", "status", "observacoes"
									);
			$this->_chave 		= "id_conta"; // PK é username,dominio,tipo_conta só que jogando isso pegamos o id conta no retorno do insert
			$this->_ordem 		= "username,dominio,tipo_conta";
			$this->_tabela		= "cntb_conta";
			$this->_sequence	= "cnsq_id_conta";
			
			$this->_filtros 	= array("status" => "custom", "conta_mestre" => "bool");
			
		}
		
		public function filtroCampo($campo,$valor) {
		
			switch($campo) {
				case 'status':
					/** 
					 * Status Validos:
					 * 		'A'	=> Ativo (default)
					 *		'B' => Bloqueado
					 * 		'C' => Cancelado
					 *		'S'	=> Suspenso (falta de pagamento)
					 */
					
					$retorno = $valor ? $valor : 'A';
					break;
				
				default:
					$retorno = $valor;
			}
			
			return($retorno);
		}
	}

?>
