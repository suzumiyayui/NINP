<?php

class dbiclass {

    var $con;

    function __construct() {


    
    }

    public function db_con($ser_name, $usename, $password, $db_name) {
      
        $this->con = mysqli_connect($ser_name, $usename, $password, $db_name);
        
        mysqli_set_charset($this->con, "utf8");
        
        if (mysqli_connect_errno($this->con)) {
            echo "Failed to connect to MySQLi: " . mysqli_connect_error();
        
            
        }
    }

    protected function query_start($sql, $id = NULL) {

        if (!$this->con)
            exit('No Connet To DB');

        $query = mysqli_query($this->con, $sql);
        $error = mysqli_error($this->con);

        if (!$id) {
            if ($error) {
                exit($error);
            } else {
                return $query;
            }
        } else {

            if ($error) {
                exit($error);
            } else {
                return mysqli_insert_id($this->con);
            }
        }
    }

   protected function query_end($query) {

        mysqli_free_result($query);
    }

   protected function sql_clean($content, $ignore_field = FALSE) {

        if (!get_magic_quotes_gpc() || $GLOBALS['AUTO_STRIPSLASHES'] == true) {

            if (is_array($content) && $content != NULL) {

                foreach ($content as $key => $value) {
                    if ($value == NULL || (is_array($ignore_field) && in_array($key, $ignore_field)))
                        continue;

                    $content[$key] = mysqli_real_escape_string($value);
                }
            } else {


                $content = mysqli_real_escape_string($this->con, $content);
            }
        }


        return $content;
    }

   protected function trim_variable($content, $ignore_empty = FALSE) {
        //if $content is an array
        if (is_array($content)) {

            foreach ($content as $field => $value) {

                if (is_array($value))
                    $new_content[$field] = trim_variable($value);
                else
                    $new_content[$field] = trim($value);
            }

            $content = $new_content;
        } else {

            $content = trim($content);
        }

        return $content;
    }

    public function db_row($sql) {

        $query = $this->query_start($sql);

        $row = mysqli_fetch_array($query, MYSQLI_ASSOC);

        $this->query_end($query);

        return $row;
    }

    public function db_all($sql) {

        $query = $this->query_start($sql);

        $return = array();

        while ($row = mysqli_fetch_assoc($query)) {

            $return[] = $row;
        }

        $this->query_end($query);

        return $return;
    }

    public function db_insert($tableName, $array) {

        mysqli_set_charset($this->con, "utf8");

        $sql = "INSERT INTO `" . $tableName . "` ";
        $value_to_db = '';
        $field_to_db = '';

        foreach ($array as $field => $val) {


            $field_to_db .= "`$field`, ";

            if (strtolower($val) == 'null')
                $value_to_db .= "NULL, ";
            elseif (strtolower($val) == 'now()')
                $value_to_db .= "NOW(), ";
            elseif (is_string($val))
                $value_to_db .= "'" . $this->sql_clean($val) . "', ";
            else
                $value_to_db .= "'" . $this->sql_clean($val) . "', ";
        }

        $sql .= "(" . rtrim($field_to_db, ', ') . ") VALUES (" . rtrim($value_to_db, ', ') . ");";

        return $this->query_start($sql, TRUE);
    }

    public function db_updata($tableName, $array, $condition = '') {

        $data = $this->trim_variable($array);

        $sql = "UPDATE `" . $tableName . "` SET ";

        foreach ($data as $field => $val) {
            if (strtolower($val) == 'null')
                $sql .= "`$field` = NULL, ";
            elseif (strtolower($val) == 'now()')
                $sql .= "`$field` = NOW(), ";
            elseif (preg_match("/^increment\((\-?\d+)\)$/i", $val, $m))
                $sql .= "`$field` = `$field` + $m[1], ";
            elseif (is_string($val))
                $sql.= "`$field`='" . $this->sql_clean($val) . "', ";
            else
                $sql.= "`$field`='" . $this->sql_clean($val) . "', ";
        }

        $sql = rtrim($sql, ', ');



        if (!empty($condition))
            $sql .= " WHERE " . $condition;

        return $this->query_start($sql);
    }

    public function db_remove($tableName, $condition = '') {

        $sql = "DELETE FROM `" . $tableName . "` ";

        if (!empty($condition))
            $sql .= ' WHERE ' . $condition;

        return $this->query_start($sql);
    }

    public function to($path) {

        header("Location:$path");
    }

}
