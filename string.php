<?php

/*======================================================================*\
    FD String Utility Class
    Author: Fernan de Dios
    Email: info@fernandedios.com
    Date: 09/28/2012
    Version: 1.02
\*======================================================================*/

class FDUtilString {
	

	/*======================================================================*\
    	Function: getRandomId
    	Purpose:  create a random string
	\*======================================================================*/
	public function getRandomId($length = 32) {
		$randstr='';
		srand((double)microtime()*1000000);
		$chars = array("", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
		for ($rand = 0; $rand <= $length; $rand++) {
			$random = rand(0, count($chars) -1);
			$randstr .= $chars[$random];
		}
		return $randstr;
	}

	/*======================================================================*\
    	Function: spaceToUnderscore
    	Purpose:  replace space to underscore
	\*======================================================================*/
	public function spaceToUnderscore($string) {
		$string	= trim($string);
		return str_replace(" ", "_", $string);
	}

	/*======================================================================*\
    	Function: httpAppend
    	Purpose:  add http if url/link does not have it
	\*======================================================================*/
	public function httpAppend($url) {
		$htt = 'http://';
		$htts = 'https://';
		
		$pos = strpos($url, $htt);
		$pos_s = strpos($url, $htts);
		
		if ($pos === false) { //see if http isn't present
			if($pos_s === false){ //then see if https isnt either
				$url = $htt . $url;
			}
		}
		
		return $url;
	}

	/*======================================================================*\
    	Function: getFileExtension
    	Purpose:  extract file extension
    	Notes: $file = file field id 
	\*======================================================================*/
	public function getFileExtension($file){
		return strtolower(substr($_FILES[$file]['name'], -3));
	}

	/*======================================================================*\
    	Function: doFileRename
    	Purpose:  extract file extension
    	Notes: $file = filename (e.g. pic.jpg)
	\*======================================================================*/
	public function doFileRename($filename, $ext){
		$date_ext = date("_ymd");
		$filename = substr($filename, 0, strlen($filename) - 4);
		$filename .= $date_ext . "_" . $this->getRandomId(3) . $ext;
		return $filename;
	}

	/*======================================================================*\
    	Function: showError
    	Purpose:  provide a more detailed information about the error
    	Notes: $show can only be "trigger" or "die"
	\*======================================================================*/
	public function showError($msg, $show = "trigger", $file = __FILE__, $line = __LINE__){
		$err = 'FDClass Error: ' . $msg . '<br />';
		$err .= 'On file: ' . $file . ', line: ' . $line;

		if($show == "trigger"){
			trigger_error($err);
		}
		else if($show == "die"){
			die($err);
		}
	}

}

?>