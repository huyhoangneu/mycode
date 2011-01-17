<?php
/**
 * Mysql class
 */
class MYSQLDB {
	var $Host     = '';
	var $Database = '';
	var $User     = '';
	var $Password = '';
	
	var $Link_id  = 0;
	var $Query_id = 0;
	var $Record   = array();
	var $Row;
	
	var $Errno    = 0;
	var $Error    = '';
	
	var $Auto_free = 0;

	// Connect Mysql Database
	function connect() {
		if(0 == $this->Link_id) {
			$this->Link_id = @mysql_connect($this->Host, $this->User, $this->Password);
			$mysql_server    = mysql_get_server_info();
			if(!$this->Link_id) return 'Link-ID == false, connect failed';
			if(!mysql_query(sprintf('use %s', $this->Database), $this->Link_id)) return 'Can\'t use database ' . $this->Database;
			if($mysql_server > '4.1.10a') mysql_query('SET NAMES \'utf8\'');
			if($mysql_server > '5.0') mysql_query('SET sql_mode=\'\'');
		}
	}
	
	// Mysql Query
	function query($query_str) {
		$this->Query_id = mysql_query($query_str, $this->Link_id);
		$this->Row      = 0;
		if(!$this->Query_id) {
			$this->Errno = mysql_errno();
			$this->Error = mysql_error();
			printf('<font style="font-size: 14px;">SQL: ' . $query_str . '</font><br><font style="font-size: 12px;">Error Number: ' . $this->Errno . ' <br>Error: ' . $this->Error . '</font>');
			exit;
		}
		return $this->Query_id;
	}
	
	function next_record() {
		$this->Record = mysql_fetch_array($this->Query_id);
		$this->Row   += 1;

		$stat = is_array($this->Record);
		if(!$stat && $this->Auto_free) {
			mysql_free_result($this->Query_id);
			$this->Query_id = 0;
		}
		return $stat;
	}
	
	function f($Name) {
		return $this->Record[$Name];
	}
	
	function affected_rows() {
		return mysql_affected_rows($this->Link_id);
	}

	function num_rows() {
		return mysql_num_rows($this->Query_id);
	}
	
	function insert($tbale, $data) {
		while (list($key, $val) = each($data)) {
			$fields[] = $key;
			$values[] = "'" . $val . "'";
		}
		$query_str = 'INSERT INTO ' . $tbale . ' (' . implode(',', $fields). ') VALUES (' . implode(',', $values). ')';
		$this->query($query_str);
	}
	
	function instid() {
		return mysql_insert_id($this->Link_id);
	}
	
	function getRow($sql, $limited = false)
    {
        if ($limited == true)
        {
            $sql = trim($sql . ' LIMIT 1');
        }

        $res = $this->query($sql);
        if ($res !== false)
        {
            return mysql_fetch_assoc($res);
        }
        else
        {
            return false;
        }
    }
	
	function getOne($sql, $limited = false) 
	{
		if ($limited == true)
        {
            $sql = trim($sql . ' LIMIT 1');
        }

        $res = $this->query($sql);
        if ($res !== false)
        {
            $row = mysql_fetch_row($res);

            if ($row !== false)
            {
                return $row[0];
            }
            else
            {
                return '';
            }
        }
        else
        {
            return false;
        }
		
	}

	function getAll($sql) 
	{
		$res = $this->query($sql);
        if ($res !== false)
        {
            $arr = array();
            while ($row = mysql_fetch_assoc($res))
            {
                $arr[] = $row;
            }

            return $arr;
        }
        else
        {
            return false;
        }
	}
	function autoExecute($table, $field_values, $mode = 'INSERT', $where = '', $querymode = '')
    {
        $field_names = $this->getCol('DESC ' . $table);

        $sql = '';
        if ($mode == 'INSERT')
        {
            $fields = $values = array();
            foreach ($field_names AS $value)
            {
                if (array_key_exists($value, $field_values) == true)
                {
                    $fields[] = $value;
                    $values[] = "'" . $field_values[$value] . "'";
                }
            }

            if (!empty($fields))
            {
                $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
            }
        }
        else
        {
            $sets = array();
            foreach ($field_names AS $value)
            {
                if (array_key_exists($value, $field_values) == true)
                {
                    $sets[] = $value . " = '" . $field_values[$value] . "'";
                }
            }

            if (!empty($sets))
            {
                $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
            }
        }

        if ($sql)
        {
            return $this->query($sql, $querymode);
        }
        else
        {
            return false;
        }
    }
	function getCol($sql)
    {
        $res = $this->query($sql);
        if ($res !== false)
        {
            $arr = array();
            while ($row = mysql_fetch_row($res))
            {
                $arr[] = $row[0];
            }

            return $arr;
        }
        else
        {
            return false;
        }
    }
	// Close Mysql Database
	function close(){
		if( 0 != $this->Link_id ) mysql_close($this->Link_id);
	}
	
}

class MYSQL extends MYSQLDB {
	var $Host     = '';
	var $Database = '';
	var $User     = '';
	var $Password = '';
	
	function MYSQL($db_config) {
		$this->Host     = $db_config['host'];
		$this->Database = $db_config['db'];
		$this->User     = $db_config['user'];
		$this->Password = $db_config['password'];
		$this->connect();
	}
}
?>
