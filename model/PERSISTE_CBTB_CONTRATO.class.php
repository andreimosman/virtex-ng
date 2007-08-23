<?

	class PERSISTE_CBTB_CONTRATO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			$this->_campos 		= array("id_cliente_produto", "data_contratacao", "vigencia", "data_renovacao", "valor_contrato", "id_cobranca", "status", "tipo_produto", "valor_produto", "num_emails", "quota_por_conta", "tx_instalacao", "comodato", "valor_comodato", "desconto_promo", "periodo_desconto", "hosp_dominio", "hosp_franquia_em_mb", "hosp_valor_mb_adicional", "disc_franquia_horas", "disc_permitir_duplicidade", "disc_valor_hora_adicional", "bl_banda_upload_kbps", "bl_banda_download_kbps", "bl_franquia_trafego_mensal_gb", "bl_valor_trafego_adicional_gb", "cod_banco", "carteira", "agencia", "num_conta", "convenio", "cc_vencimento", "cc_numero", "cc_operadora", "db_banco", "db_agencia", "db_conta", "vencimento", "carencia", "data_alt_status", "id_produto");
			$this->_chave 		= "id_cliente_produto";
			$this->_ordem 		= "";
			$this->_tabela 		= "cbtb_contrato";
			$this->_sequence	= "";
			$this->_filtro		= array("id_cliente_produto"=>"number", "data_contratacao"=>"date", "vigencia"=>"number", "data_renovacao"=>"date", "valor_contrato"=>"number", "id_cobranca"=>"number", "valor_produto"=>"number", "num_emails"=>"number", "quota_por_conta"=>"number", "tx_instalacao"=>"number", "comodato" => "bool", "valor_comodato"=>"number", "desconto_promo"=>"number", "periodo_desconto"=>"number", "hosp_dominio" => "bool", "hosp_franquia_em_mb"=>"number", "hosp_valor_mb_adicional"=>"number", "disc_franquia_horas"=>"number", "disc_permitir_duplicidade" => "bool", "disc_valor_hora_adicional"=>"number", "bl_banda_upload_kbps"=>"number", "bl_banda_download_kbps"=>"number", "bl_franquia_trafego_mensal_gb"=>"number", "bl_valor_trafego_adicional_gb"=>"number", "cod_banco"=>"number", "agencia"=>"number", "num_conta"=>"number", "convenio"=>"number", "db_banco"=>"number", "db_agencia"=>"number", "db_conta"=>"number", "vencimento"=>"number", "carencia"=>"number", "data_alt_status"=>"date", "id_produto"=>"number");

		}
	
	}

?>
