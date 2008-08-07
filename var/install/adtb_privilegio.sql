--
-- PostgreSQL database dump
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: adtb_privilegio; Type: TABLE DATA; Schema: public; Owner: virtex
--

INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (2, '_CLIENTES_RELATORIOS', 'Clientes::Relatórios', true, false, 'Visualização dos Relatórios de Clientes', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (4, '_CLIENTES_DISCADO', 'Clientes::Contas::Discado', true, true, 'Visualização dos Dados das Contas de Acesso Discado', 'Cadastro/Alteração de Contas de Acesso Discado');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (3, '_CLIENTES_BANDALARGA', 'Clientes::Contas::Banda Larga', true, true, 'Visualização dos Dados das Contas Banda Larga', 'Cadastro/Alteração de Contas Banda Larga');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (5, '_CLIENTES_EMAIL', 'Clientes::Contas::Email', true, true, 'Visualização dos Dados das Contas de Email', 'Cadastro/Alteração de Contas de Email');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (6, '_CLIENTES_HOSPEDAGEM', 'Clientes::Contas::Hospedagem', true, true, 'Visualização dos Dados das Contas de Hospedagem', 'Cadastro/Alteração de Contas de Hospedagem');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (7, '_CLIENTES_CONTRATOS', 'Clientes::Contratos', true, true, 'Visualização dos Contratos', 'Cadastro e Migração de Contratos');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (9, '_CLIENTES_FATURAS', 'Clientes::Faturas', true, true, 'Visualização das Faturas', 'Amortização das Faturas');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (32, '_CLIENTES_FATURAS_REAGENDAMENTO', 'Clientes::Faturas::Reagendamento', false, true, NULL, 'Reagendamento de Faturas');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (10, '_CLIENTES_FATURAS_DESCONTO', 'Clientes::Faturas::Aplicação de Desconto', false, true, NULL, 'Concessão de descontos em faturas');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (34, '_CLIENTES_FATURAS_ESTORNO', 'Clientes::Faturas::Estornar', false, true, NULL, 'Estorno de Faturas');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (33, '_CLIENTES_EMAILS_CANCELADOS', 'Clientes::Emails Cancelados', true, true, 'Visualizar os e-mails cancelados', 'Recuperar e-mails cancelados');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (16, '_SUPORTE_GRAFICOS', 'Suporte::Gráficos', true, false, 'Visualização dos Gráficos de Utilização', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (17, '_SUPORTE_MONITORAMENTO', 'Suporte::Monitoramento', true, false, 'Visualização do Monitoramento dos Elementos de Rede', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (18, '_SUPORTE_FERRAMENTAS_ARP', 'Suporte::Ferramentas::ARP', true, false, 'Visualização das tabelas ARP', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (19, '_SUPORTE_FERRAMENTAS_PING', 'Suporte::Ferramentas::Ping', true, false, 'Testes de ICMP Ping', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (20, '_SUPORTE_FERRAMENTAS_CALCULADORA_IP', 'Suporte::Ferramentas::Calculadora IP', true, false, 'Calculadora IP', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (21, '_SUPORTE_RELATORIOS', 'Suporte::Relatorios', true, false, 'Visualização dos Relatórios de Suporte', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (28, '_ADMINISTRACAO_FERRAMENTAS_BACKUP', 'Administração::Ferramentas::Backup & Restore', false, true, NULL, 'Execução dos Backups');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (29, '_ADMINISTRACAO_RELATORIOS', 'Administração::Relatórios', true, false, 'Visualização dos Relatórios de Administração', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (30, '_SUPORTE_LINKS', 'Suporte::Links', true, false, 'Visualização/Acesso aos links externos', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (8, '_CLIENTES_CONTRATOS_CANCELAMENTO', 'Clientes::Contratos::Cancelamento', true, true, NULL, 'Cancelamento de Contrato');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (1, '_CLIENTES', 'Clientes', false, true, 'Pesquisa/Visualização de Clientes', 'Cadastro/Alteração de Clientes');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (11, '_FINANCEIRO_COBRANCA_BLOQUEIOS', 'Financeiro::Cobranca::Bloqueios', true, true, 'Visualização dos Clientes Passíveis de Bloqueio', 'Execução dos bloqueios em clientes passíveis de bloqueio');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (12, '_FINANCEIRO_COBRANCA_AMORTIZACAO', 'Financeiro::Cobranca::Amortização', false, true, NULL, 'Amortização de Faturas');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (13, '_FINANCEIRO_COBRANCA_GERAR_BOLETOS', 'Financeiro::Cobranca::Gerar Cobrança/Boletos', false, true, NULL, 'Gerar lotes de cobrança/boletos');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (14, '_FINANCEIRO_COBRANCA_TROCA_ARQUIVOS', 'Financeiro::Cobranca::Troca de Arquivos', false, true, NULL, 'Download do arquivo de remessa/Upload do arquivo de retorno');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (31, '_FINANCEIRO_COBRANCA_RENOVACAO', 'Financeiro::Cobranca::Renovação de Contratos', true, true, 'Visualização da Listagem para Renovação', 'Renovação de Contratos');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (15, '_FINANCEIRO_COBRANCA_RELATORIOS', 'Financeiro::Cobranca::Relatórios', true, true, 'Visualização dos Relatórios de Cobrança', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (22, '_CADASTRO_EQUIPAMENTOS', 'Cadastro::Equipamentos', true, true, 'Visualização das Informações dos Equipamentos', 'Cadastro/Alteração dos dados de equipamentos');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (23, '_ADMINISTRACAO_PREFERENCIAS', 'Administração::Preferências', true, true, 'Visualização das Preferências', 'Alteração das Preferências');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (24, '_ADMINISTRACAO_PREFERENCIAS_REGISTRO', 'Administração::Preferências::Registro do Software', true, true, 'Visualização do Registro do Software', 'Upload do Arquivo de Licença');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (27, '_CADASTRO_PLANOS', 'Cadastro::Planos', true, true, 'Visualização dos Planos', 'Cadastro/Alteração de Planos');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (26, '_CADASTRO_ADMINISTRADORES', 'Cadastro::Administradores', true, true, 'Visualização dos Administradores', 'Cadastro/Alteração dos Administradores e seus Privilégios');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (25, '_CADASTRO_RELATORIOS', 'Cadastro::Relatórios', true, false, 'Visualização dos Relatórios de Configurações', NULL);


--
-- PostgreSQL database dump complete
--

