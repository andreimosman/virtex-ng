/**
 * TABELAS DE PRODUTO
 */

CREATE TABLE tmp_pr_bl AS
SELECT 
   p.id_produto,p.nome,descricao,p.tipo,valor,p.disponivel,p.num_emails,p.quota_por_conta,p.vl_email_adicional,
   p.permitir_outros_dominios,p.email_anexado,p.numero_contas,p.comodato,p.valor_comodato,p.desconto_promo,
   p.periodo_desconto,p.tx_instalacao,p.valor_estatico,
   pbl.banda_upload_kbps,pbl.banda_download_kbps,pbl.franquia_trafego_mensal_gb,pbl.valor_trafego_adicional_gb
FROM
   prtb_produto p INNER JOIN prtb_produto_bandalarga pbl ON (p.id_produto = pbl.id_produto)
;

CREATE TABLE tmp_pr_h AS
SELECT 
   p.id_produto,p.nome,descricao,p.tipo,valor,p.disponivel,p.num_emails,p.quota_por_conta,p.vl_email_adicional,
   p.permitir_outros_dominios,p.email_anexado,p.numero_contas,p.comodato,p.valor_comodato,p.desconto_promo,
   p.periodo_desconto,p.tx_instalacao,p.valor_estatico,
   ph.dominio, ph.franquia_em_mb,ph.valor_mb_adicional
FROM
   prtb_produto p INNER JOIN prtb_produto_hospedagem ph ON (p.id_produto = ph.id_produto)
;

CREATE TABLE tmp_pr_d AS
SELECT 
   p.id_produto,p.nome,descricao,p.tipo,valor,p.disponivel,p.num_emails,p.quota_por_conta,p.vl_email_adicional,
   p.permitir_outros_dominios,p.email_anexado,p.numero_contas,p.comodato,p.valor_comodato,p.desconto_promo,
   p.periodo_desconto,p.tx_instalacao,p.valor_estatico,
   pd.franquia_horas,pd.permitir_duplicidade,pd.valor_hora_adicional
FROM
   prtb_produto p INNER JOIN prtb_produto_discado pd ON (p.id_produto = pd.id_produto)
;

/**
 * TABELAS DE CONTAS
 */
CREATE TABLE tmp_cn_bl AS
SELECT
   c.username, c.dominio, c.tipo_conta, c.senha, c.id_cliente, c.id_cliente_produto, c.id_conta, c.senha_cript,
   c.status, c.observacoes,
   ce.id_pop,ce.tipo_bandalarga,ce.ipaddr,ce.rede,ce.upload_kbps,ce.download_kbps,ce.mac,ce.id_nas
FROM
   cntb_conta c INNER JOIN cntb_conta_bandalarga ce on (c.username = ce.username AND c.dominio = ce.dominio AND c.tipo_conta = ce.tipo_conta)
;

CREATE TABLE tmp_cn_h AS
SELECT
   c.username, c.dominio, c.tipo_conta, c.senha, c.id_cliente, c.id_cliente_produto, c.id_conta, c.senha_cript,
   c.status, c.observacoes,
   ce.tipo_hospedagem,ce.uid,ce.gid,ce.home,ce.shell,ce.dominio_hospedagem
FROM
   cntb_conta c INNER JOIN cntb_conta_hospedagem ce on (c.username = ce.username AND c.dominio = ce.dominio AND c.tipo_conta = ce.tipo_conta)
;

CREATE TABLE tmp_cn_d AS
SELECT
   c.username, c.dominio, c.tipo_conta, c.senha, c.id_cliente, c.id_cliente_produto, c.id_conta, c.senha_cript,
   c.status, c.observacoes,
   ce.foneinfo
FROM
   cntb_conta c INNER JOIN cntb_conta_discado ce on (c.username = ce.username AND c.dominio = ce.dominio AND c.tipo_conta = ce.tipo_conta)
;

CREATE TABLE tmp_cn_e AS
SELECT
   c.username, c.dominio, c.tipo_conta, c.senha, c.id_cliente, c.id_cliente_produto, c.id_conta, c.senha_cript,
   c.status, c.observacoes,
   ce.quota
FROM
   cntb_conta c INNER JOIN cntb_conta_email ce on (c.username = ce.username AND c.dominio = ce.dominio AND c.tipo_conta = ce.tipo_conta)
;







CREATE TABLE tmp_cnbl 
   SELECT