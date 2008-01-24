<?

	/**
	 * Sistema p/ geração de gráficos individuais do sistema.
	 */
	class VAPPGraph extends VirtexApplication {

		protected $arquivoLog;
		protected $arquivoMRTG;
		protected $diretorioMRTG;

		protected $username;
		
		protected $tpl;

		public function __construct() {
			parent::__construct();
		
		}
		
		public function selfConfig() {
			$this->_shortopts = "U:";

			$this->arquivoLog 		= "etc/stats.log";
			$this->arquivoMRTG		= "etc/mrtg.users.cfg";
			$this->diretorioMRTG	= "/mosman/virtex/dados/estatisticas";
			
			$this->username 		= "";
			
			$this->tpl = new MTemplate("view/templates/backend");

		}
		

		protected function numero($num) {
			return( number_format((double)$num,0,"","") );
		}
		
		
		protected function geraArquivosIndividuais($contas) {

			/**
			 * Gera arquivo truncado para todas as contas
			 */
			for($i=0;$i<count($contas);$i++) {
				$arq = $this->diretorioMRTG ."/valog-" . strtolower( trim($contas[$i]["username"]) );
				$fd=fopen($arq,"w");
				if($fd) {
					fputs($fd,"0\n0\n0\n0");
					fclose($fd);
				}
			}

			$fd = fopen($this->arquivoLog,"r");
			while( ($linha=fgets($fd)) && !feof($fd) ) {
				@list($user,$up,$down) = explode(",",$linha);
				if( $user ) {
					$user = str_replace("/","_",$user);
					$arq = $this->diretorioMRTG ."/valog-" . strtolower(trim($user));
					$fc = fopen($arq,"w");
					if($fc) {
						$info = $this->numero($down) . "\n". $this->numero($up) . "\n". "0\n0";
						fputs($fc,$info);
						fclose($fc);
					}
				}
			}

		}
		
		
		public function executa() {

			for($i=0;$i<count($this->options);$i++) {
				switch($this->options[$i][0]) {
					case 'U':
						$this->username = $this->options[$i][1];
						break;
				}
			}
			
			/**
			 * Verificar as estatísticas do usuário no arquivo.
			 */
			if( $this->username ) {
				$fd = fopen($this->arquivoLog,"r");

				if(!$fd) {
					echo "0\n0\n0\n0";
					return(0);
				}

				/**
				* Varre o arquivo
				*/
				while( ($linha=fgets($fd)) && !feof($fd) ) {
					$linha = trim($linha);
					if( $linha ) {
						@list($user,$up,$down) = explode(",",$linha);
						if( trim($user) == trim($this->username) ) {
							echo ((int)$down) . "\n";
							echo ((int)$up) . "\n";
							echo "0\n0";
							return(0);
						}
					}

				}

				echo "0\n0\n0\n0";
				return(0);

			}
			
			/**********************************************************
			 *                                                        *
			 * COLETAR AS ESTATXSTICAS DE TODOS OS HOSTS CONFIGURADOS *
			 *                                                        *
			 **********************************************************/
			$equipamentos = VirtexModelo::factory('equipamentos');
			
			$servidores = $equipamentos->obtemListaServidores(true);
			// print_r($servidores);
			
			$comm = new VirtexCommClient(VirtexComm::$INC_GRAFICOS);
			

			$arqtmp = tempnam( "/tmp" , "vastat-" );

			$fh = fopen($arqtmp,"w");
			if(!$fh) die("Cannot open temp file '$arqtmp' for writting\n");

			foreach($servidores as $servidor) {
				if( trim($servidor["ip"]) && trim($servidor["porta"]) && trim($servidor["chave"]) && trim($servidor["usuario"]) && trim($servidor["senha"]) ) {
					if( !$comm->open($servidor["ip"],$servidor["porta"],$servidor["chave"],$servidor["usuario"],$servidor["senha"] ) ) {
						continue;
					}
					
					fwrite($fh,$comm->getStats());
					
					$comm->close();
					
				}			
			}

			// Fecha o arquivo.
			fclose($fh);

			// Copia pro arquivo utilizado pelo sistema para gerar as estatXsticas
			copy($arqtmp,$this->arquivoLog);

			// Apaga o arquivo temporário
			unlink($arqtmp);



			/**********************************************************
			 *                                                        *
			 * VARRER O BANCO DE DADOS E GERAR OS ARQUIVOS DO MRTG    *
			 *                                                        *
			 **********************************************************/
			
			$contas = VirtexModelo::factory('contas');
			
			$lista_nas = $equipamentos->obtemListaNAS();
			
			$listaContas = array();
			
			for($i=0;$i<count($lista_nas);$i++) {
				$lC = $contas->obtemContasBandaLarga($lista_nas[$i]["id_nas"]);
				for($x=0;$x<count($lC);$x++) {
					$lC[$x]["maxbytes"] = ($lC[$x]["download_kbps"]/8) * 1000;
					$lC[$x]["username"] = strtolower(trim($lC[$x]["username"]));
				}
				$listaContas = array_merge($listaContas,$lC);
				unset($lC);
			}
			
			// Cria o diretório do MRTG caso não exista.
			SOFreeBSD::installDir($this->diretorioMRTG);
			
			// Cria os arquivos individuais de estatísticas.
			$this->geraArquivosIndividuais($listaContas);
			
			// Gera o arquivo do MRTG.
			$fd = fopen($this->arquivoMRTG,"w");
			if( !$fd ) die("Cannot write '".$this->arquivoMRTG."'");
			$this->tpl->atribui("workdir",$this->diretorioMRTG);
			$this->tpl->atribui("contas",$listaContas);
			fwrite($fd,$this->tpl->obtemPagina("mrtg.users.conf"));
			fclose($fd);


			// Executa o MRTG
			SOFreeBSD::executa("/usr/local/bin/mrtg " . $this->arquivoMRTG);
		
		}
	
	}


?>
