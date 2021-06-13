<?php
require_once '../vendor/autoload.php';
class_alias('\RedBeanPHP\R', '\R');
R::setup('sqlite:../data/students.db');
R::ext('xdispense',function ($type){
    return R::getRedBean()->dispense($type);
});     //$test = R::xdispense('test_table');

include 'EditDelete.php';
if(!R::testConnection()){
    exit("Connection Error");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Lab10</title>
    <style>
        table {
            margin: 0 auto;
            font-size: large;
            border: 1px solid black;
        }
        h1 {
            text-align: center;
            color: #006600;
            font-size: xx-large;
            font-family: 'Gill Sans', 'Gill Sans MT',
            ' Calibri', 'Trebuchet MS', 'sans-serif';
        }
        td {
            background-color: #E4F5D4;
            border: 1px solid black;

        }
        th,
        td {
            font-weight: bold;
            border: 1px solid black;
            padding: 10px;
            text-align: center;

        }
        td {
            font-weight: lighter;
        }
    </style>
</head>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>


<body>
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- SELECT GROUP   -->
            <form id="select_group" action="" method="post">
                <select name="group" class="form-control-sm">
                    <option value="" disabled selected>Choose a group</option>
                    <?php
                    $query = "select direction from groups group by groups.direction";
                    $row = R::getAll($query);
                    foreach ($row as $r){
                        foreach ($r as $a){
                            ?>
                             <option value="<?=$a?>"><?=$a?></option>
                             <?php
                        }
                    }
                    ?>
                    <option>All students</option>
                </select>
                <input type="submit" class="btn btn-outline-success" name="submit" vlaue="Choose options">
            </form>
        </div>
    </div>
</div>
<?php
    $selected = 0;
    if(isset($_POST['submit'])){
        if(!empty($_POST['group'])) {
            $selected = $_POST['group'];
        }
    }
?>

    <!-------        STUDENTS TABLE   --------->
    <section>
        <h1>students</h1>
        <div>
            <table id = "student_table">
                <tr>
                    <th>group</th>
                    <th>institute</th>
                    <th>surname</th>
                    <th>name</th>
                    <th>lastname</th>
                    <th>gender</th>
                    <th>date of birth</th>
                    <th>actions</th>
                </tr>

                <?php
                echo $selected;
                if ($selected == 0 or $selected == "All students") {
                    $q = "select  g.student_id, g.direction, 
                                          a.direction as 'institute', 
                                          a.surname,a.name,a.lastname, a.gender, a.date_of_birth, 
                                          a.student_card from groups g inner join
                                          (select s.student_card, s.surname, s.name, s.lastname,s.student_card, 
                                                  s.date_of_birth, case when s.gender = 1 then 'M' else 'Ж' end as 'gender',  
                                                  d.direction, s.id from students s 
                                                      inner join directions d on s.direction_id = d.id) a
                                          on g.student_id = a.id order by g.direction, a.surname";

                }else{
                    $q = "select a.student_card ,g.student_id, g.direction, a.direction as 'institute', a.surname,a.name, a.lastname, a.gender, a.date_of_birth from groups g 
                    inner join (select s.student_card, s.surname, s.name, s.lastname, s.date_of_birth, case when s.gender = 1 then 'М' else 'Ж' end as 'gender',  
                                       d.direction, s.id from students s inner join directions d on s.direction_id = d.id) a
                    on g.student_id = a.id 
                    where g.direction = '$selected'order by g.direction, a.surname ";
                }
                $rows = R::getAll($q);
                foreach($rows as $row)
                {
                ?>
                                <tr>
                                    <td><?= $row['direction'];?></td>
                                    <td><?= $row['institute'];?></td>
                                    <td><?= $row['surname'];?></td>
                                    <td><?= $row['name'];?></td>
                                    <td><?= $row['lastname'];?></td>
                                    <td><?= $row['gender'];?></td>
                                    <td><?= $row['date_of_birth'];?></td>
                                    <td><div>
                                            <p><a href="?id=<?$row['student_id']?>" class="btn btn-group-lg" data-toggle ="modal" data-target="#edit<?=$row['student_id']?>">Редактировать</a>
                                                <a href="" class="btn btn-group-lg" data-toggle ="modal" data-target="#delete<?=$row['student_id']?>">Удалить</a></p>
                                            <a href="session_front.php?id=<?=$row['student_id']?>&course= <?=$row['direction'][0]?>" class="btn btn-group-lg">Результаты сессии</a>
                                        </div>
                                    </td>
                                </tr>

                                <<!-- Modal EDIT STUDENT INFO-->
                    <div class="modal fade" id="edit<?= $row['student_id']?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Редактировать данные</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="?id=<?= $row['student_id']?>" method="post">
                                        <p><label>Group: : <input name="group_form" type="text" value="<?= $row['direction'];?>"></label></p>
                                        <p><label>Surname: <input name="surname" type="text" value="<?= $row['surname'];?>"></label></p>
                                        <p><label>Name: <input name="name" type="text" value="<?= $row['name'];?>"/></label></p>
                                        <p><label>Lastname: <input name="lastname" type="text" value="<?= $row['lastname'];?>"></label></p>
                                        <!--                                    <p><label>Gender:</label></p>-->
                                        <!--                                    <p><label>Male:<input name="gender" type="radio" value=1></label>-->
                                        <!--                                        <label>Female:<input name="gender" type="radio" value=0></label>-->
                                        <!--                                    </p>-->
                                        <p><label>Date of birth: <input name="date_of_birth" type="date" value="<?= $row['date_of_birth'];?>"></label></p>
                                        <p><label>Student card: <input name="card" type="number" value="<?=$row['student_card'];?>"></label></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                                    <button type="submit" class="btn btn-primary" name="edit">Сохранить</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END OF ------Modal  EDIT STUDENT INFO--------------->

                    <!------------- Modal DELETE STUDENT  ------->
                    <div class="modal fade" id="delete<?= $row['student_id'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Удалить студента <?= $row['surname']." ".$row['name']?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-footer">
                                    <form action="?id=<?= $row['student_id']?>", method="post">
                                        <button type="submit" class="btn btn-primary" name="delete">Удалить</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END OF ------Modal DELETE STUDENT--------------->
                    <?php
                }
                ?>
            </table>
        </div>
    </section>
    <!--  ADD NEW STUDENT BUTTON   -->
    <hr>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <button class="btn btn-success col-md-12" data-toggle="modal" data-target="#create">Add student</button>
            </div>
        </div>
    </div>

    <!-- Modal ADD NEW STUDENT-->
    <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Add new student</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <p><label>Group:
                                <select name="group">
                                    <?php
                                        $query = "select direction from groups group by groups.direction";
                                        $row = R::getAll($query);
                                        foreach ($row as $r){
                                            foreach ($r as $a){
                                                ?>
                                                <option value="<?=$a?>"><?=$a?></option>
                                                <?php
                                            }
                                        }
                                    ?>
                                </select>
                            </label>
                        </p>
                        <p><label>Surname: <input name="surname" type="text"></label></p>
                        <p><label>Name: <input name="name" type="text"/></label></p>
                        <p><label>Lastname: <input name="lastname" type="text"></label></p>
                        <p><label>Gender:</label></p>
                        <p><label>Male:<input name="gender" type="radio" value=1></label>
                            <label>Female:<input name="gender" type="radio" value=0></label>
                        </p>
                        <p><label>Date of birth: <input name="date_of_birth" type="date"></label></p>
                        <p><label>Student card: <input name="card" type="number"></label></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="add">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END OF ------Modal ADD NEW STUDENT--------------->



    #var_dump(R::find('students', 'ORDER BY '));




