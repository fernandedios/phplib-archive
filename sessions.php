<?php

/*======================================================================*\
    FD Sessions Class
    Author: Fernan de Dios
    Email: info@fernandedios.com
    Date: 10/04/2012
    Version: 1.00
\*======================================================================*/

include_once("string.php");

class FDSessions extends FDUtilString {

	public function setSession($key, $val){
		$_SESSION[$key] = $val;
	}

	public function getSession($key){
		return $_SESSION[$key];
	}

	public function destroySession($key){
		$_SESSION[$key] = NULL;
		unset($_SESSION[$key]);
	}

	public function checkSession($key, $url){
		if(!$_SESSION[$key]){
			header("Location: $url");
		}
	}

	public function doLogOut($sessions, $url){
		foreach($sessions as $key){
			$this->destroySession($key);
		}
		header("Location: $url");
	}

	public function dumpSessions($sessions){
		$str = '';
		foreach($sessions as $key){
			$str .= $key . ' = ' . $_SESSION[$key] . '<br />';
		}
		return $str;
	}
}


?>