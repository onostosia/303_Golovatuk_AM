<?php

require_once '../vendor/autoload.php';

#$database = new PDO('sqlite:../data/students.db');
if(!R::testConnection()){
    exit("Connection Error");
}

// ADD NEW STUDENT
if(isset($_POST['add'])) {
    #var_dump($_POST);
    //print_r($_POST);
    $id = R::getAll("select max(id) as m from students");
    $max_id = $id[0]["m"]+1;

    $s = $_POST['surname'];
    $n = $_POST['name'];
    $l = $_POST['lastname'];
    $g = $_POST['gender'];
    $d = $_POST['date_of_birth'];
    $d_a = (date("Y") - $_POST['group'][0])."-09-01";
    $d_id = $_POST['group'][-1];
    $c = $_POST['card'];
    R::exec("INSERT INTO students(
                     id,
                     surname,
                     name,
                     lastname,
                     date_of_birth,
                     gender,
                     date_of_admission,
                     direction_id,
                     student_card) VALUES (?,?,?,?,?,?,?,?,?)", [$max_id,$s,$n,$l,$d,$g,$d_a,$d_id,$c]);

    R::exec("INSERT INTO groups VALUES (?, ?);", [$max_id, $_POST['group']]);
    header("Location:".$_SERVER['HTTP_REFERER']);
}

// DELETE STUDENT
// DELETE STUDENT (обновить страницу после удаления )
if(isset($_POST['delete'])){
    $id = $_GET['id'];
    $student = R::load('students', $id);
    R::trash( $student );
    header("Location:".$_SERVER['HTTP_REFERER']);
}

if(isset($_POST['edit'])) {
    print_r($_GET);
    print_r($_POST);

    $id = $_GET['id'];
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $bday = $_POST['date_of_birth'];
    $card = $_POST['card'];

    $d_a = (date("Y") - $_POST['group_form'][0]) . "-09-01";
    $d_id = $_POST['group_form'][-1];
    R::exec("UPDATE students SET surname=?, name =?, lastname=?, date_of_birth=?, date_of_admission=?,direction_id =?, student_card=? where id =?;", [$surname, $name, $lastname, $bday, $d_a, $d_id, $card, $id]);

    R::exec("UPDATE groups SET direction = ? where student_id =?", [$_POST['group_form'], $id]);
    header("Location:".$_SERVER['HTTP_REFERER']);
}



