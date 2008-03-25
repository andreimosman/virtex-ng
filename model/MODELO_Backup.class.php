<?

	/**
	 * Modelo de backup
	 */
	class MODELO_Backup extends VirtexModelo {

		protected $nbtb_backup;
		protected $nbtb_backup_arquivos;
		
		public static $OK = 'OK';
		public static $ERRO = 'ERR';
		public static $EM_ANDAMENTO = 'RUN';
		
		public function __construct() {
			parent::__construct();
			
			$this->nbtb_backup = VirtexPersiste::factory("nbtb_backup");
			$this->nbtb_backup_arquivos = VirtexPersiste::factory("nbtb_backup_arquivos");
			
		}
		
		public function novoBackup($id_admin="") {
			$dados = array("status" => self::$EM_ANDAMENTO, "datahora" => "=now");
			
			if( $id_admin ) {
				$dados["id_admin"] = $id_admin;	// Aplicativos de background não enviam id_admin
			}
			
			return($this->nbtb_backup->insere($dados));

		}
		
		public function adicionaArquivo($id_backup,$arquivo,$conteudo,$status) {
			$dados = array("id_backup" => $id_backup, "arquivo" => $arquivo, "conteudo" => $conteudo, "status" => $status);
			return($this->nbtb_backup_arquivos->insere($dados));
		}
		
		public function alteraStatusBackup($id_backup,$status) {
			$dados = array("status" => $status);
			$filtro = array("id_backup" => $id_backup);
			return($this->nbtb_backup->altera($dados,$filtro));
		}
		
		public function obtemInfoArquivo($registroArquivo) {
			$fs = (int)@filesize($registroArquivo["arquivo"]);
			$registroArquivo["filesize"] = $fs;

			if( $fs >= 1024*1024*1024 ) {
				$unidade = 'GB';
				$tamanho = $fs / 1024 / 1024 / 1024;
			} else if( $fs >= 1024*1024 ) {
				$unidade = 'MB';
				$tamanho = $fs / 1024 / 1024;
			} else if( $fs >= 1024 ) {
				$unidade = 'KB';
				$tamanho = $fs / 1024;
			} else {
				$unidade = 'B';
				$tamanho = $fs;
			}
			
			$registroArquivo["tamanho"] = number_format($tamanho,2,",",".") . ' ' . $unidade;

			$tmp = explode("/",$registroArquivo["arquivo"]);
			$nome = @$tmp[ count($tmp)-1 ];
			$registroArquivo["nome"] = $nome;

			$tmp = explode(".",$nome);
			array_shift($tmp);
			$registroArquivo["nome_sem_prefixo"] = implode(".",$tmp);
			
			$tmp = explode("/",$registroArquivo["arquivo"]);
			array_pop($tmp);
			$registroArquivo["path"] = implode("/",$tmp);

			unset($nome);
			unset($tamanho);
			unset($unidade);
			unset($tmp);

			
			return($registroArquivo);
		}
		
		public function obtemArquivos($id_backup) {
			return($this->nbtb_backup_arquivos->obtem(array("id_backup" => $id_backup)));
		}
		
		public function obtemArquivo($id_arquivo) {
			$arquivo = $this->nbtb_backup_arquivos->obtemUnico(array("id_arquivo" => $id_arquivo));
			return($this->obtemInfoArquivo($arquivo));
		}
		
		
		
		public function obtemBackups($limite=0) {
			$backups = $this->nbtb_backup->obtem(array(),"",$limite);
			$administradores = VirtexModelo::factory("administradores");
			
			for($i=0;$i<count($backups);$i++) {
			
			
				$backups[$i]["admin"] = $backups[$i]["id_admin"] ? $administradores->obtemAdminPeloId($backups[$i]["id_admin"]) : array();
			
				$backups[$i]["arquivos"] = $this->obtemArquivos($backups[$i]["id_backup"]);
				for( $x=0;$x<count($backups[$i]["arquivos"]);$x++) {
					$backups[$i]["arquivos"][$x] = $this->obtemInfoArquivo( $backups[$i]["arquivos"][$x] );				
				}
			}
			
			//echo "<pre>";
			//print_r($backups);
			//echo "</pre>";
			
			return($backups);
		
		}
		
		
		
	
	}
?>
