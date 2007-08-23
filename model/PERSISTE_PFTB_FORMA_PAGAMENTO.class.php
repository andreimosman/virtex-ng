<?

	class PERSISTE_PFTB_FORMA_PAGAMENTO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_forma_pagamento","descricao","codigo_banco","carteira","agencia","dv_agencia","conta","dv_conta","convenio","cnpj_agencia_cedente","codigo_cedente","operacao_cedente","tipo_cobranca","disponivel","carne","nossonumero_inicial","nossonumero_final","nossonumero_atual");
			$this->_chave 		= "id_forma_pagamento";
			$this->_ordem 		= "";
			$this->_tabela		= "pftb_forma_pagamento";
			$this->_sequence	= "pfsq_id_forma_pagamento";
			$this->_filtros		= array("id_forma_pagamento" => "number","codigo_banco" => "number","agencia" => "number", "conta" => "number", "convenio" => "number", "tipo_cobranca" => "custom", "disponivel" => "bool", "carne" => "bool", "nossonumero_inicial" => "number","nossonumero_final" => "number","nossonumero_atual" => "number");
		}
		
		public function obtemTiposCobranca() {
			return(array("BL" => "Boleto", "DA" => "Débito Automático", "MO" => "Manual/Outro Sistema", "PC" => "Pag-Contas (carnê)"));
		}
		
		public function obtemBancos() {
			$bancos = array();
			
			$bancos["1"] 	= "Banco do Brasil S/A";
			$bancos["237"] 	= "Banco Bradesco S/A";
			$bancos["104"] 	= "Caixa Econômica Federal";
			
			return($bancos);
			
		}
		
		public function obtemProximoNumeroSequencial() {
		
		}
		
		
	}
		
?>
