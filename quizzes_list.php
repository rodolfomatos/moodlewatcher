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

global $DB, $CFG, $MWCFG, $USER, $debugue;
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

$debugue[]='1.';
//----------------------------------------------------------------------
//
// Funções
//
//----------------------------------------------------------------------

function quizzes_of_the_day($quizzes, $day, $month, $year, $uo){
	global $MWCFG, $CFG, $debugue;
	if(isset($debugue)) unset($debugue);
    try {
//		echo '<table class="calendario">';
		echo '<table class="table">';
		echo '<tr class="active"><th>Quiz</th><th>Data</th><th>Open</th><th>Close</th><th>Rede</th><th>Password</th><th>Shortname</th><th>Fullname</th></tr>';
		$debugue[]='1. foreach';
		foreach ($quizzes as $quiz) {
		$debugue[]='2. $quiz='.serialize($quiz);
		// os testes podem nao ter definido o tempo de inicio e de fim e mesmo assim terem tentativas
		if($quiz->timeopen == 0){
			$inicio=$quiz->attemptsstart;
		} else {
			$inicio=$quiz->timeopen;
		}
		if($quiz->timeclose == 0){
			$fim=$quiz->timeclose;
		} else {
			$fim=$quiz->timeclose;
		}
		// criar o calendario
		$d=gmdate('j',$inicio);
		$d=$d+0;
		$m=gmdate('m',$inicio);
		$m=$m+0;
		$Y=gmdate('Y',$inicio);
		$Y=$Y+0;

		$calendario[$Y][$m][$d][]=$quiz->id;

		if (($day."/".$month."/".$year == $d."/".$m."/".$Y) && (preg_match("/^".strtoupper($uo)."/",course_shortname($quiz->id) ))){

			// colocar um indicador se é "well behaved or not"...
		if(quiz_is_well_behaved($quiz->id)) {
			echo '<tr style="color:green">';
		} else {
			echo '<tr style="color:red">';
			
		};
		echo "<td><a href=\"{$MWCFG->weburl}/index2.php?quiz=".$quiz->id."\" target=\"_blank\">".$quiz->id."</a></td>";
		echo "<td>";
		echo $d."/".$m."/".$Y;
		echo "</td>";
		echo "<td>";
		echo gmdate('H:i',$inicio);
		echo "</td>";
		echo "<td>";
		echo gmdate('H:i',$quiz->timeclose);
		echo "</td>";
		echo "<td>";
		echo subnet_from_quiz($quiz->id);
		echo "</td>";
		echo "<td>";
		echo $quiz->password;
		echo "</td>";
		echo "<td>";
		echo "<a href={$CFG->wwwroot}/course/view.php?id=".course_id_from_quiz($quiz->id).">".course_shortname($quiz->id)."</a>";
		echo "</td>";
		echo "<td>";
		echo course_fullname($quiz->id);
		echo "</td>";

		echo "</tr>";
		}
	  }
	  echo "</table>";
	$MWCFG->ERRORS[] = array('file: quizzes_list.php, quizzes_of_the_day',$debugue);
} catch (Exception $err) {
	$MWCFG->ERRORS[] = array('file: quizzes_list, quizzes_of_the_day',$err->getMessage(),$debugue);
}

}
// ---------------------------------------------------------------------
include "header.php";

	// APENAS PARA PESSOAL AUTORIZADO

	if(!user_is_GOD()) {
		//ErrorMessageBox($title, $msg="", $button_text="", $button_link="", $header="", $footer="")
		ErrorMessageBox("Acesso Proibido!", "Não tem as permiss&otilde;es necess&aacute;rias para aceder a esta p&aacute;gina!", 'Voltar', 'index.php', 'Forbiden Access!','Este evento ir&aacute; ser comunicado aos administradores');
		writeToLog("quizzes_list.php: acesso proibido!");
	} else {

	// --------------------
	// ALLOW TO SEE (START)

//--------------------------------------------------------------------------------------------------------------------
$debugue[]='2. form';
?>
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-4"><img src="img/moodlewatcher-logo.png" class="img-responsive center-block" style="min-width:200px"></div>		
			<div class="col-md-6">
		
			</div>
		</div>
			<div class="col-md-12"></div>

<h2>Lista mensal dos testes</h2>
<hr>
<form action="quizzes_list.php" class="form-inline">
<div class="form-group"></div>

Day:<input type="number" class="form-control" name="day" min="1" max="31" maxlength="2" > 
Month:<input type="number" class="form-control" name="month"  min="1" max="12" maxlength="2" > 
Year:<input type="number" class="form-control" name="year"  min="2011" maxlength="4" > 
UO:<select name="uo" class="form-control">
<option value="">*</option>
<?php
foreach($MWCFG->UOS as $uo){
?>
<option value="<?php print $uo; ?>"><?php print $uo; ?></option>
<?php
}
?>
</select>
<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span>&nbsp;Procurar</button>
 </div>
</form>
<hr>
<?php

//--------------------------------------------------------------------------------------------------------------------
// Calendar (start)

 $CFG->day_was_set=0;
 $CFG->month_was_set=0;
 $CFG->year_was_set=0;
 //This gets today's date 
 $date =time () ; 

//--------------------------------------------------------------------------------------------------------------------
 //This puts the day, month, and year in separate variables 
 // with $_GET override
  if ( isset($_GET["day"]) && ($_GET["day"] != NULL )) {
	$day = $_GET["day"];
	$CFG->day_was_set=1;
  } else {
	$day = date('d', $date)+0;
  }
  if ( isset($_GET["month"]) && ($_GET["month"] != NULL )) { 
	$month = $_GET["month"]; 
	$CFG->month_was_set=1; 
  } else { 
	$month = date('m', $date)+0; 
  }
  if ( isset($_GET["year"]) && ($_GET["year"] != NULL )) { 
	$year = $_GET["year"]; 
	$CFG->year_was_set=1;
  } else { 
	$year = date('Y', $date)+0; 
  }

  if ( isset($_GET["uo"]) && ($_GET["uo"] != NULL )) { $uo = $_GET["uo"]; } else { $uo = ""; }

//--------------------------------------------------------------------------------------------------------------------
// registar nos logs o acesso
writeToLog('quizzes_list.php: ($day='.$day.',$month='.$month.',$year='.$year.'$uo='.$uo.')');

//--------------------------------------------------------------------------------------------------------------------
//$sql='SELECT qa.quiz AS id, q.password, q.timeopen, q.timeclose, MIN(qa.timestart) AS attemptsstart FROM {quiz_attempts} qa, {quiz} q WHERE q.id=qa.quiz GROUP BY q.id ORDER BY q.timeopen ASC';
//$quizzes=$DB->get_records_sql($sql);
$debugue[]='4. all_quizzes_with_attempts_for_list';
$quizzes=all_quizzes_with_attempts_for_list();

if($CFG->month_was_set && !$CFG->day_was_set){
  // fazer todos os dias do mes
  $days_in_month = cal_days_in_month(0, $month, $year) ;
  for ($d = 1; $d <= $days_in_month; $d++) {
    echo "<hr><br><h3>".$d."/".$month."/".$year."</h3><br>";
    quizzes_of_the_day($quizzes, $d, $month, $year, $uo);
  } 
} else {
    echo "<br><h3>".$day."/".$month."/".$year."</h3><br>";
  // fazer o dia apenas
    quizzes_of_the_day($quizzes, $day, $month, $year, $uo);
}
}
// ---------------------------------------------------------------------

// ---------------------------------------------------------------------

// Calendar (end)
	$MWCFG->ERRORS[] = array('file: quizzes_list.php', get_caller_method(),$debugue);
} catch (Exception $err) {
	$MWCFG->ERRORS[] = array('file: quizzes_list.php', get_caller_method(),$err->getMessage(),$debugue);
}
include('footer.php');

?>
