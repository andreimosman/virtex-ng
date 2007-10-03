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
			return(array("BL" => "Boleto", "DA" => "D�bito Autom�tico", "MO" => "Manual/Outro Sistema", "PC" => "Pag-Contas (carn�)"));
		}
		
		public function obtemBancos() {
			$bancos = array();
			
			$bancos["1"] 	= "Banco do Brasil S/A";
			$bancos["237"] 	= "Banco Bradesco S/A";
			$bancos["104"] 	= "Caixa Econ�mica Federal";
			
			return($bancos);
			
		}
		
		/**
		 * Fun��o utilizada para obter o pr�ximo nossonumero dispon�vel para a forma de pagamento especificada.
		 * O aplicativo em quest�o dever� gerar um erro interno no caso (remoto) deste aplicativo retornar 0,
		 * que indicar� o final dos n�meros dispon�veis para esta forma de pagamento.
		 */
		public function obtemProximoNumeroSequencial($id_forma_pagamento) {
			$sql = "SELECT nossonumero_atual,nossonumero_final,nossonumero_inicial FROM pftb_forma_pagamento WHERE id_forma_pagamento = '".$this->bd->escape($id_forma_pagamento)."' FOR UPDATE";
			$info = $this->bd->obtemUnicoRegistro($sql);
			
			$inicial = (int)$info["nossonumero_inicial"];
			$final = (int)$info["nossonumero_final"];
			$atual = (int)$info["nossonumero_atual"];
			$proximo = 0;
			
			if( $atual < $inicial ) {
				// Contagem n�o come�ou. Iniciar.
				$proximo = $inicial;
			} else {
				$proximo=$atual+1;
			}
			
			if( $proximo == $final ) {
				// Esse foi o �ltimo n�mero gerado, travar o registro para ningu�m usar.
				$sql = "UPDATE pftb_forma_pagamento SET disponivel = 'f',nossonumero_atual = '".$this->bd->escape($proximo)."' WHERE id_forma_pagamento = '".$this->bd->escape($id_forma_pagamento)."'";
				$this->bd->consulta($sql);
			}
			
			if( $proximo > $final ) {
				// Situa��o que pode chegar somente no caso de concorr�ncia no �ltimo registro.
				return(0);
			}
			
			$sql = "UPDATE pftb_forma_pagamento SET nossonumero_atual = '".$this->bd->escape($proximo)."' WHERE id_forma_pagamento = '".$this->bd->escape($id_forma_pagamento)."'";
			$this->bd->consulta($sql);
			
			return($proximo);
			
		}
		
		
	}
		
?>
