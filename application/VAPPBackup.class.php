<?


	class VAPPBackup extends VirtexApplication {
	
		protected function selfConfig() {
			$this->_startdb 	= true;
			$this->_shortopts 	= "DECR:"; // -D (dados) -S (estrutura) -C (configuração) -R <requestor> (Administrador que executou a operação).
		}
		
		public function executa() {
		
			// print_r($this->bd);
			
			
			// $bdUser = $this->bd->
			
			
			$dsn = MDatabase::parseDSN( @$this->_cfg->config["DB"]["dsn"] ? @$this->_cfg->config["DB"]["dsn"] : @$this->_cfg->config["geral"]["dsn"]);
			
			
			$bd = MDatabase::getInstance();
			
			//$bd->dumpDatabase("virtex");
			// SistemaOperacional::pgDump($dsn["hostspec"],$dsn["username"],$dsn["password"],$dsn["database"],$arquivoOutput,$opcoes="-Da");

			
			// Cria o diretório para backup
			$path = "var/backup/" . date("Y-m");
			$this->SO->installDir($path,777);
			
			$prefixoArquivo = date("Ymd-His.");
			
			$backup = VirtexModelo::factory("backup");
			
			$requestor = $this->obtemOpcao("R");
			if( $requestor ) $requestor = $requestor[1];
			
			if( $this->obtemOpcao("D") || $this->obtemOpcao("E") || $this->obtemOpcao("C") ) {
				$id_backup = $backup->novoBackup($requestor);
			} else {
				return(0);
			}
					
			if( $this->obtemOpcao("D") ) {
				$arquivo =  $prefixoArquivo . "dados.sql";
				$this->SO->pgDump("localhost",$dsn["username"],$dsn["password"],$dsn["database"],$path."/".$arquivo,"-Da");
				$this->SO->gzip($path."/".$arquivo);
				$backup->adicionaArquivo($id_backup,$path."/".$arquivo.".gz","D",MODELO_Backup::$OK);
			}
			
			if( $this->obtemOpcao("E") ) {
				$arquivo =  $prefixoArquivo . "estrutura.sql";
				$this->SO->pgDump("localhost",$dsn["username"],$dsn["password"],$dsn["database"],$path."/".$arquivo,"-s");
				$this->SO->gzip($path."/".$arquivo);
				$backup->adicionaArquivo($id_backup,$path."/".$arquivo.".gz","E",MODELO_Backup::$OK);
			}
			
			if( $this->obtemOpcao("C") ) {
				$arquivo =  $prefixoArquivo . "app_config.tar";
				$this->SO->tar($path."/".$arquivo,"etc");
				$this->SO->gzip($path."/".$arquivo);
				$backup->adicionaArquivo($id_backup,$path."/".$arquivo.".gz","C",MODELO_Backup::$OK);
			}
			
			$backup->alteraStatusBackup($id_backup,MODELO_Backup::$OK);
		
		}
	
	}

