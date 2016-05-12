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

//define('CLI_SCRIPT','1');
require_once('config.php');

// no guest autologin
require_login(0, false);

// Bibliotecas do MoodleWatcher
require_once('lib/lib.php');

global $DB, $MWCFG, $USER;

global $attendees_only, $orderby, $asc;

//----------------------------------------------------------------------
if ( isset($_GET["quiz"]) && ($_GET["quiz"] != NULL )) { 
	$quiz = filter_var($_GET["quiz"], FILTER_VALIDATE_INT); 
} else die();
if ( isset($_GET["orderby"]) && ($_GET["orderby"] != NULL )) {
	$orderby=filter_var($_GET["orderby"], FILTER_SANITIZE_STRING); 
} else {
	$orderby = "userid";
}//userid, idnumber, name, room

if ( isset($_GET["asc"]) && ($_GET["asc"] != NULL )) {
	$asc=filter_var($_GET["asc"], FILTER_VALIDATE_BOOLEAN);
} else {
	$asc=true;
} 

if ( isset($_GET["attendees_only"]) && ($_GET["attendees_only"] != NULL )) {
	$attendees_only=filter_var($_GET["attendees_only"], FILTER_VALIDATE_BOOLEAN);
} else {
	$attendees_only=false;
} 
//----------------------------------------------------------------------
include('header.php');
writeToLog("attendees.php: (".$quiz.")");
//----------------------------------------------------------------------
if(!only_authorized_personnel_can_see( $USER->username, $quiz )) {
		//ErrorMessageBox($title, $msg="", $button_text="", $button_link="", $header="", $footer="")
  		ErrorMessageBox("Acesso Proibido!", "Não tem as permiss&otilde;es necess&aacute;rias para aceder a esta p&aacute;gina!", 'Voltar', 'index.php', 'Forbiden Access!','Este evento ir&aacute; ser comunicado aos administradores');
		writeToLog("attendees.php: acesso proibido! (".$quiz.")");
}
//----------------------------------------------------------------------
// ALLOW TO SEE (START)
else {
//echo "#7:";
?>

<?php
function writeGETstring($quiz, $orderby, $asc, $attendees_only){
	print basename($_SERVER['PHP_SELF']);?>?quiz=<?php print "$quiz"; ?>&orderby=<?php print "$orderby"; ?>&asc=<?php print "$asc"; ?>&attendees_only=<?php print "$attendees_only";
}	
//writeGETstring($quiz, $orderby, $asc, $attendees_only);

//----------------------------------------------------------------------
// Descrição do quiz

  $quiz_info = quiz_info($quiz);
  
  $quiz_info["quiz"];
    
  $quizname = $quiz_info["quizname"];
  $coursefullname = $quiz_info["coursefullname"];
  $categoryname = $quiz_info["categoryname"];


    $quiz_time_start[$quiz]  = $quiz_info["quiz_time_start"];
    $quiz_time_finish[$quiz] = $quiz_info["quiz_time_finish"];
    $quiz_subnet=subnet_from_quiz( $quiz );
   echo '<div>';

//----------------------------------------------------------------------
// How many users have taken the quiz?  (Each user could have > 1 role)
	echo '<div style="text-align:left">';

// DATA do teste (start)-------------------
// se dia de inicio é igual ao dia do fim
	$dia_da_prova="";
	if(date('d/m/Y', $quiz_time_start[$quiz]) == date('d/m/Y', $quiz_time_start[$quiz])) {
			$dia_da_prova .= "Dia da prova: ".date('d/m/Y', $quiz_time_start[$quiz]);
			$dia_da_prova .= " das ".date('H:i', $quiz_time_start[$quiz])." às ".date('H:i', $quiz_time_finish[$quiz]);
	} else {
			$dia_da_prova .= "Dia do in&iacute;cio da prova: ".date('d/m/Y', $quiz_time_start[$quiz]);
			$dia_da_prova .= "Dia do fim da prova: ".date('d/m/Y', $quiz_time_finnish[$quiz]);
	}		

// DATA do teste (end)-------------------


$msg = "<strong>Categoria: </strong>".$quiz_info["categoryname"]." / ".course_shortname($quiz);
$msg .= "<br><strong>Total de utilizadores: </strong>".$quiz_info["totalcount"];
$msg .= "<br><strong>Course: </strong>".course_fullname(course_from_quiz($quiz));
?>
<div class="col-md-2"></div>
<div class="col-md-8">
<?php
   // $totalcount[$quiz] = $quiz_info["totalcount"];
//	print "<h4>Total de utilizadores que fizeram o teste: ".$totalcount[$quiz]."</h4>";

	MessageBox($quiz_info["quizname"],$msg,"","",'<a href="index2.php?quiz='.$quiz_info["quiz"].'">Teste (id='.$quiz_info["quiz"].')</a><span class="glyphicon glyphicon-chevron-right"></span>'.'Folha de Presenças',$dia_da_prova);
?>
</div>
<div class="col-md-2"></div>
<?php

	echo '</div>';

	
?>
</div>
	<!-- Export to Excel (start)-->
        <button id="export2excel" class="btn btn-default">Exportar para Excel</button>
	<!-- Export to Excel (end)-->


<!-- Form (start) -->
<form role="form" action="attendees.php">
	<div class="form-group">
		<label for=""></label>
		<input type="text" class="hide" name="quiz" value="<?php print $quiz; ?>">
	</div>
	<div class="checkbox">
    <label><input type="checkbox" name="attendees_only" <?php if($attendees_only) {print "checked";} ?>>Presentes no teste apenas</label>
  </div>
  <button type="submit" class="btn btn-info btn-lg"><span class="glyphicon glyphicon-refresh">&nbsp;Refrescar</span></button>
</form>
<!-- Form (end) -->

<div id="folha" class="col-md-12">

<div>

<table class="table table2excel" data-tableName="Lista" id="table2excel" summary="" border="0" cellspacing="0" cellpadding="0" bordercolor="gray" width="100%">
<tr align="left" class="noExl">
	<th></th>
	<th class="noExl">Foto</th>
	<th>TAG ID</th>
	<th>User&nbsp;<a href="<?php writeGETstring($quiz, 'userid', $asc, $attendees_only) ?>">
		<span class="glyphicon glyphicon-sort"></span></a></th>
	<th>ID&nbsp;<a href="<?php writeGETstring($quiz, "idnumber", $asc, $attendees_only) ?>">
		<span class="glyphicon glyphicon-sort"></span></a></th>
	<th>Nome/Assinatura&nbsp;<a href="<?php writeGETstring($quiz, "name", $asc, $attendees_only) ?>">
		<span class="glyphicon glyphicon-sort"></span></a></th>
	<th>Sala&nbsp;<a href="<?php writeGETstring($quiz, "room", $asc, $attendees_only) ?>">
		<span class="glyphicon glyphicon-sort"></span></a></th>
</tr>
<?php
if($attendees_only) {
	$rows=all_users_that_attended_quiz( $quiz );
//		print "<pre>";
//		print_r($rows);
//		print "</pre>pre>";
} else {
	if(isset($orderby)){
		$rows=all_students_enrolled_in_course_of_quiz( $quiz , $orderby);
	} else {
		$rows=all_students_enrolled_in_course_of_quiz( $quiz );
	}
	
}
//print_r($rows);
if(!empty($rows)) {
	//$rows=array_multisort($rows, SORT_ASC);
	//print "<pre>";print_r($rows);print "</pre>";
?>
<h2>Folha de Presen&ccedil;as</h2>
<?php

// para ver se o userid já não foi usado...
	global $used;
	$used = array();
	
	function used( $userid) {
		global $used;
		$virgin=true;
		foreach($used as $u){
			if("$u"=="$userid") {
				$virgin=false;
			}
			if(!$virgin) break;
		}
		$used[].=$userid;
		return $virgin;
	}

//	print "<pre>used=";print_r($used);print "</pre>";
	
	foreach ($rows as $r) {
		if(!used($r->userid)) break;
		//$used[].=$r->userid;

		// alternar a cor da linha (start)
		if(($r->rownumber % 2) == 0) { $corlinha='lightgray'; } else { $corlinha='white'; }
		// alternar a cor da linha	(end)
//		print_user_picture($user, $course->id, $user->picture, true, false, false);
//		print '<tr><td><!--<img height="50%" width="50%" src="'.$CFG->wwwroot.'/user/view.php?id='.$r[userid].'">--></td>';
		
		print '<tr bgcolor="'.$corlinha.'"><td>'.$r->rownumber.'</td><td style="width=51px; height=51px" ><img width="50px" height="50px" src="'.$CFG->wwwroot.'/user/pix.php/'.$r->userid.'/f1.jpg">';

		print "</td><td>";
	// TAG ID (start)
		$ss=all_current_sessionid_from_user($r->userid);
		$i=0;$t="";
		foreach($ss as $s) {
			if(user_is_GOD()) {
	//			delete_session_from_user($r->userid,$s->sid);
				$t .= '<a href="logout.php?quiz='.$quiz.'&userid='.$r->userid.'&sesskey='.$s->sid.'">';
				$t .= tag_id($s->sid).'</a><br>';
			} else {
				$t .= tag_id($s->sid);
			}
			$i++;
		}
		if($i) {
			print '<h4>'.$t.'</h4>';
		} else {
			print $t;
		}
			print '</td>';
	// TAG ID (end)

			print '<td><a target ="_blank" href="user.php?userid='.$r->userid.'&quiz='.$quiz.'">'.$r->userid.'</a></td>';
			print '<td>'.$r->idnumber.'</td>';
	// Firstname
			print '<td valign="top" style="white-space:nowrap; ">'.$r->firstname.' ';
	// Lastname
			print $r->lastname.'</td><td>';
			$IPs=user_IPs_in_quiz_attempts( $r->userid, $quiz); 		
			$attempts=user_attempts_in_a_quiz( $r->userid, $quiz);
			
		//returns recordset with attempt, IP, timestart, timefinish	
			if(!empty($IPs) && user_attempts_in_a_quiz( $r->userid, $quiz )) {
//			if(1) {
				//print "<pre>";print_r($IPs);print "</pre>";
	?>
				<table border="1" border="0" cellspacing="0" cellpadding="0" align="top" width="100%">
					<tr class="noExl" align="left">
						<th>IP</th><th>Sala</th>
					</tr>
	<?php
		// attempts (start)
		//for ($a = 1; $a <= $attempts; $a++) {
			// IPS (start)
				foreach ($IPs as $i) {
					//if($i->attempt==$a) {
						// definir cor do IP (start): laranja se subnet não definida, verde se dentro, vermelho se fora
						if(address_in_subnet($i->ip,$quiz_subnet)) {
							$subnet_status = ' bgcolor="lightgreen" '; 
						} else { 
							$subnet_status = ' bgcolor="red" '; 
						}


//						print '<tr class="noExl"><td width="25px" align="center">'.$i->attempt.'</td><td width="90px" '.$subnet_status.'>'.$i->ip.'</td><td width="45px" align="center">'.IPtoRoom($i->ip).'</td><td width="45px" align="center">'.date('H:i', $i->timestart).'</td><td width="45px" align="center">'.$tf.'</td></tr>';
						print '<tr class="noExl"><td width="90px" '.$subnet_status.'>'.$i->ip.'</td><td width="45px" align="center">'.IPtoRoom($i->ip).'</td></tr>';
						
					//}
				}
			// IPS (end)
		//}
		// attempts (end)

?>
			</table>
<?php
		}
?>
		</tr>
<?php			
	}
}
?>
</table>
</div>
</div>
<?php
// ALLOWED TO SEE (END)
}
include('footer.php');
?>
