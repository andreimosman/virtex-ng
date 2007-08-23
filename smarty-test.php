<?
	include("autoload.php");
	$tmp=base64_decode(@$_REQUEST["tmp"]);
	$tpl = MTemplate::getInstance("/tmp");
	@$tpl->obtemPagina($tmp);
	echo "OK";
?>
