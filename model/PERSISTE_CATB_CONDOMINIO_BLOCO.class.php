<?

	class PERSISTE_CATB_CONDOMINIO_BLOCO extends VirtexPersiste {
	
	
		public function __construct($bd=null) {
			parent::__construct($bd);
			$this->_campos	 	= array("id_condominio", "id_bloco", "nome", "numero_andares", "apartamentos_andar", "total_apartamentos", "situacao", "id_pop", "observacoes");
			$this->_chave 		= "id_bloco";			
			$this->_ordem 		= "nome";
			$this->_tabela		= "catb_condominio_bloco";
			$this->_sequence	= "casq_id_bloco";
			$this->_filtro 		= array("numero_andares" => "number", "apartamentos_andar" => "number", "total_apartamentos" => "number", "id_pop" => "number");
		}
		
		
		/**
		 * cadastrarCondominioBloco
		 *		Faz o cadastro de um bloco do condomínio e retorna o id do condominio cadastrado
		 */
		public function cadastrarCondominioBloco ($id_condominio, $nome, $numero_andares, $apartamentos_andar, $total_apartamentos, $situacao, $id_pop, $observacoes) {

			$dados = array (
	
			  "id_condominio" => $id_condominio,
			  "nome" => $nome,
			  "numero_andares" => $numero_andares, 
			  "apartamentos_andar" => $apartamentos_andar,
			  "total_apartamentos" => $total_apartamentos, 
			  "situacao" => $situacao,
			  "observacoes" => $observacoes,
	
			);
			
			if($id_pop) {
				$dados["id_pop"] = $id_pop;
			}

			$retorno = $this->insere($dados);
			return $retorno;
		}
		

		/**
		 * alterarCondominio
		 *		Faz a alteração de um bloco desejado do condomínio 
		 */
		public function alterarCondominioBloco ($id_bloco, $nome, $numero_andares, $apartamentos_andar, $total_apartamentos, $situacao, $id_pop, $observacoes) {

			$dados = array (
				"nome" => $nome,
				"situacao" => $situacao,
				"observacoes" => $observacoes
			);
			
			
			$dados["numero_andares"] = $numero_andares ? $numero_andares : NULL;
			$dados["apartamentos_andar"] = $apartamentos_andar ? $apartamentos_andar : NULL;
			$dados["total_apartamentos"] = $total_apartamentos ? $total_apartamentos : NULL;
			$dados["id_pop"] = $id_pop ? $id_pop : NULL;

			$this->altera($dados, array("id_bloco" => $id_bloco));
		}
		
		
		/**
		 * obtemCondominio
		 *		Obtem o condomínio pelo ID do condominio o/ou pelo ID do Bloco. Caso o ID seja omitido, então será retornado uma listagem
		 */
		public function obtemCondominioBloco($id_condominio=NULL, $id_bloco=NULL, $ativo=NULL) {

			$sql = "SELECT ";
			$sql .= "	cb.id_condominio, cb.id_bloco, cb.nome, cb.numero_andares, cb.apartamentos_andar ";
			$sql .= "	, cb.total_apartamentos, cb.situacao, cb.id_pop, cb.observacoes, pop.nome as popnome ";
			$sql .= "FROM ";
			$sql .= "	catb_condominio_bloco cb LEFT OUTER JOIN cftb_pop pop ON cb.id_pop = pop.id_pop ";
			
			$extrasql = "";
			
			if($id_condominio) {
				$extrasql .= "WHERE ";
				$extrasql .= "	cb.id_condominio = $id_condominio ";
			}
			
			if($id_bloco) {
				if($extrasql) $extrasql .= " AND ";
				else $extrasql .= "WHERE ";
				
				$extrasql .= "	cb.id_bloco = $id_bloco ";
			}
			
			if($ativo) {
				if($extrasql) $extrasql .= " AND ";
				else $extrasql .= "WHERE ";
								
				$extrasql .= "	cb.situacao IN ('I', 'P') ";
			}
			
			$sql .= $extrasql . " ORDER BY cb.nome ";
			
			//echo $sql;
			
			if($id_bloco) {
				$retorno = $this->bd->obtemUnicoRegistro($sql);
			} else {
				$retorno = $this->bd->obtemRegistros($sql);
			}
			
			return $retorno;
		}		

	}
		
?>


  