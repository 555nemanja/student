<?php

require_once("config.php");
require_once("database.class.php");
require_once("student.class.php");

if (isset($_GET["student"])) {

    $student = new Student($_GET["student"]);
    if ($student->error) {
        die($student->error);
    }

    $student->print_data();

} else {

    header("Location: /?student=" . rand(1, 10));
    exit;

}
