<?


	class VCAJs extends VirtexControllerAdmin {
	
		protected $jsPath;
	
		public function __construct() {
			parent::__construct();
			
		}
		
		public function init() {
			$this->jsPath = "view/templates/jslib";
		}
		
		public function executa() {
			$js = @$_REQUEST["js"];
			$tp = @$_REQUEST["tp"];
			
			if( strstr($js,"..") ) {
				return;
			}
			
			switch($tp) {
				case 'form':
				case 'prototype':
				case 'window':
				case 'zips':
				case 'jsdomenubar':
					$dir = $tp;
					break;
				default:
					$dir = "";
			}
			
			if( !$dir ) return;
			
			$arq = $this->jsPath . "/" . $dir . "/" . $js . ".js";
			
			// echo "// $arq";
			
			if( !file_exists($arq) ) return;
			
			$fd = @fopen($arq,"r");
			
			if(!$fd) return;
			
			while(!feof($fd)) {
				echo fgets($fd,4096);
			}
			
			fclose($fd);

		}
	
	
	}


?>
