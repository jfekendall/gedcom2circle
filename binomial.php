<?php
include("db.php");
header("Content-type: image/png");
$size = 135;
$ring_width = 200;
$number_of_rings = 10;//Number of Generations

$max = 2;
$divisions = 2;

$im_size = (2*$ring_width*$number_of_rings)- (2* $ring_width);

//Center X and Y
$c = $im_size/2;

//Generate canvas
$im = @imagecreate($im_size, $im_size);

//Colors
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);

//Fonts
$font = 'arialn.ttf';
$name_font_size = 14;
$birth_font_size = 8;

/////////////////////////
//Generate the Polygons//
/////////////////////////
$point = array();
$ring = 1;
while ($ring <= $number_of_rings){
    if ($ring == $number_of_rings){
        $color = $white;
    }else{
        $color = $black;
    }
    if ($ring != 1){
        $div = pi()/$max;
        $firstNum = round($max / $divisions);

        $multiplier = $ring+($size/2);
        $line = array();

        for($k = 0; $k < $divisions; $k++){

            $num = $firstNum * $k;

                if ($num <= $max) {
                    $numAngle = $num * $div;

                    $x = round($c + cos($numAngle) * $multiplier);
                    $y = round($c + sin($numAngle) * $multiplier);
                    $m_x = round($c - cos($numAngle) * $multiplier);
                    $m_y = round($c - sin($numAngle) * $multiplier);

                    ////////////////////////////////////////
                    //Set the points for the outward lines//
                    ////////////////////////////////////////
                    $point['ring'.$ring.'num'.$num.'cartx'] = $x;
                    $point['ring'.$ring.'num'.$num.'carty'] = $y;
                    $point['ring'.$ring.'numm'.$num.'cartx'] = $m_x;
                    $point['ring'.$ring.'numm'.$num.'carty'] = $m_y;
                    //Done

                    $tangent = (- 2 * $x + 140) / (2 * $y - 180);

                    $angle = 0 - rad2deg(atan($tangent));

                    $start = array();
                    $end = array();
                    for($l = 1; $l <= 10000; $l+=4){
                        $origin = 1;
                        $start[$l] = $l;
                    }
                    for($l = 3; $l <= 10000; $l+=4){
                        $origin = 3;
                        $end[$l] = $l;
                    }

                    if (array_search($num, $start)){
                        $line[0] = $x;
                        $line[1] = $y;
                        $line[4] = $m_x;
                        $line[5] = $m_y;

                    }else if (array_search($num, $end)){
                        $line[2] = $x;
                        $line[3] = $y;
                        $line[6] = $m_x;
                        $line[7] = $m_y;
                    }
                    if (sizeof($line) == 8){
                        imageline($im, $line[0],$line[1],$line[2],$line[3],$color);
                        imageline($im, $line[4],$line[5],$line[6],$line[7],$color);
                        $line = array();
                    }
                }
        }
    }
    $max *= 2;
    $divisions *= 2;
    if ($ring != 1){$size += ($ring_width*2);}
    $ring++;
}

///////////////////////////////////////
//Generate the lines the names sit on//
///////////////////////////////////////
$r = 2;

while($r < $number_of_rings){

    $next = $r+1;
    $n = 1;
    while ($n < 1024){
        $startx = $point["ring".$r."num".$n."cartx"];
        $starty = $point["ring".$r."num".$n."carty"];

        //Now some stuff to determine the start point of the line.

        $m = ($n*2)-1;
        $o = ($n*2)+1;


        $cx = $point["ring".$next."num".$m."cartx"];
        $cy = $point["ring".$next."num".$m."carty"];
        $bx = $point["ring".$next."num".$o."cartx"];
        $by = $point["ring".$next."num".$o."carty"];

        $ax = $cx;
        $ay = $by;

        $mx = (($ax - $bx)/2)+$bx;
        $my = (($ay - $cy)/2)+$cy;

        imageline($im, $startx,$starty,$mx,$my,$black);

        $n+=2;
    }
        $n = 1;
    while ($n < 1024){
        $startx = $point["ring".$r."numm".$n."cartx"];
        $starty = $point["ring".$r."numm".$n."carty"];

        //Now some stuff to determine the end point of the line.

        $m = ($n*2)-1;
        $o = ($n*2)+1;


        $cx = $point["ring".$next."numm".$m."cartx"];
        $cy = $point["ring".$next."numm".$m."carty"];
        $bx = $point["ring".$next."numm".$o."cartx"];
        $by = $point["ring".$next."numm".$o."carty"];

        $ax = $cx;
        $ay = $by;

        $mx = (($ax - $bx)/2)+$bx;
        $my = (($ay - $cy)/2)+$cy;

        imageline($im, $startx,$starty,$mx,$my,$black);

        $n+=2;
    }

    $r++;
    
}


//////////////////////
//Generation Markers//
//////////////////////

$i = 1;
$j = 3;
while ($i < $number_of_rings-1 && $j <= 8){
    imagettftext  ($im, $birth_font_size, 0, $c-($ring_width*$i), $c, $black, $font, $j);
    imagettftext  ($im, $birth_font_size, 0, $c+($ring_width*$i), $c, $black, $font, $j);
    $i++;
    $j++;
}

imagettftext($im, 20, 90, $c+10, $c-200, $black, $font, 'Father\'s Family');
imagettftext($im, 20, 270, $c-10, $c+200, $black, $font, 'Mother\'s Family');

////////////
//The Seed//
////////////
$seed = $_GET['seed'];

$name = mysql_fetch_array(mysql_query("SELECT short_name, suffix, famc, birth_date, birth_place FROM tree_people WHERE id=$seed"));
$seed = $name['short_name'].' '.$name['suffix'];

$seed_width = $name_font_size * strlen($seed);
$seed_center = ceil(($im_size)/2) - ($seed_width/4);

imagettftext($im, $name_font_size, 0, $seed_center, $c, $black, $font, $seed);

$birth_text = "b.".$name['birth_date']." ".$name['birth_place'];
$seed_width = $birth_font_size * strlen($birth_text);

$seed_center = ceil(($im_size)/2) - ($seed_width/4);
imagettftext($im, $birth_font_size, 0, $seed_center, $c+(2*$birth_font_size), $black, $font, $birth_text);

///////////
//Parents//
///////////
//The parents function returns an array like 0fathers name, 1fathers famc, 2mothers name, 3mothers famc, 4fathers birth info 5mothers birth info.

if($name['famc'] != ''){
    $parents = parents($name['famc']);
}

$father_width = $name_font_size * strlen($parents[0]);
$father_center = ceil(($im_size)/2)-($father_width/4);
$mother_width = $name_font_size * strlen($parents[2]);
$mother_center = ceil(($im_size )/2)-($mother_width/4);

imagettftext  ($im, $name_font_size, 0, $father_center, $c-145-($birth_font_size*2.5), $black, $font, $parents[0]);

imagettftext  ($im, $birth_font_size, 0, $father_center, $c-155, $black, $font, $parents[4]);

imagettftext  ($im, $name_font_size, 0, $mother_center, $c+155, $black, $font, $parents[2]);
imagettftext  ($im, $birth_font_size, 0, $mother_center, $c+155+(2*$birth_font_size), $black, $font, $parents[5]);

imageline($im, $c, $c-50,$c,$c-140,$black);
imageline($im, $c, $c+50,$c,$c+140,$black);

/////////////////////////////
//Third Ring: Grand Parents//
/////////////////////////////

if ($number_of_rings > 2){

    $first_array = array();
    if (parents($parents[1]) != ''){
        $first_array[0] = parents($parents[1]);
    }
    if (parents($parents[3]) != ''){
        $first_array[1] = parents($parents[3]);
    }

    imagettftext  ($im, $name_font_size, -45, $point['ring3numm2cartx']+15, $point['ring3numm2carty']+15, $black, $font, name($first_array[0][0]));
    imagettftext  ($im, $birth_font_size, -45, $point['ring3numm2cartx']+5, $point['ring3numm2carty']+25, $black, $font, $first_array[0][4]);

    imagettftext  ($im, $name_font_size, 45, $point['ring2numm3cartx'], $point['ring2numm3carty'], $black, $font,name($first_array[0][2]));
    imagettftext  ($im, $birth_font_size, 45, $point['ring2numm3cartx']+20, $point['ring2numm3carty'], $black, $font,$first_array[0][5]);

    imagettftext  ($im, $name_font_size, 45, $point['ring3num6cartx']+15, $point['ring3num6carty']-15, $black, $font, name($first_array[1][0]));
    imagettftext  ($im, $birth_font_size, 45, $point['ring3num6cartx']+25, $point['ring3num6carty']-10, $black, $font, $first_array[1][4]);

    imagettftext  ($im, $name_font_size, -45, $point['ring2num1cartx'], $point['ring2num1carty'], $black, $font, name($first_array[1][2]));
    imagettftext  ($im, $birth_font_size, -45, $point['ring2num1cartx']-5, $point['ring2num1carty']+10, $black, $font, $first_array[1][5]);
}



$second_array = array();
$how_many = 2;
$ring = 3;

$outer = 4;
$inner = 3;


$small_angle = 45;

while ($ring < $number_of_rings){
    $how_many = $how_many*2;
    $small_angle = $small_angle/2;
    $large_angle = $small_angle*2;
    $i = 0;
    $j = 0;
    $k = 1;
    while($i < $how_many){
        $second_array[$i] = parents($first_array[$j][$k]);
        $i++;
        if ($k == 3){
            $j++;
        }
        if ($k == 1){
            $k = 3;
        }else{
            $k = 1;
        }
    }

    ///////////
    //PP Quad//
    ///////////

    	$angle = -$small_angle;
        $i = 0;
        $j = 2;
        
        while($i <= (($how_many*.25)-1)){

            if($angle > -45){
                $boffsetx = 0;
                $boffsety = 15;
            }else{
                $boffsetx = -20;
                $boffsety = 10;
            }
            imagettftext($im, $name_font_size, round($angle), $point['ring'.$outer.'numm'.$j.'cartx']+10, $point['ring'.$outer.'numm'.$j.'carty'], $black, $font,  name($second_array[$i][0]));
            if($outer < 9){
                imagettftext($im, $birth_font_size, round($angle), $point['ring'.$outer.'numm'.$j.'cartx']+10+$boffsetx, $point['ring'.$outer.'numm'.$j.'carty']+$boffsety, $black, $font, $second_array[$i][4]);
            }
            $angle-=$large_angle;
            $j+=4;


            if($angle > -45){
                $boffsetx = 0;
                $boffsety = 15;
            }else{
                $boffsetx = -20;
                $boffsety = 10;
            }
            imagettftext($im, $name_font_size, round($angle), $point['ring'.$outer.'numm'.$j.'cartx']+10, $point['ring'.$outer.'numm'.$j.'carty'], $black, $font,  name($second_array[$i][2]));
            if($outer < 9){
                imagettftext($im, $birth_font_size, round($angle), $point['ring'.$outer.'numm'.$j.'cartx']+10+$boffsetx, $point['ring'.$outer.'numm'.$j.'carty']+$boffsety, $black, $font, $second_array[$i][5]);
            }
            $angle-=$large_angle;
            $j+=4;
            $i++;
            
        }
        

    ///////////
    //MP Quad//
    ///////////

    $angle = $small_angle;
        $i = ($how_many*.50);
        $j = $how_many*4-2;
        
        while($i <= ($how_many*.75)-1){
            if($angle < 45){
                $boffsetx = 20;
                $boffsety = 10;
            }else{
              //  $boffsetx = -20;
               // $boffsety = 10;
            }
            imagettftext($im, $name_font_size, round($angle), $point['ring'.$outer.'num'.$j.'cartx']-2, $point['ring'.$outer.'num'.$j.'carty']-2, $black, $font,  name($second_array[$i][0]));
            if($outer < 9){
                imagettftext($im, $birth_font_size, round($angle), $point['ring'.$outer.'num'.$j.'cartx']-2+$boffsetx, $point['ring'.$outer.'num'.$j.'carty']-2+$boffsety, $black, $font, $second_array[$i][4]);
            }
            $angle+=$large_angle;
            $j-=4;

            
            if($angle < 45){
                $boffsetx = 20;
                $boffsety = 10;
            }else{
              $boffsetx = 20;
              $boffsety = -5;
            }
            imagettftext($im, $name_font_size, round($angle), $point['ring'.$outer.'num'.$j.'cartx']-2, $point['ring'.$outer.'num'.$j.'carty']-2, $black, $font,  name($second_array[$i][2]));
            if($outer < 9){
                imagettftext($im, $birth_font_size, round($angle), $point['ring'.$outer.'num'.$j.'cartx']-2+$boffsetx, $point['ring'.$outer.'num'.$j.'carty']-2+$boffsety, $black, $font, $second_array[$i][5]);
            }
            $angle+=$large_angle;
            $j-=4;
            $i++;
        }

    ///////////
    //MM Quad//
    ///////////

    $angle = -$small_angle;
        $i = ($how_many*.75);
        $j = 1;
        
        while($i <= $how_many-1){
            if($angle > -45){
                $boffsetx = 0;
                $boffsety = 15;
            }else{
                $boffsetx = -20;
                $boffsety = 10;
            }
            imagettftext($im, $name_font_size, round($angle), $point['ring'.$inner.'num'.$j.'cartx']+5, $point['ring'.$inner.'num'.$j.'carty'], $black, $font,  name($second_array[$i][0]));
            if($inner < 8){
                imagettftext($im, $birth_font_size, round($angle), $point['ring'.$inner.'num'.$j.'cartx']+5+$boffsetx, $point['ring'.$inner.'num'.$j.'carty']+$boffsety, $black, $font,$second_array[$i][4]);
            }
            $angle-=$large_angle;
            $j+=2;
            if($angle > -45){
                $boffsetx = 0;
                $boffsety = 15;
            }else{
                $boffsetx = -20;
                $boffsety = 10;
            }
            imagettftext($im, $name_font_size, round($angle), $point['ring'.$inner.'num'.$j.'cartx']+5, $point['ring'.$inner.'num'.$j.'carty'], $black, $font,  name($second_array[$i][2]));
            if($inner < 8){
                imagettftext($im, $birth_font_size, round($angle), $point['ring'.$inner.'num'.$j.'cartx']+5+$boffsetx, $point['ring'.$inner.'num'.$j.'carty']+$boffsety, $black, $font,$second_array[$i][5]);
            }
            $angle-=$large_angle;
            $j+=2;
            $i++;
            
        }
        

    ///////////
    //PM Quad//
    ///////////

    $angle = 90-$small_angle;
        $i = ($how_many*.25);
        $j = $how_many+1;
        
        while($i <= ($how_many*.50)-1){
            if($angle < 45){
                $boffsetx = 20;
                $boffsety = 10;
            }else{
              $boffsetx = 20;
              $boffsety = -5;
            }
            imagettftext($im, $name_font_size, round($angle), $point['ring'.$inner.'numm'.$j.'cartx']-2, $point['ring'.$inner.'numm'.$j.'carty']-2, $black, $font,  name($second_array[$i][0]));
            if($inner < 8){
                imagettftext($im, $birth_font_size, round($angle), $point['ring'.$inner.'numm'.$j.'cartx']-2+$boffsetx, $point['ring'.$inner.'numm'.$j.'carty']-2+$boffsety, $black, $font, $second_array[$i][4]);
            }
            $angle-=$large_angle;
            $j+=2;


            if($angle < 45){
                $boffsetx = 20;
                $boffsety = 10;
            }else{
              $boffsetx = 20;
              $boffsety = -5;
            }
            imagettftext($im, $name_font_size, round($angle), $point['ring'.$inner.'numm'.$j.'cartx']-2, $point['ring'.$inner.'numm'.$j.'carty']-2, $black, $font,  name($second_array[$i][2]));
            if($inner < 8){
                imagettftext($im, $birth_font_size, round($angle), $point['ring'.$inner.'numm'.$j.'cartx']-2+$boffsetx, $point['ring'.$inner.'numm'.$j.'carty']-2+$boffsety, $black, $font, $second_array[$i][5]);
            }
            $angle-=$large_angle;
            $j+=2;
            $i++;
            
        }
        
        $first_array = $second_array;
        $ring++;
        $inner++;
        $outer++;
}

//////////////////
//Save the image//
//////////////////

$seed = str_replace(" ","",$seed);
$seed = str_replace(".","",$seed);
if(!is_dir("./".date('Ymd'))){
    mkdir("./".date('Ymd'));
}
imagepng($im, "./".date(Ymd)."/".$seed.".png");
imagepng($im);
imagedestroy($im);



/////////////
//Functions//
/////////////



function parents($famc){

        $parents = @mysql_fetch_array(mysql_query("SELECT husb, wife FROM tree_family WHERE id=".$famc));

        $father = @mysql_fetch_array(mysql_query("SELECT short_name, suffix, famc, birth_date, birth_place FROM tree_people WHERE id=".$parents['husb']));

        
        $mother = @mysql_fetch_array(mysql_query("SELECT short_name, suffix, famc, birth_date, birth_place FROM tree_people WHERE id=".$parents['wife']));

        if($father['birth_date'] != '' && $father['birth_place'] != ''){
            $father_birth = "b. ".$father['birth_date']." ".$father['birth_place'];
            $father_birth = preg_replace('/[^a-zA-Z0-9-]/', ' ', $father_birth);
            $father_birth = preg_replace('/^[-]+/', '', $father_birth);
            $father_birth = preg_replace('/[-]+$/', '', $father_birth);
            $father_birth = preg_replace('/[-]{2,}/', ' ', $father_birth);
        }
        if($mother['birth_date'] != '' && $mother['birth_place'] != ''){
            $mother_birth = "b. ".$mother['birth_date']." ".$mother['birth_place'];
            $mother_birth = preg_replace('/[^a-zA-Z0-9-]/', ' ', $mother_birth);
            $mother_birth = preg_replace('/^[-]+/', '', $mother_birth);
            $mother_birth = preg_replace('/[-]+$/', '', $mother_birth);
            $mother_birth = preg_replace('/[-]{2,}/', ' ', $mother_birth);
        }
        return array( $father['short_name']." ".$father['suffix'], $father['famc'],$mother['short_name']." ".$mother['suffix'], $mother['famc'], $father_birth , $mother_birth);

}



function name($text){
    global $ring_width;

    $text = explode("\n2 ", $text);
    if (isset($text[1]) == ''){
        $text = $text[0];
    }else{
        $text = $text[1];
    }

    $desired = $ring_width/2;
    $actual = strlen($text);
    $diff = $desired - $actual;
    $half_of_diff = $diff/2;

    $text = str_pad($text, $half_of_diff, " ", STR_PAD_BOTH);

    return $text;
}
?>
