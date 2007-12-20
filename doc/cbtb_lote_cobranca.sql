-- Table: cbtb_lote_cobranca

-- DROP TABLE cbtb_lote_cobranca;

CREATE TABLE cbtb_lote_cobranca
(
  id_remessa smallint NOT NULL,
  data_geracao date,
  periodo character(2) NOT NULL,
  id_admin smallint NOT NULL,
  data_referencia date,
  CONSTRAINT cbtb_lote_cobranca_pkey PRIMARY KEY (id_remessa),
  CONSTRAINT cbtb_lote_cobranca_fkey_adtb_admin FOREIGN KEY (id_admin)
      REFERENCES adtb_admin (id_admin) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
)
WITH OIDS;
ALTER TABLE cbtb_lote_cobranca OWNER TO virtex;
