<?php

/*======================================================================*\
    FDSQL Class
    Author: Fernan de Dios
    Email: info@fernandedios.com
    Date: 09/25/2010
    Version: 1.07
    General Notes: $file = file executing the script, optional
    			   $line = line number, optional
\*======================================================================*/

include_once("string.php");

class FDSQL extends FDUtilString {

	var $db_link;
	var $db_host;
	var $db_name;
	var $db_user;
	var $db_pass;

	/*======================================================================*\
    	Function: openDb
    	Purpose:  open database connection
	\*======================================================================*/
	public function openDB($host, $name, $user, $pass, $file = __FILE__, $line = __LINE__){
		$this->db_host = $host;
		$this->db_user = $user;
		$this->db_pass = $pass;
		$this->db_name = $name;

		if (!$this->db_link){
			$this->db_link = mysql_connect($this->db_host, $this->db_user, $this->db_pass) OR $this->showError("Can't connect to database: " . mysql_error(), "die", $file, $line);
		}

		if (!mysql_select_db($this->db_name))
			$this->showError("Can't select database: " . mysql_error(), "die", $file, $line);
	}

	/*======================================================================*\
    	Function: createDb
    	Purpose:  create new database
    	Notes: $usedb = if set to true, it will select the newly created db
	\*======================================================================*/
	public function createDb($name_db, $use_db = false, $file = __FILE__, $line = __LINE__) {
		$query = "CREATE DATABASE $name_db";
		$this->runQuery($query, $file, $line);

		if($use_db){
			$this->db_name = $name_db;
			if (!mysql_select_db($this->db_name)){
				$this->showError("Can't select database: " . mysql_error(), "die", $file, $line);
			}
		}
	}

	/*======================================================================*\
    	Function: createTable
    	Purpose: create a table on the database
    	Notes: $primary (e.g. $primary["name"] = "id"; 
    						  $primary["type"] = "int";
    						  $primary["inc"] = "AUTO_INCREMENT";
    						  $primary["null"] = "NOT NULL";  )

    		   $fields  (e.g.  $fields["comments"] = "varchar(255)";
    		   				   $fields["size"] = "int";  )
	\*======================================================================*/
	public function createTable($table_name, $primary, $fields){
		$primary_field = $primary["name"] . " " . $primary["type"] . " " . $primary["null"] . " " . $primary["inc"] . ", PRIMARY KEY(" . $primary["name"] . ")";

        $other_fields = array();

		foreach ($fields as $key => $value) {
			$other_fields[] = $key . " " . $value;
		}

		$com_fields = implode(",", $other_fields);
		$query = "CREATE TABLE $table_name ($primary_field, $com_fields)";
		$this->runQuery($query);
	}

	/*======================================================================*\
    	Function: getAll, getAllOrdered, getAllOrderedLimit, getField
    	Purpose:  get records from db
    	Return:   Array
    	Notes:	  $table = table name
    			  $order = column reference for ordering
    			  $dir = ASC or DESC
    			  $offset = start getting records at this number
    			  $count = number of records to get
    			  $index = column reference
    			  $id = actual index value
    			  $prefs = multiple references. 
    			  		(e.g.  $prefs["username"] = "'fernan'"; 
    			  		       $prefs["password"] = "'password'"; 
    			  		       WHERE username = 'fernan' AND password = 'password'
     			  		       important! enclose with '' if value is a string)
	\*======================================================================*/
	public function getAll($table){
		$query = "SELECT * FROM {$table}";
		$results = $this->runQuery($query);
		return $results;
	}

	public function getAllOrdered($table, $order, $dir = "ASC"){
		$query = "SELECT * FROM $table ORDER BY $order $dir";
		$results = $this->runQuery($query);
		return $results;
	}

	public function getAllOrderedLimit($table, $order, $dir = "ASC", $offset = 0, $count = 1) {
		$query = "SELECT * FROM $table ORDER BY $order $dir LIMIT $offset, $count";
		$results = $this->runQuery($query);
		return $results;
	}

	public function getField($table, $field, $index, $id) {
		$query = "SELECT $field FROM $table WHERE $index = $id";
		$results = $this->runQuery($query);
		return $results;
	}

	public function getFieldMultiRef($table, $field, $arr_pref){
        $where = array();
		foreach ($arr_pref as $key => $value) {
			$where[] = $key . " = " . $value;
		}

		$and_where = implode(" AND ", $where);
		$query = "SELECT $field FROM $table WHERE $and_where";
		$results = $this->runQuery($query);
		return $results;
	}

	public function getNumRows($table, $pref, $file = __FILE__, $line = __LINE__) {
        $where = array();
		foreach ($pref as $key => $value) {
			$where[] = $key . " = " . $value;
		}

		$and_where = implode(" AND ", $where);

		$query = "SELECT COUNT(*) as num FROM $table $and_where";
		$this->runQuery($query, $file, $line);
	}

	/*======================================================================*\
    	Function: insertRow
    	Purpose:  insert record to db
    	Return: id of the inserted record
    	Notes: $data should be an array 
    		   (e.g. $data["comments"] = "'hello there!'";
    		        where the key is the field name 
    		        important! enclose with '' if value is a string	   )
	\*======================================================================*/
	public function insertRow($table, $data, $file = __FILE__, $line = __LINE__) {
        $fields = array();
        $values = array();
		foreach($data as $key => $value){
			$fields[] = $key;
			$values[] = "'" . $value . "'";
		}

		$cm_fields = implode(',', $fields);
		$cm_values = implode(',', $values);

		$query = "INSERT into $table ($cm_fields) values ($cm_values)";
		$this->runQuery($query, $file, $line);
		return mysql_insert_id();
	}

	/*======================================================================*\
    	Function: updateRows
    	Purpose:  update database records(s)
    	Notes: $index = column reference (e.g. id)
    		   $id = actual index value (e.g. 1 )
    		   $data = array of updated values, key is the field name
    		   		   (e.g. $data["fname"] = "'Fernan'";
    		   		   		 important! enclose with '' if value is a string)
    		   $table = table name
	\*======================================================================*/
	public function updateRows($table, $data, $index, $id, $file = __FILE__, $line = __LINE__){
        $setData = array();
		foreach ($data as $key => $value) {
			$setData[] = $key . "='" . $value . "'";
		}
		$upd = implode(',', $setData);
		$query = "UPDATE $table SET $upd WHERE $index = $id";
		$this->runQuery($query, $file, $line);
	}

	/*======================================================================*\
    	Function: deleteRows
    	Purpose: remove database records(s)
    	Notes: $index = column reference (e.g. id)
    		   $id = actual index value (e.g. 1 )
    		   $table = table name
	\*======================================================================*/
	public function deleteRows($table, $index, $id, $file = __FILE__, $line = __LINE__) {
		$query = "DELETE FROM $table WHERE $index = $id";
		$this->runQuery($query, $file, $line);
	}

	/*======================================================================*\
    	Function: runQuery
    	Purpose:  main sql query method 
	\*======================================================================*/
	public function runQuery($query, $file = __FILE__, $line = __LINE__){
		$result = mysql_query($query) OR $this->showError(mysql_error() . "<br />SQL Query: " . $query, "die", $file, $line);
		$total = ($this->getQueryType($query) == "SELECT") ? mysql_num_rows($result) :  mysql_affected_rows();
		if ($total) { return $result; } else { return 0; }
	}

	/*======================================================================*\
    	Function: getQueryType
    	Purpose:  get query type from query string
	\*======================================================================*/
	public function getQueryType($query) {
		$temp = explode(" ", $query);
		return $temp[0];
	}

	/*======================================================================*\
    	Function: sanitizeSqlString
    	Purpose:  prepare string for database entry
	\*======================================================================*/
	public function sanitizeSqlString($var) {
		$var = trim($var);
		$var = $this->removeComma($var, '-|-');

		if(function_exists('mysql_real_escape_string')) {
			return mysql_real_escape_string($var);
		} 
		else {
			return addslashes($var);
		}
	}

	/*======================================================================*\
    	Function: removeComma
    	Purpose:  used to prepare string for db entry
	\*======================================================================*/
	public function removeComma($var, $rep) {
		return str_replace(",", $rep, $var);	
	}

	/*======================================================================*\
    	Function: displayFromDb
    	Purpose:  Format string coming from the database
	\*======================================================================*/
	public function displayFromDb($var) {
		$var = str_replace("-|-", ",", $var);
		return stripslashes($var);
	}

	/*======================================================================*\
    	Function: closeDB
    	Purpose:  close database connection
	\*======================================================================*/
	public function closeDB(){
		if ($this->db_link) mysql_close($this->db_link);
	}

} //end class



?>