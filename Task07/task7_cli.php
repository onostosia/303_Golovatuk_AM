<?php
#-----OPEN DB and CREATE QUERY-----
$db = new PDO('sqlite:students.db');
$q = "SELECT distinct direction as dir from groups order by direction;";
$groups = $db->prepare($q);
$groups->execute();
$res = $groups->fetchAll(PDO::FETCH_ASSOC);

$r = iconv('CP866', 'utf-8',chr(197));
$r2 = iconv('CP866', 'utf-8',chr(196));
$r3 = iconv('CP866', 'utf-8',chr(179));
$g1 = iconv('CP866', 'utf-8',chr(218));
$g2 = iconv('CP866', 'utf-8',chr(194));
$g3 = iconv('CP866', 'utf-8',chr(191));
$lg = iconv('CP866', 'utf-8',chr(195));
$rg = iconv('CP866', 'utf-8',chr(180));
$gb1 = iconv('CP866', 'utf-8',chr(192));
$gb2 = iconv('CP866', 'utf-8',chr(193));
$gb3 = iconv('CP866', 'utf-8',chr(217));
$p = iconv('CP866', 'utf-8',chr(32));

$split_str = "\n".$lg.str_repeat($r2, 7).$r.str_repeat($r2, 39).$r.str_repeat($r2, 15).$r.str_repeat($r2, 15).$r.str_repeat($r2, 15).$r.str_repeat($r2, 3).$r.str_repeat($r2, 12).$r.str_repeat($r2, 9).$rg."\n";
$top_split = "\n".$g1.str_repeat($r2, 7).$g2.str_repeat($r2, 39).$g2.str_repeat($r2, 15).$g2.str_repeat($r2, 15).$g2.str_repeat($r2, 15).$g2.str_repeat($r2, 3).$g2.str_repeat($r2, 12).$g2.str_repeat($r2, 9).$g3."\n";
$bottom_split = "\n".$gb1.str_repeat($r2, 7).$gb2.str_repeat($r2, 39).$gb2.str_repeat($r2, 15).$gb2.str_repeat($r2, 15).$gb2.str_repeat($r2, 15).$gb2.str_repeat($r2, 3).$gb2.str_repeat($r2, 12).$gb2.str_repeat($r2, 9).$gb3."\n";


echo "\n".$g1.str_repeat($r2, 10).$g3."\n";
echo $r3."All groups".$r3;
$array_groups = array();
foreach($res as $res_string)
{
    array_push($array_groups, $res_string['dir']);
    $value1 = sprintf("  %' -8d", $res_string['dir']);
    echo "\n".$lg.str_repeat($r2, 10).$rg."\n";
    echo $r3.$value1.$r3;
}

echo "\n".$gb1.str_repeat($r2, 10).$gb3."\n";

$group_number = "";

while($group_number!="escape"){
    echo "\nEnter group's number: ";
    $group_number = "";
    fscanf(STDIN, "%s", $group_number);
    if(ctype_digit($group_number)){
        if (in_array($group_number, $array_groups)){
            $q = "select  g.direction as 'g_num', 
                    a.direction as 'institute', 
                    a.surname,a.name, a.lastname, a.g, a.date_of_birth , a.student_card
                    from groups g 
                    inner join (select s.surname, s.name, s.lastname, s.date_of_birth, s.student_card,
                               s.gender as 'g',  
                                d.direction, s.id from students s inner join directions d on s.direction_id = d.id) a
                  on g.student_id = a.id 
                  where g.direction = '$group_number'order by g.direction, a.surname;";
            $st_gr = $db->prepare($q);
            $st_gr->execute();
            $result = $st_gr->fetchAll(PDO::FETCH_ASSOC);
            #$result = $st_gr->fetchAll(PDO::FETCH_ASSOC);
            echo $top_split;
            $counter = 0;
            foreach($result as $res)
            {
                $value1 = sprintf(" %' -4d\t", $res['g_num']);
                $value2 = sprintf(" %' -56s\t", $res['institute']);
                $value3 = sprintf(" %' -15s\t", $res['surname']);
                $value4 = sprintf(" %' -13s\t", $res['name']);
                $value5 = sprintf(" %' -20s\t", $res['lastname']);
                $value6 = sprintf(" %' -2d", $res['g']);
                $value7 = sprintf(" %' -11s", $res['date_of_birth']);
                $value8 = sprintf(" %' -8d", $res['student_card']);
                echo $r3,$value1,$r3, $value2,$r3, $value3,$r3, $value4,$r3, $value5,$r3, $value6,$r3, $value7,$r3,$value8, $r3;
                if(++$counter==count($result)){
                    echo $bottom_split;
                }
                else{
                    echo $split_str;
                }

            }
        }
        else{
            echo "There isn't any group with that name\n";
        }
    }
    else if($group_number==""){
        $q = "select  g.direction as 'num', a.direction as 'institute', a.surname,a.name,a.lastname, a.g, a.date_of_birth,a.student_card 
                  from groups g inner join
                 (select s.surname, s.name, s.lastname,s.student_card, s.date_of_birth, 
                         s.gender as 'g',  
                         d.direction, s.id from students s inner join directions d on s.direction_id = d.id) a
                  on g.student_id = a.id order by g.direction, a.surname;";

        $st = $db->prepare($q);
        $st->execute();
        $results = $st->fetchAll(PDO::FETCH_ASSOC);

        echo $top_split;
        $counter = 0;
        foreach($results as $res){
            $value1 = sprintf(" %' -4d\t", $res['num']);
            $value2 = sprintf(" %' -56s\t", $res['institute']);
            $value3 = sprintf(" %' -15s\t", $res['surname']);
            $value4 = sprintf(" %' -13s\t", $res['name']);
            $value5 = sprintf(" %' -20s\t", $res['lastname']);
            $value6 = sprintf(" %' -2d", $res['g']);
            $value7 = sprintf(" %' -11s", $res['date_of_birth']);
            $value8 = sprintf(" %' -8d", $res['student_card']);
            echo $r3,$value1,$r3, $value2,$r3, $value3,$r3, $value4,$r3, $value5,$r3, $value6,$r3, $value7,$r3,$value8, $r3;
            if(++$counter==count($results)){
                echo $bottom_split;
            }
            else{
                echo $split_str;
            }
        }

    }
    else if($group_number!="escape"){
        echo "Oops! Something's wrong >_<";
    }

}
?>