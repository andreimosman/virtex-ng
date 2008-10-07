<?

	class PERSISTE_CFTB_NAS extends VirtexPersiste {
	
		public static $PADRAO_VIRTEX 		= '';
		public static $PADRAO_OUTROSERVIDOR 	= 'O';	
	
		public function __construct() {
			parent::__construct();
			
			$this->_campos 		= array("id_nas", "nome","ip","secret","tipo_nas","infoserver","padrao", "id_servidor");
			$this->_chave		= "id_nas";
			$this->_tabela		= "cftb_nas";
			$this->_ordem		= "nome";
			$this->_sequence	= "cfsq_id_nas";
			
		}
		
		public static function enumPadroes() {
			return(array(
						self::$PADRAO_VIRTEX => "Padrão Virtex",
						self::$PADRAO_OUTROSERVIDOR => "Outro Servidor/Mikrotik"
						)
					);
		}
		
		public static function enumTipoNas() {
			$_LS_TIPO_NAS = array(
					                "I" => "IP",
									"P" => "PPPoE",
									/** "H" => "HotSpot", */
									"R" => "RAS (Discado)"
								);
			return($_LS_TIPO_NAS);
		}
		
		public function obtemNasBandaLarga() {
		
			$filtro = array("tipo_nas" => "in:I::P::H");	// IP ou PPPoE ou HOTSPOT (future)
			
			$reg = $this->obtem($filtro,"tipo_nas,nome");
			
			$tipos = self::enumTipoNas();
			
			for($i=0;$i<count($reg);$i++) {
				// Tradução do tipo
				$reg[$i]["tp"] = $tipos[ $reg[$i]["tipo_nas"] ];
			}
			
			return($reg);
		
		}
	
	}
