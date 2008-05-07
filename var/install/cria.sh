#!/bin/sh
pg_dump -DaU virtex -t cftb_uf > cftb_uf.sql
pg_dump -DaU virtex -t cftb_cidade > cftb_cidade.sql
pg_dump -DaU virtex -t adtb_privilegio > adtb_privilegio.sql
