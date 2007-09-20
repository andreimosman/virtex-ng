<?

	class PERSISTE_CBTB_CONTRATO extends VirtexPersiste {
	
		public function __construct($bd=null) {
   		parent::__construct();
   		
			$this->_campos 		= array("id_cliente_produto", "data_contratacao", "vigencia", "data_renovacao", "valor_contrato", "id_cobranca", "status", "tipo_produto", "valor_produto", "num_emails", "quota_por_conta", "tx_instalacao", "comodato", "valor_comodato", "desconto_promo", "periodo_desconto", "hosp_dominio", "hosp_franquia_em_mb", "hosp_valor_mb_adicional", "disc_franquia_horas", "disc_permitir_duplicidade", "disc_valor_hora_adicional", "bl_banda_upload_kbps", "bl_banda_download_kbps", "bl_franquia_trafego_mensal_gb", "bl_valor_trafego_adicional_gb", "cod_banco", "carteira", "agencia", "num_conta", "convenio", "cc_vencimento", "cc_numero", "cc_operadora", "db_banco", "db_agencia", "db_conta", "vencimento", "carencia", "data_alt_status", "id_produto", "nome_produto", "descricao_produto", "disponivel", "vl_email_adicional", "permitir_outros_dominios", "email_anexado", "numero_contas", "valor_estatico",
       "da_cod_banco", "da_carteira", "da_convenio", "da_agencia", "da_num_conta", "bl_cod_banco", "bl_carteira", "bl_convenio", "bl_agencia", "bl_num_conta", "id_forma_pagamento");
			$this->_chave 		= "id_cliente_produto";
			$this->_ordem 		= "";
			$this->_tabela 		= "cbtb_contrato";
			$this->_sequence	= "";
			$this->_filtros		= array("id_cliente_produto"=>"number", "data_contratacao"=>"date", "vigencia"=>"number", "data_renovacao"=>"date", "valor_contrato"=>"number", "id_cobranca"=>"number", "valor_produto"=>"number", "num_emails"=>"number", "quota_por_conta"=>"number", "tx_instalacao"=>"number", "comodato"=>"bool", "valor_comodato"=>"number", "desconto_promo"=>"number", "periodo_desconto"=>"number", "hosp_dominio"=>"bool", "hosp_franquia_em_mb"=>"number", "hosp_valor_mb_adicional"=>"number", "disc_franquia_horas"=>"number", "disc_permitir_duplicidade"=>"bool", "disc_valor_hora_adicional"=>"number", "bl_banda_upload_kbps"=>"number", "bl_banda_download_kbps"=>"number", "bl_franquia_trafego_mensal_gb"=>"number", "bl_valor_trafego_adicional_gb"=>"number", "cod_banco"=>"number", "agencia"=>"number", "num_conta"=>"number", "convenio"=>"number", "db_banco"=>"number", "db_agencia"=>"number", "db_conta"=>"number", "vencimento"=>"number", "carencia"=>"number", "data_alt_status"=>"date", "id_produto"=>"number",
      "disponivel"=>"bool", "vl_email_adicional"=>"number", "permitir_outros_dominios"=>"bool", "email_anexado"=>"bool", "numero_contas"=>"number", "valor_estatico"=>"bool",
       "da_cod_banco"=>"number", "da_convenio"=>"number", "da_agencia"=>"number", "da_num_conta"=>"number", "bl_cod_banco"=>"number", "bl_convenio"=>"number", "bl_agencia"=>"number", "bl_num_conta"=>"number", "id_forma_pagamento"=>"number");
			$this->_chave 		= "id_cliente_produto";

		}
	
	}

?>