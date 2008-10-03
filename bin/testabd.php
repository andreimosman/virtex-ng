<?

  /**
   * Script para aguardar a inicialização do banco de dados principal.
   */

  $path = ini_get('include_path');
  ini_set('include_path',$path.':/mosman/virtex/framework');
  // echo "P: $path";
  //exit;
  require_once("/mosman/virtex/app/autoload.php");

  $arqConfig = "/mosman/virtex/app/etc/virtex.ini";
  $cfg = new MConfig($arqConfig);

  $dsn = $cfg->config["DB"]["dsn"];

  $options = array('debug' =>0,'portability' => MDB2_PORTABILITY_NONE,'seqname_format' => '%s', 'idxname_format' => '%s');



  echo "VERIFICANDO SERVIDOR PRINCIPAL\n";
  echo "----------------------------------------\n";

  while(true) {
    $bd = MDB2::factory($dsn,$options);
    $bd->disconnect();
    $conn = $bd->getConnection();

    if( PEAR::isError($conn) ) {
      echo " - Aguardando servidor principal... \n";
      sleep(5);
    } else {
      echo " - Servidor principal OK... \n";
      break;
    }
  }
  echo "----------------------------------------\n";


?>
