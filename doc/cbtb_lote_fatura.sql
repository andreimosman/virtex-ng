-- Table: cbtb_lote_fatura

-- DROP TABLE cbtb_lote_fatura;

CREATE TABLE cbtb_lote_fatura
(
  id_remessa bigint,
  id_cobranca bigint,
  CONSTRAINT cbtb_lote_cobranca_id_remessa_fkey FOREIGN KEY (id_remessa)
      REFERENCES cbtb_lote_cobranca (id_remessa) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITHOUT OIDS;
ALTER TABLE cbtb_lote_fatura OWNER TO virtex;

-- Index: fki_cbtb_lote_cobranca_id_remessa_fkey

-- DROP INDEX fki_cbtb_lote_cobranca_id_remessa_fkey;

CREATE INDEX fki_cbtb_lote_cobranca_id_remessa_fkey
  ON cbtb_lote_fatura
  USING btree
  (id_remessa);

