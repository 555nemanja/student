<?php

class Student
{
    private $db;
    public $error;

    public $data;


    public function __construct($id)
    {
        $this->db = new Database;

        $student = (INT)$this->db->escape($id);
        $existsStudent = $this->db->numRows("students WHERE id = '$student'");

        if ($existsStudent != 0) {
            $this->data = $this->db->fetch("SELECT * FROM students WHERE id = '$student'");
        } else {
            $this->error = "Student doesn't exist, try between 1 and 10!";
        }
    }

    public function print_data()
    {
        $this->data["grades"] = $this->db->select("SELECT grade FROM grades WHERE student_id = '" . $this->data["id"] . "'");
        $this->data["average"] = array_sum($this->data["grades"]) / count($this->data["grades"]);

        if ($this->data["board"] == 1) {
            $this->data["final"] = $this->data["average"] >= 7 ? "Pass" : "Fail";
            $this->data["grades"] = implode(", ", $this->data["grades"]);

            header('Content-Type: application/json');

            unset($this->data["board"]);

            print json_encode($this->data, JSON_PRETTY_PRINT);

        } elseif ($this->data["board"] == 2) {

            if (count($this->data["grades"]) > 2) {
                sort($this->data["grades"]);
                array_shift($this->data["grades"]);
            }
            $this->data["final"] = end($this->data["grades"]) > 8 ? "Pass" : "Fail";

            $this->data["grades"] = implode(", ", $this->data["grades"]);


            header("Content-type: text/xml; charset=utf-8");

            unset($this->data["board"]);

            print $this->arrayToXml($this->data, "<student/>");

        } else {
            die("Student's board error!");
        }
    }
    public function arrayToXml($array, $rootElement = null, $_xml = null)
    {
        if ($_xml === null) {
            $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
        }
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $this->arrayToXml($v, $k, $_xml->addChild($k));
            } else {
                $_xml->addChild($k, $v);
            }
        }
        return $_xml->asXML();
    }
}
