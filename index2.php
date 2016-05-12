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


global $userid, $CFG, $DB, $USER, $MWCFG;
try {
require_once('config.php');

/// no guest autologin
require_login(0, false);

// Bibliotecas do Moodle
require_once($CFG->libdir.'/dmllib.php');
require_once($CFG->libdir.'/moodlelib.php');

// Bibliotecas do MoodleWatcher
require_once('lib/lib.php');

if ( isset($_GET["cmid"]) && ($_GET["cmid"] != NULL )) { 
	$quiz = quiz_id_from_context(intval($_GET["cmid"])); 
} else { 
	if ( isset($_GET["quiz"]) && ($_GET["quiz"] != NULL )) {
		$quiz = intval($_GET["quiz"]); 
	}
}

if(!isset($quiz) || !quiz_exists($quiz)) exit;

include('header.php');

// APENAS PARA PESSOAL AUTORIZADO
if(!only_authorized_personnel_can_see( $USER->username, $quiz )) {
?>
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-4"><img src="img/moodlewatcher-logo.png" class="img-responsive center-block" style="min-width:200px"></div>		
			<div class="col-md-6">
		</div>
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-8">
				<?php
					ErrorMessageBox("Não tem permissão para aceder ao teste ".quiz_name($quiz)." (".$quiz.")!","<ul>
					<li>Um teste só pode ser examinado até <u>15 dias</u> após o seu término, por questões legais.</li></ul>",'Voltar','index.php','Acesso negado!','');
					writeToLog('live.php: PERMISSION DENIED: '.$USER->username.' tentou aceder ao quiz '.$quiz);
				?>
			</div>
			<div class="col-md-3"></div>

		</div>
<?php
}
// --------------------
// ALLOW TO SEE (START)

else {

//----------------------------------------------------------------------
// Descrição do quiz

$howmanyusers = how_many_users_in_quizz( $quiz );

$quiz_info = quiz_info($quiz);

$quiz_info["quiz"];

$quizname = $quiz_info["quizname"];
$coursefullname = $quiz_info["coursefullname"];
$categoryname = $quiz_info["categoryname"];

$quiz_time_start[$quiz]  = $quiz_info["quiz_time_start"];
$quiz_time_finish[$quiz] = $quiz_info["quiz_time_finish"];
$quiz_subnet=subnet_from_quiz( $quiz );

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


$msg = "<strong>Unidade Curricular: </strong>".$quiz_info["coursefullname"];
$msg .= "<br><strong>Categoria: </strong>".$quiz_info["categoryname"];
$msg .= "<br><strong>Total de utilizadores a fazerem o teste: </strong>".$quiz_info["totalcount"];
?>
<div class="row">
	<div class="col-md-2"></div>
	<div class="col-md-8">
		<?php
			if($howmanyusers == 0) {
				$button_link="";
				$button_text="";
			} else {
				$button_link="attendees.php?quiz=".$quiz;
				$button_text="Folha de Presenças";
			}
			
			MessageBox($quiz_info["quizname"],$msg,$button_text,$button_link,"Teste (id=".$quiz_info["quiz"].")",$dia_da_prova);
		?>
			<!-- </div> -->
			<!-- <div class="col-md-3"></div> -->

		<?php
		//----------------------------------------------------------------------
		?>
	</div>
	<div class="col-md-2"></div>

	<?php
//-------------------------------------------------------------------------------------------------------------------
//
// Folha de Presenças
//
//-------------------------------------------------------------------------------------------------------------------
// NAVBAR (start)
?>
<!-- <div class="row"> -->
<!--	<div class="col-md-3"></div> -->
	<div class="btn-group">
		<form  role="form" action="index2.php">
			<input type="hidden" name="quiz" value="<?php echo $quiz; ?>" />
			<button type="submit" class="btn btn-info btn-lg"><span class="glyphicon glyphicon-refresh">&nbsp;Refrescar</span></button>
		</form>
	</div>
 </div>


<!-- <nav role="navigation" class="navbar navbar-default"></nav> -->
<?php
// NAVBAR (end)
//-------------------------------------------------------------------------------------------------------------------
?>
<br>
<div class="row">
	<div class="col-md-12">
					<?php if($howmanyusers > 0) {?>
<table class="table table-bordered">
	<tr>
		<td align="left">
			<fieldset>
				<Legend>
					<h1 class="text-danger"><strong>OCORRÊNCIAS</strong></h1> 
					<blockquote>
						Eventos anormais que ocorreram durante o período em que decorre o teste.
					</blockquote>
					<?php } ?>

<!-- -->
<?php
//----------------------------------------------------------------------
// QUIZ is well behaved (start)
if($howmanyusers == 0) {
	checklist($quiz);
} else if(quiz_is_well_behaved($quiz)) {
//if(1){

	$report=wtf($quiz);

//----------------------------------------------------------------------
// Alert Boxes (start)


//----------------------------------------------------------------------
// Aviso se a rede restrita faz NAT
$subnet_uses_nat= subnet_is_NATed($quiz_subnet);
$title='Atenção: A restrição de rede do teste usa salas que fazem NAT ';
$msg='Como todos os estudantes presentes numa sala configurada desse modo, 
vão aparecer como estando presentes no mesmo computador, as regras do MoodleWatcher que nos 
permitem detectar que dois estudantes estão no "mesmo computador" 
(por partilharem a conta, por exemplo), irão ser inibidas, não mostrando esses resultados erróneos.
No entanto, podemos ainda detectar esse tipo de comportamentos, usando duas outras regras que permitem compensar essa falta:
<ol>
<li>Se um estudante fizer "múltiplos logins" durante o tempo em que decorre o teste, como o Moodle da U.Porto desliga todas as sessões mais antigas de um utilizador, se um estudante entrar com uma conta de um colega, este último será forçado a entrar de novo, e deste modo podemos identificá-lo. É portanto crucial que todos os "múltiplos logins" legítimos (como por avaria de um computador, que obrigue um estudante a mudar de posto de trabalho) sejam perfeitamente identificados e reportados.</li>
<li>Toda e qualquer conta de estudante (que não esteja inscrito no teste) que surja na rede restrita terá de ser terminantemente proibida. O que poderá acontecer é que um estudante esteja a usar uma conta "externa" para poder comunicar com colegas ou aceder a conteúdos proibidos. (Como as contas são pessoais e intransmissíveis, poder-se-á tomar medidas mesmo que o estudante não esteja inscrito na UC!)</li>
</ol>

';
$notes='NAT, ou Network Address Translation, significa que os computadores da sala configurada desse modo, aparecem para o Moodle como um computador só, impedindo a detecção de utilizadores que usem a conta de colegas em simultâneo.';
if($subnet_uses_nat){
	notification_box("subnet_uses_nat", $title, $msg, $notes, "warning");
}
//----------------------------------------------------------------------




//----------------------------------------------------------------------
// different users on same IP (siamese_users) (start)
//print "<br>0. MEM:".getVirtualMemoryTaken();
$msg='';

$siamese_users=0;

// no caso de isto ser "live" e os putos ainda não terem fechado o teste
$tempo = now();

//----------------------------------------------------------------------

if($MWCFG->GRAPHVIZ) {
	require_once 'Image/GraphViz.php';
	$gv_siamese_users = new Image_GraphViz();
	$gv_siamese_users->addAttributes(array('bgcolor' => "#F2DEDE", 'overlap' => 'true'));
	
}
//----------------------------------------------------------------------

$debugue[]='index2.php: 1. $tempo='.$tempo;
// para todos os estudantes no quiz
$rows=all_users_that_attended_quiz($quiz);
$debugue[]='index2.php: 2. $rows='.serialize($rows);
if(!empty($rows)) {
	foreach ($rows as $estudante) {
		$debugue[]='index2.php: 3. $estudante='.$estudante->userid;
		// se estudante está de quarentena
		$start=quarantine_start($estudante->userid, $quiz);
		$debugue[]='index2.php: 4. $start='.$start;
		$end=quarantine_end($estudante->userid, $quiz);
		$debugue[]='index2.php: 5. $end='.$end;
		if($tempo < $end) {
			$end=$tempo;		
			$debugue[]='index2.php: 6. $tempo < $end=('.$start.'/'.$end.')';
		}
						
			//1. testar se tem mais do que 1 IP
			$ips=IP_count_from_user( $estudante->userid, $start, $end);
			$debugue[]='index2.php: 7. $ips='.$ips;
			if($ips>0) {
				$siamese_users++;

				$msg .= '<div class="text-danger"><strong>O utilizador "<a target="_blank" href="user.php?userid='.$estudante->userid.'&quiz='.$quiz.'">'.user_Moodle_username($estudante->userid).'</a>" está a usar vários computadores ou existem outros utilizadores a usarem o mesmo computador que o utilizador!</strong>';

				//1.1.Mostrar quais os IP's
				$msg .= '<ul>';;
				$ips_of_user=what_distinct_IPs_were_used_by_user($estudante->userid,$start, $end);
				$debugue[]='index2.php: 8.$ips_of_user='.serialize($ips_of_user);
				if(!empty($ips_of_user)) {
					if($MWCFG->GRAPHVIZ) {
						$gv_siamese_users->addNode('Moodle', array('root' => 'true', 'shape' => "doublecircle"));
					}
					foreach ($ips_of_user as $i) {
						
//----------------------------------------------------------------------
						if($MWCFG->GRAPHVIZ) {
							if(!address_in_subnet($i->ip,$quiz_subnet)){
								$color="red";
								$bgcolor="red";
							} else {
								$color="darkgreen";
								$bgcolor="lawngreen";
							}
//							print_r(sizeof($ips_of_user));
							$room=IPtoRoom($i->ip);
							$gv_siamese_users->addNode($room, array('shape' => "doublecircle", "color" => $color, "fillcolor" => $bgcolor, "style" => "filled"));
							if(!(IPisNATed($i->ip) && count($ips_of_user)==1)){
								$gv_siamese_users->addEdge(array(user_Moodle_username($estudante->userid) => $room));
								$gv_siamese_users->addNode(user_Moodle_username($estudante->userid));
							}
							$gv_siamese_users->addEdge(array($room => 'Moodle'));
						}
//----------------------------------------------------------------------
						
						$debugue[]='index2.php: 9. $i->ip='.$i->ip;
						// 1.1.1 (start) definir cor do IP (start): laranja se subnet não definida, verde se dentro, vermelho se fora
//								if(address_in_subnet($i->ip, $quiz_subnet)) {
//									$subnet_status = ' color="green" ';
//									} else { $subnet_status = ' color="red" '; }
			
							if($quiz_subnet == "") {
								$subnet_status = ' color="orange" ';
							} else {
								if(!address_in_subnet($i->ip, $quiz_subnet)) {
									$subnet_status = ' color="purple" ';
								} else {
									$subnet_status = ' color="red" ';
								}
							} 
						// 1.1.1 (end)
						$msg .= '<li><font '.$subnet_status.'>'.$i->ip.' '.IPtoRoom($i->ip).'</font></li> ';
						

						
						//$msg .= '<li><font '.$subnet_status.'>'.$i->ip.' '.$i->ip.'</font></li> ';
						// -----------							
						//1.2. testar se tem algum outro utilizador num dos IPs usados (start)
						$dups=user_count_from_IP( str_replace(':', '\:', $i->ip), $start, $end);
//						$dups=user_count_from_IP($i->ip, $start, $end);
						$debugue[]='index2.php: 13. $dups='.serialize($dups);
						$msg .= "<ul>Outros utilizadores a usar os mesmos endereços de computador:<br><li>";
						$tmp=0;
						if(!empty($dups))
							foreach ($dups as $d) {
								if($estudante->userid != $d->userid) {
									$debugue[]='index2.php: 14. $d='.$d->userid;
									//print '<br>index2.php: 14. $d='.$d->userid;

									//mais do que 10 userid fica fora da borda...
									$tmp++;if($tmp==10) {$msg .= "<br>";$tmp=0;};
									$msg .= '[<a target=\"_blank\" href="user.php?userid='.$d->userid.'&quiz='.$quiz.'">'.user_Moodle_username($d->userid).'</a>]&nbsp;';
//----------------------------------------------------------------------									
									if($MWCFG->GRAPHVIZ) {
										
										// se o IP não for NATed e o count for > 1 mostrar
										// se o IP for NATed
										if(count($ips_of_user) > 2){
											$gv_siamese_users->addEdge(array(user_Moodle_username($d->userid) => $room));
										}
										/*
										if((IPisNATed($i->ip) && ((int) count($ips_of_user) == 1))){
											//print "<br>X";
										} else {
											print "<hr>";
											print '<br>IPisNATed='.IPisNATed($i->ip);
											print '<br>$ips_of_user='.count($ips_of_user);
											print "<hr>";
											$gv_siamese_users->addEdge(array(user_Moodle_username($d->userid) => $room));
//											$gv_siamese_users->addNode(user_Moodle_username($d->userid));
										}
										*/
									}
//----------------------------------------------------------------------																		
								}
							}
						$msg .= "</li></ul>";
						//1.2. (end)	
								
								
						// -----------							
						}
						$msg .= "</ul>";
				}
				$msg .= '<b><font color="red"></font></b></div><br> ';
				//1.1. (end)
			}
		}
	
}


$notas = 'Ou os computadores estão a fazer NAT, ou temos múltiplas contas a serem usadas no mesmo computador.
No primeiro caso, este alerta pode estar a dar falsos positivos! Será necessário ignorá-lo, atendendo que uma das condições seguintes esteja assegurada:
<ol>
<li>Existe um applet java ou flash instalada no Moodle que permita identificar univocamente os utilizadores</li>
<li>Existe um plugin na autenticação do Moodle que assegure que apenas os utilizadores inscritos no teste podem estar presentes num computador dentro da subnet do teste e mesmo esses só podem ter um login activo de cada vez</li>
</ol> 
No segundo caso, salvo situações que têm obrigatoriamente de ser reportadas ao docente - como falha de sistema ou navegador que obrigue a mudar de computador - são muito provavelmente tentativas de fraude.';

if(sizeof($siamese_users)!=0) {

	// Graphviz (start)
	if($MWCFG->GRAPHVIZ) {
		//<div class="img-thumbnail img-responsive"></div>
		$graph='
					<img src="data:image/png;base64,'. 
					base64_encode($gv_siamese_users->fetch('png','dot')).
					 '"/>
				';
	}
	// Graphviz (end)
	notification_box("siamese_users", "ALERTA: Vários utilizadores partilham o mesmo computador no período em que decorre o teste.", $msg , $graph.$notas, "danger");
}

// different users on same IP (end)
//----------------------------------------------------------------------


//----------------------------------------------------------------------
// Invalid users on quiz network (start)
//print "<br>0. MEM:".getVirtualMemoryTaken();

$msg='';

$invalid_users=invalid_users_on_quiz_subnet($quiz);
$debugue[]='index2.php: 15.$invalid_users='.serialize($invalid_users);
//print '<br>index2.php: 15.$invalid_users='.serialize($invalid_users);
//print '<br>index2.php: 15. array_filter=<pre>';
//print_r(array_filter($invalid_users));
//print "</pre>";

if(array_filter($invalid_users) != NULL) {
	$debugue[]='index2.php: 16.';
	//print '<br>index2.php: 16.';
	foreach($invalid_users as $u => $userid) {
		$debugue[]='index2.php: 17.';
		//print '<br>index2.php: 17.';
		if($userid=="") break;
		$debugue[]='index2.php: 18.';
		//print '<br>index2.php: 18.';
		$xs=what_IPs_were_used_by_user($userid,quiz_opentime($quiz),time());
		$debugue[]='index2.php: 19.$xs='.serialize($xs);
		//print '<br>index2.php: 19.$xs='.serialize($xs);
		if(!empty($xs)){
			$msg .= '<table class="table">';
			$msg .= '	<thead><tr><th>Utilizador</th><th>IP</th><th>Tempo</th><th>Evento</th><th>URL</th></tr></thead>';
				
			foreach($xs as $x => $details) {
				$msg .= '<tr>';
				$msg .= '<td><a target="_blank" href="user.php?userid='.$userid.'&quiz='.$quiz.'">'.user_Moodle_username($userid).'</a></td>';
				$msg .= '<td>'.$details->ip.'</td>';
				$msg .= '<td>'.date('Y-m-d H:i:s',$details->time).'</td>';
				$msg .= '<td>'.$details->action.'</td>';
				$msg .= '<td>'.$details->url.'</td>';
				$msg .= '</tr>';
			}
			$msg .= '</table>';
		}
	}
}

$notas = 'Um estudante em exame pode ter aberto uma sessão com uma conta de um utilizador que não esteja inscrito no teste para poder aceder a conteúdos proibidos';

if(array_filter($invalid_users) != NULL) {
	notification_box("invalidusersonquiznetwork", "ALERTA: Utilizadores que não estão inscritos na Unidade Curricular do teste como estudantes estão a fazer coisas em algum computador presente na rede restrita.", $msg , $notas, "danger");
}

// Invalid users on quiz network (end)
//----------------------------------------------------------------------

//----------------------------------------------------------------------
// Eventos antes da quarentena de cada estudante (start)
$msg='';
$notes='';

$alert_before=0;
$txt='';
foreach ($report as $r) {
	if($r['quarantine']=='before'){
		$alert_before++;
		if(!isset($r['accesses'])) $r['accesses']="Utilizador tem ac&ccedil;&atilde;es registadas em v&aacute;rios(!) computadores";
		$txt .= '<tr><td><a target="_blank" href="user.php?quiz='.$quiz.'&userid='.$r['userid'].'">'.user_Moodle_username($r['userid']).'</a></td><td>'.$r['action'].'</td><td>'.$r['accesses'].'</td></tr>';
	}	
}
if($alert_before) {
		$msg .= '<table class="table">';
		$msg .=	'<thead><tr><th>Utilizador</th><th>Acção</th><th>Acessos</th></tr></thead>';
		$msg .= $txt;
		$msg .= '</table>';
}

if($alert_before ) {
	notification_box("eventsbeforequarantine", "Aten&ccedil;&atilde;o: Estudante(s) fizeram alguma coisa at&iacute;pica no período compreendido entre o in&iacute;cio do teste e o in&iacute;cio da tentativa (por parte do estudante).", $msg, $notes);
}

// Eventos antes da quarentena de cada estudante (end)
//----------------------------------------------------------------------

//----------------------------------------------------------------------
// Eventos durante a quarentena de cada estudante (start)
$msg='';
$notes='';

$alert_during=0;
$txt='';

foreach ($report as $r) {
	if($r['quarantine']=='during'){
		$alert_during++;
		if(!isset($r['accesses'])) $r['accesses']="Utilizador com registos em vários(!) computadores";
		$txt .= '<tr><td><a target="_blank" href="user.php?quiz='.$quiz.'&userid='.$r['userid'].'">'.user_Moodle_username($r['userid']).'</a></td><td>'.$r['action'].'</td><td>'.$r['accesses'].'</td></tr>';
	}	
}
if($alert_during) {
		$msg .= '<table class="table">';
		$msg .=	'<thead><tr><th>Utilizador</th><th>Acção</th><th>Acessos</th></tr></thead>';
		$msg .= $txt;
		$msg .= '</table>';
}

if($alert_during) {
	notification_box("eventsduringquarantine", "ALERTA: Estudante(s) fizeram alguma coisa at&iacute;pica no tempo de 'quarentena' (ou seja, no tempo compreendido entre o início da primeira tentativa por parte do estudante, até ao momento em que este fecha a última tentativa)!", $msg, $notes, "danger");
}
// Eventos durante a quarentena de cada estudante (end)
//----------------------------------------------------------------------

//----------------------------------------------------------------------
// Eventos depois da quarentena de cada estudante (start)
$msg='';
$notes='';

$alert_after=0;
$txt='';
foreach ($report as $r) {
	if($r['quarantine']=='after'){
		$alert_after++;
		if(!isset($r['accesses'])) $r['accesses']="Utilizador com registos em vários(!) computadores";
		$txt .= '<tr><td><a target="_blank" href="user.php?quiz='.$quiz.'&userid='.$r['userid'].'">'.user_Moodle_username($r['userid']).'</a></td><td>'.$r['action'].'</td><td>'.$r['accesses'].'</td></tr>';
	}	
}
if($alert_after) {
		$msg .= '<table class="table">';
		$msg .=	'<thead><tr><th>Utilizador</th><th>Acção</th><th>Acessos</th></tr></thead>';
		$msg .= $txt;
		$msg .= '</table>';
}

if($alert_after) {
	notification_box("eventsafterquarantine", "Atenção: estudante(s) fizeram alguma coisa depois de terem fechado a última tentativa, mas ainda dentro do período do teste.", $msg, $notes);
}
// Eventos depois da quarentena de cada estudante (end)
//----------------------------------------------------------------------


$all_is_well= (int) $invalid_users+$alert_before+$alert_during+$alert_after;
$title='Parece que está tudo bem...';
$msg='desde que o procedimento seja seguido, e haja restrições de rede';
$notes='Os estudantes ainda podem copiar de formas tradicionais...';
if($all_is_well==0){
	notification_box("alliswell", $title, $msg, $notes, "success");
}

//----------------------------------------------------------------------
// Alert Boxes (end)
//----------------------------------------------------------------------

?>
				</Legend>
			</fieldset>
		</td>
	</tr>
</table>

</div>
<?php
// QUIZ is well behaved (end)
//----------------------------------------------------------------------
} else {
// restrição de rede não definida (start)

$title='ATENÇÃO: O teste não está bem definido.';
$msg='O MoodleWatcher não irá reportar nenhuma ocorrência!';
$notes='Deverá garantir que o teste tem o tempo de início e de término bem definido e tem de aplicar as restrições de rede';

notification_box("alliswrong", $title, $msg, $notes, "danger");

// restrição de rede não definida (end)
}


// --------------------
// ALLOW TO SEE (END)
}
    } catch (Exception $err) {
 		$MWCFG->ERRORS[] = array('index2.php', get_caller_method(),$debugue,$err->getMessage());
    }	
include('footer.php');

?>
