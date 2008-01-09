<?

	/**
	 * Modelo de log de eventos do sistema.
	 */
	class MODELO_Eventos extends VirtexModelo {
	
		protected $evtb_evento;
		
		public static $TIPO_INFO				= 'INFO';
		public static $TIPO_ERRO				= 'ERRO';
		public static $TIPO_ALTERTA				= 'ALERTA';
		
		public static $NATUREZA_LOGIN	 		= 'LOGIN';
		public static $NATUREZA_ALT_CONTA		= 'ALTERACAO CONTA';
		public static $NATUREZA_PAG_FATURA  	= 'PAGAMENTO FATURA';
		
		public static $DESCRICAO_ERRO			= 'ERRO';
		public static $DESCRICAO_LOGIN_PRIMEIRO = 'PRIMEIRO LOGIN';
		public static $DESCRICAO_PAG_FAT_DESC	= 'PAGAMENTO COM DESCONTO';
		public static $DESCRICAO_PAG_FAT_ACRESC	= 'PAGAMENTO COM ACRESCIMO';
		public static $DESCRICAO_CONTA_ALT_POP	= 'ALTERACAO DE POP';
		public static $DESCRICAO_CONTA_ALT_MAC  = 'ALTERACAO DE MAC';
		public static $DESCRICAO_CONTA_ALT_NAS	= 'ALTERACAO DE NAS';
		public static $DESCRICAO_CONTA_ALT_END	= 'ALTERACAO DE ENDERECO';
		public static $DESCRICAO_CONTA_ALT_STAT = 'ALTERACAO DE STATUS';
		public static $DESCRICAO_CONTA_ALT_UP	= 'ALTERACAO DE BANDA(UPLOAD)';
		public static $DESCRICAO_CONTA_ALT_DOWN	= 'ALTERACAO DE BANDA(DOWNLOAD)';
		
		public function __construct() {
			parent::__construct();
			$this->evtb_evento = VirtexPersiste::factory("evtb_evento");
		}
		
		public function obtemTipos(){
				
					$retorno = array();
		
						$retorno[self::$TIPO_INFO] = "Informações";
						$retorno[self::$TIPO_ERRO] = "Erros";
						$retorno[self::$TIPO_ALTERTA] = "Alertas";
		
					return($retorno);
		
		
		}
		
		public function obtemNatureza(){
				
			$retorno = array();

				$retorno[self::$NATUREZA_LOGIN] = "Login";
				$retorno[self::$NATUREZA_ALT_CONTA] = "Alteração de Conta";
				$retorno[self::$NATUREZA_PAG_FATURA] = "Pagamento de Faturas";

			return($retorno);
		
		
		}
	

		public function registraLoginOk($ipaddr,$id_admin,$primeiroLogin = false) {
			$dados = array("id_admin" => $id_admin,"ipaddr" => $ipaddr, "natureza" => self::$NATUREZA_LOGIN, "tipo" => self::$TIPO_INFO);
			
			if( $primeiroLogin ) {
				$dados["descricao"] = self::$DESCRICAO_LOGIN_PRIMEIRO;
			}
			return($this->evtb_evento->insere($dados));
		}
		
		public function registraLoginErro($ipaddr,$id_admin,$admin) {
			$dados = array("ipaddr" => $ipaddr,"descricao" => $admin, "natureza" => self::$NATUREZA_LOGIN, "tipo" => self::$TIPO_ERRO);
			if($id_admin) {
				$dados["id_admin"] = $id_admin;
			}
			return($this->evtb_evento->insere($dados));
		}
		
		public function registraAlteracaoConta($ipaddr,$id_admin,$id_conta,$id_cliente_produto,
												$status_orig,$status_novo,
												$end_orig,$end_novo,
												$pop_orig,$pop_novo,
												$nas_orig,$nas_novo,
												$mac_orig,$mac_novo,
												$banda_up_orig,$banda_up_novo,
												$banda_dn_orig,$banda_dn_novo) {
			$descricao = "";
			
			if($status_orig != $status_novo) {
				$descricao .= self::$DESCRICAO_CONTA_ALT_STAT . ": $status_orig -> $status_novo\n";
			}
			
			if($pop_orig != $pop_novo ) {
				$descricao .= self::$DESCRICAO_CONTA_ALT_POP . ": $pop_orig -> $pop_novo\n";
			}
			
			if($nas_orig != $nas_novo ) {
				$descricao .= self::$DESCRICAO_CONTA_ALT_NAS . ": $nas_orig -> $nas_novo\n";
			}

			if($end_orig != $end_novo ) {
				$descricao .= self::$DESCRICAO_CONTA_ALT_END . ": $end_orig -> $end_novo\n";
			}

			if($mac_orig != $mac_novo ) {
				if( !$mac_orig ) $mac_orig = 'Sem MAC';
				if( !$mac_novo ) $mac_novo = 'Sem MAC';
				$descricao .= self::$DESCRICAO_CONTA_ALT_MAC . ": $mac_orig -> $mac_novo\n";
			}
			
			if($banda_up_orig != $banda_up_novo ) {
				$banda_up_orig = $banda_up_orig > 0 ? $banda_up_orig . " kbps" : "Sem Controle";
				$banda_up_novo = $banda_up_novo > 0 ? $banda_up_novo . " kbps" : "Sem Controle";
				$descricao .= self::$DESCRICAO_CONTA_ALT_UP . ": $banda_up_orig -> $banda_up_novo\n";
			}
			
			if($banda_dn_orig != $banda_dn_novo ) {			
				$banda_dn_orig = $banda_dn_orig > 0 ? $banda_dn_orig . " kbps" : "Sem Controle";
				$banda_dn_novo = $banda_dn_novo > 0 ? $banda_dn_novo . " kbps" : "Sem Controle";			
				$descricao .= self::$DESCRICAO_CONTA_ALT_DOWN . ": $banda_dn_orig -> $banda_dn_novo\n";
			}
			
			$dados = array("ipaddr" => $ipaddr, "natureza" => self::$NATUREZA_ALT_CONTA, "tipo" => self::$TIPO_INFO,
							"id_admin" => $id_admin, "id_conta" => $id_conta, 
							"id_cliente_produto" => $id_cliente_produto, "descricao" => $descricao);
			
			return($this->evtb_evento->insere($dados));
			
		}
		
		public function registraPagamentoFatura($ipaddr,$id_admin,$id_cobranca,$valor,$acrescimo,$desconto) {
			$dados = array("ipaddr" => $ipaddr, "id_admin" => $id_admin, "id_cobranca" => $id_cobranca, 
							"natureza" => self::$NATUREZA_PAG_FATURA, "tipo" => self::$TIPO_INFO);
			
			$descricao = "VALOR: $valor\n";
			
			if( $desconto ) {
				$descricao .= self::$DESCRICAO_PAG_FAT_DESC . ": " . $desconto . "\n";
			}

			if( $acrescimo ) {
				$descricao .= self::$DESCRICAO_PAG_FAT_ACRESC . ": " . $acrescimo . "\n";
			}
			
			$dados["descricao"] = $descricao;
			
			return($this->evtb_evento->insere($dados));
			
		}
		
		/**
		 * Obtem os eventos
		 */
		public function obtem($filtro=array(),$limite=20) {
			
			$eventos = $this->evtb_evento->obtem($filtro,"",$limite);
			
			$contas = VirtexModelo::factory("contas");
			$cobranca = VirtexModelo::factory("cobranca");
			$administradores = VirtexModelo::factory("administradores");
			
			for($i=0;$i<count($eventos);$i++) {
				if( $eventos[$i]["id_conta"] ) {
					$eventos[$i]["conta"] = $contas->obtemContaPeloId($eventos[$i]["id_conta"]);
				}
				
				if( $eventos[$i]["id_cliente_produto"] ) {
					$eventos[$i]["contrato"] = $cobranca->obtemContratoPeloId($eventos[$i]["id_cliente_produto"]);
				}
				
				if( $eventos[$i]["id_admin"] ) {
					$eventos[$i]["admin"] = $administradores->obtemAdminPeloId($eventos[$i]["id_admin"]);
				}
			}
			
			return($eventos);
		}
		
	}

?>
