
DELETE FROM adtb_usuario_privilegio;
DELETE FROM adtb_privilegio;

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

INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (1, '_CLIENTES', 'Clientes', true, true, 'Pesquisa/Visualiza��o de Clientes', 'Cadastro/Altera��o de Clientes');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (2, '_CLIENTES_RELATORIOS', 'Clientes::Relat�rios', true, false, 'Visualiza��o dos Relat�rios de Clientes', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (4, '_CLIENTES_DISCADO', 'Clientes::Contas::Discado', true, true, 'Visualiza��o dos Dados das Contas de Acesso Discado', 'Cadastro/Altera��o de Contas de Acesso Discado');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (3, '_CLIENTES_BANDALARGA', 'Clientes::Contas::Banda Larga', true, true, 'Visualiza��o dos Dados das Contas Banda Larga', 'Cadastro/Altera��o de Contas Banda Larga');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (5, '_CLIENTES_EMAIL', 'Clientes::Contas::Email', true, true, 'Visualiza��o dos Dados das Contas de Email', 'Cadastro/Altera��o de Contas de Email');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (6, '_CLIENTES_HOSPEDAGEM', 'Clientes::Contas::Hospedagem', true, true, 'Visualiza��o dos Dados das Contas de Hospedagem', 'Cadastro/Altera��o de Contas de Hospedagem');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (7, '_CLIENTES_CONTRATOS', 'Clientes::Contratos', true, true, 'Visualiza��o dos Contratos', 'Cadastro e Migra��o de Contratos');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (8, '_CLIENTES_CONTRATOS_CANCELAMENTO', 'Clientes::Contratos::Cancelamento', false, true, NULL, 'Cancelamento de Contrato');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (9, '_CLIENTES_FATURAS', 'Clientes::Faturas', true, true, 'Visualiza��o das Faturas', 'Amortiza��o das Faturas');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (10, '_CLIENTES_FATURAS_DESCONTO', 'Clientes::Faturas::Aplica��o de Desconto', false, true, NULL, 'Concess�o de descontos em faturas');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (11, '_COBRANCA_BLOQUEIOS', 'Cobranca::Bloqueios', true, true, 'Visualiza��o dos Clientes Pass�veis de Bloqueio', 'Execu��o dos bloqueios em clientes pass�veis de bloqueio');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (12, '_COBRANCA_AMORTIZACAO', 'Cobranca::Amortiza��o', false, true, NULL, 'Amortiza��o de Faturas');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (13, '_COBRANCA_GERAR_BOLETOS', 'Cobranca::Gerar Cobran�a/Boletos', false, true, NULL, 'Gerar lotes de cobran�a/boletos');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (14, '_COBRANCA_TROCA_ARQUIVOS', 'Cobranca::Troca de Arquivos', false, true, NULL, 'Download do arquivo de remessa/Upload do arquivo de retorno');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (15, '_COBRANCA_RELATORIOS', 'Cobranca::Relat�rios', true, true, 'Visualiza��o dos Relat�rios de Cobran�a', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (16, '_SUPORTE_GRAFICOS', 'Suporte::Gr�ficos', true, false, 'Visualiza��o dos Gr�ficos de Utiliza��o', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (17, '_SUPORTE_MONITORAMENTO', 'Suporte::Monitoramento', true, false, 'Visualiza��o do Monitoramento dos Elementos de Rede', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (18, '_SUPORTE_FERRAMENTAS_ARP', 'Suporte::Ferramentas::ARP', true, false, 'Visualiza��o das tabelas ARP', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (19, '_SUPORTE_FERRAMENTAS_PING', 'Suporte::Ferramentas::Ping', true, false, 'Testes de ICMP Ping', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (20, '_SUPORTE_FERRAMENTAS_CALCULADORA_IP', 'Suporte::Ferramentas::Calculadora IP', true, false, 'Calculadora IP', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (21, '_SUPORTE_RELATORIOS', 'Suporte::Relatorios', true, false, 'Visualiza��o dos Relat�rios de Suporte', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (22, '_CONFIGURACOES_EQUIPAMENTOS', 'Configura��es::Equipamentos', true, true, 'Visualiza��o das Informa��es dos Equipamentos', 'Cadastro/Altera��o dos dados de equipamentos');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (23, '_CONFIGURACOES_PREFERENCIAS', 'Configura��es::Prefer�ncias', true, true, 'Visualiza��o das Prefer�ncias', 'Altera��o das Prefer�ncias');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (24, '_CONFIGURACOES_PREFERENCIAS_REGISTRO', 'Configura��es::Prefer�ncias::Registro do Software', true, true, 'Visualiza��o do Registro do Software', 'Upload do Arquivo de Licen�a');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (25, '_CONFIGURACOES_RELATORIOS', 'Configura��es::Relat�rios', true, false, 'Visualiza��o dos Relat�rios de Configura��es', NULL);
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (26, '_ADMINISTRACAO_ADMINISTRADORES', 'Administra��o::Administradores', true, true, 'Visualiza��o dos Administradores', 'Cadastro/Altera��o dos Administradores e seus Privil�gios');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (27, '_ADMINISTRACAO_PLANOS', 'Administra��o::Planos', true, true, 'Visualiza��o dos Planos', 'Cadastro/Altera��o de Planos');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (28, '_ADMINISTRACAO_FERRAMENTAS_BACKUP', 'Administra��o::Ferramentas::Backup & Restore', false, true, NULL, 'Execu��o dos Backups');
INSERT INTO adtb_privilegio (id_priv, cod_priv, nome, tem_leitura, tem_gravacao, descricao_leitura, descricao_gravacao) VALUES (29, '_ADMINISTRACAO_RELATORIOS', 'Administra��o::Relat�rios', true, false, 'Visualiza��o dos Relat�rios de Administra��o', NULL);


--
-- PostgreSQL database dump complete
--
