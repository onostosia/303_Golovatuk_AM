<?php
$database = new PDO('sqlite:../data/students.db');

if(isset($_POST['delete'])){
    $q_delete =("DELETE FROM students WHERE id = ?");
    $q = $database->prepare($q_delete);
    //print_r($_GET['id']);
    $q->execute([$_GET['id']]);

    if($q){
        header("Location:".$_SERVER['HTTP_REFERER']);
    }
}