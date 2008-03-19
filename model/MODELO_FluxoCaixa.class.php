<?

	/**
	 * Modelo para gerenciamento de Fluxo de Caixa.
	 */
	class MODELO_FluxoCaixa extends VirtexModelo {
	
		protected $cxtb_fluxo;
		
		/** 
		 * Origem da entrada: Arquivo de Retorno 
		 */
		public static $TIPO_ORIGEM_RETORNO 		= 'R';
		
		/** 
		 * Origem da entrada: Processamento Manual 
		 */
		public static $TIPO_ORIGEM_MANUAL		= 'M';
		


		/** 
		 * Esp�cie: Dinheiro 
		 */
		public static $ESPECIE_DINHEIRO			= 'D';
		
		/** 
		 * Esp�cie: Transfer�ncia ou DOC 
		 */
		public static $ESPECIE_TRANSFERENCIA	= 'T';
		
		/**
		 * Esp�cie: Cheque
		 */
		public static $ESPECIE_CHEQUE			= 'C';
		
		/**
		 * Esp�cie: Cart�o de Cr�dito
		 */
		public static $ESPECIE_CARTAO_CREDITO	= 'R';


		/**
		 * Esp�cie: Boleto/T�tulo 
		 * (Usado pelo arquivo de processamento de retorno)
		 */
		public static $ESPECIE_BOLETO			= 'B';

		

		public function __construct() {
			parent::__construct();
			$this->cxtb_fluxo = VirtexPersiste::factory("cxtb_fluxo");
		}
		
		public static function obtemListaOrigens() {
			return(array(
						"Arquivo de Retorno" => self::$TIPO_ORIGEM_RETORNO, 
						"Processamento Manual" => self::$TIPO_ORIGEM_MANUAL
						)
					);
		}
		
		public static function obtemListaEspecies() {
			$r = array(
							"Dinheiro" => self::$ESPECIE_DINHEIRO,
							"Cheque" => self::$ESPECIE_CHEQUE,
							"Cart�o de Cr�dito" => self::$ESPECIE_CARTAO_CREDITO,
							"Transfer�ncia/DOC/D�bito Autom�tico" => self::$ESPECIE_TRANSFERENCIA,
							"Boleto/T�tulo" => self::$ESPECIE_BOLETO
						);						
			return $r;			
		}
		
		
		// public function cadastraEntrada($valor,$tipo_origem,$origem,$especie,
		public function pagamentoComDinheiro($valor,$data_pagamento,$id_cobranca,$admin) {
			return($this->cxtb_fluxo->pagamentoComDinheiro($valor,$data_pagamento,$id_cobranca,$admin));
		}
		
		public function pagamentoViaRetorno($valor,$data_registro,$data_compensacao,$id_cobranca,$arquivo) {
			return($this->cxtb_fluxo->pagamentoViaRetorno($valor,$data_registro,$data_compensacao,$id_cobranca,$arquivo));
		}
	
	
	
	
	}


?>
