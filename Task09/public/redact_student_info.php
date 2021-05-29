<?php
$database = new PDO('sqlite:../data/students.db');

if(isset($_POST['edit'])){
    #print_r($_POST);
    //print_r( $_GET);
    $id = $_GET['id'];
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $bday = $_POST['date_of_birth'];
    $card = $_POST['card'];

    $d_a = (date("Y") - $_POST['group_form'][0])."-09-01";
    $d_id = $_POST['group_form'][-1];
    //print($d_a."    ".$d_id);
    $update_students_data =("UPDATE students SET surname=?, name =?, lastname=?, date_of_birth=?, date_of_admission=?,direction_id =?, student_card=? where id =?");
    $q_st = $database->prepare($update_students_data);
    $q_st-> execute([$surname, $name, $lastname, $bday, $d_a, $d_id, $card, $id]);

    $update_groups_data =("UPDATE groups SET direction = ? where student_id =?");
    $q_group = $database->prepare($update_groups_data);
    $q_group->execute([$_POST['group_form'], $id]);

}
