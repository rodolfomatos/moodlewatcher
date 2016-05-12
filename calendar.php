<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// MoodleWatcher Audit Report: Moodle Quiz Fraud Detector                //
//                                                                       //
// Copyright (C) 2011 onwards  Rodolfo Matos                             //
//                                                                       //
//                   <rodolfo.matos@gmail.com>                           //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

// direct access die!
//defined('MWPATH_BASE') or die();
global $DB, $MWCFG, $USER, $debugue;
if(isset($debugue)) unset($debugue);
try {
	require_once('config.php');

	/// no guest autologin
	require_login(0, false);

	// Bibliotecas do Moodle
	require_once($CFG->libdir.'/dmllib.php');
	require_once($CFG->libdir.'/moodlelib.php');
	// Bibliotecas do MoodleWatcher
	require_once('lib/lib.php');

//	if(user_is_allowed_to_see_moodlewatcher($USER->id)){
	include "header.php";
	// APENAS PARA PESSOAL AUTORIZADO

	if(!user_is_allowed_to_see_moodlewatcher($USER->id)) {
		//ErrorMessageBox($title, $msg="", $button_text="", $button_link="", $header="", $footer="")
		ErrorMessageBox("Acesso Proibido!", "Não tem as permiss&otilde;es necess&aacute;rias para aceder a esta p&aacute;gina!", 'Voltar', 'index.php', 'Forbiden Access!','Este evento ir&aacute; ser comunicado aos administradores');
		writeToLog("calendar.php: acesso proibido!");
	} else {
?>
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-4"><img src="img/moodlewatcher-logo.png" class="img-responsive center-block" style="min-width:200px"></div>		
			<div class="col-md-6"></div>
			
			<div class="col-md-2"></div>
			
		<div class="row">
			<div class="col-md-2">

			</div>
			<div class="col-md-12">
						<h2>Calendário</h2>
<hr>
<?php
	// --------------------
	// ALLOW TO SEE (START)
	//--------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------
	// Calendar (start)
	?>
	
	<style>
table.calendario{
	margin: 0 1em;
}
	
table.calendario tr td {
	padding: .25em .5em;
	font-size: 10pt; 
}

table.calendario th{
	padding: .25em .5em;
	background-color: #eee;
	font-weight: bold;
	border: 1px solid #ccc;
}

table.calendario th.mes{
	background-color: #fff;
}

td.diaSemana{
	font-weight: bold;
	text-align: center;
	border: 1px solid #ccc;
}
td.diaSemanaWE{
	font-weight: bold;
	text-align: center;
	background-color: #ccc;
	border: 1px solid #ccc;
}
td.dia{
	border: 1px solid #ccc;
	background-color: #efefef;
}
td.diaVazio{
	border: 1px solid #ccc;
	background-color: #aaa;
}

table.constr td{
	border-width: 0;
}

span.numDia{
	padding: .5em;
	background-color: #999;
	color: #fff;
}

a.SegOK{
	background-color: #0a0;
	color: #fff;
	padding: .25em;
	border-radius: 3px;
}

a.SegKO{
	background-color: #a00;
	color: #fff;
	padding: .25em;
	border-radius: 3px;
}

</style>
	<div>
	<table class="table calendario">
	<?php

	$quizzes=all_quizzes_with_attempts();
	$calendario=array();

	foreach ($quizzes as $quiz) {
		$d=gmdate('j',$quiz->timeopen);
		$d=$d+0;
		$m=gmdate('m',$quiz->timeopen);
		$m=$m+0;
		$Y=gmdate('Y',$quiz->timeopen);
		$Y=$Y+0;
		//	if(quiz_is_well_behaved($quiz->id))	{
		if(user_is_allowed_to_see_quiz($USER->id, $quiz->id)) {
			$calendario[$Y][$m][$d][]=$quiz->id;
		}
	}

	 //This gets today's date 
	 $date =time () ; 

	 //This puts the day, month, and year in separate variables 
	// with $_GET override
	if ( isset($_GET["day"]) && ($_GET["day"] != NULL )) { $day = $_GET["day"]; } else { $day = date('d', $date)+0; }
	if ( isset($_GET["month"]) && ($_GET["month"] != NULL )) { $month = $_GET["month"]; } else { $month = date('m', $date)+0; }
	if ( isset($_GET["year"]) && ($_GET["year"] != NULL )) { $year = $_GET["year"]; } else { $year = date('Y', $date)+0; }

	 //Here we generate the first day of the month 
	 $first_day = mktime(0,0,0,$month, 1, $year) ; 

	 //This gets us the month name 
	 $title = date('F', $first_day) ; 
	  //Here we find out what day of the week the first day of the month falls on 
	 $day_of_week = date('D', $first_day) ; 

	 //Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
	 switch($day_of_week){ 
	 case "Sun": $blank = 0; break; 
	 case "Mon": $blank = 1; break; 
	 case "Tue": $blank = 2; break; 
	 case "Wed": $blank = 3; break; 
	 case "Thu": $blank = 4; break; 
	 case "Fri": $blank = 5; break; 
	 case "Sat": $blank = 6; break; 
	 }

	 //We then determine how many days are in the current month
	 $days_in_month = cal_days_in_month(0, $month, $year) ; 
	 //Here we start building the table heads 
	 //echo '<table>';
	 echo "<tr class=\"active\"><th><a class=\"btn btn-info btn-lg\" href=\"calendar.php?"; //.($month-1).
	// **************************************************************************************
	// Se for janeiro, o mês anterior é dezembro do ano anterior
	if ($month==1) {
		echo "month=12&year=".($year-1);
	} else echo "month=".($month-1)."&year=".$year;
	 echo "\"><span class=\"glyphicon glyphicon-minus\"></span></a></th><th colspan=\"5\" class=\"mes\"> $title $year </th><th class=\"text-right\"><a class=\"btn btn-info btn-lg\" href=\"calendar.php?";
	// echo "\"><a href=\"#\" class=\"btn btn-info btn-lg\">&lt;&lt;</a></a></th><th colspan=\"5\" class=\"mes\"> $title $year </th><th><a href=\"calendar.php?";
	// **************************************************************************************
	if ($month==12) {
		echo "month=1&year=".($year+1);
	} else echo "month=".($month+1)."&year=".$year;	 

	// **************************************************************************************
	 
	 echo "\"><span class=\"glyphicon glyphicon-plus\"></span></a></th></tr>";
	 echo "<tr><td class=\"diaSemanaWE\">S</td><td class=\"diaSemana\">M</td><td 
	 class=\"diaSemana\">T</td><td class=\"diaSemana\">W</td><td class=\"diaSemana\">T</td><td 
	 class=\"diaSemana\">F</td><td class=\"diaSemanaWE\">S</td></tr>";

	 //This counts the days in the week, up to 7
	 $day_count = 1;

	 echo "<tr>";

	 //first we take care of those blank days
	 while ( $blank > 0 ) 
	 { 
		 echo "<td class=\"diaVazio\">&nbsp;</td>"; 
		$blank = $blank-1; 
		$day_count++;
	 } 
	  //sets the first day of the month to 1 
	 $day_num = 1;

	 //count up the days, untill we've done all of them in the month
	 while ( $day_num <= $days_in_month ) {
	?>
	<td class="dia">
	<table class="constr">
	<?php
	// Array com os testes por dia (start)
	if(isset($calendario[$year][$month][$day_num]))
		$pratododia=count($calendario[$year][$month][$day_num]);
	else
		$pratododia="";

	for($i=0; $i<$pratododia; $i++) {
		?>
		<tr>
		<td>
		<?php if ($i == 0) { echo '<span class="numDia">'.$day_num.'</span>'; } ?>
		</td>
		<td>
		<?php

		if(quiz_is_well_behaved($calendario[$year][$month][$day_num][$i])) {
		 $daycolor="SegOK";
		 } else {
		 $daycolor="SegKO";
		 }
		echo "<a href=\"index2.php?quiz={$calendario[$year][$month][$day_num][$i]}\" class=\"{$daycolor}\">{$calendario[$year][$month][$day_num][$i]}</a>&nbsp;&nbsp;";
		echo "</td><td><a href={$CFG->wwwroot}/course/view.php?id=".course_id_from_quiz($calendario[$year][$month][$day_num][$i]).">".str_replace("-"," - ",str_replace("_"," ", course_shortname($calendario[$year][$month][$day_num][$i])))."</a>&nbsp;&nbsp;";
		//HelpPopup( '<img src="images/info.png">', quiz_name($calendario[$year][$month][$day_num][$i])."<br />".course_fullname($calendario[$year][$month][$day_num][$i])."<br />". course_category_name($calendario[$year][$month][$day_num][$i]));
		//echo "(".$day_num."/".$month."/".$year.")";
		//echo "(".gmdate('Y/m/d H:i:s',quiz_opentime($calendario[$year][$month][$day_num][$i])).")";
		echo "<br /></td>
	</tr>
	";
	} 
	//	echo "XPTO";

	// dias do fim do calendário que não têm exames
	for($i=$pratododia; $i<$days_in_month; $i++) {
		?>
		<tr>
		<td>
		<?php if ($i == 0) { echo '<span class="numDia">'.$day_num.'</span>'; } ?>
		</td>
		<td>
		<?php	
	}
	// Array com os testes por dia (end)
	?>
	</table>

	</td>
	<?php

		$day_num++; 
		$day_count++;
		//Make sure we start a new row every week
		if ($day_count > 7) {
			echo "</tr><tr>";
			$day_count = 1;
		}
	 } 
	 //Finaly we finish out the table with some blank details if needed
	while ( $day_count >1 && $day_count <=7 ) { 
		echo "<td> </td>"; 
		$day_count++; 
	} 
	echo "</tr></table>";
	echo "</div>"; 
}

	// Calendar (end)
	$MWCFG->ERRORS[] = array('file: calendar.php', get_caller_method(),$debugue);
} catch (Exception $err) {
	$MWCFG->ERRORS[] = array('file: calendar.php', get_caller_method(),$err->getMessage(),$debugue);
}
include('footer.php');

?>


