<?php

class Database
{
    private $conn;
    public function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $this->conn->set_charset("utf8");
    }
    public function query($sql)
    {
        $result = $this->conn->query($sql);
        if (strpos(strtoupper($sql), 'INSERT INTO') !== false) $result = $this->conn->insert_id;
        if (strpos(strtoupper($sql), 'UPDATE') !== false) $result = $this->conn->affected_rows;
        if ($this->conn->error) die($this->conn->error . "<br><small>" . $sql . "</small>");
        return $result;
    }
    public function select($sql)
    {
        $return = array();
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $return[] = current($row);
            }
        }
        return $return;
    }
    public function fetch($sql)
    {
        return $this->query($sql . " LIMIT 1")->fetch_assoc();
    }
    public function numRows($sql)
    {
        return $this->query("SELECT id FROM " . $sql)->num_rows;
    }
    public function escape($sql)
    {
        return $this->conn->real_escape_string($sql);
    }
    public function check($sql)
    {
        @$b = (empty(current($this->fetch($sql)))) ? 0 : current($this->fetch($sql));
        return $b;
    }
}
