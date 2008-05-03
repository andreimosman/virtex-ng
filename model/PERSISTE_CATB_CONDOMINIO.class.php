<?

	class PERSISTE_CATB_CONDOMINIO extends VirtexPersiste {
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_condominio", "nome", "endereco", "complemento", "bairro", "id_cidade", "cep", "fone", "quantidade_edificios", "situacao", "data_instalacao", "data_ativacao", "sindico_nome", "sindico_fone", "zelador_nome", "zelador_fone", "observacoes");
			$this->_chave 		= "id_condominio";
			$this->_ordem 		= "nome";
			$this->_tabela		= "catb_condominio";
			$this->_sequence	= "casq_id_condominio";	
			$this->_filtros		= array("data_instalacao" => "date", "data_ativacao" => "date");
		}



		/**
		 * cadastrarCondominio
		 *		Faz o cadastro do condomínio e retorna o id do condominio cadastrado
		 */
		public function cadastrarCondominio ($nome, $endereco, $complemento, $bairro, $id_cidade, $cep, $fone, $quantidade_edificios, $situacao, $data_instalacao, $data_ativacao, $sindico_nome, $sindico_fone, $zelador_nome, $zelador_fone, $observacoes) {

			$dados = array (
				"nome" => $nome, 
				"endereco" => $endereco, 
				"complemento" => $complemento, 
				"bairro" => $bairro, 
				"id_cidade" => $id_cidade, 
				"cep" => $cep, 
				"fone" => $fone, 
				"quantidade_edificios" => $quantidade_edificios, 
				"situacao" => $situacao, 		
				"data_instalacao" => $data_instalacao,
				"data_ativacao" => $data_ativacao,
				"sindico_nome" => $sindico_nome, 
				"sindico_fone" => $sindico_fone, 
				"zelador_nome" => $zelador_nome, 
				"zelador_fone" => $zelador_fone, 
				"observacoes" => $observacoes
			);


			$retorno = $this->insere($dados);
			return $retorno;
		}
		

		/**
		 * alterarCondominio
		 *		Faz a alteração do condomínio desejado
		 */
		public function alterarCondominio ($id_condominio, $nome, $endereco, $complemento, $bairro, $id_cidade, $cep, $fone, $quantidade_edificios, $situacao, $data_instalacao, $data_ativacao, $sindico_nome, $sindico_fone, $zelador_nome, $zelador_fone, $observacoes) {

			$dados = array (
				"nome" => $nome, 
				"endereco" => $endereco, 
				"complemento" => $complemento, 
				"bairro" => $bairro, 
				"id_cidade" => $id_cidade, 
				"cep" => $cep, 
				"fone" => $fone, 
				"quantidade_edificios" => $quantidade_edificios, 
				"situacao" => $situacao, 		
				"data_instalacao" => $data_instalacao,
				"data_ativacao" => $data_ativacao,
				"sindico_nome" => $sindico_nome, 
				"sindico_fone" => $sindico_fone, 
				"zelador_nome" => $zelador_nome, 
				"zelador_fone" => $zelador_fone, 
				"observacoes" => $observacoes
			);

			$this->altera($dados, array("id_condominio" => $id_condominio));
		}
		
		
		/**
		 * obtemCondominio
		 *		Obtem o condomínio pelo ID. Caso o ID seja omitido, então será retornado uma listagem
		 */
		 
		public function obtemCondominio($id_condominio=NULL, $ativo=false) {

			$sql  = "SELECT ";
			$sql .= "	cd.id_condominio, cd.nome, cd.endereco, cd.complemento, cd.bairro, cd.id_cidade, cd.cep, ";
			$sql .= "	cd.fone, cd.quantidade_edificios, cd.situacao, cd.data_instalacao, cd.data_ativacao, cd.sindico_nome, ";
			$sql .= "	cd.sindico_fone, cd.zelador_nome, cd.zelador_fone, cd.observacoes, ci.cidade, ci.uf ";
			$sql .= "FROM ";
			$sql .= "	catb_condominio cd INNER JOIN  cftb_cidade ci ON cd.id_cidade = ci.id_cidade ";
			
			$extrasql = "";
			
			if($id_condominio) {
				$sql .= "WHERE ";
				$sql .= "	cd.id_condominio = $id_condominio ";
			}
			
			if($ativo) {
				if (!$extrasql) $extrasql .= "WHERE ";
				else $extrasql .= "AND ";
				$extrasql .= "cd.situacao IN ('I', 'P') ";
			}
			
			$sql .= $extrasql . " ORDER BY cd.nome ";
			
			//echo $sql . "<br><br>";
			
			if($id_condominio) {
				$retorno = $this->bd->obtemUnicoRegistro($sql);
			} else {
				$retorno = $this->bd->obtemRegistros($sql);
			}

			return $retorno;
		}
	}
		
?>