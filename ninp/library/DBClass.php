<?php

class DBclass {

    function db_con($ser_name, $usename, $password, $db_name) {
        $mysql_connect = mysql_pconnect($ser_name, $usename, $password) or die("connect mysql error");
        mysql_select_db($db_name, $mysql_connect);
        mysql_query("SET NAMES utf8");
    }

//con_function_end

    function show_404() {
        header('HTTP/1.0 404 Not Found');
        exit(0);
    }

    function redirect($url) {
        header("Location: $url");
        exit(0);
    }

    /*     * ****************************  
      Variable Handle
     * **************************** */

    function last_strstr($haystack, $needle) {
        while (strstr($haystack, $needle) != '') {
            $haystack = substr($haystack, 1, strlen($haystack) - 1);
        }

        return $haystack;
    }

    function trim_variable($content, $ignore_empty = FALSE) {
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

    function clear_duplicate_array($origin_array) {
        //if $content is an array
        if (is_array($origin_array)) {

            $new_array = array();

            foreach ($origin_array as $field => $value) {
                if (!in_array($value, $new_array) && $value != '')
                    $new_array[$field] = $value;
            }

            $origin_array = $new_array;
        }

        return $origin_array;
    }

    /*     * ***************
     * Database
     * ************** */

    /**
     * execute sql and return single field
     */
    function db_row_field($sql, $field) {
        $result = mysql_query($sql) or die(mysql_error());
        if (!$result || !mysql_num_rows($result))
            return false;

        return mysql_result($result, 0, $field);
    }

    /**
     * execute sql and return single row
     */
    function db_row($sql) {

        $result = mysql_query($sql) or die(mysql_error());
        $record = mysql_fetch_assoc($result);
        if (!$record)
            return false;

        return $record;
    }

    /**
     * execute sql and return count of row
     */
    function db_row_number($sql) {
        $result = mysql_query($sql) or die(mysql_error());
        return mysql_num_rows($result);
    }

    /**
     * execute sql and return all record
     */
    function db_all($sql) {
        $result       = mysql_query($sql) or die(mysql_error());
        $record_fetch = mysql_fetch_assoc($result);

        if (!$record_fetch)
            return false;

        do {
            $record[] = $record_fetch;
        } while ($record_fetch = mysql_fetch_assoc($result));

        return $record;
    }

    function db_insert($tbname, $data) {

//        $data = $this->trim_variable($data);

        $sql         = "INSERT INTO `" . $tbname . "` ";
        $value_to_db = '';
        $field_to_db = '';
        
        
      

        foreach ($data as $field => $val) {
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

    
        if (mysql_query($sql))
            return mysql_insert_id();
        else
            return die(mysql_error());
    }

    /**
     * Update record
     */
    function db_update($tbname, $data, $condition = '') {

        $data = $this->trim_variable($data);

        $sql = "UPDATE `" . $tbname . "` SET ";

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

       

        return mysql_query($sql) or die(mysql_error());
    }

    function db_batch_insert($tbname, $field, $batch_data) {

        $sql       = "INSERT INTO `" . $tbname . "` ";
        $row_value = array();

        $field_value = array();
        foreach ($field as $name) {
            array_push($field_value, $name);
        }

        foreach ($batch_data as $data) {
            $data = trim_variable($data);

            $value_to_db = '';
            foreach ($data as $field => $val) {

                if (strtolower($val) == 'null')
                    $value_to_db .= "NULL, ";
                elseif (strtolower($val) == 'now()')
                    $value_to_db .= "NOW(), ";
                elseif (is_string($val))
                    $value_to_db .= "'" . sql_clean($val) . "', ";
                else
                    $value_to_db .= "'" . sql_clean($val) . "', ";
            }
            array_push($row_value, "(" . rtrim($value_to_db, ', ') . ")");
        }

        $sql .= "(" . implode(", ", $field_value) . ") VALUES ";
        $sql .= implode(", ", $row_value);

        if (mysql_query($sql))
            return mysql_insert_id();
        else
            return die(mysql_error());
    }

    function db_remove($tbname, $condition = '') {
        $sql = "DELETE FROM `" . $tbname . "` ";

        if (!empty($condition))
            $sql .= ' WHERE ' . $condition;

        return mysql_query($sql) or die(mysql_error());
    }

    /*     * ***************
     * Security
     * ************** */

    function sql_clean($content, $ignore_field = FALSE) {

        //if magic_quotes_gpc=On do nothing
        if (!get_magic_quotes_gpc() || $GLOBALS['AUTO_STRIPSLASHES'] == true) {

            //if $content is an array
            if (is_array($content) && $content != NULL) {

                foreach ($content as $key => $value) {
                    if ($value == NULL || (is_array($ignore_field) && in_array($key, $ignore_field)))
                        continue;

                    $content[$key] = mysql_real_escape_string($value);
                }
            } else {

                //if $content is not an array
                $content = mysql_real_escape_string($content);
            }
        }

        return $content;
    }

    function html_encode($content, $ignore_field = FALSE) {
        //if $content is an array
        if (is_array($content) && $content != NULL) {

            foreach ($content as $key => $value) {
                if ($value == NULL || (is_array($ignore_field) && in_array($key, $ignore_field)))
                    continue;
                $content[$key] = htmlentities($value, ENT_COMPAT, 'UTF-8');
            }
        } else {

            //if $content is not an array
            $content = htmlentities($content, ENT_COMPAT, 'UTF-8');
        }

        return $content;
    }

    function unset_empty_array($content, $ignore_field = FALSE) {
        //if $content is an array
        if (is_array($content)) {

            /*             * *********		william try to stop this action 2012/4/7
              foreach ($content as $field => $value) {
              if(is_array($ignore_field) && in_array($field, $ignore_field))
              continue;
              if(empty($value)) unset($content[$field]);
              }********** */
        }
        else {

            if (empty($content))
                return false;
        }

        return $content;
    }

    function clear_empty_array($content, $ignore_field = FALSE) {
        //if $content is an array
        if (is_array($content)) {

            foreach ($content as $field => $value) {
                if (is_array($ignore_field) && in_array($field, $ignore_field))
                    continue;
                if (empty($value))
                    $content[$field] = NULL;
            }
        } else {

            if (empty($content))
                return false;
        }

        return $content;
    }

    /**
     * replcae quotes to HTML entities by names or numbers
     *
     * @param (string) escaped string value
     * @param (string) default ='number' will be return to number entities you can use ='name' to return name entities
     * Note : don't use ='name' coz (&apos;) (does not work in IE)
     */
    function quote2entities($string, $entities_type = 'number') {
        $search                     = array("\"", "'");
        $replace_by_entities_name   = array("&quot;", "&apos;");
        $replace_by_entities_number = array("&#34;", "&#39;");
        $do                         = null;
        if ($entities_type == 'number') {
            $do = str_replace($search, $replace_by_entities_number, $string);
        }
        else if ($entities_type == 'name') {
            $do = str_replace($search, $replace_by_entities_name, $string);
        }
        else {
            $do = addslashes($string);
        }
        return $do;
    }

    /*     * ***************
     * Date and Time
     * ************** */

    function set_date($H = '', $i = '', $s = '', $m = '', $d = '', $Y = '') {
        $date_setting = array('H' => "$H",
            'i' => "$i",
            's' => "$s",
            'm' => "$m",
            'd' => "$d",
            'Y' => "$Y");

        foreach ($date_setting as $date_key => $date_value) {
            if ($date_value == '') {
                $arr_date_exp[] = date($date_key);
            }
            else {
                $arr_date_exp[] = date($date_key) + ($date_value);
            }
        }

        return date('Y-m-d H:i:s', mktime($arr_date_exp[0], $arr_date_exp[1], $arr_date_exp[2], $arr_date_exp[3], $arr_date_exp[4], $arr_date_exp[5]));
    }

    function datetime_format($datetime, $format = 'Y-m-d') {
        return date($format, strtotime($datetime));
    }

    /*     * ****************************  
      File
     * **************************** */

    function remove_file($file_path) {
        if (is_file($file_path)) {
            unlink($file_path);
            return true;
        }

        return false;
    }

    /*     * ****************************  Recursively delete a directory that is not empty ***************************** */

    function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);

            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        rrmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }

            reset($objects);
            rmdir($dir);
        }
    }

    /*     * ****************************  
      Ob Series
     * **************************** */

    function ob_get_clear() {
        $return = ob_get_contents();
        ob_end_clean();

        return $return;
    }

    /*     * ****************************  
      Input
     * **************************** */

    function select_option($foreach, $selectName, $optionValue, $checkValue, $optionName) {

        if (count($foreach) > 0) {
            $return = '
		<select name="' . $selectName . '">
		';

            foreach ($foreach as $value) {
                $return .=
                        '
			<option value="' . $call_sql_fetch[$optionValue] . '" ' . ($checkValue == $call_sql_fetch[$optionValue] ? 'selected="selected"' : '') . '>' . $call_sql_fetch[$optionName] . '</option>
			';
            }
            $return .=
                    '
		</select>
		';
        }

        return $return;
    }

    /*     * ****************************  
      Serialize
     * **************************** */

    function adv_serialize($data) {
        return serialize(trim_variable($data));
    }

    function adv_unserialize($data) {
        return unserialize(preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $data));
    }

    /**
     * This program is free software. It comes without any warranty, to
     * the extent permitted by applicable law. You can redistribute it
     * and/or modify it under the terms of the Do What The Fuck You Want
     * To Public License, Version 2, as published by Sam Hocevar. See
     * http://sam.zoy.org/wtfpl/COPYING for more details.
     */

    /**
     * Tests if an input is valid PHP serialized string.
     *
     * Checks if a string is serialized using quick string manipulation
     * to throw out obviously incorrect strings. Unserialize is then run
     * on the string to perform the final verification.
     *
     * Valid serialized forms are the following:
     * <ul>
     * <li>boolean: <code>b:1;</code></li>
     * <li>integer: <code>i:1;</code></li>
     * <li>double: <code>d:0.2;</code></li>
     * <li>string: <code>s:4:"test";</code></li>
     * <li>array: <code>a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}</code></li>
     * <li>object: <code>O:8:"stdClass":0:{}</code></li>
     * <li>null: <code>N;</code></li>
     * </ul>
     *
     * @author		Chris Smith <code+php@chris.cs278.org>
     * @copyright	Copyright (c) 2009 Chris Smith (http://www.cs278.org/)
     * @license		http://sam.zoy.org/wtfpl/ WTFPL
     * @param		string	$value	Value to test for serialized form
     * @param		mixed	$result	Result of unserialize() of the $value
     * @return		boolean			True if $value is serialized data, otherwise false
     */
    function is_serialized($value, &$result = null) {
        // Bit of a give away this one
        if (!is_string($value)) {
            return false;
        }

        // Serialized false, return true. unserialize() returns false on an
        // invalid string or it could return false if the string is serialized
        // false, eliminate that possibility.
        if ($value === 'b:0;') {
            $result = false;
            return true;
        }

        $length = strlen($value);
        $end    = '';

        switch ($value[0]) {
            case 's':
                if ($value[$length - 2] !== '"') {
                    return false;
                }
            case 'b':
            case 'i':
            case 'd':
                // This looks odd but it is quicker than isset()ing
                $end .= ';';
            case 'a':
            case 'O':
                $end .= '}';

                if ($value[1] !== ':') {
                    return false;
                }

                switch ($value[2]) {
                    case 0:
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        break;

                    default:
                        return false;
                }
            case 'N':
                $end .= ';';

                if ($value[$length - 1] !== $end[0]) {
                    return false;
                }
                break;

            default:
                return false;
        }

        if (($result = @unserialize($value)) === false) {
            $result = null;
            return false;
        }
        return true;
    }

    function translation($str) {
        //$str=preg_replace("/[\]/","(xiegang)",$str);
        $str = preg_replace('/\\\\\\\/', "&#92;", $str);
        $str = preg_replace("/\\\'/", "&#34;", $str);
        $str = preg_replace('/\\\"/', "&#39;", $str);

        return $str;
    }

    function sendmsg($str) {
        //$str=preg_replace("/[\]/","(xiegang)",$str);
        $str = preg_replace('/\\\\\\\/', "", $str);
        $str = preg_replace("/\\\'/", "'", $str);
        $str = preg_replace('/\\\"/', '"', $str);

        return $str;
    }

    function to($path) {

        header("Location:$path");
    }

}
