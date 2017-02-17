<?php

/*======================================================================*\
    FD Pagination Class
    Author: Fernan de Dios
    Email: info@fernandedios.com
    Date: 10/04/2012
    Version: 1.00
\*======================================================================*/

include_once("sql.php");

class FDPagination extends FDSQL {

	/*======================================================================*\
    	Function: generatePages
    	Purpose: generate pagination 
    	Notes:  $id =  table primary id
				$table = table name
				$limit = number of items to include
				$path = url for links, (e.g. index.php?section=items&)
				$filter = conditions (e.g. "WHERE is_video = 1")
				$page = current page number
				$class = css class to use (e.g. 'pagination')
	\*======================================================================*/
	public function generatePages($id, $table, $limit, $path, $filter, $page, $class) {

		$query = $this->runQuery("SELECT COUNT(*) as num FROM $table $filter", __FILE__, __LINE__);
		$row = mysql_fetch_array(mysql_query($query));
		$total_pages = $row['num'];

		$start = ($page - 1) * $limit;
		
		$adjacents = "2";

		$sql = $this->runQuery("SELECT " . $id . " FROM " . $table . " " . $filter . " LIMIT $start, $limit", __FILE__, __LINE__);
		$result = mysql_query($sql);

		$prev = $page - 1;
		$next = $page + 1;
		$lastPage = ceil($total_pages/$limit);
		$lpm1 = $lastPage - 1;

		$pagination = "";

		if($lastPage > 1) {
			$pagination .= "<div class='" . $class . "'><ul>";
			
			if ($page > 1) {
				$pagination .= "<li><a href='" . $path . "page=$prev'>prev</a></li>";
			}

			if ($lastPage < 7 + ($adjacents * 2)) {
				for ($counter = 1; $counter <= $lastPage; $counter++) {
					if ($counter == $page) {
						$pagination .= "<li><strong>" . $counter . "</strong></li>";
					}
					else {
						$pagination .= "<li><a href='".$path."page=$counter'>$counter</a></li>";
					}
				}
			}
			elseif($lastPage > 5 + ($adjacents * 2)) {
				if($page < 1 + ($adjacents * 2)){
					
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++){
						if ($counter == $page){
							$pagination .= "<li><strong>" . $counter . "</strong></li>";
						}
						else {
							$pagination .= "<li><a href='" . $path . "page=$counter'>$counter</a></li>";
						}
					}
					$pagination .= "...";
					$pagination .= "<li><a href='" . $path . "page=$lpm1'>$lpm1</a></li>";
					$pagination .= "<li><a href='" . $path . "page=$lastPage'>$lastPage</a></li>";
				}
				elseif($lastPage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
					$pagination .= "<li><a href='" . $path . "page=1'>1</a></li>";
					$pagination .= "<li><a href='" . $path . "page=2'>2</a></li>";
					$pagination .= "...";
					
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
						if ($counter == $page){
							$pagination .= "<li><strong>" . $counter . "</strong></li>";
						}
						else {
							$pagination .= "<li><a href='" . $path . "page=$counter'>$counter</a></li>";
						}
					}
					$pagination .= "..";
					$pagination .= "<li><a href='" . $path . "page=$lpm1'>$lpm1</a></li>";
					$pagination .= "<li><a href='" . $path . "page=$lastPage'>$lastPage</a></li>";
				}
				else {
					$pagination .= "<li><a href='" . $path . "page=1'>1</a></li>";
					$pagination .= "<li><a href='" . $path . "page=2'>2</a></li>";
					$pagination .= "..";
					
					for ($counter = $lastPage - (2 + ($adjacents * 2)); $counter <= $lastPage; $counter++) {
						if ($counter == $page){
							$pagination .= "<li><strong>" . $counter . "</strong></li>";
						}
						else {
							$pagination .= "<li><a href='" . $path . "page=$counter'>$counter</a><li>";
						}
					}
				}
			}
		
			if ($page < $counter - 1) {
				$pagination .= "<li><a href='" . $path . "page=$next'>next</a></li>";
			}
			else {
				$pagination .= "</ul></div>\n"; 
			}
		}
		return $pagination;
	}

}


?>