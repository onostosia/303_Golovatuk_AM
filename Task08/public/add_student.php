<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Adda a new student</title>
</head>

<body>
    <?php $database = new PDO('sqlite:../data/students.db');?>
    <!--ADD NEW STUDENT INTO A GROUP -->
    <form id = "1" name="add-student" method = "post" enctype="application/x-www-form-urlencoded" >
        <fieldset>
            <legend>Student information</legend>
            <select name="group">
                <option value="" disabled selected>Choose a group</option>
                <?php $data = $database->prepare("select distinct direction  from groups order by direction");
                $data ->execute();
                $row = $data->fetchAll(PDO::FETCH_ASSOC);
                foreach ($row as $r)
                {?>
                    <option value="<?=$r['direction']?>"><?=$r['direction']?></option>
                <?php }?>
            </select>
            <hr>
            <p><label>Surname: <input name="surname" type="text"></label></p>
            <hr><p><label>Name: <input name="name" type="text"/></label></p>
            <hr><p><label>Lastname: <input name="lastname" type="text"></label></p>
            <hr> <p><label>Gender:</label></p>
            <p> <label>Male:<input name="gender" type="radio" value=1></label>
                <label>Female:<input name="gender" type="radio" value=0></label>
            </p>
            <hr><p><label>Date of birth: <input name="date_of_birth" type="date"></label></p>
            <hr> <p><label>Student card: <input name="card" type="number"></label></p>
            <input type="submit" name="submit-student" vlaue="student-info">
        </fieldset>
    </form>

    <?php
    /** @var TYPE_NAME $selected */
    $selected = 0;
        if(isset($_POST['submit-student']))
        {
            # обработчик пустых окон СДЕЛАТЬ ЕГО НОРМАЛЬНЫМ
            if(!empty($_POST['group'])){
                $message = "";
                $keys = array_keys($_POST);
                foreach($keys as $k)
                {
                    if(empty($_POST[$k])){
                        $message = "  ".$k.", ".$message;
                    }
                }
                if(empty($_POST['gender'])){$message = "  gender, ".$message;}

                if($message==""){
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
                    $q_new_st = "INSERT INTO students VALUES"."(:id,:surname, :name, :lastname, :date_of_birth, :gender, :date_of_admission, :direction_id, :student_card);";
                    $ins1=$database->prepare($q_new_st);
                    $ins1->execute([
                        ':id'=> $max_id,
                        ':surname' => $s,
                        ':name' => $n,
                        ':lastname' => $l,
                        ':date_of_birth' => $d,
                        ':gender' => $g,
                        ':date_of_admission' => $d_a,
                        ':direction_id' => $d_id,
                        ':student_card' => $c
                    ]);
                    $q_add_in_group = "INSERT INTO groups VALUES"."(:student_id, :direction)";
                    $ins2=$database->prepare($q_add_in_group);
                    $ins2->execute([
                        ':student_id' => $max_id,
                        ':direction' => $_POST['group']
                    ]);

                }else{
                    print( "Заполните форму! ПУСТЫЕ поля: ".$message." ");
                }
            }else{
                ?>
                <script>alert("Select a group!")</script>
                <?php
            }
        }
    ?>
</body>
</html>
