<?php

/*======================================================================*\
    FD File Upload Class
    Author: Fernan de Dios
    Email: info@fernandedios.com
    Date: 09/28/2012
    Version: 1.02
    General Notes: Basic file upload, 
                   use upload_image for advanced img uploads
\*======================================================================*/

include_once("string.php");

class FDUpload extends FDUtilString {

	/*======================================================================*\
    	Function: processFile
    	Purpose: upload a file to a directory
    	Notes: $file = file input name (e.g. 'pdf')
    	       $filetype = can be 'overwrite' or 'not_overwrite'
    	       $upload_dir = e.g. '../images/'   needs trailing slash
    	       $permissions = e.g. 0777
	\*======================================================================*/
	public function processFile($file, $filetype, $upload_dir, $permissions) {

		$file_ext = '.' . $this->getFileExtension($file);

		$file_name = $_FILES[$file]['tmp_name'];
		$user_filename = $this->spaceToUnderscore($_FILES[$file]['name']);
		$file_check = $upload_dir . $user_filename;

		// check to see if directory exists
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, $permissions);
		}
		
		// check to see if file needs to be renamed
		if ($filetype == "not_overwrite" && file_exists($file_check)) {
			$user_filename = $this->doFileRename($user_filename, $file_ext);
		}

		// add file to directory
		$final_file = $upload_dir . $user_filename;
		$this->doUpload($file_name, $final_file, $permissions);

		return $user_filename;
	}

	/*======================================================================*\
    	Function: doUpload
    	Purpose: place file on a specified directory
    	Notes: $tmp_name = tmp filename
    		   $final = directory + filename
    		   $permissions = file permissions
	\*======================================================================*/
	private function doUpload($tmp_name, $final, $permissions){
		move_uploaded_file($tmp_name, $final);
		chmod($final, $permissions);
	}

}


?>