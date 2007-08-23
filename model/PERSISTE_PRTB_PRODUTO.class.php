<?

	/**
	 * Modelo de Produtos
	 */
	class PERSISTE_PRTB_PRODUTO extends VirtexPersiste {
		protected static $_mapaTabelas = array("BL" => "prtb_produto_bandalarga", "D" => "prtb_produto_discado", "H" => prtb_produto_hospedagem);
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_produto", "nome", "descricao", "tipo", "valor", "disponivel", "num_emails", "quota_por_conta", "vl_email_adicional",
										"permitir_outros_dominios", "email_anexado", "numero_contas", "comodato", "valor_comodato", "desconto_promo", 
										"periodo_desconto", "tx_instalacao", "valor_estatico"
									);
			$this->_chave 		= "id_produto"; 
			$this->_ordem 		= "nome";
			$this->_tabela		= "prtb_produto";
			$this->_sequence	= "prsq_id_produto";
			$this->_filtros		= array("id_produto" => "number", "valor" => "number", "num_emails" => "number", "disponivel" => "bool", 
										"quota_por_conta" => "number", "vl_email_adicional" => "number", "permitir_outros_dominios" => "bool", 
										"numero_contas" => "custom", "valor_comodato" => "number", "desconto_promo" => "number",
										"periodo_desconto" => "number", "tx_instalacao" => "number", "valor_estatico" => "bool");
		}
		
		public static function &factoryByType($tipo) {
			return(self::factory(self::$_mapaTabelas[$tipo]));			
		}
		
		
		public function filtroCampo($campo,$valor) {
			$valor = parent::filtroCampo($campo,$valor);
			
			switch($campo) {
			
				case 'numero_contas':
					if( !((int)$numero_contas) ) {
						$retorno = "1";
					}
					break;
				default:
					$retorno = $valor;
					
			
			}
			
			return($retorno);

		}
		
		/**
		 * Enums (utilizado nos selects)
		 */
		
		public static function enumDisponivel() {
			return(array("t" => "Sim", "f" => "Não"));
		}

		public static function enumPermitirOutrosDominios() {
			return(array("t" => "Sim", "f" => "Não"));
		}
		
	}

?>
