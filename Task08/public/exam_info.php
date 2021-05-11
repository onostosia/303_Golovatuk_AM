<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add a new student</title>
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="ajax.js"></script>

<body>
<?php $database = new PDO('sqlite:../data/students.db'); ?>

    <form  method="get" action = "" id="form" onchange="changeForm(this)">
        <fieldset class="fieldset_class" id="fieldset_class">
            <legend> Exam information </legend>

                <select name = "group" id ="group" >
                    <option>Select a group</option>
                    <?php
                    $all_groups_q = "SELECT distinct direction from groups;";
                    $all_groups_arr = $database->prepare($all_groups_q);
                    $all_groups_arr->execute();
                    $all_groups = $all_groups_arr->fetchAll(PDO::FETCH_ASSOC);
                    foreach($all_groups as $gr) {
                        $val = $gr['direction'];
                    if(isset($_GET['group'])){
                        if($val==$_REQUEST['group']){
                            ?>
                            <option id = "sel_val" selected><?=$val;?></option>
                            <?php
                        }
                        else{
                            ?>
                            <option id = "sel_val"><?=$val;?></option>
                            <?php
                         }
                    }else{
                        ?>
                        <option id = "sel_val"><?=$val;?></option>
                        <?php
                    }
                    }
                    ?>
                </select>

                <hr>

                    <select name = "surname" id = "surname" class="surname">
                        <option>Select a  student</option>
                        <?php
                        #print_r($_GET);
                        if(isset($_REQUEST['group'])){
                            $stud_id = 0;
                            $select_group = $_GET['group'];
                            $st = $database->prepare("SELECT s.surname as sur, s.id as stud_id from students as s join groups as g on g.student_id = s.id where g.direction = '$select_group';");
                            $st->execute();
                            $res_st = $st->fetchAll(PDO::FETCH_ASSOC);
                            foreach($res_st as $temp){
                                $value1 = $temp['sur'];
                                if(isset($_GET['surname'])){
                                    if($value1==$_REQUEST['surname']){
                                        $stud_id = $temp['stud_id'];

                                        ?>
                                        <option id = "sel_val" selected><?=$value1;?></option>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <option id = "sel_val"><?=$value1;?></option>
                                        <?php
                                    }
                                }
                                else{
                                    ?>
                                    <option id = "sel_val"><?=$value1;?></option>
                                    <?php
                                }
                            }
                        }
                        ?>
                        </select>

                <hr>

                    <select name="course" id="course" class="course">
                        <option class="option_st">Select course</option>
                        <?php
                        if(isset($_REQUEST['group'])){
                            $temp = $_GET['group'];
                            $course = $temp[0];
                            for($i=1;$i<=$course;$i++){
                                if(isset($_GET['course'])){
                                    if($i==$_REQUEST['course']){
                                        ?>
                                        <option id = "sel_val" selected><?=$i;?></option>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <option id = "sel_val"><?=$i;?></option>
                                        <?php
                                    }
                                }
                                else{
                                    ?>
                                    <option id = "sel_val"><?=$i;?></option>
                                    <?php
                                }

                            }
                        }
                        ?>
                    </select>

                <hr>

                    <select name="semester" id="semester" class="semester">
                        <option class="option_st" selected>Select semester</option>
                        <?php
                        if(isset($_REQUEST['semester'])){
                            if($_REQUEST['semester']==1){
                                ?>
                                <option class="option_st" selected>1</option>
                                <option class="option_st">2</option>
                                <?php
                            }
                            else{
                                ?>
                                <option class="option_st">1</option>
                                <option class="option_st" selected>2</option>
                                <?php
                            }
                        }
                        else{
                            ?>
                            <option class="option_st">1</option>
                            <option class="option_st">2</option>
                            <?php
                        }
                        ?>

                    </select>

                <hr>

                    <select name="subject" id="subject" class="subject">
                        <option class="option_st">Select a subject</option>
                        <?php
                        if(isset($_REQUEST['semester'])){
                            $c = $_REQUEST['course'];
                            $temp_s = $_REQUEST['semester'];
                            $s = 'cert_type'.$temp_s;
                            $d = $_REQUEST['group'][2];
                            $sub_id = 0;
                            $set_res = $database->prepare("SELECT s.subject as subj, s.id as sub_id from subjects as s join(SELECT sd.subject_id as i from subjects_directions as sd where sd.direction_id='$d') where s.course = '$c' and s.'$s'>=-1 and s.'$s'<=1 and i=s.id;");
                            $set_res->execute();
                            $subj = $set_res->fetchAll(PDO::FETCH_ASSOC);
                            foreach($subj as $temp){
                                $su = $temp['subj'];
                                if(!empty($_REQUEST['subject'])){
                                    if($su==$_REQUEST['subject']){
                                        $sub_id = $temp['sub_id'];
                                        ?>
                                        <option selected><?=$su;?></option>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <option><?=$su;?></option>
                                        <?php
                                    }
                                }
                                else{
                                    ?>
                                    <option><?=$su;?></option>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </select>

                <hr>
                <h> Input results:
                    <input required class = "mark" type="number" name="mark" value="" id="mark">
                </h>

        </fieldset>
        <input class = "sendInfoNewStudent" type = "submit"  onclick="sendClear()"/>
    </form>


<?php
if(isset($_REQUEST['group']) && isset($_REQUEST['surname']) && isset($_REQUEST['course']) && isset($_REQUEST['semester']) && isset($_REQUEST['subject']) && isset($_REQUEST['mark'])){
    $inds = $database->prepare("SELECT MAX(id) from session_results;");
    $inds->execute();
    $ress = $inds->fetchColumn();
    $ind1s = $ress+1;
    $ins2=$database->prepare("INSERT INTO session_results (id,student_id, subject_id, points) VALUES (:id,:student_id, :subject_id, :points);");
    $ins2->execute([
        ':id'=> $ind1s,
        ':student_id' => $stud_id,
        ':subject_id' => $sub_id,
        ':points' => $_REQUEST['mark'],
    ]);
    echo ' <script type="text/javascript"> location.reload(); </script>';
}

?>

</body>
</html>