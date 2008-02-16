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
 * emailValido - Valida uma string de email
 */
function emailValido(string) {
	//var reEmail = /^[a-zA-Z]+[a-zA-Z0-9._]*@[a-zA-Z0-9._]+\.[a-zA-Z0-9._]{2,}$/;
	var reEmail = /^[a-zA-Z]+[a-zA-Z0-9._]*@[a-zA-Z0-9._]+\.[a-zA-Z0-9._]{2,}$/;
	return reEmail.test(string);
}
 
 /**
  * ipValido - Valida uma string de endere�o IPv4
  */
function ipValido(string) {
	var reIP = /^([0-2]?[0-9]?[0-9]\.){3}([0-2]?[0-9]?[0-9])$/;

	if(!reIP.test(string)) {
		return false;
	} 

	partes = string.split(".");
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



/**
 * valitaTextoEntrada - Valida a entrada de textos com a quantidade m�nima de caracteres v�lidos excluindo os caracteres "%" e "*"
 * 	retorno:	true - Se houver uma quantidade maior ou a ao valor de numero a quantidade de caracteres v�lidos
 *			false - Se n�o satisfazer as condi��es.
 */

function validaTextoEntrada(texto, numero) {
	reEspecialChar = /([*%]|\s)/g;

	texto_temp = texto.replace(reEspecialChar, "");

	if(texto_temp.length >= numero) return true;

	return false;
}