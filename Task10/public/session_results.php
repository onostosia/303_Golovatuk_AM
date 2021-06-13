<?php

require_once '../vendor/autoload.php';

#$database = new PDO('sqlite:../data/students.db');
if(!R::testConnection()){
    exit("Connection Error");
}
//EDIT SESSION RES
if(isset($_POST['edit_session_res'])){
//    print("SES POST = ");
//    print_r($_POST);
    $id_st = $_GET['id'];
    $sub_id = $_GET['sub_id'];
    $points =  $_POST['points'];
    $course = $_GET['course'];
    //print($id_st." ".$sub_id." ".$points." ".$course);
    R::exec("UPDATE session_results SET points = ? where student_id= ? and subject_id=?;",[$points, $id_st,$sub_id]);
}

///
///DELETE RESULT
if(isset($_POST['del_res'])){
    R::exec("DELETE FROM session_results WHERE student_id = ? and subject_id =?;",[$_GET['id'], $_GET['sub_id']]);
    header("Location:".$_SERVER['HTTP_REFERER']);
}

// ADD NEW RESULT
if(isset($_POST['add_res'])){
    print_r($_GET);
    print_r($_POST);
    $id_st = $_GET['id'];
    $sub_id = $_POST['selected_sub'];
    $points = $_POST['set_new_points'];
    $course = $_GET['course'];

    R::exec(
        "INSERT INTO session_results (student_id, subject_id, points) VALUES(?,?,?);",[$id_st,$sub_id, $points]);
    header("Location:".$_SERVER['HTTP_REFERER']);
}
//"select * from subjects inner join subjects_directions sd on subjects.id = sd.subject_id where sd.direction_id = 2 and course <=2"

