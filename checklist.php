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

//if(!isset($quiz)) exit;

global $userid, $DB;

include('header.php');

		?>
		<div class="row">
			<div class="col-md-2"></div>
			<div class="col-md-4"><img src="img/moodlewatcher-logo.png" class="img-responsive center-block" style="min-width:200px"></div>		
			<div class="col-md-6">
<?php
	if(!user_is_allowed_to_see_moodlewatcher($USER->id)) {
		//ErrorMessageBox($title, $msg="", $button_text="", $button_link="", $header="", $footer="")
		ErrorMessageBox("Acesso Proibido!", "Não tem as permiss&otilde;es necess&aacute;rias para aceder a esta p&aacute;gina!", 'Voltar', "{$CFG->wwwroot}", 'Forbiden Access!','');
		writeToLog("about.php: acesso proibido!");
	}
?>			
			
			
			</div>		
		</div>

<?php
$quiz_info=quiz_info_for_checklist( $quiz);
//print "XPTO=".quiz_exists($quiz);

?>
<!-- -->
    <nav role="navigation" class="navbar navbar-default">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="#" class="navbar-brand"><span class="glyphicon glyphicon-education"></span></a>
        </div>
        <!-- Collection of nav links, forms, and other content for toggling -->
        <div id="navbarCollapse" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Checklist</a></li>
                <li class="divider"><span class="glyphicon glyphicon-chevron-right"></span></li>
                <li ><a href="#">Folha de Presenças</a></li>
                <li class="divider"><span class="glyphicon glyphicon-chevron-right"></span></li>
                <li ><a href="#">Painel de Controlo</a></li>
                <li class="divider"><span class="glyphicon glyphicon-chevron-right"></span></li>
                <li ><a href="#">Análise de Relatórios</a></li>
            </ul>
        </div>
    </nav>

<ol class="breadcrumb">
  <li class="active"><a href="#">Checklist</a></li>
  <li><a href="#">Folha de Presenças</a></li>
  <li><a href="#">Painel de Controlo</a></li>
  <li><a href="#">Análise de Relatórios</a></li>
</ol>
<!-- -->


<h1>Checklist para testes sumativos no Moodle</h1>

<ol>
	<li>
		<h3>Reservar a sala de computadores</h3>
		<blockquote>
			<p>A reserva da(s) sala(s) de computadores têm obrigatóriamente que incluir a restrição de acesso à Internet. O funcionamento do MoodleWatcher pressupõe que o único acesso disponível aos estudantes é ao Moodle da U.Porto.</p>
		</blockquote>
	</li>
	<li>
		<h3>Configurar as restrições ao teste no Moodle</h3>
<?php
//print "<pre>";
//print_r(quiz_info_for_checklist( $quiz));
//print "</pre>";
if($quiz_info[key($quiz_info)]->timeopen) { $opentime="check"; } else { $opentime="unchecked"; }
if($quiz_info[key($quiz_info)]->timeclose) { $closetime="check"; } else { $closetime="unchecked"; }
if($quiz_info[key($quiz_info)]->timelimit) { $timelimit="check"; } else { $timelimit="unchecked"; }
if($quiz_info[key($quiz_info)]->password) { $password="check"; } else { $password="unchecked"; }
if(subnet_from_quiz($quiz)!="") { $subnet="check"; } else { $subnet="unchecked"; }
?>
		<blockquote>
			<p>Para que o MoodleWatcher funcione em condições é necessário que estejam definidas as seguintes opções:</p>
			<p><span class="glyphicon glyphicon-<?php echo $opentime; ?>"></span> Hora de início do teste definido</p>
			<p><span class="glyphicon glyphicon-<?php echo $closetime; ?>"></span> Hora de fecho do teste definido</p>
			<p><span class="glyphicon glyphicon-<?php echo $password; ?>"></span> Palavra-chave para entrar no teste definido</p>
			<p><span class="glyphicon glyphicon-<?php echo $subnet; ?>"></span> Restrição de acesso à rede definida</p>
			<p><span class="glyphicon glyphicon-<?php echo $closetime; ?>"></span> Duração do teste definida</p>
		</blockquote>
	</li>
	<li>
		<h3>Planear vários turnos</h3>
		<blockquote>
			<p>Planear vários turnos, distribuindo os estudantes de acordo com o número de lugares disponíveis vs número de estudantes a avaliar. No entanto, a haver turnos, deverá ser utilizado um teste separado para cada turno.</p>
			<p>De igual modo, deve ser evitado usar a mesma sala para outras utilizações por parte dos estudantes, incluindo outros testes de outras UC's.</p>
		</blockquote>
	</li>
	<li>
		<h3>Solicitar apoio local atempadamente</h3>
		<blockquote>
			<p>Solicitar apoio in loco no dia do exame, caso seja pretendido segundo as regras da instituição</p>
		</blockquote>
	</li>
	<li>
		<h3>Verificar se todos os estudantes têm acesso ao Moodle</h3>
		<blockquote>
			<p>Pedir aos estudantes que verifiquem o acesso via AAI atempadamente</p>
		</blockquote>
	</li>
</ol>
<?php
include('footer.php');

?>
