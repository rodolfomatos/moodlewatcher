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
try {
	require_once('config.php');

	/// no guest autologin
	require_login(0, false);

	// Bibliotecas do Moodle
	require_once($CFG->libdir.'/dmllib.php');
	require_once($CFG->libdir.'/moodlelib.php');
	// Bibliotecas do MoodleWatcher
	require_once('lib/lib.php');

	global $DB, $USER;

	//------------------------------------------------------------------


	include('header.php');
		?>
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-4"><img src="img/moodlewatcher-logo.png" class="img-responsive center-block" style="min-width:200px"></div>		
			<div class="col-md-6"></div>
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<?php
	//if(!($MOODLE_admin || $MW_admin) ) die;
	//if(!user_is_GOD() ) die;

	if(!user_is_GOD()) {
			//ErrorMessageBox($title, $msg="", $button_text="", $button_link="", $header="", $footer="")
			ErrorMessageBox("Acesso Proibido!", "Não tem as permiss&otilde;es necess&aacute;rias para aceder a esta p&aacute;gina!", 'Voltar', 'index.php', 'Forbiden Access!','Este evento ir&aacute; ser comunicado aos administradores');
	} else {
		// ALLOWED_TO_SEE (start)
		?>

		<?php
		// Well-behaved quizzes list (start)
		?>
			</div>		
			<div class="col-md-2"></div>
		</div>
		<h3>Lista dos Quizzes</h3>
		
		<?php
		// Well-behaved quizzes list (end)	
		//--------------------------------------------------------------------------------------------------------------------

		?>
		<table class="table table-bordered table-hover table-condensed table-striped"  bordercolor="lightgray">
		<div id="main">
		<?php
			
		//--------------------------------------------------------------
		$sql='SELECT q.id as id, q.name as name, q.course as course, c.shortname as coursename,
		 cc.name as categoryname, c.category as category 
		 FROM {quiz} q, {course} c, {course_categories} cc 
		 WHERE c.category=cc.id AND q.course=c.id'; // AND q.timeclose > '.now();
		 
		$debugue[]='reports.php: 1. $sql='.$sql;
		$lista=$DB->get_records_sql($sql);
		$debugue[]='reports.php: 2. $lista='.serialize($lista);
		//--------------------------------------------------------------
		echo '<th class="darkred">ID</th>';
		echo '<th class="darkred">Nome</th>';
		echo '<th class="darkred">Bem-definido</th>';
		echo '<th class="darkred">Total presenças</th>';

	$key= isset($_GET['key']) ? $_GET['key'] : '';
	//$lista=customSearch($key,$lista);

    $array = array();
	
		foreach ($lista as $l) {
			$debugue[]='reports.php: 3. $l='.serialize($l);
			
			$quiz=course_categories_as_string($l->category).'/'.$l->coursename.'/'.$l->name;
			$quiz_info = quiz_info($l->id);
			
			//print "<br><strong>quiz_id=".$l->id."</strong><br>";
			if(user_is_allowed_to_see_quiz($USER->id, $l->id)) {
				echo '<tr><td><a href="index2.php?quiz='.$l->id.'">'.$l->id.'</a></td>';
				echo '<td>'.$quiz.'</td>';
				if(quiz_is_well_behaved($l->id)) {
					$behaved='<span class="glyphicon glyphicon-ok"></span>';
				} else {
					$behaved='';					
				}
				echo '<td>'.$behaved.'</td>';
				echo '<td>'.$quiz_info["totalcount"].'</td>';
				
				echo '</tr>';
				
				if($key!=NULL){
					if(strpos($quiz,$key))
						$array[$l->id]=utf8_encode($quiz);
				}
			}
		}

		?>
		</div>
		</table>
			
		<?php
		
		// ALLOWED_TO_SEE (end)
	}

	$MWCFG->ERRORS[] = array('file: reports.php', get_caller_method(),$debugue);
} catch (Exception $err) {
	$MWCFG->ERRORS[] = array('file: reports.php', get_caller_method(),$err->getMessage(),$debugue);
}
include('footer.php');
?>
