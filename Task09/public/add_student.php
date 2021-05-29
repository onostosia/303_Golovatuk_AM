<?php
$database = new PDO('sqlite:../data/students.db');

if(isset($_POST['add'])) {

    $data = $database->prepare("select max(id) as m from students");
    $data ->execute();
    $id = $data->fetchAll(PDO::FETCH_ASSOC);
    $max_id = array_values($id);
    $max_id = $max_id[0]['m']+1;

    $s = $_POST['surname'];
    $n = $_POST['name'];
    $l = $_POST['lastname'];
    $g = $_POST['gender'];
    $d = $_POST['date_of_birth'];
    $d_a = (date("Y") - $_POST['group'][0])."-09-01";
    $d_id = $_POST['group'][-1];
    $c = $_POST['card'];

    $sql = ("INSERT INTO students(
                     id,
                     surname,
                     name,
                     lastname,
                     date_of_birth,
                     gender,
                     date_of_admission,
                     direction_id,
                     student_card) VALUES (?,?,?,?,?,?,?,?,?)");
    $q = $database->prepare($sql);
    $q->execute([$max_id,$s,$n,$l,$d,$g,$d_a,$d_id,$c]);

    $q_add_in_group = "INSERT INTO groups VALUES"."(:student_id, :direction)";
    $ins2=$database->prepare($q_add_in_group);
    $ins2->execute([
        ':student_id' => $max_id,
        ':direction' => $_POST['group']
    ]);
    if($q){
        header("Location:".$_SERVER['HTTP_REFERER']);
    }

}



