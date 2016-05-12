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
			
			<div class="col-md-2"></div>
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-8">
				<?php
	//if(!($MOODLE_admin || $MW_admin) ) die;
	//if(!user_is_GOD() ) die;

	if(!user_is_allowed_to_see_moodlewatcher($USER->id)) {
		//ErrorMessageBox($title, $msg="", $button_text="", $button_link="", $header="", $footer="")
		ErrorMessageBox("TAG ID: ".tag_id(), "Identifica&ccedil;&atilde;o da sess&atilde;o utilizada.<br>(Em exame apenas &eacute; admitido um identificador por estudante!)", 'Voltar', "{$CFG->wwwroot}", 'Access as Student!','');
		writeToLog("index.php: acesso de estudante!");
	} else {
		// ALLOWED_TO_SEE (start)
		?>
		
					</div>		
			<div class="col-md-2"></div>

		<!-- Search (start) -->
<?php
if ( isset($_GET["search"]) && ($_GET["search"] != NULL )) { 
	$search=htmlentities($_GET["search"]);
} else {
	$search="";
}
//	$quiz = quiz_id_from_context(intval($_GET["cmid"])); 
?>
		<div class="row">
			<div class="col-md-12">
<!--				<div class="panel panel-default"> -->
					<div class="bs-example">
						
<!--						<form class="navbar-form navbar-left" role="search" action="index2.php"> -->
						<form class="form-inline" role="search" action="index.php">
							<div class="form-group">
						
						<input type="text" name="search" value="<?php echo $search; ?>" class="tt-query" autocomplete="off" spellcheck="false" placeholder="Procurar quiz">
							</div>
						<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span>&nbsp;Procurar</button>
					  </form>
						
					</div>
<!--				</div> -->
			</div>
		</div>
		
		<!-- Search (end) -->
		<?php
		// Well-behaved quizzes list (start)
		?>
			</div>		
			<div class="col-md-2"></div>
		</div>
		
		<?php
//		<h3>Lista dos Quizzes</h3>

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
		echo '<th class="darkred">ID</th><th class="darkred" colspan="4">Nome</th>';
	

	$key= isset($_GET['key']) ? $_GET['key'] : '';
	//$lista=customSearch($key,$lista);
		//--------------------------------------------------------------
	$total_quizzes=sizeof($lista);
	$pagelength=$total_quizzes;
	
		if ( isset($_GET["page"]) && ($_GET["page"] != NULL )) { 
			$page=htmlentities($_GET["page"]);
		} else {
			$page=1;
		}
		// saber qual o inicio de cada página
		if($page==1) {
			$start=1;
		} else {
			$start=$page*$pagelength-1;
		}
		$end=$start+$pagelength-1;
		//print "start:";print_r($start);print "<br>end:";print_r($end);
		//--------------------------------------------------------------

    $array = array();
	$count=0;
		foreach ($lista as $l) {
			$debugue[]='reports.php: 3. $l='.serialize($l);
			$count++;
			$quiz=course_categories_as_string($l->category).'/'.$l->coursename.'/'.$l->name;
			//print "<br><strong>quiz_id=".$l->id."</strong><br>";
			$allow=true;
			if($search!="") {
			 $allow=contains(strtoupper($search), strtoupper($l->id.' '.$quiz));
			}
			if($count>=$start && $count<=$end) {
				$inpage=true;
			} else {
				$inpage=false;	
			}
			if(user_is_allowed_to_see_quiz($USER->id, $l->id) && $allow && $inpage) {
				echo '<tr><td><a href="index2.php?quiz='.$l->id.'">'.$l->id.'</a></td>';
				echo '<td><a href="index2.php?quiz='.$l->id.'">'.$quiz.'</a></td></tr>';
				
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

		pagination($lista, $search, $page,$start,$pagelength, "index.php");
		// ALLOWED_TO_SEE (end)
	}

	$MWCFG->ERRORS[] = array('file: index.php', get_caller_method(),$debugue);
} catch (Exception $err) {
	$MWCFG->ERRORS[] = array('file: index.php', get_caller_method(),$err->getMessage(),$debugue);
}
include('footer.php');
?>
