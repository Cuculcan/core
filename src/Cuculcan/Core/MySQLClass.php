<?php

namespace Cuculcan\Core;

class MySQLClass {

    private $link = NULL;
    private $host = '';
    private $user = '';
    private $password = '';
    private $database = '';
    private $result = 0;

    public function __construct($h, $u, $p, $db) {
        $this->host = $h;
        $this->user = $u;
        $this->password = $p;
        $this->database = $db;
        $this->Connect();
    }

    function Connect() {
        $this->link = mysqli_connect($this->host, $this->user, $this->password);
        mysqli_query($this->link, 'SET NAMES utf8');
        $return = mysqli_select_db($this->link, $this->database);
        return $return;
    }

    function Query($q) {
        $this->result = mysqli_query($this->link, $q);
        return $this->result;
    }

    function MultiQuery($q) {

        if(mysqli_multi_query($this->link, $q)) {
            do {
            } while (mysqli_more_results($this->link) && mysqli_next_result($this->link));

            return true;
        }

        return false;
    }

    function MysqlInsertId() {
        return mysqli_insert_id($this->link);
    }

    function FetchArray($result = 0) {
        if ($result) return mysqli_fetch_array($result);
        return mysqli_fetch_array($this->result);
    }

    function NumRows($result = false) {
        if ($result!==false)
        {
            if (!$result){
                return false;
            }
            else {
                return mysqli_num_rows($result);
            }
        }
        return mysqli_num_rows($this->result);
    }

    function Close() {
        if(isset($this->link)){
            mysqli_close($this->link);
        }
    }

    public function getResult() {
        return $this->result;
    }

    /**
     * @return null
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param null $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    public function escape($string){
        return mysqli_real_escape_string($this->link, $string);
    }

}
?>