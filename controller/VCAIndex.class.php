<?

	class VCAIndex extends VirtexControllerAdmin {
	
		public function __construct() {
			parent::__construct();
		}
	
		protected function init() {
			// Inicializações da SuperClasse
			parent::init();
			$this->_view = VirtexViewAdmin::factory("index");

		}

		protected function executa() {

			$target = "conteudo";

			$menu = new MMenu(0);

			//
			// Home
			//
			$menu->addItem("Home", "admin-home.php", $target);
			
			//
			// Menu de Clientes
			//
			$menuClientes = new MMenu();
			$menuClientes->addItem("Cadastro", "admin-clientes.php?op=cadastro", $target);
			$menuClientes->addItem("Pesquisa", "admin-clientes.php?op=pesquisa", $target);

			// Submenu Relatórios
			$menuClientes->addSeparator();
			$menuClientesRelatorios = new MMenu();
			$menuClientesRelatorios->addItem("Lista Geral","admin-clientes.php?op=relatorios&relatorio=lista_geral",$target);
			$menuClientesRelatorios->addItem("Clientes por Cidade","admin-clientes.php?op=relatorios&relatorio=cliente_cidade",$target);
			$menuClientes->addSubmenu("Relatórios", $menuClientesRelatorios);

			$menuClientes->addSeparator();
			$menuClientes->addItem("Eliminar","admin-clientes.php?op=eliminar", $target);

			$menu->addSubmenu("Clientes",$menuClientes);

			//
			// Menu de Cobrança
			//

			$menuCobranca = new MMenu();
			$menuCobranca->addItem("Bloqueios", "admin-cobranca.php?op=bloqueios", $target);

			$menuCobranca->addSeparator();
			$menuCobranca->addItem("Amortização", "admin-cobranca.php?op=amortizacao", $target);

			$menuCobranca->addSeparator();
			$menuCobranca->addItem("Gerar Cobrança/Boletos", "admin-cobranca.php?op=gerar_cobranca", $target);
			$menuCobranca->addItem("Troca de Arquivos", "admin-cobranca.php?op=arquivos",$target);

			$menuCobranca->addSeparator();
			$menuCobrancaRelatorios = new MMenu();
			$menuCobrancaRelatorios->addItem("Atrasos", "admin-cobranca.php?op=relatorios&relatorio=atrasos", $target);
			$menuCobrancaRelatorios->addItem("Reagendamentos", "admin-cobranca.php?op=relatorios&relatorio=reagendamentos", $target);
			$menuCobrancaRelatorios->addItem("Bloqueios e Desbloqueios", "admin-cobranca.php?op=relatorios&relatorio=bloqueios_desbloqueios", $target);
			$menuCobrancaRelatorios->addSeparator();
			$menuCobrancaRelatorios->addItem("Emails de Cobrança", "admin-cobranca.php?op=relatorios&relatorio=emails_cobranca", $target);
			$menuCobrancaRelatorios->addSeparator();
			$menuCobrancaRelatorios->addItem("Cortesias", "admin-cobranca.php?op=relatorios&relatorio=cortesias", $target);
			$menuCobrancaRelatorios->addSeparator();
			$menuCobrancaRelatorios->addItem("Adesões", "admin-cobranca.php?op=relatorios&relatorio=adesoes", $target);
			$menuCobrancaRelatorios->addItem("Cancelamentos", "admin-cobranca.php?op=relatorios&relatorio=cancelamentos", $target);
			$menuCobrancaRelatorios->addItem("Evolução", "admin-cobranca.php?op=relatorios&relatorio=evolucao", $target);
			$menuCobrancaRelatorios->addSeparator();
			$menuCobrancaRelatorios->addItem("Clientes por Produto", "admin-cobranca.php?op=relatorios&relatorio=cliente_produto", $target);
			$menuCobrancaRelatorios->addItem("Clientes por Tipo Produto", "admin-cobranca.php?op=relatorios&relatorio=cliente_tipo_produto", $target);
			$menuCobranca->addSubmenu("Relatórios",$menuCobrancaRelatorios);

			$menu->addSubmenu("Cobrança",$menuCobranca);


			//
			// Menu de Suporte
			//

			$menuSuporte = new MMenu();
			$menuSuporte->addItem("Gráficos", "admin-suporte.php?op=graficos", $target);
			$menuSuporte->addItem("Monitoramento", "admin-suporte.php?op=monitoramento", $target);

			$menuSuporte->addSeparator();
			$menuSuporteFerramentas = new MMenu();
			$menuSuporteFerramentas->addItem("ARP", "admin-suporte.php?op=ferramentas&ferramenta=arp", $target);
			$menuSuporteFerramentas->addItem("PING", "admin-suporte.php?op=ferramentas&ferramenta=ping", $target);
			$menuSuporteFerramentas->addSeparator();
			$menuSuporteFerramentas->addItem("Calculadora IP", "admin-suporte.php?op=ferramentas&ferramenta=ipcalc", $target);
			$menuSuporte->addSubmenu("Ferramentas", $menuSuporteFerramentas);

			$menuSuporte->addSeparator();
			$menuSuporteRelatorios = new MMenu();
			$menuSuporteRelatorios->addItem("Clientes por Banda","admin-suporte.php?op=relatorios&relatorio=banda", $target);
			$menuSuporteRelatorios->addItem("Clientes sem MAC","admin-suporte.php?op=relatorios&relatorio=cliente_sem_mac", $target);
			$menuSuporte->addSubmenu("Relatórios", $menuSuporteRelatorios);		

			$menu->addSubmenu("Suporte",$menuSuporte);

			//
			// Menu de Configurações
			//

			$menuConfiguracoes = new MMenu();

			$menuConfiguracoesEquipamentos 	= new MMenu();
			$menuConfiguracoesEquipamentos->addItem("Servidores", "admin-configuracoes.php?op=equipamentos&tela=servidores", $target);
			$menuConfiguracoesEquipamentos->addItem("POPs", "admin-configuracoes.php?op=equipamentos&tela=pops", $target);
			$menuConfiguracoesEquipamentos->addItem("NAS", "admin-configuracoes.php?op=equipamentos&tela=nas", $target);
			$menuConfiguracoes->addSubmenu("Equipamentos", $menuConfiguracoesEquipamentos);

			$menuConfiguracoesPreferencias 	= new MMenu();
			$menuConfiguracoesPreferencias->addItem("Resumo","admin-configuracoes.php?op=preferencias&tela=resumo", $target);
			$menuConfiguracoesPreferencias->addSeparator();
			$menuConfiguracoesPreferencias->addItem("Preferências Gerais","admin-configuracoes.php?op=preferencias&tela=geral", $target);
			$menuConfiguracoesPreferencias->addItem("Preferências Provedor","admin-configuracoes.php?op=preferencias&tela=provedor", $target);
			$menuConfiguracoesPreferencias->addItem("Preferencias Cobrança","admin-configuracoes.php?op=preferencias&tela=cobranca", $target);
			$menuConfiguracoesPreferencias->addSeparator();
			$menuConfiguracoesPreferencias->addItem("Modelos de Contrato","admin-configuracoes.php?op=preferencias&tela=modelo", $target);
			$menuConfiguracoesPreferencias->addSeparator();
			$menuConfiguracoesPreferencias->addItem("Cidades","admin-configuracoes.php?op=preferencias&tela=cidades", $target);
			$menuConfiguracoesPreferencias->addItem("Faixas de Banda","admin-configuracoes.php?op=preferencias&tela=banda", $target);
			$menuConfiguracoesPreferencias->addItem("Monitoramento","admin-configuracoes.php?op=preferencias&tela=monitoramento", $target);
			$menuConfiguracoesPreferencias->addItem("Links Externos","admin-configuracoes.php?op=preferencias&tela=links", $target);
			$menuConfiguracoesPreferencias->addSeparator();
			$menuConfiguracoesPreferencias->addItem("Registro do Software","admin-configuracoes.php?op=preferencias&tela=registro", $target);
			$menuConfiguracoes->addSubmenu("Preferências", $menuConfiguracoesPreferencias);

			$menuConfiguracoes->addSeparator();
			$menuConfiguracoesRelatorios	= new MMenu();
			$menuConfiguracoesRelatorios->addItem("Carga por AP", "admin-configuracoes.php?op=relatorios&relatorio=carga&tipo=ap", $target);
			$menuConfiguracoesRelatorios->addItem("Carga por POP", "admin-configuracoes.php?op=relatorios&relatorio=carga&tipo=pop", $target);
			$menuConfiguracoesRelatorios->addItem("Carga por NAs", "admin-configuracoes.php?op=relatorios&relatorio=carga&tipo=nas", $target);
			$menuConfiguracoesRelatorios->addItem("Clientes por AP", "admin-configuracoes.php?op=relatorios&relatorio=clientes_ap", $target);
			$menuConfiguracoes->addSubmenu("Relatórios", $menuConfiguracoesRelatorios);

			$menu->addSubmenu("Configurações", $menuConfiguracoes);

			//
			// Menu de Administração
			//

			$menuAdministracao = new MMenu();
			$menuAdministracao->addItem("Alterar Minha Senha", "admin-administracao.php?op=altsenha", $target);
			$menuAdministracao->addItem("Administradores", "admin-administracao.php?op=administradores&tela=listagem", $target);
			$menuAdministracao->addSeparator();
			$menuAdministracao->addItem("Planos", "admin-administracao.php?op=planos&tela=listagem", $target);

			$menuAdministracao->addSeparator();
			$menuAdministracaoFerramentas = new MMenu();
			$menuAdministracaoFerramentas->addItem("Backup & Restore", "admin-administracao.php?op=backup", $target);
			$menuAdministracao->addSubmenu("Ferramentas", $menuAdministracaoFerramentas);

			$menuAdministracao->addSeparator();
			$menuAdministracaoRelatorios = new MMenu();
			$menuAdministracaoRelatorios->addItem("Log dos Administradores", "admin-administracao.php?op=relatorios&relatorio=logs_admin", $target);
			$menuAdministracao->addSubmenu("Relatórios",$menuAdministracaoRelatorios);

			$menu->addSubmenu("Administração", $menuAdministracao);

			//
			// Logout
			//
			$menu->addItem("Sair", "admin-login.php", "_top");
			

			// $menu->printRecursive();
			$this->_view->atribui("jsMenuItens", $menu->jsOutput());

		
		
		
		
		
		}
		
	}

?>
