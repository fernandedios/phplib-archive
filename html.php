<?php

/*======================================================================*\
    FD HTML Utility Class
    Author: Fernan de Dios
    Email: info@fernandedios.com
    Date: 09/28/2012
    Version: 1.03
\*======================================================================*/

include_once("string.php");

class FDUtilHTML extends FDUtilString  {
	

	/*======================================================================*\
    	Function: generateSelect
    	Purpose:  create a select menu element
    	Notes:  $result = mysql result array
    		    $param = set of parameters 
    		    	$param["name"] = name of select element
    		    	$param["selected"] = option value selected
					$param["value"] = column to use for value
					$param["desc"] = column(s) to use for description
									 this is comma delimited
									 (e.g. name,address)
					$param["flag"] = can be 0 for no flag
									 or 1 for use flag
					$param["flag_val"] = value of flag
					$param["flag_desc"] = description of flag
	\*======================================================================*/
	public function generateSelect($result, $param) {
		$fval = $param["value"];

		$select = '<select id="' . $param["name"] . '" name="' . $param["name"] . '">';

		if($param["flag"] == 1){
			$selected = '';
			if($param["selected"] == $param["flag_val"]){
				$selected = 'selected="selected"';
			}

			$select .= '<option value="' . $param["flag_val"] . '" ' . $selected . '>' . ucfirst($param["flag_desc"]) . '</option>';
		}

		while ($array = mysql_fetch_array($result)) {
			$selected = '';
			if($param["selected"] == $array[$fval]){
				$selected = 'selected="selected"';
			}

			$arrDesc = explode(',', $param["desc"]);
			$curDesc = '';
			foreach ($arrDesc as $value) {
				$curDesc .= ucfirst($array[$value]) . ' ';
			}
			
			$select .= '<option value="' .  $array[$fval] . '" ' . $selected . '>' . trim($curDesc) . '</option>';

		}

		$select .= '</select>';
		return $select;
	}

	/*======================================================================*\
    	Function: generateImg
    	Purpose:  create an image element
		Notes: $width and $height are optional, 
			   if declared it will override the dimensions of $image
	\*======================================================================*/
	public function generateImg($image, $width = 0, $height = 0){
		if(is_file($image)){

			if($width == 0 && $height == 0){
				$size_output = '';
			}
			else {
				$size = getimagesize($image);
				$size_output = $size[3];
			}

			return '<img src="' . $image . '" ' . $size_output . ' border="0">';
		}
	}

	/*======================================================================*\
    	Function: generateMailto
    	Purpose:  create an anchor element with mailto link
		Notes: $email = email address
			   $subject = prefill subject
			   $body = prefill email message 
	\*======================================================================*/
	public function generateMailTo($email, $subject = '', $body = ''){
		$href = $email;
		if($subject != ''){
			$href .= '?subject=' . urlencode($subject) . '&body=' . urlencode($body);
		}
		return '<a href="' . $href . '">' . $email . '</a>';
	}

	/*======================================================================*\
    	Function: generateTableAdm
    	Purpose:  create a table element to list dbase entries for admin
    	Notes:  $result = mysql result array

    		    $param = set of table parameters 
    		    	$param["width"] = table width
					$param["padding"] = padding value
					$param["spacing"] = spacing value
					$param["columns"] = columns for table
										comma delimited vales
										(e.g. names,address,action)
					$param["pagination"] = 0 for no, 1 for yes
					$param["action_edit"] = url for edit, ignored if ''
					$param["action_delete"] = url for delete, ignored if ''
					$param["action_view"] = url for view, ignored if ''
					$param["icon_edit"] = image location for icon
					$param["icon_delete"] = image location for icon
					$param["icon_view"] = image location for icon
	\*======================================================================*/
	public function generateTableAdm($result, $param){
		
	}
	

} //end class

?>