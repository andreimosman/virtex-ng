<?

	class PERSISTE_PFTB_PREFERENCIA_COBRANCA extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_provedor", "tx_juros", "multa", "dia_venc", "carencia", "pagamento", "observacoes", "path_contrato","enviar_email","mensagem_email","email_remetente", "dias_minimo_cobranca");
			$this->_chave 		= "id_provedor";
			$this->_ordem 		= "";
			$this->_tabela		= "pftb_preferencia_cobranca";
			$this->_sequence	= "";
			$this->_filtros		= array("id_provedor" => "number", "tx_juros" => "number", "multa" => "number", "carencia" => "number", "pagamento" => "custom", "dias_minimo_cobranca" => "number");
		}
		
		public function obtemTiposPagamento() {
			return(array("PRE" => "Pré-Pago", "POS" => "Pós-Pago"));
		}
		
		
	}
		
?>
