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
		
			$permissao_monitoramento = $this->requirePrivLeitura("_SUPORTE_MONITORAMENTO",false);
			$this->_view->atribui("permissao_monitoramento", $permissao_monitoramento);

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
			$menuClientes->addItem("Inclusão", "admin-clientes.php?op=cadastro", $target);
			$menuClientes->addItem("Pesquisa", "admin-clientes.php?op=pesquisa", $target);

			// Submenu Relatórios
			$menuClientes->addSeparator();
			$menuClientesRelatorios = new MMenu();
			$menuClientesRelatorios->addItem("Lista Geral","admin-clientes.php?op=relatorios&relatorio=lista_geral",$target);
			$menuClientesRelatorios->addItem("Clientes por Cidade","admin-clientes.php?op=relatorios&relatorio=cliente_cidade",$target);
			$menuClientes->addSubmenu("Relatórios", $menuClientesRelatorios);

			// $menuClientes->addSeparator();
			// $menuClientes->addItem("Eliminar","admin-clientes.php?op=eliminar", $target);

			$menu->addSubmenu("Clientes",$menuClientes);
			

			//
			// Menu de Suporte
			//

			$menuSuporte = new MMenu();
			$menuSuporte->addItem("Helpdesk", "admin-suporte.php?op=graficos", $target);
			$menuSuporte->addItem("Gráficos", "admin-suporte.php?op=graficos", $target);
			$menuSuporte->addItem("Monitoramento", "admin-suporte.php?op=monitoramento", $target);

			$menuSuporte->addSeparator();
			$menuSuporte->addItem("Links Externos", "admin-suporte.php?op=links", $target);

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
			// Menu de Cadastro
			//

			$menuCadastro = new MMenu();

			$menuCadastro->addItem("Condomínios", "admin-cadastro.php?op=condominios&tela=listagem", $target);
			$menuCadastroEquipamentos = new MMenu();
			$menuCadastroEquipamentos->addItem("Servidores", "admin-cadastro.php?op=equipamentos&tela=servidores", $target);
			$menuCadastroEquipamentos->addItem("POPs", "admin-cadastro.php?op=equipamentos&tela=pops", $target);
			$menuCadastroEquipamentos->addItem("NAS", "admin-cadastro.php?op=equipamentos&tela=nas", $target);			
			$menuCadastro->addSubmenu("Equipamentos", $menuCadastroEquipamentos);

			$menuCadastro->addSeparator();
			$menuCadastro->addItem("Administradores", "admin-cadastro.php?op=administradores&tela=listagem", $target);
			
			
			$menuCadastro->addSeparator();
			$menuCadastro->addItem("Planos", "admin-cadastro.php?op=planos&tela=listagem", $target);
			$menuCadastro->addItem("Produtos", "admin-cadastro.php?op=planos&tela=listagem", $target);
			
			$menuCadastroServicos	= new MMenu();
			$menuCadastroServicos->addItem("Contratados (comprado)", "admin-cadastro.php?op=planos&tela=listagem", $target);
			$menuCadastroServicos->addItem("Fornecido (vendido)", "admin-cadastro.php?op=planos&tela=listagem", $target);
			$menuCadastro->addSubmenu("Serviços", $menuCadastroServicos);			


			
			$menuCadastro->addSeparator();
			$menuCadastro->addItem("Centros de Custo", "admin-cadastro.php?op=planos&tela=listagem", $target);
			$menuCadastro->addItem("Plano de Contas", "admin-cadastro.php?op=planos&tela=listagem", $target);
			
			$menuCadastro->addSeparator();
			$menuCadastroRelatorios	= new MMenu();
			$menuCadastroRelatorios->addItem("Carga por AP", "admin-cadastro.php?op=relatorios&relatorio=carga&tipo=ap", $target);
			$menuCadastroRelatorios->addItem("Carga por POP", "admin-cadastro.php?op=relatorios&relatorio=carga&tipo=pop", $target);
			$menuCadastroRelatorios->addItem("Carga por NAS", "admin-cadastro.php?op=relatorios&relatorio=carga&tipo=nas", $target);			
			$menuCadastro->addSubmenu("Relatórios", $menuCadastroRelatorios);


			$menu->addSubmenu("Cadastro", $menuCadastro);

			//
			// Menu de Financeiro
			//

			$menuFinanceiro = new MMenu();
			
			
			$menuFinanceiro->addItem("Contas a Pagar", "admin-financeiro.php?op=relatorios&relatorio=faturamento", $target);
			$menuFinanceiro->addItem("Contas a Receber", "admin-financeiro.php?op=relatorios&relatorio=faturamento", $target);

			$menuFinanceiro->addSeparator();
			$menuFinanceiroCobranca = new MMenu();
			$menuFinanceiroCobranca->addItem("Bloqueios", "admin-financeiro.php?op=bloqueios", $target);

			$menuFinanceiroCobranca->addSeparator();
			$menuFinanceiroCobranca->addItem("Amortização", "admin-financeiro.php?op=amortizacao", $target);

			//$menuFinanceiroCobranca->addSeparator();
			$menuFinanceiroCobranca->addItem("Gerar Cobrança/Boletos", "admin-financeiro.php?op=gerar_cobranca", $target);
			$menuFinanceiroCobranca->addItem("Troca de Arquivos", "admin-financeiro.php?op=arquivos",$target);

			$menuFinanceiroCobranca->addSeparator();
			$menuFinanceiroCobrancaRelatorios = new MMenu();
			$menuFinanceiroCobrancaRelatorios->addItem("Atrasos", "admin-financeiro.php?op=relatorios_cobranca&relatorio=atrasos", $target);
			$menuFinanceiroCobrancaRelatorios->addItem("Reagendamentos", "admin-financeiro.php?op=relatorios_cobranca&relatorio=reagendamentos", $target);
			$menuFinanceiroCobrancaRelatorios->addItem("Bloqueios e Desbloqueios", "admin-financeiro.php?op=relatorios_cobranca&relatorio=bloqueios_desbloqueios", $target);
			$menuFinanceiroCobrancaRelatorios->addSeparator();
			$menuFinanceiroCobrancaRelatorios->addItem("Emails de Cobrança", "admin-financeiro.php?op=relatorios_cobranca&relatorio=emails_cobranca", $target);
			$menuFinanceiroCobrancaRelatorios->addSeparator();
			$menuFinanceiroCobrancaRelatorios->addItem("Cortesias", "admin-financeiro.php?op=relatorios_cobranca&relatorio=cortesias", $target);
			$menuFinanceiroCobrancaRelatorios->addSeparator();
			$menuFinanceiroCobrancaRelatorios->addItem("Adesões", "admin-financeiro.php?op=relatorios_cobranca&relatorio=adesoes", $target);
			$menuFinanceiroCobrancaRelatorios->addItem("Cancelamentos", "admin-financeiro.php?op=relatorios_cobranca&relatorio=cancelamentos", $target);
			$menuFinanceiroCobrancaRelatorios->addItem("Evolução", "admin-financeiro.php?op=relatorios_cobranca&relatorio=evolucao", $target);
			$menuFinanceiroCobrancaRelatorios->addSeparator();
			$menuFinanceiroCobrancaRelatorios->addItem("Clientes por Produto", "admin-financeiro.php?op=relatorios_cobranca&relatorio=cliente_produto", $target);
			$menuFinanceiroCobrancaRelatorios->addItem("Clientes por Tipo Produto", "admin-financeiro.php?op=relatorios_cobranca&relatorio=cliente_tipo_produto", $target);
			$menuFinanceiroCobranca->addSubmenu("Relatórios",$menuFinanceiroCobrancaRelatorios);

			$menuFinanceiro->addSubmenu("Cobrança",$menuFinanceiroCobranca);
			
			$menuFinanceiroFaturamento = new MMenu();
			$menuFinanceiroFaturamentoRelatorios 	= new MMenu();
			$menuFinanceiroFaturamentoRelatorios->addItem("Faturamento Anual", "admin-financeiro.php?op=relatorios_faturamento&relatorio=faturamento", $target);
			$menuFinanceiroFaturamentoRelatorios->addItem("Faturamento por Produto ", "admin-financeiro.php?op=relatorios_faturamento&relatorio=por_produto", $target);
			$menuFinanceiroFaturamentoRelatorios->addItem("Faturamento por Período ", "admin-financeiro.php?op=relatorios_faturamento&relatorio=por_periodo", $target);
			$menuFinanceiroFaturamentoRelatorios->addSeparator();
			$menuFinanceiroFaturamentoRelatorios->addItem("Previsão de Faturamento", "admin-financeiro.php?op=relatorios_faturamento&relatorio=previsao", $target);
			$menuFinanceiroFaturamento->addSubmenu("Relatorios", $menuFinanceiroFaturamentoRelatorios);
			$menuFinanceiro->addSubmenu("Faturamento", $menuFinanceiroFaturamento);
			
			$menu->addSubmenu("Financeiro", $menuFinanceiro);
			
			$menuFinanceiro->addSeparator();
			$menuFinanceiroRelatorios = new MMenu();
			$menuFinanceiroRelatorios->addItem("Fluxo de Caixa", "admin-financeiro.php?op=relatorios&relatorio=faturamento", $target);
			$menuFinanceiro->addSubmenu("Relatórios", $menuFinanceiroRelatorios);
			
			
			//
			// Menu de Comercial
			//
			
			$menuComercial = new MMenu();
			$menuComercial->addItem("Promoções", "admin-administracao.php?op=altsenha", $target);
			
			$menu->addSubmenu("Comercial", $menuComercial);
			

			//
			// Menu de Administração
			//

			$menuAdministracao = new MMenu();
			$menuAdministracao->addItem("Alterar Minha Senha", "admin-administracao.php?op=altsenha", $target);
			
			$menuAdministracaoPreferencias 	= new MMenu();
			$menuAdministracaoPreferencias->addItem("Helpdesk","admin-administracao.php?op=preferencias&tela=helpdesk", $target);
			$menuAdministracaoPreferencias->addItem("Central do Assinante","admin-administracao.php?op=preferencias&tela=resumo", $target);
			$menuAdministracaoPreferencias->addItem("E-mails","admin-administracao.php?op=preferencias&tela=resumo", $target);
			$menuAdministracaoPreferencias->addSeparator();
			$menuAdministracaoPreferencias->addItem("Resumo","admin-administracao.php?op=preferencias&tela=resumo", $target);
			$menuAdministracaoPreferencias->addSeparator();
			$menuAdministracaoPreferencias->addItem("Preferências Gerais","admin-administracao.php?op=preferencias&tela=geral", $target);
			$menuAdministracaoPreferencias->addItem("Preferências Provedor","admin-administracao.php?op=preferencias&tela=provedor", $target);
			$menuAdministracaoPreferencias->addItem("Preferencias Cobrança","admin-administracao.php?op=preferencias&tela=cobranca", $target);
			$menuAdministracaoPreferencias->addSeparator();
			$menuAdministracaoPreferencias->addItem("Modelos de Contrato","admin-administracao.php?op=preferencias&tela=modelos", $target);
			$menuAdministracaoPreferencias->addSeparator();
			$menuAdministracaoPreferencias->addItem("Cidades","admin-administracao.php?op=preferencias&tela=cidades", $target);
			$menuAdministracaoPreferencias->addItem("Faixas de Banda","admin-administracao.php?op=preferencias&tela=banda", $target);
			$menuAdministracaoPreferencias->addItem("Monitoramento","admin-administracao.php?op=preferencias&tela=monitoramento", $target);
			$menuAdministracaoPreferencias->addItem("Links Externos","admin-administracao.php?op=preferencias&tela=links", $target);
			$menuAdministracaoPreferencias->addSeparator();
			$menuAdministracaoPreferencias->addItem("Registro do Software","admin-administracao.php?op=preferencias&tela=registro", $target);
			$menuAdministracao->addSubmenu("Preferências", $menuAdministracaoPreferencias);				


			$menuAdministracao->addSeparator();
			$menuAdministracaoFerramentas = new MMenu();
			$menuAdministracaoFerramentas->addItem("Envio de E-mails", "admin-administracao.php?op=ferramentas&ferramenta=backup", $target);
			$menuAdministracaoFerramentas->addItem("Backup & Restore", "admin-administracao.php?op=ferramentas&ferramenta=backup", $target);
			$menuAdministracao->addSubmenu("Ferramentas", $menuAdministracaoFerramentas);

			$menuAdministracaoBancoDados = new MMenu();
			$menuAdministracaoBancoDados->addItem("Eliminar Cliente", "admin-administracao.php?op=bancodados&eliminar=cliente", $target);
			$menuAdministracaoBancoDados->addItem("Eliminar Contrato", "admin-administracao.php?op=bancodados&eliminar=contrato", $target);
			$menuAdministracaoBancoDados->addItem("Eliminar Conta", "admin-administracao.php?op=bancodados&eliminar=conta", $target);
			$menuAdministracao->addSubmenu("Banco de Dados", $menuAdministracaoBancoDados);


			$menuAdministracao->addSeparator();
			$menuAdministracaoRelatorios = new MMenu();
			$menuAdministracaoRelatorios->addItem("Log de Eventos", "admin-administracao.php?op=relatorios&relatorio=eventos", $target);
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