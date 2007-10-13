--
-- PostgreSQL database dump
--

SET client_encoding = 'LATIN1';
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

--
-- Name: plpgsql_call_handler(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION plpgsql_call_handler() RETURNS language_handler
    AS '$libdir/plpgsql', 'plpgsql_call_handler'
    LANGUAGE c;


ALTER FUNCTION public.plpgsql_call_handler() OWNER TO postgres;

--
-- Name: plpgsql_validator(oid); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION plpgsql_validator(oid) RETURNS void
    AS '$libdir/plpgsql', 'plpgsql_validator'
    LANGUAGE c;


ALTER FUNCTION public.plpgsql_validator(oid) OWNER TO postgres;

--
-- Name: plpgsql; Type: PROCEDURAL LANGUAGE; Schema: public; Owner: 
--

CREATE TRUSTED PROCEDURAL LANGUAGE plpgsql HANDLER plpgsql_call_handler VALIDATOR plpgsql_validator;


--
-- Name: adsq_id_admin; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE adsq_id_admin
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.adsq_id_admin OWNER TO virtex;

--
-- Name: adsq_id_priv; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE adsq_id_priv
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.adsq_id_priv OWNER TO virtex;

SET default_tablespace = '';

SET default_with_oids = true;

--
-- Name: adtb_admin; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE adtb_admin (
    id_admin smallint NOT NULL,
    admin character varying(20) NOT NULL,
    senha character varying(64) NOT NULL,
    status character(1),
    nome character varying(40),
    email character varying(255),
    primeiro_login boolean
);


ALTER TABLE public.adtb_admin OWNER TO virtex;

--
-- Name: adtb_privilegio; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE adtb_privilegio (
    id_priv smallint NOT NULL,
    cod_priv character varying(60),
    nome character varying(60)
);


ALTER TABLE public.adtb_privilegio OWNER TO virtex;

--
-- Name: adtb_usuario_privilegio; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE adtb_usuario_privilegio (
    id_admin smallint NOT NULL,
    id_priv smallint NOT NULL,
    pode_gravar boolean
);


ALTER TABLE public.adtb_usuario_privilegio OWNER TO virtex;

--
-- Name: aged_rdtb_accounting; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE aged_rdtb_accounting (
    session_id character varying(64),
    username character varying(64),
    login timestamp without time zone,
    logout timestamp without time zone,
    tempo numeric(30,0),
    caller_id character varying(30),
    nas character varying(128),
    framed_ip_address character varying(20),
    terminate_cause character varying(128),
    bytes_in numeric(30,0),
    bytes_out numeric(30,0)
);


ALTER TABLE public.aged_rdtb_accounting OWNER TO virtex;

--
-- Name: bktb_arquivos; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE bktb_arquivos (
    id_backup integer NOT NULL,
    data_backup date,
    tipo_backup character varying(50),
    arquivo_backup character varying(150),
    status_backup character varying(4)
);


ALTER TABLE public.bktb_arquivos OWNER TO virtex;

--
-- Name: bktb_backup; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE bktb_backup (
    id_backup serial NOT NULL,
    data_backup date,
    status_backup character varying(4),
    admin smallint,
    operador_backup character varying(2),
    data timestamp without time zone DEFAULT now()
);


ALTER TABLE public.bktb_backup OWNER TO virtex;

--
-- Name: blsq_carne_nossonumero; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE blsq_carne_nossonumero
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.blsq_carne_nossonumero OWNER TO virtex;

--
-- Name: blsq_nosso_numero_banco; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE blsq_nosso_numero_banco
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.blsq_nosso_numero_banco OWNER TO virtex;

--
-- Name: cbsq_id_carne; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cbsq_id_carne
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cbsq_id_carne OWNER TO virtex;

--
-- Name: cbsq_id_cliente_produto; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cbsq_id_cliente_produto
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cbsq_id_cliente_produto OWNER TO virtex;

--
-- Name: cbsq_id_cobranca; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cbsq_id_cobranca
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cbsq_id_cobranca OWNER TO virtex;

--
-- Name: cbsq_id_endereco_cobranca; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cbsq_id_endereco_cobranca
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cbsq_id_endereco_cobranca OWNER TO virtex;

--
-- Name: cbtb_carne; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cbtb_carne (
    id_carne smallint NOT NULL,
    data_geracao date,
    status character varying(2) DEFAULT 'A'::character varying,
    id_cliente_produto smallint,
    valor numeric(30,2),
    vigencia smallint,
    id_cliente smallint
);


ALTER TABLE public.cbtb_carne OWNER TO virtex;

--
-- Name: cbtb_cliente_produto; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cbtb_cliente_produto (
    id_cliente_produto smallint NOT NULL,
    id_cliente smallint NOT NULL,
    id_produto smallint NOT NULL,
    dominio character varying(255),
    excluido boolean DEFAULT false
);


ALTER TABLE public.cbtb_cliente_produto OWNER TO virtex;

--
-- Name: cbtb_cliente_venda; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cbtb_cliente_venda (
    id_venda smallint NOT NULL,
    quantidade integer,
    data date,
    id_cliente_produto smallint NOT NULL
);


ALTER TABLE public.cbtb_cliente_venda OWNER TO virtex;

--
-- Name: cbtb_contrato; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cbtb_contrato (
    id_cliente_produto smallint NOT NULL,
    data_contratacao date,
    vigencia smallint,
    data_renovacao date,
    valor_contrato numeric(7,2),
    id_cobranca smallint NOT NULL,
    status character(2),
    tipo_produto character(2),
    valor_produto numeric(7,2),
    num_emails smallint,
    quota_por_conta integer,
    tx_instalacao numeric(7,2),
    comodato boolean,
    valor_comodato numeric(7,2),
    desconto_promo numeric(7,2),
    periodo_desconto smallint,
    hosp_dominio boolean,
    hosp_franquia_em_mb smallint,
    hosp_valor_mb_adicional numeric(7,2),
    disc_franquia_horas smallint,
    disc_permitir_duplicidade boolean,
    disc_valor_hora_adicional numeric(7,2),
    bl_banda_upload_kbps smallint,
    bl_banda_download_kbps smallint,
    bl_franquia_trafego_mensal_gb smallint,
    bl_valor_trafego_adicional_gb numeric(7,2),
    cod_banco smallint,
    carteira character varying(100),
    agencia integer,
    num_conta integer,
    convenio integer,
    cc_vencimento character varying(5),
    cc_numero character varying(25),
    cc_operadora character(2),
    db_banco smallint,
    db_agencia smallint,
    db_conta smallint,
    vencimento smallint,
    carencia smallint,
    data_alt_status date,
    id_produto smallint,
    nome_produto character varying(30),
    descricao_produto text,
    disponivel boolean,
    vl_email_adicional numeric(7,2),
    permitir_outros_dominios boolean,
    email_anexado boolean,
    numero_contas smallint,
    valor_estatico boolean,
    da_cod_banco smallint,
    da_carteira character varying(100),
    da_convenio integer,
    da_agencia integer,
    da_num_conta integer,
    bl_cod_banco smallint,
    bl_carteira character varying(100),
    bl_agencia integer,
    bl_num_conta integer,
    bl_convenio integer,
    id_forma_pagamento smallint,
    pagamento character varying(3),
    migrado_para smallint,
    migrado_em date,
    migrado_por character varying(64)
);


ALTER TABLE public.cbtb_contrato OWNER TO virtex;

--
-- Name: cbtb_endereco_cobranca; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cbtb_endereco_cobranca (
    id_endereco_cobranca smallint NOT NULL,
    id_cliente_produto smallint,
    endereco character varying(50),
    complemento character varying(50),
    bairro character varying(30),
    id_cidade smallint,
    cep character(10),
    id_cliente smallint
);


ALTER TABLE public.cbtb_endereco_cobranca OWNER TO virtex;

--
-- Name: cbtb_faturas; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cbtb_faturas (
    id_cliente_produto smallint NOT NULL,
    data date NOT NULL,
    descricao text,
    valor numeric(7,2) NOT NULL,
    status character varying(2),
    observacoes text,
    reagendamento date,
    pagto_parcial numeric(7,2),
    data_pagamento date,
    desconto numeric(7,2),
    acrescimo numeric(7,2),
    valor_pago numeric(7,2),
    id_cobranca serial NOT NULL,
    cod_barra character varying(50),
    anterior boolean DEFAULT false,
    id_carne smallint,
    nosso_numero character varying(100),
    linha_digitavel character varying(150),
    nosso_numero_banco numeric(17,0),
    tipo_retorno integer,
    email_aviso boolean DEFAULT false,
    id_forma_pagamento smallint
);


ALTER TABLE public.cbtb_faturas OWNER TO virtex;

--
-- Name: cbtb_produtos_venda; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cbtb_produtos_venda (
    id_venda smallint NOT NULL,
    id_produto smallint NOT NULL,
    quantidade smallint,
    data date
);


ALTER TABLE public.cbtb_produtos_venda OWNER TO virtex;

--
-- Name: cbtb_venda; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cbtb_venda (
    id_venda smallint NOT NULL,
    id_cliente_produto smallint NOT NULL,
    valor numeric(7,2),
    data date,
    admin smallint
);


ALTER TABLE public.cbtb_venda OWNER TO virtex;

--
-- Name: cfsq_id_cobranca; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cfsq_id_cobranca
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cfsq_id_cobranca OWNER TO virtex;

--
-- Name: cfsq_id_nas; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cfsq_id_nas
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cfsq_id_nas OWNER TO virtex;

--
-- Name: cfsq_id_pop; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cfsq_id_pop
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cfsq_id_pop OWNER TO virtex;

--
-- Name: cfsq_id_rede; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cfsq_id_rede
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cfsq_id_rede OWNER TO virtex;

--
-- Name: cfsq_servidor_id_servidor; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cfsq_servidor_id_servidor
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cfsq_servidor_id_servidor OWNER TO virtex;

--
-- Name: cftb_backup; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_backup (
    path_backup character varying(150),
    ftp character varying(255),
    usuario character varying(100),
    senha character varying(150)
);


ALTER TABLE public.cftb_backup OWNER TO virtex;

--
-- Name: cftb_banda; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_banda (
    banda character varying(255),
    id smallint NOT NULL
);


ALTER TABLE public.cftb_banda OWNER TO virtex;

--
-- Name: cftb_cidade; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_cidade (
    id_cidade smallint NOT NULL,
    uf character(2) NOT NULL,
    cidade character varying(50),
    disponivel boolean DEFAULT false
);


ALTER TABLE public.cftb_cidade OWNER TO virtex;

--
-- Name: cftb_forma_pagamento; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_forma_pagamento (
    id_cobranca smallint NOT NULL,
    nome_cobranca character varying(20),
    disponivel boolean
);


ALTER TABLE public.cftb_forma_pagamento OWNER TO virtex;

--
-- Name: cftb_ip; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_ip (
    ipaddr inet NOT NULL
);


ALTER TABLE public.cftb_ip OWNER TO virtex;

--
-- Name: cftb_ip_externo; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_ip_externo (
    ip_externo inet NOT NULL,
    id_nas smallint NOT NULL
);


ALTER TABLE public.cftb_ip_externo OWNER TO virtex;

--
-- Name: cftb_links; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_links (
    id_link serial NOT NULL,
    titulo character varying(255),
    url character varying(255),
    target character varying(50),
    descricao text
);


ALTER TABLE public.cftb_links OWNER TO virtex;

--
-- Name: cftb_nas; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_nas (
    id_nas smallint NOT NULL,
    nome character varying(20) NOT NULL,
    ip inet NOT NULL,
    secret character varying(64),
    tipo_nas character(1),
    infoserver character varying(255),
    padrao character(1),
    id_servidor integer
);


ALTER TABLE public.cftb_nas OWNER TO virtex;

--
-- Name: cftb_nas_rede; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_nas_rede (
    rede cidr NOT NULL,
    id_nas smallint NOT NULL
);


ALTER TABLE public.cftb_nas_rede OWNER TO virtex;

--
-- Name: cftb_pop; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_pop (
    id_pop smallint NOT NULL,
    nome character varying(40) NOT NULL,
    info text,
    tipo character varying(2),
    id_pop_ap smallint,
    status character(1) DEFAULT 'A'::bpchar,
    ipaddr inet,
    snmp_rw_com character varying(255),
    infoserver character varying(255),
    snmp_versao character varying(20),
    snmp_ro_com character varying(255),
    ativar_snmp boolean DEFAULT false,
    ativar_monitoramento boolean DEFAULT false,
    id_servidor integer
);


ALTER TABLE public.cftb_pop OWNER TO virtex;

--
-- Name: cftb_preferencias; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_preferencias (
    id_provedor smallint NOT NULL,
    dominio_padrao character varying(150),
    nome character varying(255),
    localidade character varying(255),
    radius_server inet,
    hosp_server inet,
    hosp_ns1 inet,
    hosp_ns2 inet,
    hosp_uid smallint,
    hosp_gid smallint,
    mail_server inet,
    mail_uid smallint,
    mail_gid smallint,
    pop_host character varying(255),
    smtp_host character varying(255),
    hosp_base character varying(255),
    tx_juros smallint,
    multa smallint,
    dia_venc smallint,
    carencia smallint,
    cnpj character(25),
    cod_banco smallint,
    agencia smallint,
    num_conta smallint,
    observacoes text,
    carteira smallint,
    convenio smallint,
    pagamento character(3)
);


ALTER TABLE public.cftb_preferencias OWNER TO virtex;

--
-- Name: cftb_rede; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_rede (
    rede cidr NOT NULL,
    tipo_rede character(1),
    id_rede smallint
);


ALTER TABLE public.cftb_rede OWNER TO virtex;

--
-- Name: cftb_servidor; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_servidor (
    id_servidor integer NOT NULL,
    hostname character varying(255),
    ip inet,
    porta integer,
    chave character varying(64),
    usuario character varying(32),
    senha character varying(32),
    disponivel boolean
);


ALTER TABLE public.cftb_servidor OWNER TO virtex;

--
-- Name: cftb_uf; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cftb_uf (
    uf character(2) NOT NULL,
    estado character varying(50)
);


ALTER TABLE public.cftb_uf OWNER TO virtex;

--
-- Name: clsq_id_cliente; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE clsq_id_cliente
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.clsq_id_cliente OWNER TO virtex;

--
-- Name: clsq_id_cliente_produto; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE clsq_id_cliente_produto
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.clsq_id_cliente_produto OWNER TO virtex;

--
-- Name: clsq_id_conta; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE clsq_id_conta
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.clsq_id_conta OWNER TO virtex;

--
-- Name: cltb_cliente; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cltb_cliente (
    id_cliente smallint NOT NULL,
    data_cadastro date,
    nome_razao character varying(50),
    tipo_pessoa character(1),
    rg_inscr character varying(20),
    rg_expedicao character varying(20),
    cpf_cnpj character varying(25),
    email character varying(255),
    endereco character varying(50),
    complemento character varying(50),
    id_cidade smallint,
    cidade character varying(35),
    estado character(2),
    cep character(10),
    bairro character varying(30),
    fone_comercial character varying(30),
    fone_residencial character varying(30),
    fone_celular character varying(30),
    contato character varying(20),
    banco character varying(20),
    conta_corrente character varying(10),
    agencia character varying(10),
    dia_pagamento smallint,
    ativo boolean,
    obs text,
    provedor boolean DEFAULT false,
    excluido boolean DEFAULT false,
    info_cobranca boolean DEFAULT false,
    expedicao date
);


ALTER TABLE public.cltb_cliente OWNER TO virtex;

--
-- Name: cnsq_id_conta; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cnsq_id_conta
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cnsq_id_conta OWNER TO virtex;

--
-- Name: cnsq_id_endereco_instalacao; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cnsq_id_endereco_instalacao
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cnsq_id_endereco_instalacao OWNER TO virtex;

--
-- Name: cntb_conta; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cntb_conta (
    username character varying(30) NOT NULL,
    dominio character varying(255) NOT NULL,
    tipo_conta character varying(2) NOT NULL,
    senha character varying(64),
    id_cliente smallint,
    id_cliente_produto smallint NOT NULL,
    id_conta smallint,
    senha_cript character varying(64),
    conta_mestre boolean DEFAULT true,
    status character(1) DEFAULT 'A'::bpchar,
    observacoes text
);


ALTER TABLE public.cntb_conta OWNER TO virtex;

--
-- Name: cntb_conta_bandalarga; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cntb_conta_bandalarga (
    status character(1),
    id_pop smallint NOT NULL,
    tipo_bandalarga character(1),
    ipaddr inet,
    rede cidr,
    upload_kbps smallint,
    download_kbps smallint,
    mac macaddr,
    id_nas smallint NOT NULL,
    ip_externo inet,
    bl_status character(1)
)
INHERITS (cntb_conta);


ALTER TABLE public.cntb_conta_bandalarga OWNER TO virtex;

--
-- Name: cntb_conta_discado; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cntb_conta_discado (
    foneinfo character varying(64)
)
INHERITS (cntb_conta);


ALTER TABLE public.cntb_conta_discado OWNER TO virtex;

--
-- Name: cntb_conta_email; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cntb_conta_email (
    quota integer,
    email character varying(255),
    redirecionar_para character varying(255)
)
INHERITS (cntb_conta);


ALTER TABLE public.cntb_conta_email OWNER TO virtex;

--
-- Name: cntb_conta_hospedagem; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cntb_conta_hospedagem (
    tipo_hospedagem character(1),
    uid integer,
    gid integer,
    home character varying(255),
    shell character varying(255),
    dominio_hospedagem character varying(255)
)
INHERITS (cntb_conta);


ALTER TABLE public.cntb_conta_hospedagem OWNER TO virtex;

--
-- Name: cntb_endereco_instalacao; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cntb_endereco_instalacao (
    id_endereco_instalacao smallint NOT NULL,
    id_conta smallint,
    endereco character varying(50),
    complemento character varying(50),
    bairro character varying(30),
    id_cidade smallint,
    cep character(10),
    id_cliente smallint
);


ALTER TABLE public.cntb_endereco_instalacao OWNER TO virtex;

--
-- Name: cxsq_autenticacao; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cxsq_autenticacao
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cxsq_autenticacao OWNER TO virtex;

--
-- Name: cxsq_id_fluxo; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE cxsq_id_fluxo
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.cxsq_id_fluxo OWNER TO virtex;

--
-- Name: cxtb_fluxo; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE cxtb_fluxo (
    id_fluxo smallint NOT NULL,
    tipo_movimentacao character(1),
    valor numeric(7,2),
    tipo_origem character(1),
    origem character varying(128),
    data_registro date,
    data_compensacao date,
    especie character(1),
    cheque_nome character varying(50),
    cheque_banco numeric(3,0),
    cheque_agencia numeric(4,0),
    cheque_conta numeric(8,0),
    cheque_serie numeric(8,0),
    cheque_numero numeric(8,0),
    cheque_pre date,
    boleto_linhadigitavel character varying(64),
    boleto_codigodebarras character varying(64),
    transf_codigo character varying(64),
    cartao_autorizacao character varying(64),
    id_cobranca integer,
    autenticacao integer
);


ALTER TABLE public.cxtb_fluxo OWNER TO virtex;

--
-- Name: dominio; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE dominio (
    dominio character varying(255) NOT NULL,
    id_cliente smallint,
    provedor boolean DEFAULT false,
    status character(1),
    dominio_provedor boolean
);


ALTER TABLE public.dominio OWNER TO virtex;

--
-- Name: id_venda_seq; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE id_venda_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.id_venda_seq OWNER TO virtex;

--
-- Name: lgsq_id_exclusao; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE lgsq_id_exclusao
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.lgsq_id_exclusao OWNER TO virtex;

--
-- Name: lgsq_id_processo; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE lgsq_id_processo
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.lgsq_id_processo OWNER TO virtex;

--
-- Name: lgsq_id_remessa; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE lgsq_id_remessa
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.lgsq_id_remessa OWNER TO virtex;

--
-- Name: lgtb_administradores; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_administradores (
    id_admin smallint NOT NULL,
    data timestamp without time zone DEFAULT now(),
    operacao character varying(255),
    valor_original character varying(100),
    valor_alterado character varying(100),
    username character varying(100),
    id_fatura smallint,
    tipo_conta character varying(2),
    ip inet,
    id_cliente_produto smallint,
    extras character varying(255)
);


ALTER TABLE public.lgtb_administradores OWNER TO virtex;

--
-- Name: lgtb_backup; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_backup (
    id_backup smallint NOT NULL,
    admin character varying(50),
    data date,
    nome_arq character varying(100),
    status character(2),
    bd_dados boolean,
    bd_struct boolean,
    cfg_so boolean,
    cfg_vtx boolean,
    cfg_utilitarios boolean,
    obs text
);


ALTER TABLE public.lgtb_backup OWNER TO virtex;

--
-- Name: lgtb_backup_id_backup; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE lgtb_backup_id_backup
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.lgtb_backup_id_backup OWNER TO virtex;

--
-- Name: lgtb_bloqueio_automatizado; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_bloqueio_automatizado (
    id_processo smallint NOT NULL,
    id_cliente_produto smallint NOT NULL,
    data_hora timestamp without time zone DEFAULT now() NOT NULL,
    tipo character varying(1) NOT NULL,
    admin character varying(20),
    auto_obs character varying(50),
    admin_obs text
);


ALTER TABLE public.lgtb_bloqueio_automatizado OWNER TO virtex;

--
-- Name: lgtb_contas_excluidas; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_contas_excluidas (
    id_excluida serial NOT NULL,
    id_cliente character varying(100),
    id_cliente_produto character varying(100),
    id_conta character varying(100),
    username character varying(30),
    tipo_conta character varying(2),
    dominio character varying(255),
    id_pop character varying(100),
    tipo_bandalarga character varying(1),
    ipaddr character varying(100),
    rede character varying(100),
    upload_kbps character varying(100),
    download_kbps character varying(100),
    status character(1),
    mac character varying(100),
    id_nas character varying(100),
    ip_externo character varying(100),
    quota character varying(100),
    email character varying(255),
    foneinfo character varying(64),
    tipo_hospedagem character(1),
    senha_cript character varying(64),
    uid character varying(100),
    gid character varying(100),
    home character varying(255),
    shell character varying(255),
    dominio_hospedagem character varying(255),
    senha character varying(64),
    conta_mestre character varying(20),
    observacoes text,
    admin character varying(255),
    data_exclusao date DEFAULT now()
);


ALTER TABLE public.lgtb_contas_excluidas OWNER TO virtex;

--
-- Name: lgtb_emails_cobranca; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_emails_cobranca (
    data_envio timestamp without time zone NOT NULL,
    id_cliente_produto smallint NOT NULL,
    data date NOT NULL,
    email character varying(255),
    username character varying(255),
    tipo_conta character(2),
    id_cliente smallint NOT NULL,
    hora_envio timestamp without time zone
);


ALTER TABLE public.lgtb_emails_cobranca OWNER TO virtex;

--
-- Name: lgtb_erros_db; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_erros_db (
    data timestamp without time zone DEFAULT now() NOT NULL,
    query text,
    mensagem character varying(255)
);


ALTER TABLE public.lgtb_erros_db OWNER TO virtex;

--
-- Name: lgtb_exclusao; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_exclusao (
    id_exclusao smallint NOT NULL,
    admin character varying(255),
    data timestamp without time zone,
    tipo character varying(3),
    id_excluido smallint,
    observacao text
);


ALTER TABLE public.lgtb_exclusao OWNER TO virtex;

--
-- Name: lgtb_reagendamento; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_reagendamento (
    id_reagendamento serial NOT NULL,
    id_cliente_produto smallint NOT NULL,
    data date NOT NULL,
    admin smallint,
    data_reagendamento date DEFAULT now(),
    data_para_reagendamento date
);


ALTER TABLE public.lgtb_reagendamento OWNER TO virtex;

--
-- Name: lgtb_remessas; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_remessas (
    id_remessa smallint NOT NULL,
    id_cliente_produto smallint,
    data_remessa timestamp without time zone,
    data_vencimento date,
    valor numeric(7,2),
    periodo smallint,
    mes smallint,
    ano smallint
);


ALTER TABLE public.lgtb_remessas OWNER TO virtex;

--
-- Name: lgtb_renovacao; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_renovacao (
    id_cliente_produto smallint NOT NULL,
    data_renovacao date,
    data_proxima_renovacao date,
    historico text,
    id_renovacao serial NOT NULL
);


ALTER TABLE public.lgtb_renovacao OWNER TO virtex;

--
-- Name: lgtb_restore; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_restore (
    id_restore serial NOT NULL,
    arquivo_restore character varying(255),
    data_restore timestamp without time zone,
    admin character varying(50),
    status_restore character varying(4)
);


ALTER TABLE public.lgtb_restore OWNER TO virtex;

--
-- Name: lgtb_retorno; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_retorno (
    id_arquivo serial NOT NULL,
    nome_arquivo character varying(50),
    tamanho smallint,
    data timestamp without time zone,
    qtde_registros smallint,
    status character(1),
    nra character(2),
    nrpr character(2),
    nrsc character(2),
    nrpe character(2),
    admin character varying(20),
    tipo_retorno character varying(20),
    agencia integer,
    dv_agencia integer,
    cedente integer,
    dv_cedente integer,
    convenente integer,
    nome_empresa character varying(255),
    seq_retorno integer
);


ALTER TABLE public.lgtb_retorno OWNER TO virtex;

--
-- Name: lgtb_retorno_faturas; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_retorno_faturas (
    nsr integer,
    data_pagamento date,
    data_credito date,
    valor_recebido numeric(7,2),
    codigo_barras character varying(50),
    valor_tarifa numeric(7,2),
    status character(2),
    id_arquivo integer NOT NULL,
    motivo character varying(100),
    agencia integer,
    dv_agencia integer,
    cedente integer,
    dv_cedente integer,
    convenente integer,
    nome_empresa character varying(20),
    seq_retorno integer
);


ALTER TABLE public.lgtb_retorno_faturas OWNER TO virtex;

--
-- Name: lgtb_status_conta; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE lgtb_status_conta (
    id_processo serial NOT NULL,
    id_cliente_produto smallint NOT NULL,
    username character varying(30) NOT NULL,
    dominio character varying(255) NOT NULL,
    tipo_conta character varying(2) NOT NULL,
    data_hora timestamp without time zone,
    id_admin smallint,
    ip_admin inet,
    operacao character varying(255),
    cod_operacao smallint
);


ALTER TABLE public.lgtb_status_conta OWNER TO virtex;

--
-- Name: pfsq_id_forma_pagamento; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE pfsq_id_forma_pagamento
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.pfsq_id_forma_pagamento OWNER TO virtex;

--
-- Name: pfsq_id_modelo_contrato; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE pfsq_id_modelo_contrato
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.pfsq_id_modelo_contrato OWNER TO virtex;

--
-- Name: pftb_forma_pagamento; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE pftb_forma_pagamento (
    id_forma_pagamento integer NOT NULL,
    descricao text,
    codigo_banco integer,
    carteira character varying(10),
    agencia integer,
    dv_agencia character varying(1),
    conta integer,
    dv_conta character varying(1),
    convenio integer,
    cnpj_agencia_cedente character varying(4),
    codigo_cedente character varying(8),
    operacao_cedente character varying(3),
    tipo_cobranca character varying(2),
    disponivel boolean,
    carne boolean,
    nossonumero_inicial double precision,
    nossonumero_final double precision,
    nossonumero_atual double precision
);


ALTER TABLE public.pftb_forma_pagamento OWNER TO virtex;

--
-- Name: pftb_modelo_contrato; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE pftb_modelo_contrato (
    id_modelo_contrato integer NOT NULL,
    descricao character varying(50),
    data_upload timestamp without time zone DEFAULT now(),
    disponivel boolean,
    tipo character varying(2),
    padrao boolean
);


ALTER TABLE public.pftb_modelo_contrato OWNER TO virtex;

--
-- Name: pftb_preferencia_cobranca; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE pftb_preferencia_cobranca (
    id_provedor smallint NOT NULL,
    tx_juros smallint,
    multa smallint,
    dia_venc smallint,
    carencia smallint,
    cod_banco integer,
    carteira character varying(10),
    agencia integer,
    num_conta integer,
    convenio integer,
    observacoes text,
    pagamento character(3),
    path_contrato character varying(255),
    cod_banco_boleto integer,
    carteira_boleto character varying(10),
    agencia_boleto integer,
    conta_boleto integer,
    convenio_boleto integer,
    enviar_email boolean,
    mensagem_email text,
    email_remetente character varying(255),
    cnpj_ag_cedente character varying(4),
    codigo_cedente character varying(8),
    operacao_cedente character varying(3),
    div_agencia character varying(1),
    dias_minimo_cobranca smallint
);


ALTER TABLE public.pftb_preferencia_cobranca OWNER TO virtex;

--
-- Name: pftb_preferencia_geral; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE pftb_preferencia_geral (
    id_provedor smallint NOT NULL,
    dominio_padrao character varying(150),
    nome character varying(255),
    radius_server inet,
    hosp_server inet,
    hosp_ns1 inet,
    hosp_ns2 inet,
    hosp_uid integer,
    hosp_gid integer,
    mail_server inet,
    mail_uid integer,
    mail_gid integer,
    pop_host character varying(255),
    smtp_host character varying(255),
    hosp_base character varying(255),
    agrupar smallint,
    email_base character varying(255)
);


ALTER TABLE public.pftb_preferencia_geral OWNER TO virtex;

--
-- Name: pftb_preferencia_monitoracao; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE pftb_preferencia_monitoracao (
    id_provedor smallint NOT NULL,
    emails text,
    exibir_monitor boolean,
    alerta_sonoro boolean,
    celular boolean,
    nro_celular smallint,
    num_pings integer
);


ALTER TABLE public.pftb_preferencia_monitoracao OWNER TO virtex;

--
-- Name: pftb_preferencia_provedor; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE pftb_preferencia_provedor (
    id_provedor smallint NOT NULL,
    endereco character varying(255),
    localidade character varying(255),
    cep character varying(20),
    cnpj character(25),
    fone character varying(30)
);


ALTER TABLE public.pftb_preferencia_provedor OWNER TO virtex;

--
-- Name: prsq_id_produto; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE prsq_id_produto
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.prsq_id_produto OWNER TO virtex;

--
-- Name: prtb_produto; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE prtb_produto (
    id_produto smallint NOT NULL,
    nome character varying(30),
    descricao text,
    tipo character(2),
    valor numeric(7,2),
    disponivel boolean,
    num_emails smallint,
    quota_por_conta integer,
    vl_email_adicional numeric(7,2),
    permitir_outros_dominios boolean,
    email_anexado boolean,
    numero_contas smallint,
    comodato boolean,
    valor_comodato numeric(7,2),
    desconto_promo numeric(7,2),
    periodo_desconto smallint,
    tx_instalacao numeric(7,2),
    valor_estatico boolean DEFAULT true
);


ALTER TABLE public.prtb_produto OWNER TO virtex;

--
-- Name: prtb_produto_bandalarga; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE prtb_produto_bandalarga (
    banda_upload_kbps smallint,
    banda_download_kbps smallint,
    franquia_trafego_mensal_gb smallint,
    valor_trafego_adicional_gb numeric(7,2)
)
INHERITS (prtb_produto);


ALTER TABLE public.prtb_produto_bandalarga OWNER TO virtex;

--
-- Name: prtb_produto_discado; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE prtb_produto_discado (
    franquia_horas smallint,
    permitir_duplicidade boolean DEFAULT false,
    valor_hora_adicional numeric(7,2)
)
INHERITS (prtb_produto);


ALTER TABLE public.prtb_produto_discado OWNER TO virtex;

--
-- Name: prtb_produto_hospedagem; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE prtb_produto_hospedagem (
    dominio boolean,
    franquia_em_mb smallint,
    valor_mb_adicional numeric(7,2)
)
INHERITS (prtb_produto);


ALTER TABLE public.prtb_produto_hospedagem OWNER TO virtex;

--
-- Name: rdsq_id_accounting; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE rdsq_id_accounting
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.rdsq_id_accounting OWNER TO virtex;

--
-- Name: rdtb_accounting; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE rdtb_accounting (
    session_id character varying(64) NOT NULL,
    username character varying(64),
    login timestamp without time zone DEFAULT now(),
    logout timestamp without time zone,
    tempo numeric(30,0),
    caller_id character varying(30),
    nas character varying(128),
    framed_ip_address character varying(20),
    terminate_cause character varying(128),
    bytes_in numeric(30,0),
    bytes_out numeric(30,0)
);


ALTER TABLE public.rdtb_accounting OWNER TO virtex;

--
-- Name: rdtb_accounting_session_id; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE rdtb_accounting_session_id
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.rdtb_accounting_session_id OWNER TO virtex;

--
-- Name: rdtb_log; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE rdtb_log (
    id_log integer DEFAULT nextval('rdtb_log_id_log'::text) NOT NULL,
    datahora timestamp without time zone DEFAULT now(),
    tipo character varying(2),
    username character varying(64),
    mensagem text,
    caller_id character varying(30)
);


ALTER TABLE public.rdtb_log OWNER TO virtex;

--
-- Name: rdtb_log_id_log; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE rdtb_log_id_log
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.rdtb_log_id_log OWNER TO virtex;

--
-- Name: spsq_id_spool; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE spsq_id_spool
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.spsq_id_spool OWNER TO virtex;

--
-- Name: sptb_spool; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE sptb_spool (
    id_spool serial NOT NULL,
    registro timestamp without time zone DEFAULT now(),
    execucao timestamp without time zone,
    destino character varying(50),
    tipo character varying(2) NOT NULL,
    op character(1),
    id smallint,
    parametros text,
    status character varying(2) NOT NULL,
    cod_erro smallint
);


ALTER TABLE public.sptb_spool OWNER TO virtex;

--
-- Name: sptb_spool_id_spool; Type: SEQUENCE; Schema: public; Owner: virtex
--

CREATE SEQUENCE sptb_spool_id_spool
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.sptb_spool_id_spool OWNER TO virtex;

--
-- Name: sttb_pop_status; Type: TABLE; Schema: public; Owner: virtex; Tablespace: 
--

CREATE TABLE sttb_pop_status (
    id_pop smallint NOT NULL,
    min_ping integer,
    max_ping integer,
    media_ping integer,
    num_perdas integer,
    status character varying(3),
    num_erros integer,
    num_ping integer,
    laststats timestamp without time zone DEFAULT now()
);


ALTER TABLE public.sttb_pop_status OWNER TO virtex;

--
-- Name: adtb_admin_admin_key; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY adtb_admin
    ADD CONSTRAINT adtb_admin_admin_key UNIQUE (admin);


ALTER INDEX public.adtb_admin_admin_key OWNER TO virtex;

--
-- Name: adtb_admin_email_key; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY adtb_admin
    ADD CONSTRAINT adtb_admin_email_key UNIQUE (email);


ALTER INDEX public.adtb_admin_email_key OWNER TO virtex;

--
-- Name: adtb_admin_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY adtb_admin
    ADD CONSTRAINT adtb_admin_pkey PRIMARY KEY (id_admin);


ALTER INDEX public.adtb_admin_pkey OWNER TO virtex;

--
-- Name: adtb_privilegio_cod_priv_key; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY adtb_privilegio
    ADD CONSTRAINT adtb_privilegio_cod_priv_key UNIQUE (cod_priv);


ALTER INDEX public.adtb_privilegio_cod_priv_key OWNER TO virtex;

--
-- Name: adtb_privilegio_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY adtb_privilegio
    ADD CONSTRAINT adtb_privilegio_pkey PRIMARY KEY (id_priv);


ALTER INDEX public.adtb_privilegio_pkey OWNER TO virtex;

--
-- Name: adtb_usuario_privilegio_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY adtb_usuario_privilegio
    ADD CONSTRAINT adtb_usuario_privilegio_pkey PRIMARY KEY (id_admin, id_priv);


ALTER INDEX public.adtb_usuario_privilegio_pkey OWNER TO virtex;

--
-- Name: bktb_backup_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY bktb_backup
    ADD CONSTRAINT bktb_backup_pkey PRIMARY KEY (id_backup);


ALTER INDEX public.bktb_backup_pkey OWNER TO virtex;

--
-- Name: cbtb_carne_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cbtb_carne
    ADD CONSTRAINT cbtb_carne_pkey PRIMARY KEY (id_carne);


ALTER INDEX public.cbtb_carne_pkey OWNER TO virtex;

--
-- Name: cbtb_cliente_produto_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cbtb_cliente_produto
    ADD CONSTRAINT cbtb_cliente_produto_pkey PRIMARY KEY (id_cliente_produto);


ALTER INDEX public.cbtb_cliente_produto_pkey OWNER TO virtex;

--
-- Name: cbtb_cliente_venda_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cbtb_cliente_venda
    ADD CONSTRAINT cbtb_cliente_venda_pkey PRIMARY KEY (id_venda, id_cliente_produto);


ALTER INDEX public.cbtb_cliente_venda_pkey OWNER TO virtex;

--
-- Name: cbtb_contrato_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cbtb_contrato
    ADD CONSTRAINT cbtb_contrato_pkey PRIMARY KEY (id_cliente_produto);


ALTER INDEX public.cbtb_contrato_pkey OWNER TO virtex;

--
-- Name: cbtb_endereco_cobranca_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cbtb_endereco_cobranca
    ADD CONSTRAINT cbtb_endereco_cobranca_pkey PRIMARY KEY (id_endereco_cobranca);


ALTER INDEX public.cbtb_endereco_cobranca_pkey OWNER TO virtex;

--
-- Name: cbtb_faturas_cod_barra_key; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cbtb_faturas
    ADD CONSTRAINT cbtb_faturas_cod_barra_key UNIQUE (cod_barra);


ALTER INDEX public.cbtb_faturas_cod_barra_key OWNER TO virtex;

--
-- Name: cbtb_faturas_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cbtb_faturas
    ADD CONSTRAINT cbtb_faturas_pkey PRIMARY KEY (id_cliente_produto, data);


ALTER INDEX public.cbtb_faturas_pkey OWNER TO virtex;

--
-- Name: cbtb_venda_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cbtb_venda
    ADD CONSTRAINT cbtb_venda_pkey PRIMARY KEY (id_venda);


ALTER INDEX public.cbtb_venda_pkey OWNER TO virtex;

--
-- Name: cftb_banda_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_banda
    ADD CONSTRAINT cftb_banda_pkey PRIMARY KEY (id);


ALTER INDEX public.cftb_banda_pkey OWNER TO virtex;

--
-- Name: cftb_cidade_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_cidade
    ADD CONSTRAINT cftb_cidade_pkey PRIMARY KEY (id_cidade);


ALTER INDEX public.cftb_cidade_pkey OWNER TO virtex;

--
-- Name: cftb_forma_pagamento_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_forma_pagamento
    ADD CONSTRAINT cftb_forma_pagamento_pkey PRIMARY KEY (id_cobranca);


ALTER INDEX public.cftb_forma_pagamento_pkey OWNER TO virtex;

--
-- Name: cftb_ip_externo_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_ip_externo
    ADD CONSTRAINT cftb_ip_externo_pkey PRIMARY KEY (ip_externo);


ALTER INDEX public.cftb_ip_externo_pkey OWNER TO virtex;

--
-- Name: cftb_ip_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_ip
    ADD CONSTRAINT cftb_ip_pkey PRIMARY KEY (ipaddr);


ALTER INDEX public.cftb_ip_pkey OWNER TO virtex;

--
-- Name: cftb_links_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_links
    ADD CONSTRAINT cftb_links_pkey PRIMARY KEY (id_link);


ALTER INDEX public.cftb_links_pkey OWNER TO virtex;

--
-- Name: cftb_nas_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_nas
    ADD CONSTRAINT cftb_nas_pkey PRIMARY KEY (id_nas);


ALTER INDEX public.cftb_nas_pkey OWNER TO virtex;

--
-- Name: cftb_nas_rede_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_nas_rede
    ADD CONSTRAINT cftb_nas_rede_pkey PRIMARY KEY (rede, id_nas);


ALTER INDEX public.cftb_nas_rede_pkey OWNER TO virtex;

--
-- Name: cftb_pop_nome_key; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_pop
    ADD CONSTRAINT cftb_pop_nome_key UNIQUE (nome);


ALTER INDEX public.cftb_pop_nome_key OWNER TO virtex;

--
-- Name: cftb_pop_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_pop
    ADD CONSTRAINT cftb_pop_pkey PRIMARY KEY (id_pop);


ALTER INDEX public.cftb_pop_pkey OWNER TO virtex;

--
-- Name: cftb_preferencias_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_preferencias
    ADD CONSTRAINT cftb_preferencias_pkey PRIMARY KEY (id_provedor);


ALTER INDEX public.cftb_preferencias_pkey OWNER TO virtex;

--
-- Name: cftb_rede_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_rede
    ADD CONSTRAINT cftb_rede_pkey PRIMARY KEY (rede);


ALTER INDEX public.cftb_rede_pkey OWNER TO virtex;

--
-- Name: cftb_servidor_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_servidor
    ADD CONSTRAINT cftb_servidor_pkey PRIMARY KEY (id_servidor);


ALTER INDEX public.cftb_servidor_pkey OWNER TO virtex;

--
-- Name: cftb_uf_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cftb_uf
    ADD CONSTRAINT cftb_uf_pkey PRIMARY KEY (uf);


ALTER INDEX public.cftb_uf_pkey OWNER TO virtex;

--
-- Name: cltb_cliente_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cltb_cliente
    ADD CONSTRAINT cltb_cliente_pkey PRIMARY KEY (id_cliente);


ALTER INDEX public.cltb_cliente_pkey OWNER TO virtex;

--
-- Name: cntb_conta_id_conta_key; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cntb_conta
    ADD CONSTRAINT cntb_conta_id_conta_key UNIQUE (id_conta);


ALTER INDEX public.cntb_conta_id_conta_key OWNER TO virtex;

--
-- Name: cntb_conta_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cntb_conta
    ADD CONSTRAINT cntb_conta_pkey PRIMARY KEY (username, dominio, tipo_conta);


ALTER INDEX public.cntb_conta_pkey OWNER TO virtex;

--
-- Name: cntb_endereco_instalacao_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cntb_endereco_instalacao
    ADD CONSTRAINT cntb_endereco_instalacao_pkey PRIMARY KEY (id_endereco_instalacao);


ALTER INDEX public.cntb_endereco_instalacao_pkey OWNER TO virtex;

--
-- Name: cxtb_fluxo_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY cxtb_fluxo
    ADD CONSTRAINT cxtb_fluxo_pkey PRIMARY KEY (id_fluxo);


ALTER INDEX public.cxtb_fluxo_pkey OWNER TO virtex;

--
-- Name: dominio_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY dominio
    ADD CONSTRAINT dominio_pkey PRIMARY KEY (dominio);


ALTER INDEX public.dominio_pkey OWNER TO virtex;

--
-- Name: lgtb_backup_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY lgtb_backup
    ADD CONSTRAINT lgtb_backup_pkey PRIMARY KEY (id_backup);


ALTER INDEX public.lgtb_backup_pkey OWNER TO virtex;

--
-- Name: lgtb_bloqueio_automatizado_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY lgtb_bloqueio_automatizado
    ADD CONSTRAINT lgtb_bloqueio_automatizado_pkey PRIMARY KEY (id_processo);


ALTER INDEX public.lgtb_bloqueio_automatizado_pkey OWNER TO virtex;

--
-- Name: lgtb_contas_excluidas_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY lgtb_contas_excluidas
    ADD CONSTRAINT lgtb_contas_excluidas_pkey PRIMARY KEY (id_excluida);


ALTER INDEX public.lgtb_contas_excluidas_pkey OWNER TO virtex;

--
-- Name: lgtb_emails_cobranca_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY lgtb_emails_cobranca
    ADD CONSTRAINT lgtb_emails_cobranca_pkey PRIMARY KEY (data_envio, id_cliente_produto, data);


ALTER INDEX public.lgtb_emails_cobranca_pkey OWNER TO virtex;

--
-- Name: lgtb_erros_db_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY lgtb_erros_db
    ADD CONSTRAINT lgtb_erros_db_pkey PRIMARY KEY (data);


ALTER INDEX public.lgtb_erros_db_pkey OWNER TO virtex;

--
-- Name: lgtb_exclusao_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY lgtb_exclusao
    ADD CONSTRAINT lgtb_exclusao_pkey PRIMARY KEY (id_exclusao);


ALTER INDEX public.lgtb_exclusao_pkey OWNER TO virtex;

--
-- Name: lgtb_reagendamento_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY lgtb_reagendamento
    ADD CONSTRAINT lgtb_reagendamento_pkey PRIMARY KEY (id_reagendamento);


ALTER INDEX public.lgtb_reagendamento_pkey OWNER TO virtex;

--
-- Name: lgtb_renovacao_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY lgtb_renovacao
    ADD CONSTRAINT lgtb_renovacao_pkey PRIMARY KEY (id_renovacao);


ALTER INDEX public.lgtb_renovacao_pkey OWNER TO virtex;

--
-- Name: lgtb_restore_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY lgtb_restore
    ADD CONSTRAINT lgtb_restore_pkey PRIMARY KEY (id_restore);


ALTER INDEX public.lgtb_restore_pkey OWNER TO virtex;

--
-- Name: lgtb_retorno_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY lgtb_retorno
    ADD CONSTRAINT lgtb_retorno_pkey PRIMARY KEY (id_arquivo);


ALTER INDEX public.lgtb_retorno_pkey OWNER TO virtex;

--
-- Name: lgtb_status_conta_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY lgtb_status_conta
    ADD CONSTRAINT lgtb_status_conta_pkey PRIMARY KEY (id_processo);


ALTER INDEX public.lgtb_status_conta_pkey OWNER TO virtex;

--
-- Name: pftb_forma_pagamento_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY pftb_forma_pagamento
    ADD CONSTRAINT pftb_forma_pagamento_pkey PRIMARY KEY (id_forma_pagamento);


ALTER INDEX public.pftb_forma_pagamento_pkey OWNER TO virtex;

--
-- Name: pftb_modelo_contrato_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY pftb_modelo_contrato
    ADD CONSTRAINT pftb_modelo_contrato_pkey PRIMARY KEY (id_modelo_contrato);


ALTER INDEX public.pftb_modelo_contrato_pkey OWNER TO virtex;

--
-- Name: pftb_preferencia_cobranca_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY pftb_preferencia_cobranca
    ADD CONSTRAINT pftb_preferencia_cobranca_pkey PRIMARY KEY (id_provedor);


ALTER INDEX public.pftb_preferencia_cobranca_pkey OWNER TO virtex;

--
-- Name: pftb_preferencia_geral_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY pftb_preferencia_geral
    ADD CONSTRAINT pftb_preferencia_geral_pkey PRIMARY KEY (id_provedor);


ALTER INDEX public.pftb_preferencia_geral_pkey OWNER TO virtex;

--
-- Name: pftb_preferencia_monitoracao_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY pftb_preferencia_monitoracao
    ADD CONSTRAINT pftb_preferencia_monitoracao_pkey PRIMARY KEY (id_provedor);


ALTER INDEX public.pftb_preferencia_monitoracao_pkey OWNER TO virtex;

--
-- Name: pftb_preferencia_provedor_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY pftb_preferencia_provedor
    ADD CONSTRAINT pftb_preferencia_provedor_pkey PRIMARY KEY (id_provedor);


ALTER INDEX public.pftb_preferencia_provedor_pkey OWNER TO virtex;

--
-- Name: prtb_produto_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY prtb_produto
    ADD CONSTRAINT prtb_produto_pkey PRIMARY KEY (id_produto);


ALTER INDEX public.prtb_produto_pkey OWNER TO virtex;

--
-- Name: rdtb_accounting_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY rdtb_accounting
    ADD CONSTRAINT rdtb_accounting_pkey PRIMARY KEY (session_id);


ALTER INDEX public.rdtb_accounting_pkey OWNER TO virtex;

--
-- Name: rdtb_log_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY rdtb_log
    ADD CONSTRAINT rdtb_log_pkey PRIMARY KEY (id_log);


ALTER INDEX public.rdtb_log_pkey OWNER TO virtex;

--
-- Name: sptb_spool_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY sptb_spool
    ADD CONSTRAINT sptb_spool_pkey PRIMARY KEY (id_spool);


ALTER INDEX public.sptb_spool_pkey OWNER TO virtex;

--
-- Name: sttb_pop_status_pkey; Type: CONSTRAINT; Schema: public; Owner: virtex; Tablespace: 
--

ALTER TABLE ONLY sttb_pop_status
    ADD CONSTRAINT sttb_pop_status_pkey PRIMARY KEY (id_pop);


ALTER INDEX public.sttb_pop_status_pkey OWNER TO virtex;

--
-- Name: cxix_fluxo_datas; Type: INDEX; Schema: public; Owner: virtex; Tablespace: 
--

CREATE INDEX cxix_fluxo_datas ON cxtb_fluxo USING btree (data_registro, data_compensacao);


ALTER INDEX public.cxix_fluxo_datas OWNER TO virtex;

--
-- Name: rdtb_accounting_id_user_login; Type: INDEX; Schema: public; Owner: virtex; Tablespace: 
--

CREATE INDEX rdtb_accounting_id_user_login ON rdtb_accounting USING btree (session_id, username, login);


ALTER INDEX public.rdtb_accounting_id_user_login OWNER TO virtex;

--
-- Name: rdtb_accounting_id_user_login_logout; Type: INDEX; Schema: public; Owner: virtex; Tablespace: 
--

CREATE INDEX rdtb_accounting_id_user_login_logout ON rdtb_accounting USING btree (session_id, username, login, logout);


ALTER INDEX public.rdtb_accounting_id_user_login_logout OWNER TO virtex;

--
-- Name: rdtb_accounting_login_logout; Type: INDEX; Schema: public; Owner: virtex; Tablespace: 
--

CREATE INDEX rdtb_accounting_login_logout ON rdtb_accounting USING btree (login, logout);


ALTER INDEX public.rdtb_accounting_login_logout OWNER TO virtex;

--
-- Name: adtb_usuario_privilegio_id_admin_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY adtb_usuario_privilegio
    ADD CONSTRAINT adtb_usuario_privilegio_id_admin_fkey FOREIGN KEY (id_admin) REFERENCES adtb_admin(id_admin) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: adtb_usuario_privilegio_id_priv_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY adtb_usuario_privilegio
    ADD CONSTRAINT adtb_usuario_privilegio_id_priv_fkey FOREIGN KEY (id_priv) REFERENCES adtb_privilegio(id_priv) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: bktb_arquivos_id_backup_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY bktb_arquivos
    ADD CONSTRAINT bktb_arquivos_id_backup_fkey FOREIGN KEY (id_backup) REFERENCES bktb_backup(id_backup) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cbtb_cliente_produto_id_cliente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cbtb_cliente_produto
    ADD CONSTRAINT cbtb_cliente_produto_id_cliente_fkey FOREIGN KEY (id_cliente) REFERENCES cltb_cliente(id_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cbtb_contrato_id_cliente_produto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cbtb_contrato
    ADD CONSTRAINT cbtb_contrato_id_cliente_produto_fkey FOREIGN KEY (id_cliente_produto) REFERENCES cbtb_cliente_produto(id_cliente_produto) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cbtb_contrato_id_forma_pagamento; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cbtb_contrato
    ADD CONSTRAINT cbtb_contrato_id_forma_pagamento FOREIGN KEY (id_forma_pagamento) REFERENCES pftb_forma_pagamento(id_forma_pagamento) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cbtb_faturas_id_carne_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cbtb_faturas
    ADD CONSTRAINT cbtb_faturas_id_carne_fkey FOREIGN KEY (id_carne) REFERENCES cbtb_carne(id_carne) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cbtb_faturas_id_cliente_produto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cbtb_faturas
    ADD CONSTRAINT cbtb_faturas_id_cliente_produto_fkey FOREIGN KEY (id_cliente_produto) REFERENCES cbtb_contrato(id_cliente_produto) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cbtb_faturas_id_forma_pagamento; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cbtb_faturas
    ADD CONSTRAINT cbtb_faturas_id_forma_pagamento FOREIGN KEY (id_forma_pagamento) REFERENCES pftb_forma_pagamento(id_forma_pagamento) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cbtb_produtos_venda_id_venda_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cbtb_produtos_venda
    ADD CONSTRAINT cbtb_produtos_venda_id_venda_fkey FOREIGN KEY (id_venda) REFERENCES cbtb_venda(id_venda) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cftb_cidade_uf_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cftb_cidade
    ADD CONSTRAINT cftb_cidade_uf_fkey FOREIGN KEY (uf) REFERENCES cftb_uf(uf) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cftb_ip_externo_id_nas_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cftb_ip_externo
    ADD CONSTRAINT cftb_ip_externo_id_nas_fkey FOREIGN KEY (id_nas) REFERENCES cftb_nas(id_nas) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cftb_nas_id_servidor_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cftb_nas
    ADD CONSTRAINT cftb_nas_id_servidor_fkey FOREIGN KEY (id_servidor) REFERENCES cftb_servidor(id_servidor) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cftb_nas_rede_id_nas_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cftb_nas_rede
    ADD CONSTRAINT cftb_nas_rede_id_nas_fkey FOREIGN KEY (id_nas) REFERENCES cftb_nas(id_nas) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cftb_nas_rede_rede_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cftb_nas_rede
    ADD CONSTRAINT cftb_nas_rede_rede_fkey FOREIGN KEY (rede) REFERENCES cftb_rede(rede) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cftb_pop_id_pop_ap_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cftb_pop
    ADD CONSTRAINT cftb_pop_id_pop_ap_fkey FOREIGN KEY (id_pop_ap) REFERENCES cftb_pop(id_pop) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cftb_pop_id_servidor_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cftb_pop
    ADD CONSTRAINT cftb_pop_id_servidor_fkey FOREIGN KEY (id_servidor) REFERENCES cftb_servidor(id_servidor) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cltb_cliente_id_cidade_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cltb_cliente
    ADD CONSTRAINT cltb_cliente_id_cidade_fkey FOREIGN KEY (id_cidade) REFERENCES cftb_cidade(id_cidade) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cntb_conta_bandalarga_id_nas_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cntb_conta_bandalarga
    ADD CONSTRAINT cntb_conta_bandalarga_id_nas_fkey FOREIGN KEY (id_nas) REFERENCES cftb_nas(id_nas) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cntb_conta_bandalarga_id_pop_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cntb_conta_bandalarga
    ADD CONSTRAINT cntb_conta_bandalarga_id_pop_fkey FOREIGN KEY (id_pop) REFERENCES cftb_pop(id_pop) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cntb_conta_bandalarga_ip_externo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cntb_conta_bandalarga
    ADD CONSTRAINT cntb_conta_bandalarga_ip_externo_fkey FOREIGN KEY (ip_externo) REFERENCES cftb_ip_externo(ip_externo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cntb_conta_bandalarga_ipaddr_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cntb_conta_bandalarga
    ADD CONSTRAINT cntb_conta_bandalarga_ipaddr_fkey FOREIGN KEY (ipaddr) REFERENCES cftb_ip(ipaddr) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cntb_conta_bandalarga_rede_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cntb_conta_bandalarga
    ADD CONSTRAINT cntb_conta_bandalarga_rede_fkey FOREIGN KEY (rede) REFERENCES cftb_rede(rede) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cntb_conta_dominio_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cntb_conta
    ADD CONSTRAINT cntb_conta_dominio_fkey FOREIGN KEY (dominio) REFERENCES dominio(dominio) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- Name: cntb_conta_id_cliente_produto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cntb_conta
    ADD CONSTRAINT cntb_conta_id_cliente_produto_fkey FOREIGN KEY (id_cliente_produto) REFERENCES cbtb_cliente_produto(id_cliente_produto) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cntb_conta_id_cliente_produto_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cntb_conta
    ADD CONSTRAINT cntb_conta_id_cliente_produto_fkey1 FOREIGN KEY (id_cliente_produto) REFERENCES cbtb_cliente_produto(id_cliente_produto) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cntb_endereco_cobranca_id_cidade_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cbtb_endereco_cobranca
    ADD CONSTRAINT cntb_endereco_cobranca_id_cidade_fkey FOREIGN KEY (id_cidade) REFERENCES cftb_cidade(id_cidade) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cntb_endereco_instalacao_id_cidade_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cntb_endereco_instalacao
    ADD CONSTRAINT cntb_endereco_instalacao_id_cidade_fkey FOREIGN KEY (id_cidade) REFERENCES cftb_cidade(id_cidade) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: cntb_endereco_instalacao_id_cliente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY cntb_endereco_instalacao
    ADD CONSTRAINT cntb_endereco_instalacao_id_cliente_fkey FOREIGN KEY (id_cliente) REFERENCES cltb_cliente(id_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: dominio_id_cliente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY dominio
    ADD CONSTRAINT dominio_id_cliente_fkey FOREIGN KEY (id_cliente) REFERENCES cltb_cliente(id_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: lgtb_administradores_id_admin_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY lgtb_administradores
    ADD CONSTRAINT lgtb_administradores_id_admin_fkey FOREIGN KEY (id_admin) REFERENCES adtb_admin(id_admin) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: lgtb_bloqueio_automatizado_id_cliente_produto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY lgtb_bloqueio_automatizado
    ADD CONSTRAINT lgtb_bloqueio_automatizado_id_cliente_produto_fkey FOREIGN KEY (id_cliente_produto) REFERENCES cbtb_cliente_produto(id_cliente_produto) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: lgtb_emails_cobranca_id_cliente_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY lgtb_emails_cobranca
    ADD CONSTRAINT lgtb_emails_cobranca_id_cliente_fkey FOREIGN KEY (id_cliente) REFERENCES cltb_cliente(id_cliente) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: lgtb_emails_cobranca_id_cliente_produto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY lgtb_emails_cobranca
    ADD CONSTRAINT lgtb_emails_cobranca_id_cliente_produto_fkey FOREIGN KEY (id_cliente_produto, data) REFERENCES cbtb_faturas(id_cliente_produto, data) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: lgtb_reagendamento_id_cliente_produto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY lgtb_reagendamento
    ADD CONSTRAINT lgtb_reagendamento_id_cliente_produto_fkey FOREIGN KEY (id_cliente_produto, data) REFERENCES cbtb_faturas(id_cliente_produto, data) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: lgtb_retorno_faturas_id_arquivo_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY lgtb_retorno_faturas
    ADD CONSTRAINT lgtb_retorno_faturas_id_arquivo_fkey FOREIGN KEY (id_arquivo) REFERENCES lgtb_retorno(id_arquivo) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: lgtb_status_conta_id_cliente_produto_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY lgtb_status_conta
    ADD CONSTRAINT lgtb_status_conta_id_cliente_produto_fkey FOREIGN KEY (id_cliente_produto) REFERENCES cbtb_cliente_produto(id_cliente_produto) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: lgtb_status_conta_username_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY lgtb_status_conta
    ADD CONSTRAINT lgtb_status_conta_username_fkey FOREIGN KEY (username, dominio, tipo_conta) REFERENCES cntb_conta(username, dominio, tipo_conta) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: sttb_pop_status_id_pop_fkey; Type: FK CONSTRAINT; Schema: public; Owner: virtex
--

ALTER TABLE ONLY sttb_pop_status
    ADD CONSTRAINT sttb_pop_status_id_pop_fkey FOREIGN KEY (id_pop) REFERENCES cftb_pop(id_pop) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

