/**
 * validacao.js
 * Cont�m v�rias fun��es de valida��o em JS
 */
 
 /**
  * estaVazio - Compara se o conte�do de uma string � vazio(ou n�o cont�m nenhum caractere ou cont�m somente caracteres de espa�o
  */
function estaVazio(string) {
	var reVazio = /^\s*$/;			//Texto vazio ou preenchido somente com caracteres de espa�o
	return reVazio.test(string);
}
 
 /**
  * ipValido - Valida uma string de endere�o IPv4
  */
function ipValido(stringIP) {
	var reIP = /^([0-2]?[0-9]?[0-9]\.){3}([0-2]?[0-9]?[0-9])$/;

	if(!reIP.test(stringIP)) {
		return false;
	} 

	partes = stringIP.split(".");
	for(i=0; i<partes.length; i++) {
		if(parteInt(partes[i]) > 255 || parteInt(partes[i]) < 0) return false;
	}

	return true;
}

/**
 * macValido - Valida um endere�o mac
 */
 
function macValido(string) {
	reMac = /^([0-9A-Fa-f]{2}:){5}([0-9A-Fa-f]{2})$/
	return reMac.test(string);
}