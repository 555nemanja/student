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
        $this->data["board_name"]       = $this->db->check("SELECT name FROM boards WHERE id = '" . $this->data["board"] . "'");
        $this->data["total_grades"]     = $this->db->check("SELECT count(id) FROM grades WHERE student_id = '" . $this->data["id"] . "'");
        $this->data["average_grade"]    = $this->db->check("SELECT sum(grade)/count(id) FROM grades WHERE student_id = '" . $this->data["id"] . "'");
        $this->data["all_grades"]       = $this->db->select("SELECT grade FROM grades WHERE student_id = '" . $this->data["id"] . "'");


        if ($this->data["board"] == 1) {
            $this->data["status"] = $this->data["average_grade"] >= 7 ? "Pass" : "Fail";

            header('Content-Type: application/json');

            $return = array();
            $return["student"] = $this->data;
            print json_encode($return, JSON_PRETTY_PRINT);

        } elseif ($this->data["board"] == 2) {

            if ($this->data["total_grades"] > 2) {
                sort($this->data["all_grades"]);
                array_shift($this->data["all_grades"]);
            }
            $this->data["status"] = end($this->data["all_grades"]) > 8 ? "Pass" : "Fail";

            $this->data["all_grades"] = implode(",", $this->data["all_grades"]);


            header("Content-type: text/xml; charset=utf-8");
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
