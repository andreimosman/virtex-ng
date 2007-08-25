/**
 * formatacao.js 
 * Funções para formatação de campos.
 */

/**
 * formataCampoDecimal() - Formata o campo para que ele temnha o formato decimal
 */
function formataCampoDecimal(obj, casasDecimais, separadorDecimal) {
	var string = obj.value;
	var tamanho = string.length;
	
	//Colocar código de formatação decimal aqui
}
 
/**
 *  reiniciaCampo - Reinicia o valor de um campo
 */
function reiniciaCampo(obj, extra) {
 	if (obj.type == "text") { 		
 		obj.value=""; 		
 		if(extra) obj.focus();
 	}
}



 