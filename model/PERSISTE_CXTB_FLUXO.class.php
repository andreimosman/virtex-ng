<?

	/**
	 * Fluxo de Caixa
	 */
	class PERSISTE_CXTB_FLUXO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			
			$this->_campos		= array(
										"id_fluxo",
										"tipo_movimentacao", 	/** (D) D�bito, (C) Cr�dito */
										"valor",
										"tipo_origem", 			/** (R) Arquivo de Retorno, (M) Entrada manual */
										"origem", 				/** Se for tipo R indicar o nome do arquivo, 
										                         ** se for M indicar o administrador que executou a operacao 
										                         **/
										"data_registro",		/** Data que ocorreu o registro do evento. */
										"data_compensacao",		/** Data que o valor foi compensado */
										"especie",				/** (D) Dinheiro, (T) Transferencia ou DOC, (C) Cheque */
										"cheque_nome",			/** Nome constando no cheque */
										"cheque_banco",			/** C�digo de 3 d�gitos do banco */
										"cheque_agencia",		/** Agencia (sem DV)*/
										"cheque_conta",			/** Conta corrente (sem DV) */
										"cheque_serie",			/** S�rie do cheque */
										"cheque_numero",		/** N�mero do cheque */
										"cheque_pre",			/** No caso de predatado especificar a data */
										"id_cobranca",			/** Quando aplic�vel a identifica��o de uma cobranca em cbtb_faturas */
										"autenticacao"			/** Sequencial que indica que o valor foi pago, usar de cxsq_autenticacao */
										);
			$this->_chave		= "id_fluxo";
			$this->_ordem		= "data_registro DESC";
			$this->_tabela		= "cxtb_fluxo";
			$this->_sequence	= "cxsq_id_fluxo";
			$this->_filtros		= array("id_fluxo" => "number", "tipo_movimentacao" => "custom", "valor" => "number", "tipo_origem" => "custom", 
										"data_registro" => "date", "data_compensacao" => "date", "especie" => "custom", "id_cobranca" => "number", 
										"autenticacao" => "number");
		
		}
	
	
	}




?>
