<?

	class PERSISTE_CBTB_FATURAS extends VirtexPersiste {
	
		public function __construct($bd=null) {
   		parent::__construct();

			$this->_campos 		= array("id_cliente_produto", "data", "descricao", "valor", "status", "observacoes", "reagendamento", "pagto_parcial", "data_pagamento", "desconto", "acrescimo", "valor_pago", "id_cobranca", "cod_barra", "anterior", "id_carne", "nosso_numero", "linha_digitavel", "nosso_numero_banco", "tipo_retorno", "email_aviso", "id_forma_pagamento");
			$this->_chave 		= "id_cliente_produto";
			$this->_ordem 		= "";
			$this->_tabela 		= "cbtb_faturas";
			$this->_sequence	= "";
			$this->_filtros		= array("id_cliente_produto"=>"number", "data"=>"date", "valor"=>"number", "reagendamento"=>"date", "pagto_parcial"=>"number", "data_pagamento"=>"date", "desconto"=>"number", "acrescimo"=>"number", "valor_pago"=>"number", "id_cobranca"=>"number", "anterior"=>"bool", "id_carne"=>"number", "nosso_numero_banco"=>"number", "tipo_retorno"=>"number", "email_aviso"=>"bool", "id_forma_pagamento"=>"number");
			$this->_chave 		= "id_cliente_produto";

		}
	
	}

?>