<?php
$database = new PDO('sqlite:../data/students.db');

//EDIT SESSION RES
if(isset($_POST['edit_session_res'])){
//    print("SES POST = ");
//    print_r($_POST);
    $id_st = $_GET['id'];
    $sub_id = $_GET['sub_id'];
    $points =  $_POST['points'];
    $course = $_GET['course'];
    //print($id_st." ".$sub_id." ".$points." ".$course);

    $update_res = "UPDATE session_results SET points = :points where student_id= :student_id and subject_id= :subject_id;";
    $q = $database->prepare($update_res);
    $q->execute([
            ':points' => $points,
            ':student_id' => $id_st,
            ':subject_id' => $sub_id
    ]);
}

///
///DELETE RESULT
if(isset($_POST['del_res'])){
      print_r($_GET);
      $q_delete =("DELETE FROM session_results WHERE student_id = ? and subject_id =?");
      $q = $database->prepare($q_delete);;
      $q->execute([$_GET['id'], $_GET['sub_id']]);
      if($q){
            header("Location:".$_SERVER['HTTP_REFERER']);
      }
}

// ADD NEW RESULT
if(isset($_POST['add_res'])){
    print_r($_GET);
    print_r($_POST);
    $id_st = $_GET['id'];
    $sub_id = $_POST['selected_sub'];
    $points = $_POST['set_new_points'];
    $course = $_GET['course'];

    $data = $database->prepare("select max(id) as m from session_results");
    $data ->execute();
    $id = $data->fetchAll(PDO::FETCH_ASSOC);
    $max_id = array_values($id);
    $max_id = $max_id[0]['m']+1;

    $sql = ("INSERT INTO session_results(id, student_id, subject_id, points)
                VALUES(?,?,?,?)");
    $q = $database->prepare($sql);

    $q->execute([$max_id, $id_st, $sub_id, $points]);
    if($q){
        header("Location:".$_SERVER['HTTP_REFERER']);
    }

}
//"select * from subjects inner join subjects_directions sd on subjects.id = sd.subject_id where sd.direction_id = 2 and course <=2"

