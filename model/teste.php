<?

/**
 * Apoio
 */
include("MDatabase.class.php");

/**
 * Exceptions
 */
include("MException.class.php");
include("ExcecaoModelo.class.php");
include("ExcecaoModeloInexistente.class.php");

/**
 * Persistencia
 */
include("MPersiste.class.php");
include("VirtexPersiste.class.php");
include("PERSISTE_ADTB_ADMIN.class.php");
include("PERSISTE_ATDB_USUARIO_PRIVILEGIO.class.php");
include("PERSISTE_CFTB_BANDA.class.php");
include("PERSISTE_CFTB_CIDADE.class.php");
include("PERSISTE_CFTB_FORMA_PAGAMENTO.class.php");
include("PERSISTE_CFTB_NAS.class.php");
include("PERSISTE_CFTB_POP.class.php");
include("PERSISTE_CLTB_CLIENTE.class.php");
include("PERSISTE_CNTB_CONTA.class.php");
include("PERSISTE_CNTB_CONTA_BANDALARGA.class.php");
include("PERSISTE_CNTB_CONTA_DISCADO.class.php");
include("PERSISTE_CNTB_CONTA_EMAIL.class.php");
include("PERSISTE_CONTA_HOSPEDAGEM.class.php");
include("PERSISTE_PRTB_PRODUTO.class.php");
include("PERSISTE_PRTB_PRODUTO_BANDALARGA.class.php");
include("PERSISTE_PRTB_PRODUTO_DISCADO.class.php");
include("PERSISTE_PRTB_PRODUTO_HOSPEDAGEM.class.php");


/**
 * Modelos
 */
include("VirtexModelo.class.php");
include("MODELO_Clientes.class.php");

$dns = "pgsql://virtex:vtx123@127.0.0.1/virtex";

//$bd = new MDatabase($dsn);
//print_r($bd);

VirtexPersiste::init();

$cli = VirtexModelo::factory("clientes");


?>
