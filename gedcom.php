<?php
include("db.php");

$target_path = basename( $_FILES['gedcom']['name']);

    if(move_uploaded_file($_FILES['gedcom']['tmp_name'], $target_path)) {

    $query = mysql_query("CREATE TABLE IF NOT EXISTS `tree_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `p_id` int(11) NOT NULL,
  `event_desc` text NOT NULL,
  `event_type` text NOT NULL,
  `event_date` text NOT NULL,
  `event_place` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=287");


$query = mysql_query("CREATE TABLE IF NOT EXISTS `tree_family` (
  `id` text NOT NULL,
  `divorced` varchar(2) NOT NULL DEFAULT ' N',
  `div_date` text NOT NULL,
  `div_place` text NOT NULL,
  `husb` int(11) NOT NULL,
  `wife` int(11) NOT NULL,
  `child1` int(11) NOT NULL,
  `child2` int(11) NOT NULL,
  `child3` int(11) NOT NULL,
  `child4` int(11) NOT NULL,
  `child5` int(11) NOT NULL,
  `child6` int(11) NOT NULL,
  `child7` int(11) NOT NULL,
  `child8` int(11) NOT NULL,
  `child9` int(11) NOT NULL,
  `child10` int(11) NOT NULL,
  `child11` int(11) NOT NULL,
  `child12` int(11) NOT NULL,
  `child13` int(11) NOT NULL,
  `child14` int(11) NOT NULL,
  `child15` int(11) NOT NULL,
  `child16` int(11) NOT NULL,
  `child17` int(11) NOT NULL,
  `child18` int(11) NOT NULL,
  `child19` int(11) NOT NULL,
  `child20` int(11) NOT NULL,
  `child21` int(11) NOT NULL,
  `child22` int(11) NOT NULL,
  `child23` int(11) NOT NULL,
  `child24` int(11) NOT NULL,
  `child25` int(11) NOT NULL,
  `marr_date` text NOT NULL,
  `marr_place` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1");


$query = mysql_query("CREATE TABLE IF NOT EXISTS `tree_notes` (
  `p_id` int(11) NOT NULL,
  `note` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1");


$query = mysql_query("CREATE TABLE IF NOT EXISTS `tree_people` (
  `id` int(11) NOT NULL,
  `short_name` text NOT NULL,
  `suffix` varchar(5) NOT NULL,
  `sex` text NOT NULL,
  `birth_date` varchar(11) NOT NULL,
  `birth_place` text NOT NULL,
  `death_date` varchar(11) NOT NULL,
  `death_place` text NOT NULL,
  `occu` text NOT NULL,
  `occu_place` text NOT NULL,
  `famc` text NOT NULL,
  `bap_date` varchar(11) NOT NULL,
  `bap_place` text NOT NULL,
  `religion` text NOT NULL,
  `religion_place` text NOT NULL,
  `fams_1` int(11) NOT NULL,
  `fams_2` int(11) NOT NULL,
  `fams_3` int(11) NOT NULL,
  `fams_4` int(11) NOT NULL,
  `fams_5` int(11) NOT NULL,
  `fams_6` int(11) NOT NULL,
  `burial_date` varchar(11) NOT NULL,
  `burial_place` text NOT NULL,
  `immi_date` varchar(11) NOT NULL,
  `immi_place` text NOT NULL,
  `long_name` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1");

$query = mysql_query("CREATE TABLE IF NOT EXISTS `tree_people_sources` (
  `id` int(11) NOT NULL,
  `person` int(11) NOT NULL,
  `info` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1");

$query = mysql_query("CREATE TABLE IF NOT EXISTS `tree_sources` (
  `id` int(11) NOT NULL,
  `source` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1");

    $query = mysql_query("TRUNCATE TABLE tree_family");
    $query = mysql_query("TRUNCATE TABLE tree_events");
    $query = mysql_query("TRUNCATE TABLE tree_people");
    $query = mysql_query("TRUNCATE TABLE tree_people_sources");
    $query = mysql_query("TRUNCATE TABLE tree_sources");
     $query = mysql_query("TRUNCATE TABLE tree_notes");
echo "Adding gedcom information to the database. This page will redirect.<br>";
        $ged = basename( $_FILES['gedcom']['name']);
        $the_gedcom = fopen($ged,'r');
        $the_gedcom = fread($the_gedcom, filesize($ged));
        //PARSE FAMILIES
        $families = explode("\n0 @", $the_gedcom, -1);
        $i = 2;
        while($i <= sizeof($families)){
            if (strpos($families[$i], '@ FAM')){
                $family = str_replace('@','', $families[$i]);
                $family = nl2br($family);
                $family = explode("<br />", $family);
                $j = 0;
                $k = 1;
                $n = 0;
                $event_dates = array();

                while ($j <= sizeof($family)){
                    if (strpos($family[$j], "FAM")){
                        $family_values = family_stuff($family[$j], "FAM", "id");
                        $family_fields = 'id';
                    }else if (strpos($family[$j], "DIV")){
                        if (!strpos($family_fields, 'divorced')){
                            $family_values .= ",' Y'";
                            $family_fields .= ",divorced";
                        }
                    }else if (strpos($family[$j], "HUSB")){
                        $family_values .= ",".family_stuff($family[$j], "HUSB", ",husb");
                        $family_fields .= ",husb";
                    }else if (strpos($family[$j], "WIFE")){
                        $family_values .= ",".family_stuff($family[$j], "WIFE", ",wife");
                        $family_fields .= ",wife";
                    }else if (strpos($family[$j], "CHIL")){
                        $family_values .= ",".family_stuff($family[$j], "CHIL");
                        $family_fields .=  ",child$k";
                        $k++;

                        //MARRIAGE AND DIVORCE *BEAT HEAD AGAINST WALL*
                    }else if (strpos($family[$j], "MARR")){/*
                        $family_events = implode($family);
                        $family_events = explode("\n",$family_events);
                        while ($n <= sizeof($family_events)){
                            if (strpos($family_events[$n], 'MARR')){
                                if (isset($family_events[$n+1])){
                                    if (!strpos($family_fields, 'marr_date')){
                                        $family_values .= ",'".str_replace("2 DATE ",'',$family_events[$n+1])."'";
                                        $family_fields .= ",marr_date";
                                    }
                                }
                                if (isset($family_events[$n+2])){
                                    if (!strpos($family_fields, 'marr_place')){
                                        $family_values .= ",'".addslashes(str_replace("2 PLAC ",'',$family_events[$n+2]))."'";
                                        $family_fields .= ",marr_place";
                                    }
                                }
                            }

                            if (strpos($family_events[$n], 'DIV')){
                                if (isset($family_events[$n+1])){
                                    if (!strpos($family_fields, 'div_date')){
                                        $family_values .= ",'".str_replace("2 DATE ",'',$family_events[$n+1])."'";
                                        $family_fields .= ",div_date";
                                    }
                                }
                                if (isset($family_events[$n+2])){
                                    if (!strpos($family_fields, 'div_place')){
                                        $family_values .= ",'".addslashes(str_replace("2 PLAC ",'',$family_events[$n+2]))."'";
                                        $family_fields .= ",div_place";
                                    }
                                }
                            }
                            $n++;
                        }
			*/
                    }
                   $j++;
                }
                $query = mysql_query("INSERT INTO tree_family ($family_fields) VALUES ($family_values)");
                
                
            }
            $i++;
        }

        //PARSE PEOPLE

        $people= explode("\n0", $the_gedcom, -1);
        $i = 2;
        
        while($i <= sizeof($people)){
            
            $person = explode("\n1", $people[$i]);
            $j = 0;
            $m = 1;

            //NOTE: All of these variables need to be unset in order to weed out notes and sources in the data where they don't belong.
            unset($id);
            unset($famc);
            unset($name);
            unset($sex);
            unset($birth);
            unset($death);
            unset($burial);
            unset($religion);
            unset($occu);
            unset($immi);
            unset($bap);
            unset($chr);

            $person_values = '';
            while($j <= sizeof($person)){
                $k = 1;
                $l = 0;
                $fams = '';
                if (strpos($person[$j], 'INDI')){
                    if (!isset($id)){
                        $id = str_replace("@I","",$person[$j]);
                        $id = str_replace("@","",$id);
                        $id = str_replace(" INDI","",$id);
                        $id = ltrim($id);
                        $id = rtrim($id);
                        $id = "'".$id."'";
                        $person_values .= $id;
                        $field_values = 'id';
                    }
               }
               if (strpos($person[$j], 'SOUR @')){
                   
                        /*$source = str_replace("@S","",$person[$j]);
                        $source = str_replace("@","",$source);
                        $source = str_replace(" SOUR","",$source);
                        $throw_away_array = explode("\n",$source);
                        $source = ltrim($throw_away_array[0]);
                        $source = rtrim($source);
                        $id = str_replace("'", '', $id);
                        if (is_numeric($source)){
                            
                            $info = implode($throw_away_array);
                            $info = str_replace("2 PAGE",'',$info);
                            $info = str_replace("3 CONT",'<br>',$info);
                            $info = str_replace("3 CONC ",'',$info);
                            $info = str_replace("3 CONC",'',$info);
                            $blah = 1;
                            $info = str_replace("$source",'',$info,$blah);
                            $info = addslashes($info);
                            $info = rtrim($info);
                            $info = ltrim($info);
                            $query = mysql_query("INSERT INTO tree_people_sources(id, person, info) VALUES ('$source', $id, '$info')");
                        }*/
                }
                if (strpos($person[$j], 'FAMC')){
                    if (!isset($famc)){
                        $famc = str_replace("@F","",$person[$j]);
                        $famc = str_replace("FAMC ","",$famc);
                        $famc = str_replace("@","",$famc);
                        $famc = ltrim($famc);
                        $famc = rtrim($famc);
                        $famc = "'".$famc."'";
                        $person_values .= ",".$famc;
                        $field_values .= ',famc';
                    }
                }
                if (strpos($person[$j], 'NAME')){
                    if (!isset($name)){
                        $name = str_replace("NAME","",$person[$j]);
                        $name = explode("2 GIVN", $name);
                        $name = str_replace("/","",$name[0]);
                        $name = ltrim($name);
                        $name = rtrim($name);
                        $name = explode("2 NPFX ", $name);
                        
                        
                        $name = str_replace('(','',$name[0]);
                        $name = str_replace(')','',$name);
                        if (!strpos('"', $name)){
                            $name = strtolower($name);
                        }

                        $name = ucwords($name);
                        $long_name = $name;
                        //echo $long_name."<br>";
                        $name = explode(" ", $name);
                        

                        $the_name = $name[0]." ";
                        while($m < (sizeof($name)-1)){
                            if (substr($name[$m],0,1) != '"'){
                                $the_name .= substr($name[$m],0,1).". ";
                            }else{
                                $the_name .= $name[$m]." ";
                            }
                            $m++;
                        }
                        $the_name .= $name[sizeof($name)-1];
                        $the_name = "'".addslashes($the_name)."'";

                        $person_values .= ",".$the_name.",'".$long_name."'";
                        $field_values .= ',short_name,long_name';
                    }
                }
                if (strpos($person[$j], 'SEX')){
                    if (!isset($sex)){
                        /*$sex = simple_info($person[$j], $sex, "SEX");
                        $person_values .= ",".$sex;
                        $field_values .= ',sex';*/
                    }
                }
                if (strpos($person[$j], 'BIRT')){
                    if (!isset($birth)){
                        $field_values .= ',birth_date';
                        $replace = array('BIRT', '2 DATE');
                        $birth = explode('2 SOUR', $person[$j]);
                        $birth = explode('NOTE', $birth[0]);
                        $birth = str_replace($replace, '', addslashes($birth[0]));
                        $birth = ltrim($birth);
                        $birth = rtrim($birth);
                        if (strpos($birth, '2 PLAC')){
                            $birth = str_replace('2 PLAC', "','", $birth);
                            $field_values .= ',birth_place';
                        }

                        $person_values .= ",'".$birth."'";
                    }
                }
                if (strpos($person[$j], 'NSFX')){
                    $suffix = explode('NSFX',$person[$j]);
                    $suffix = $suffix[sizeof($suffix)-1];
                    $suffix = ltrim($suffix);
                    $suffix = rtrim($suffix);
                    $field_values .= ',suffix';
                    $person_values .= ",'$suffix'";
                    //echo $suffix."<br>";
                }
                if (strpos($person[$j], 'RELI')){
                    if (!isset($religion)){
                        /*$field_values .= ',religion';
                        $replace = array('RELI', '2 DATE');
                        $religion = explode('2 SOUR', $person[$j]);
                        $religion = explode('NOTE', $religion[0]);
                        $religion = str_replace($replace, '', addslashes($religion[0]));
                        if (strpos($religion, '2 PLAC')){
                            $religion = str_replace('2 PLAC ', "','", $religion);
                            $field_values .= ',religion_place';
                        }

                        $person_values .= ",'".$religion."'";*/
                    }
                }
                if (strpos($person[$j], 'DEAT')){
                    if (!isset($death)){
			/*
                        $field_values .= ',death_date';
                        $replace = array('DEAT', '2 DATE');
                        $death = explode('2 SOUR', addslashes($person[$j]));
                        $death = explode('NOTE', $death[0]);
                        $death = str_replace($replace, '', $death[0]);
                        $death = ltrim($death);
                        $death = rtrim($death);
                        if (strpos($death, '2 PLAC')){
                            $death = str_replace('2 PLAC ', "','", $death);
                            $field_values .= ',death_place';
                        }

                        $person_values .= ",'".$death."'";*/
                    }
                }
                if (strpos($person[$j], 'BURI')){
                    if (!isset($burial)){
			/*
                        $field_values .= ',burial_date';
                        $replace = array('BURI', '2 DATE');
                        $burial = explode('2 SOUR', addslashes($person[$j]));
                        $burial = explode('NOTE', $burial[0]);
                        $burial = str_replace($replace, '', $burial[0]);
                        $burial = ltrim($burial);
                        $burial = rtrim($burial);
                        if (strpos($burial, 'PLAC')){
                            $burial = str_replace('2 PLAC', "','", $burial);
                            $field_values .= ',burial_place';
                        }

                        $person_values .= ",'".$burial."'";*/
                    }
                }
                if (strpos($person[$j], 'IMMI')){
                    if (!isset($immi)){
                        /*$field_values .= ',immi_date';
                        $replace = array('IMMI', '2 DATE');
                        $immi = explode('2 SOUR', addslashes($person[$j]));
                        $immi = explode('NOTE', $immi[0]);
                        $immi = str_replace($replace, '', $immi[0]);
                        $immi = ltrim($immi);
                        $immi = rtrim($immi);
                        if (strpos($immi, '2 PLAC')){
                            $immi = str_replace('2 PLAC ', "','", $immi);
                            $field_values .= ',immi_place';
                        }

                        $person_values .= ",'".$immi."'";*/
                    }
                }
                if (strpos($person[$j], 'BAPM')){
                    if (!isset($bap)){
			/*
                        $field_values .= ',bap_date';
                        $replace = array('BAPM', '2 DATE');
                        $bap = explode('2 SOUR', addslashes($person[$j]));
                        $bap = explode('NOTE', $bap[0]);
                        $bap = str_replace($replace, '', $bap[0]);
                        $bap = ltrim($bap);
                        $bap = rtrim($bap);
                        if (strpos($bap, 'PLAC')){
                            $bap = str_replace('2 PLAC ', "','", $bap);
                            $field_values .= ',bap_place';
                        }

                        $person_values .= ",'".$bap."'";*/
                    }
                }
                if (strpos($person[$j], 'CHR')){

                }
                if (strpos($person[$j], 'OCCU')){
                    if (!isset($occu)){
                        /*$field_values .= ',occu';
                        $replace = array('OCCU', '2 DATE');
                        $occu = explode('2 SOUR', addslashes($person[$j]));
                        $occu = explode('NOTE', $occu[0]);
                        $occu = str_replace($replace, '', $occu[0]);
                        $occu = ltrim($occu);
                        $occu = rtrim($occu);
                        $occu = ucwords($occu);
                        if (strpos($occu, 'PLAC')){
                            $occu = str_replace('2 PLAC', "','", $occu);
                            $field_values .= ',occu_place';
                        }

                        $person_values .= ",'".$occu."'";*/
                    }
                }
                if (strpos($person[$j], 'NOTE')){
                   /* $replace = array('2 CONC ','3 CONC', 'NOTE','!', '3 CONT', '2 DATE', 'BIRT', 'DEAT', '2 PLAC', 'EVEN', '2 TYPE');
                    $note = str_replace($replace,'',$person[$j]);
                    $note = rtrim($note);
                    $note = str_replace('2 CONT', '<br>', $note);
                    $note = ltrim($note);
                    $note = str_replace("<br>\n<br>",'',$note);
                    if (isset($id)){
                        //echo "INSERT INTO tree_notes (pid, note) VALUES ($id,'".addslashes($note)."')<br><br>";
                        $query = mysql_query("INSERT INTO tree_notes (p_id, note) VALUES ($id,'".addslashes($note)."')")or die(mysql_error());
                    }*/

                }
                if(strpos($person[$j], 'EVEN')){
                    if ($person[$j] != ''){
                        $event = explode("\nEVEN",$person[$j]);
                        
                        while($l <= 5){
                            if ($event[$l] != ''){
                               /*
                                $event_fields = "p_id,event_desc";
                                if (strpos($event[$l], "TYPE")){$event_fields .= ",event_type";}
                                $var = str_replace("2 TYPE","','",$event[$l]);
                                if (strpos($var, "2 DATE")){$event_fields .= ",event_date";}
                                $var = str_replace("2 DATE ","','",$var);
                                
                                $var = str_replace("EVEN",'',$var);
                                if (strpos($var, "2 PLAC")){$event_fields .= ",event_place";}
                                $var = str_replace("2 PLAC","','",$var);
                                
                                $the_event = $values_string."$var"."'";
                                $the_event = ltrim($the_event);
   
                                $query = mysql_query("INSERT INTO tree_events ($event_fields) VALUES ($id,'$the_event)");*/
                            }
                            $l++;
                        }
                            
                    }
                }
                if (strpos($person[$j], 'FAMS')){
                        $fams = str_replace("@F","",$person[$j]);
                        $fams = str_replace("FAMS ","",$fams);
                        $fams = str_replace("@","",$fams);
                        $fams = ltrim($fams);
                        $fams = rtrim($fams);
                        $fams = "'".$fams."'";
                        $person_values .= ",".$fams;
                        $field_values .= ",fams_$m";
                        $m++;
                }
                $j++;
            }

            $query =  mysql_query("INSERT INTO tree_people ($field_values) VALUES ($person_values)");//or die(mysql_error());
           $i++;
        }

        //unlink(basename( $_FILES['gedcom']['name']));
    }

    $sources = explode("\n0 @S", $the_gedcom, -1);
    $i = 2;
    while($i <= sizeof($sources)){
        $replace = array('@ SOUR ', '2 CONC ', "_ITALIC: Y");
        $source = str_replace($replace, '',$sources[$i]);
        
        $source = str_replace('2 CONT ', '<br>',$source);
        $source = str_replace('2 CONT', '',$source);

        $source_elements = explode("\n1 ",$source);

        $source_id = str_replace('@ SOUR','', $source_elements[0]);
        $source_id = rtrim($source_id);
        $source_id = ltrim($source_id);

        $source_title = explode("<br>",$source_elements[2]);
        $source_title = str_replace("TITL ",'',$source_title[0]);
        if(is_numeric($source_id)){
            $query = mysql_query("INSERT INTO tree_sources (id, source) VALUES ($source_id,'$source_title')");
        }
        $i++;
    }

/////////////////////
//The page redirect//
/////////////////////

echo "<script type='text/javascript'>
<!--
window.location = 'circle.php'
//-->
</script>
";
    
function simple_info($data, $var, $tag){

    $var = str_replace("$tag","",$data);
    $var = str_replace("/","",$var);
    $var = ltrim($var);
    $var = rtrim($var);
    $var = strtolower($var);
    $var = ucwords($var);
    $var = "'".addslashes($var)."'";

    return $var;
}


function family_stuff($input, $tag){
    if (strpos($input, $tag) && $input != ''){
        $output = str_replace(" $tag",'',$input);
        $output = str_replace("$tag",'',$output);  
        $output = str_replace(" ",'',$output);
        if ($tag != 'FAM'){
            return "'".substr(ltrim($output),2)."'";
        }else{
            return "'".substr(ltrim($output),1)."'";
        }
    }
}
?>
