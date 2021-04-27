<?php
    #$database = new SQLite3('students.db');
    #$q = "select  g.direction, a.direction as 'institute', a.surname,a.name,a.lastname, a.gender, a.date_of_birth, a.student_card from groups g inner join
    #(select s.surname, s.name, s.lastname, s.date_of_birth,case when s.sex = 1 then 'male' else 'female' end as 'gender', s.student_card,  d.direction, s.id from students s inner join directions d on s.direction_id = d.id) a
    #on g.student_id = a.id order by g.direction, a.surname";
    #$data = $database->query($q);

    $database = new PDO('sqlite:students.db');
    $q = "select  g.direction, a.direction as 'institute', a.surname,a.name,a.lastname, a.gender, a.date_of_birth, a.student_card from groups g inner join
    (select s.surname, s.name, s.lastname, s.date_of_birth,case when s.gender = 1 then 'male' else 'female' end as 'gender', s.student_card,  d.direction, s.id from students s inner join directions d on s.direction_id = d.id) a
     on g.student_id = a.id order by g.direction, a.surname";
    #$data = $database->prepare($q);
    #$data->execute();
    #$res = $data->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LAB7</title>

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
<body>
<form action="" method="post">
    <select name="group">
        <option value="" disabled selected>Choose a group</option>
        <?php $data = $database->prepare("select direction from groups group by groups.direction");
        $data ->execute();
        $row = $data->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $r){
            foreach ($r as $a){
                ?>
                #print_r($a." ");
                <option value="<?=$a?>"><?=$a?></option>
        <?php
            }
        }
        ?>
        <option>All students</option>
    </select>

    <input type="submit" name="submit" vlaue="Choose options">
</form>
<?php
$selected = 0;
if(isset($_POST['submit'])){
    if(!empty($_POST['group'])) {
        $selected = $_POST['group'];
        #echo 'You have chosen: ' . $selected;
    } else {
        echo "You haven't chosen a value.\nNow you're watching the full table. Choose a group again :) ";
    }
}
?>
<section>
    <h1>students</h1>
    <!-- TABLE CONSTRUCTION-->
    <table id = "student_table">
        <tr>
            <th>group</th>
            <th>institute</th>
            <th>surname</th>
            <th>name</th>
            <th>lastname</th>
            <th>gender</th>
            <th>date of birth</th>
        </tr>

        <!-- CREATE QUERIES -->
        <?php
        if($selected == "All students" or empty($_POST['group'])){
            $q = "select  g.direction, a.direction as 'institute', a.surname,a.name,a.lastname, a.gender, a.date_of_birth, a.student_card from groups g inner join
            (select s.surname, s.name, s.lastname,s.student_card, s.date_of_birth, case when s.gender = 1 then 'male' else 'female' end as 'gender',  d.direction, s.id from students s inner join directions d on s.direction_id = d.id) a
            on g.student_id = a.id order by g.direction, a.surname";
            $data = $database->prepare($q);
            $data->execute();
        }else {
            $q = "select  g.direction, a.direction as 'institute', a.surname,a.name, a.lastname, a.gender, a.date_of_birth, a.student_card from groups g 
    inner join (select s.surname, s.name, s.lastname, s.date_of_birth, case when s.gender = 1 then 'male' else 'female' end as 'gender', s.student_card, 
                       d.direction, s.id from students s inner join directions d on s.direction_id = d.id) a
    on g.student_id = a.id 
    where g.direction = '$selected'order by g.direction, a.surname ";
            $data = $database->prepare($q);
            $data->execute();
        }
        $rows=$data->fetchAll(PDO::FETCH_ASSOC);
        #print_r($rows);
        foreach($rows as $row)
        {
            ?>
            <tr>
                <!--FETCHING DATA FROM EACH
                    ROW OF EVERY COLUMN-->
                <td><?= $row['direction'];?></td>
                <td><?= $row['institute'];?></td>
                <td><?= $row['surname'];?></td>
                <td><?= $row['name'];?></td>
                <td><?= $row['lastname'];?></td>
                <td><?= $row['gender'];?></td>
                <td><?= $row['date_of_birth'];?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</body>
</html>


