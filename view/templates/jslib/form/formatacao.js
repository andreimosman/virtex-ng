/**
 * formatacao.js 
 * Funções para formatação de campos.
 */
 
/**
 *  reiniciaCampo - Reinicia o valor de um campo
 */
function reiniciaCampo(obj, extra) {
 	if (obj.type == "text") { 		
 		obj.value=""; 		
 		if(extra) obj.focus();
 	}
}
