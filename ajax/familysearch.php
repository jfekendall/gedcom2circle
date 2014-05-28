<?php

include("../db.php");

$q=$_GET["q"];

if (strpos($q,' ')){
    $q = explode(' ', $q);
}

if (!is_array($q)){
    $people = mysql_query("SELECT id, short_name, suffix FROM tree_people WHERE short_name LIKE '".$q."%' ORDER BY short_name LIMIT 10")or die(mysql_error());
}else if(sizeof($q) > 1){
    $number = 0;
    $search_string = "'%";
    while ($number < sizeof($q)){
        $search_string .= $q[$number]."%";
        $number++;
    }
    $search_string .= "'";
    //echo $search_string;
    $people = mysql_query("SELECT id, short_name, suffix FROM tree_people WHERE short_name LIKE $search_string ORDER BY short_name LIMIT 10")or die(mysql_error());
}
while ($name = mysql_fetch_array($people)){
    $response = $response."<a href='binomial.php?seed=".$name['id']."'>".$name['short_name']." ".$name['suffix']."</a><br>";
}
if ($response == ''){
    $people = mysql_query("SELECT id, short_name, suffix FROM tree_people WHERE short_name like '%$q%' ORDER BY short_name LIMIT 10")or die(mysql_error());
    while ($name = mysql_fetch_array($people)){
        $response = $response."<a href='binomial.php?seed=".$name['id']."'>".$name['name']." ".$name['suffix']."</a><br>";
        
    }
}
if ($response == ''){
    $response = "No results."; //matching \"$q\"";
}
echo $response;
?>