<?php

/*======================================================================*\
    FD Image Upload Class
    Author: Fernan de Dios
    Email: info@fernandedios.com
    Date: 10/01/2012
    Version: 1.02
    General Notes: image crop/resize/upload
\*======================================================================*/

include_once("string.php");

class FDUploadImg extends FDUtilString {

	/*======================================================================*\
    	Function: processFile
    	Purpose: upload a file to a directory
    	Notes: $file = file input name (e.g. 'pdf')
    	       $filetype = can be 'overwrite' or 'not_overwrite'
    	       $upload_dir = e.g. '../images/'   needs trailing slash
    	       $param = image setings/parameters
	       			$param["resize"] = if we need to resize 
	       							   1 = yes, 0 = no
	       			$param["crop"] = if we need to crop 
	       							   1 = yes, 0 = no
	       			$param["cropX"] = crop x position 
	       			$param["cropY"] = crop y position 
	       			$param["width"] = image width
	       			$param["height"] = image height
	       			$param["ratio"] = 1 = yes, 0 = no
	       			$param["type"] = image type (can be jpg, gif, png)
	       			$param["size"] = size limit in bytes
	           $permissions = directory permissions (e.g. 0777 )
	       	   $return = 0 if error, file_name if success

	\*======================================================================*/
	public function processFile($file, $filetype, $upload_dir, $param, $permissions) {

		$file_ext = '.' . $this->getFileExtension($file);

		$file_name = $_FILES[$file]['tmp_name'];
		$user_filename = $this->spaceToUnderscore($_FILES[$file]['name']);
		$file_check = $upload_dir . $user_filename;
		$dimensions = $this->getDimensions($file_name, $param['width'], $param['height'], $param['ratio']);

		// check to see if directory exists
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, $permissions);
		}
		
		// check to see if file needs to be renamed
		if ($filetype == "not_overwrite" && file_exists($file_check)) {
			$user_filename = $this->doFileRename($user_filename, $file_ext);
		}

		if($file_ext != '.jpg' && $file_ext != '.gif' && $file_ext != '.jpg') {
			return 0;
		}
		else {
			// add file to directory
			$final_file = $upload_dir . $user_filename;
			$this->doUpload($file_name, $final_file, $param, $dimensions, $file_ext);

			return $user_filename;
		}
	}

	/*======================================================================*\
    	Function: getDimensions
    	Purpose:  get image dimensions data
    	Notes: $tmp = file field
    		   $d_width = declared width at $this->processFile
    		   $d_height = declared height at $this->processFile
    		   $d_ratio = declared ratio at $this->processFile,
    		   			   whether to resize proportionally or not
	\*======================================================================*/
	private function getDimensions($tmp, $d_width, $d_height, $d_ratio) {
		$size = getimagesize($tmp);
		$width	= $size[0];
		$height = $size[1];		
		$new_width = $d_width;
		$new_height	= $d_height;		
		
		if($new_width != '' && $new_height != '') {
			if($d_ratio) {

				$comp_height = ($height/$width) * $new_width;
				$comp_width	= ($width/$height) * $new_height;
				
				if($comp_width > $new_width) {
					$final_width = round($new_width);
					$final_height =	round($comp_height);
					
				}
				else {
					$final_width = round($comp_width);
					$final_height = round($new_height);
				}
			}
			else {
				$final_width = $new_width;
				$final_height = $new_height;
			}			
		}
		else {
			$final_width = $size[0];
			$final_height = $size[1];
		}
					
		$data['new_width'] = $final_width;
		$data['new_height']	= $final_height;
		$data['original_width']	= $size[0];
		$data['original_height'] = $size[1];		
		
		return $data;
	}

	/*======================================================================*\
    	Function: doUpload
    	Purpose:  prepare and upload image to a specified directory
    	Notes: $tmp_name = tmp filename
    		   $final = directory + filename
    		   $dimensions = dimensions data
    		   $file_extension = .jpg, .gif or .png
	\*======================================================================*/
	private function doUpload($tmp_name, $final, $param, $dim, $file_extension) {

        $source_id = 0;

		switch ($file_extension) {
			case '.jpg':
				$source_id = imagecreatefromjpeg($tmp_name);
				break;

			case '.gif':
				$source_id = imagecreatefromgif($tmp_name);
				break;
			
			case '.png':
				$source_id = imagecreatefrompng($tmp_name);
				break;
		}
	
		$gd_tmp	= imagecreatetruecolor($dim['new_width'], $dim['new_height']) OR  $this->showError('Cannot Initialize new GD image stream: '. $dim['new_width'] . ' x ' . $dim['new_height'], "die", __FILE__, __LINE__);
		$x = 0;
		$y = 0;
		
		imagecreatetruecolor($dim['new_width'], $dim['new_height']);
		imagecopyresampled($gd_tmp, $source_id, 0, 0, $x, $y, $dim['new_width'], $dim['new_height'], $dim['original_width'], $dim['original_height']);	
		
		if($param['crop']) {
			$cropX = $param['cropX'];
			$cropY = $param['cropY'];
			$gd_tmp	= imagecreatetruecolor($cropX, $cropY);	
			$x = ($dim['new_width'] - $cropX) / 2;
			$y = ($dim['new_height'] - $cropY) / 2;
			imagecopyresampled($gd_tmp, $source_id, 0, 0, $x, $y, $dim['new_width'], $dim['new_height'], $dim['original_width'], $dim['original_height']);	
		}	
		
		if($file_extension == '.jpg') {
			imagejpeg($gd_tmp, $final, 100);
			
		}
		if($file_extension == '.gif') {
			imagegif($gd_tmp, $final, 100);
			
		}
		if($file_extension == '.png') {
			imagepng($gd_tmp, $final, 9);
			
		}			
		imagedestroy($source_id);
		imagedestroy($gd_tmp);
	}

} //end class


?>