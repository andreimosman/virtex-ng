<?


	class VAPPUpdate extends VirtexApplication {

		protected $arquivoEstrutura;
	
		public function __construct() {
			parent::__construct();			
			$this->arquivoEstrutura = "var/update/struct.xml";
		}
		
		protected function selfConfig() {
			$this->_startdb 	= true;
			$this->_shortopts 	= "DGC";
			$this->_longopts	= array("database");
		}
		
		protected function obtemOpcao($opcao) {
			for($i=0;$i<count($this->options);$i++) {			
				if( @$this->options[$i][0] == $opcao) {
					return($this->options[$i]);
				}
			}
			
			return(null);
		}
		
		public function usage() {
			echo "USAGE:\n\n";
			echo "\tphp vtx-update.php\n";
			echo "\t\t-D|--database	Comandos relacionados ao banco de dados";
			echo "\n\n";
		}
		
		public function executa() {
			echo "-- VIRTEX UPDATE UTILITY\n";
			echo "-- ====== ====== =======\n\n";
			if( $this->obtemOpcao("D") || $this->obtemOpcao("--database") ) {
				$this->executaDatabase();
			}
						
		}
		
		protected function executaDatabase() {
			
			if( $this->obtemOpcao("G") ) {
				// Gerar arquivo XML do banco de dados em var/update/struct.xml";
				echo "Gerando estrutura XML em '".$this->arquivoEstrutura . "'... ";
				$this->gerarEstruturaXML();
				echo "OK\n";
			} elseif( $this->obtemOpcao("C") ) {
				// Comparar
				
				// echo "Comparando estrutura do Banco de Dados Atual com o arquivo XML '" . $this->arquivoEstrutura . "'... \n";
				// echo "---------------------------------------------------------------------------------------------\n\n";
				
				$this->compararEstruturaXML();
				
				// echo "---------------------------------------------------------------------------------------------\n\n";
				// echo "OK!!!\n\n";
				
			}
			
		}
		
		protected function gerarEstruturaXML() {
			$bd = MDatabase::getInstance();
			$bd->preparaReverso();
			$estrutura = $bd->obtemEstrutura();
			$xml = new MXMLUtils();
			$xstruct = $xml->a2x($estrutura,"database");
			
			$fd = fopen($this->arquivoEstrutura,"w");
			if( $fd ) {
				fwrite($fd,$xstruct,strlen($xstruct));
				fclose($fd);
				return(true);
			}
			return(false);

		}
		
		protected function compararEstruturaXML() {
			
			$xml = new MXMLUtils();
			
			$fd = @fopen($this->arquivoEstrutura,"r");
			if( !$fd ) {
				// ERRO
				return(-1);
			} else {
				 $texto = fread($fd,filesize($this->arquivoEstrutura));
				 fclose($fd);
			}

			$file_info  = $xml->x2a($texto,"database");
			unset($texto);
			
			$bd = MDatabase::getInstance();
			$bd->preparaReverso();

			$local_info = $bd->obtemEstrutura();
			
			$script = $bd->scriptModificacao($local_info,$file_info);
			echo $bd->script2text($script);
			
		}
		
	
	}



?>
