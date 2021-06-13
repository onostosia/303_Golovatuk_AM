<?php
require_once '../vendor/autoload.php';
class_alias('\RedBeanPHP\R', '\R');
R::setup('sqlite:../data/students.db');
R::ext('xdispense',function ($type){
    return R::getRedBean()->dispense($type);
});
include 'session_results.php';

$course =$_GET['course'];
$st_id = $_GET['id'];

if(!R::testConnection()){
    exit("Connection Error");
}
$query = "select * from students where id = '$st_id'";
$st = R::getAll($query);
?>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>



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



<section>

    <?php
    $FIO = $st[0]['surname']." ".$st[0]['name']." ".$st[0]['lastname'];
    $dir_id = $st[0]['direction_id'];
    $q ="select b.student_id, 
                                                            b.direction, 
                                                            b.subject_id, 
                                                            b.points, subject, course 
                                                    FROM (SELECT a.student_id, direction, subject_id, points FROM 
                                                            (SELECT * from session_results where student_id = ' $st_id') a 
                                                            INNER JOIN groups on a.student_id = groups.student_id
                                                        )
                                                        b INNER JOIN subjects on b.subject_id = subjects.id 
                                                        where course <='$course' order by  course";
    $session_info = R::getAll($q);
    ?>
    <h1><?=$FIO?></h1>
    <table id = "session_table">
        <tr>
            <th>course</th>
            <th>subject</th>
            <th>points</th>
            <th>actions</th>
        </tr>
        <?php
        foreach($session_info as $s_inf){
            ?>
            <tr>
                <td><?= $s_inf['course'];?></td>
                <td><?= $s_inf['subject'];?></td>
                <td><?= $s_inf['points'];?></td>
                <td><div>
                        <a href="?id=<?php echo $s_inf['subject_id'];?>" class="btn btn-group-lg"
                           data-toggle ="modal" data-target="#e<?php echo $s_inf['subject_id'];?>">Редактировать</a>

                        <a href="?id=<?php echo $s_inf['subject_id'];?>" class="btn btn-group-lg"
                           data-toggle ="modal" data-target="#del<?php echo $s_inf['subject_id'];?>">Удалить</a>
                    </div>
                </td>
            </tr>

            <!------------- Modal DELETE SESSION RES  ------->
            <div class="modal fade" id="del<?php echo $s_inf['subject_id'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Удалить результат:</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-footer">
                            <form action="?id=<?=$st_id?>&course=<?=$course?>&sub_id=<?=$s_inf['subject_id']?>", method="post">
                                <p><label><?php echo $s_inf['subject'].": ".$s_inf['points'];?> </label></p>

                                <p><button type="submit" class="btn btn-primary" name="del_res">Удалить</button></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END OF ------Modal DELETE SESSION RES --------------->

            <!-- ---------Modal EDIT SESSION RESULTS --------------------------->
            <div class="modal fade" id="e<?=$s_inf['subject_id']?>"  tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalScrollableTitle"><label>Редактировать</label></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="?id=<?=$st_id?>&course=<?=$course?>&sub_id=<?=$s_inf['subject_id']?>" method="post">
                                <div class="form-check-inline">
                                    <label for="subject" class="col-md">Предмет:</label>
                                    <div class="col-form-label-md">
                                        <label ><?=$s_inf['subject']?></label>
                                    </div>
                                </div>

                                <div class="form-check-inline">
                                    <label for="points" class="col-sm-2 col-form-label">Баллы: </label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" name="points" value="<?= $s_inf['points'] ?>">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary" name="edit_session_res">Сохранить</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <!-----------END OF---------- Modal EDIT SESSION RESULTS -->
            <?php
        }
        ?>
    </table>
    <!--  ADD NEW RESULT BUTTON   -->
    <div class="container">
        <div class="row">
            <div class="col-12">
                <a href="index.php?" class="btn btn-outline-info  col-md-2">Назад</a>
                <button class="btn btn-success col-md-2" data-toggle="modal" data-target="#create_res">Добавить</button>

            </div>
        </div>
    </div>

    <!------------------------------- Modal ADD NEW RESULT ---------------------------------->
    <div class="modal fade" id="create_res" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog -centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Добавить результат</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    $row = R::getAll("select aa.subject as'subject', id as 'id', aa.course as 'course' 
                                        from(select subjects.subject, subjects.id, subjects.course from subjects
                                        inner join subjects_directions sd on subjects.id = sd.subject_id
                                        where sd.direction_id = ? and course <= ? order by course)aa  
                                        left outer join (select a.subject_id, subjects.subject, subjects.course, a.points from
                                        (SELECT sr.subject_id,sr.student_id,sr.points from session_results sr where student_id = ?) a
                                        INNER JOIN subjects on a.subject_id = subjects.id where course <= ?) subs  
                                        on aa.id = subs.subject_id where subs.subject_id is null", [$dir_id, $course, $st_id, $course]);

                    $leb = $row[0]['course'];
                    ?>

                    <form action="?id=<?=$st_id?>&course=<?=$course?>&sub_id=<?=$dir_id?>"  method="post">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Предмет
                            </button>
                            <ul class="dropdown-menu pre-scrollable selected_sub" aria-labelledby="menu1" name="selected_sub">
                                <?php
                                foreach ($row as $r) {
                                    ?>
                                    <li value="<?=$r['id']?>"><a class="dropdown-item" data-value="<?=$r['subject']?>" > Курс-<?=$r['course']." ".$r['subject']?></a></li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>

                        <!-- Create a hidden input -->
                        <input type='hidden' name='selected_sub'>
                        <p><label>Баллы: </label>
                            <input type="number" class="form-control" name="set_new_points" >
                        </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary" name="add_res">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-----------------------------------     END OF ------Modal ADD NEW RESULT--------------->
    <script>
        $(".dropdown-menu li a").click(function() {
            $(this).parents(".dropdown").find('.btn').html($(this).text() + ' <span class="caret"></span>');
            $(this).parents(".dropdown").find('.btn').val($(this).data('value'));
        });
    </script>
    <script>
        $(function(){
            //Listen for a click on any of the dropdown items
            $(".selected_sub li").click(function(){
                //Get the value
                var value = $(this).attr("value");
                //Put the retrieved value into the hidden input
                $("input[name='selected_sub']").val(value);
            });
        });
    </script>
</section>



