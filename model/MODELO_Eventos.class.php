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
		public static $NATUREZA_EST_FATURA  	= 'ESTORNO FATURA';
		public static $NATUREZA_ELI_CONTA		= 'ELIMINACAO CONTA';
		public static $NATUREZA_RENOVACAO		= 'RENOVACAO CONTRATO';
		
		public static $DESCRICAO_ERRO			= 'ERRO';
		public static $DESCRICAO_LOGIN_PRIMEIRO = 'PRIMEIRO LOGIN';
		public static $DESCRICAO_PAG_FAT_DESC	= 'PAGAMENTO COM DESCONTO';
		public static $DESCRICAO_PAG_FAT_ACRESC	= 'PAGAMENTO COM ACRESCIMO';
		public static $DESCRICAO_PAG_FAT_PARCIAL= 'PAGAMENTO PARCIAL REALIZADO';
		public static $DESCRICAO_PAG_FAT_REAG	= 'PAGAMENTO REAGENDADO';
		public static $DESCRICAO_ESTORNO_FATURA = 'ESTORNO DE FATURA';
		public static $DESCRICAO_CONTA_ALT_POP	= 'ALTERACAO DE POP';
		public static $DESCRICAO_CONTA_ALT_MAC  = 'ALTERACAO DE MAC';
		public static $DESCRICAO_CONTA_ALT_NAS	= 'ALTERACAO DE NAS';
		public static $DESCRICAO_CONTA_ALT_END	= 'ALTERACAO DE ENDERECO';
		public static $DESCRICAO_CONTA_ALT_STAT = 'ALTERACAO DE STATUS';
		public static $DESCRICAO_CONTA_ALT_UP	= 'ALTERACAO DE BANDA(UPLOAD)';
		public static $DESCRICAO_CONTA_ALT_DOWN	= 'ALTERACAO DE BANDA(DOWNLOAD)';
		public static $DESCRICAO_RENOVACAO		= 'RENOVAÇÃO DE CONTRATO';
		
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
				$retorno[self::$NATUREZA_RENOVACAO] = "Renovação de Contrato";

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
		
		public function registraPagamentoFatura($ipaddr,$id_admin,$id_cobranca,$valor,$acrescimo,$desconto, $amortizar, $reagendar,$id_cliente_produto, $id_conta) {
			$dados = array("ipaddr" => $ipaddr, "id_admin" => $id_admin, "id_cobranca" => $id_cobranca, "id_conta" => $id_conta, 
							"natureza" => self::$NATUREZA_PAG_FATURA, "id_cliente_produto" => $id_cliente_produto, "tipo" => self::$TIPO_INFO);
			///echo $id_cliente_produto;
			
			$descricao = "VALOR FATURA: $valor\n";
			
			if ($reagendar){
				$parcial = false;		
			}else{
				$parcial = true;
			}
			
			if( $desconto != "0.00") {
				$descricao .= self::$DESCRICAO_PAG_FAT_DESC . ": " . $desconto . "\n";
				$parcial = false;
			}
			if( $acrescimo  != "0.00" ) {
				$descricao .= self::$DESCRICAO_PAG_FAT_ACRESC . ": " . $acrescimo . "\n";
				$parcial = false;
			}
			
			$descricao .= "VALOR PAGO: $amortizar\n<br>";
			
			
			if (($amortizar != $valor) && ($parcial) && (!$reagendar) ){
				$descricao .= "<br><span style='color: red;'>" . self::$DESCRICAO_PAG_FAT_PARCIAL . "</span>";
			}elseif (($amortizar != $valor) && (!$parcial) && ($reagendar) ){
				$descricao .= "<br><span style='color: red;'>" . self::$DESCRICAO_PAG_FAT_REAG . "</span>";	
			}


			$dados["descricao"] = $descricao;
			
			return($this->evtb_evento->insere($dados));
			
		}


		public function registraEstornoFatura($ipaddr,$id_admin,$id_cobranca,$id_cliente_produto, $valor,$acrescimo,$desconto, $valor_pago, $data, $data_pagamento, $reagendamento) {
			$dados = array("ipaddr" => $ipaddr, "id_admin" => $id_admin, "id_cobranca" => $id_cobranca, 
							"natureza" => self::$NATUREZA_EST_FATURA, "id_cliente_produto" => $id_cliente_produto, "tipo" => self::$TIPO_INFO);
			///echo $id_cliente_produto;
			
			$descricao  = "VENCIMENTO...: $data\n";
			$descricao .= "VALOR FATURA.: $valor\n";
			$descricao .= "PAGAMENTO....: $data_pagamento\n";
			$descricao .= "VALOR PAGO...: $valor_pago\n";
			
			if( $reagendamento ) {
				$descricao .= "REAGENDAMENTO: $reagendamento\n";
			}

			$dados["descricao"] = $descricao;
			
			return($this->evtb_evento->insere($dados));
			
		}

		
		public function registraEliminacaoConta($id_conta, $id_cliente_produto,$ipaddr, $id_admin, $username="", $endereco="") {
		
			$descricao = "";
			if($endereco) $descricao .= "ENDERECO: " . $endereco . "\n";
			if($username) $descricao .= "USERNAME: " . $username . "\n";
		
			$dados = array (	"id_conta" => $id_conta,
								"id_cliente_produto" => $id_cliente_produto, 
								"id_admin" => $id_admin,
								"ipaddr" => $ipaddr,
								"tipo" => self::$TIPO_INFO,
								"natureza" => self::$NATUREZA_ELI_CONTA,
								"descricao" => $descricao
							);
			
			return ($this->evtb_evento->insere($dados));
		
		}
		
		public function registraRenovacaoContrato($id_cliente_produto,$ipaddr, $id_admin, $data_renovacao, $data_proxima_renovacao) {
			$descricao = "Data Base: " . MData::ISO_to_ptBR($data_renovacao) . "\nPróxima: " . MData::ISO_to_ptBR($data_proxima_renovacao);
			$dados = array (
								"id_cliente_produto" => $id_cliente_produto,
								"id_admin" => $id_admin,
								"ipaddr" => $ipaddr,
								"tipo" => self::$TIPO_INFO,
								"natureza" => self::$NATUREZA_RENOVACAO,
								"descricao" => $descricao
							);
			return($this->evtb_evento->insere($dados));
			
		}
		
		
		/**
		 * Obtem os eventos
		 */
		public function obtem($filtro=array(),$limite=20) {
		
			$limite = "";
			
			// $eventos = $this->evtb_evento->obtem($filtro,"",$limite);
			$eventos = $this->evtb_evento->obtemEventos($filtro,$limite);
			
			//$contas = VirtexModelo::factory("contas");
			//$cobranca = VirtexModelo::factory("cobranca");
			//$administradores = VirtexModelo::factory("administradores");
			//$clientes = VirtexModelo::factory("clientes");
			
			//echo "<pre>";
			
			for($i=0;$i<count($eventos);$i++) {

				$evt = $eventos[$i];
				
				// echo "I: $i\n"; 
			
				if( $eventos[$i]["id_conta"] ) {
				//	$eventos[$i]["conta"] = $contas->obtemContaPeloId($eventos[$i]["id_conta"]);
				}
				
				if( $eventos[$i]["id_cliente_produto"] ) {
				//	$eventos[$i]["contrato"] = $cobranca->obtemContratoPeloId($eventos[$i]["id_cliente_produto"]);
				//	$eventos[$i]["cliente"] = $clientes->obtemPeloId($eventos[$i]["contrato"]["id_cliente"]);

					$eventos[$i]["contrato"] = array();
					$eventos[$i]["cliente"] = array();				
				
				}
				
				if( $eventos[$i]["id_admin"] ) {
				//	$eventos[$i]["admin"] = $administradores->obtemAdminPeloId($eventos[$i]["id_admin"]);
					$eventos[$i]["admin"] = array();
				}
				
				if( $eventos[$i]["id_cobranca"] ) {
				//	$eventos[$i]["fatura"] = $cobranca->obtemFaturaPorIdCobranca($eventos[$i]["id_cobranca"]);
					$eventos[$i]["fatura"] = array();
				}
				
				
				// print_r($evt);
				
				while( list($vr,$vl) = each($eventos[$i]) ) {
					if( !is_array($vl) && $vl ) {
						// echo "$vr = $vl\n";
						
						/**
						 admin_
						 contrato_
						 cliente_
						 fatura_
						 */
						 
						$pat = "/^(admin|contrato|cliente|fatura)_(.*)$/";
						 
						preg_match($pat, $vr, $matches, PREG_OFFSET_CAPTURE);
						
						if( @$matches[1][0] && @$matches[2][0] ) {
							$eventos[$i][ $matches[1][0] ][ @$matches[2][0] ] = $vl;
							
							// echo " --- " . $matches[1][0] . "," . $matches[2][0] . " = " .$vl . "\n";
						}
						
						//echo "$vr: \n";
						//print_r($matches);
						//echo "------------\n";
					
					}
				}
				
				unset($evt);
				
			}
			
			//print_r($eventos);

			//echo "</pre>";
			
			//echo "<pre>"; 
			//
			//echo "</pre>"; 
			
			
			return($eventos);
		}
		
	}

?>
