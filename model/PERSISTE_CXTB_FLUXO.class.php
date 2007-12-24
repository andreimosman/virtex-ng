<?

	/**
	 * Fluxo de Caixa
	 */
	class PERSISTE_CXTB_FLUXO extends VirtexPersiste {


		public static $TIPO_ORIGEM_RETORNO = 'R';
		public static $TIPO_ORIGEM_MANUAL  = 'M';
		
		public static $TIPO_MOV_ENTRADA    = 'E';
		public static $TIPO_MOV_SAIDA      = 'S';
		
		public static $ESPECIE_DINHEIRO    = 'D';
		public static $ESPECIE_TEF         = 'T';
		public static $ESPECIE_CHEQUE      = 'C';
		public static $ESPECIE_BOLETO      = 'B';
		public static $ESPECIE_CARTAO      = 'R';
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			
			$this->_campos		= array(
										"id_fluxo",
										"tipo_movimentacao", 		/** (S) Sa�da, (E) Entrada */
										"valor",
										"tipo_origem", 				/** (R) Arquivo de Retorno, (M) Entrada manual */
										"origem", 					/** Se for tipo R indicar o nome do arquivo, 
										                        	 ** se for M indicar o administrador que executou a operacao 
										                        	 **/
										"data_registro",			/** Data que ocorreu o registro do evento. */
										"data_compensacao",			/** Data que o valor foi compensado */
										"especie",					/** (D) Dinheiro, (T) Transferencia/DOC/D�bito Autom�tico, (C) Cheque, 
										                        	 ** (B) Boleto/T�tulo Banc�rio, (R) Cart�o de Cr�dito 
										                        	 */
										"cheque_nome",				/** Nome constando no cheque */
										"cheque_banco",				/** C�digo de 3 d�gitos do banco */
										"cheque_agencia",			/** Agencia (sem DV)*/
										"cheque_conta",				/** Conta corrente (sem DV) */
										"cheque_serie",				/** S�rie do cheque */
										"cheque_numero",			/** N�mero do cheque */
										"cheque_pre",				/** No caso de predatado especificar a data */
										
										"boleto_linhadigitavel",	/** Linha digit�vel do Boleto/T�tulo */
										"boleto_codigodebarras",	/** C�digo de barras do Boleto/T�tulo */
										"transf_codigo",			/** N�mero de autoriza��o do cart�o de d�bito ou c�digo da transfer�ncia */
										
										"id_cobranca",				/** Quando aplic�vel a identifica��o de uma cobranca em cbtb_faturas */
										"autenticacao"				/** Sequencial que indica que o valor foi pago, usar de cxsq_autenticacao */
										);
			$this->_chave		= "id_fluxo";
			$this->_ordem		= "data_registro DESC";
			$this->_tabela		= "cxtb_fluxo";
			$this->_sequence	= "cxsq_id_fluxo";
			$this->_filtros		= array("id_fluxo" => "number", "tipo_movimentacao" => "custom", "valor" => "number", "tipo_origem" => "custom", 
										"data_registro" => "date", "data_compensacao" => "date", "especie" => "custom", "id_cobranca" => "number", 
										"autenticacao" => "number");
		
		}
		
		/**
		 * Pagamento com dinheiro.
		 * Tipo Origem: Manual
		 */
		public function pagamentoComDinheiro($valor,$data_pagamento,$id_cobranca,$admin) {
			$dados = array(
							"valor" => $valor, 
							"tipo_movimentacao" => self::$TIPO_MOV_ENTRADA,
							"id_cobranca" => $id_cobranca, 							
							"origem" => $admin, 
							"tipo_origem" => self::$TIPO_ORIGEM_MANUAL,
							"data_registro" => $data_pagamento,
							"data_compensacao" => $data_pagamento,
							"especie" => self::$ESPECIE_DINHEIRO,
							"autenticacao" => $this->obtemIdAutenticacao()							
						);
			return($this->insere($dados));
		}
		
		/**
		 * Pagamento via arquivo de retorno do banco (boleto/t�tulo).
		 */
		public function pagamentoViaBoleto($valor,$data_registro,$data_compensacao,$id_cobranca,$arquivo) {
			$dados = array(
							"valor" => $valor, 
							"tipo_movimentacao" => self::$TIPO_MOV_ENTRADA,
							"id_cobranca" => $id_cobranca, 							
							"origem" => $arquivo, 
							"tipo_origem" => self::$TIPO_ORIGEM_RETORNO,
							"data_registro" => $data_registro,
							"data_compensacao" => $data_compensacao,
							"especie" => self::$ESPECIE_BOLETO,
							"autenticacao" => $this->obtemIdAutenticacao()							
						);
			return($this->insere($dados));
		}
		



		protected function obtemIdAutenticacao() {
			return($this->bd->proximoID("cxsq_autenticacao"));
		}
	
	
	}




?>
