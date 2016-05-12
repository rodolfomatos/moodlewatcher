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

<h3>Perguntas Frequentes</h3>
<blockquote>
<h3>Para que é que o MoodleWatcher serve?</h3>
<p>O MoodleWatcher serve para auditar os exames que decorrem debaixo do Moodle.</p>
</blockquote>
<blockquote>
<h3>Por que é que o MoodleWatcher é necessário?</h3>
<p>O MoodleWatcher é necessário pois o Moodle não restringe o acesso a várias funcionalidades que podem ser usadas pelos estudantes para cometerem fraude durante o exame.</p>
</blockquote>
<blockquote>
	<h3>O que é NAT e em que é que isso afecta a viabilidade de detecção de fraudes em exames de computador?</h3>
	<p>NAT significa literalmente “Network Address Translation” é usado normalmente quando não é possível ter um IP público para todos os computadores presentes num determinado local.
	</p><p>
	Uma analogia que poderá ser utilizada é as matrículas de carrinhos de golfe, que dentro de um campo apropriado, podem ser simplesmente uma numeração simples. No entanto, temos de trocar essa matrícula por outra se quisermos que o carrinho possa circular em via pública. (Ora o que podemos fazer é usar a mesma matrícula do exterior em todos os carrinhos…)
	</p><p>
	Isto significa viabilidade da detecção de fraudes em exames de computadores fica seriamente comprometida sempre que temos uma situação em que os computadores fazem NAT para o acesso ao exterior, pois normalmente não se conseguem distinguir os computadores que estão a ser usados pelos estudantes entre si.
	</p><p>
	Consequência: Uma sala que faça NAT não pode usar as regras “múltiplos IP's por utilizador” e “múltiplos utilizadores no mesmo IP”, pois todos os estudantes presentes na sala irão partilhar os mesmos recursos. Isto implica necessariamente que se tenha de estar activo a regra de “apenas uma sessão por utilizador”, de forma a que qualquer tentativa de fraude seja remetida para a regra “novo login dentro do período de quarentena”. Além disso, um “estudante que não esteja inscrito no curso do teste” não poderá ser detectado. O que poderá estar a acontecer nesse caso é que um estudante esteja a usar uma conta de um colega “externo” para aceder a conteúdos proibidos.</p>
</blockquote>
<blockquote>
	<h3>Os estudantes não podem trazer nenhum equipamento Bluetooth, Wi-Fi, GPRS ou outro (como é óbvio).</h3>
	<p>Auriculares Bluetooth são especialmente preocupantes, pois ao estarem emparelhados com um telemóvel que pode estar guardado num saco, permite ainda assim atender chamadas, onde o estudante tem alguém a ditar-lhe as respostas.</p>
</blockquote>
<blockquote>
	<h3>Um estudante não pode ter acesso aos computadores usados para os exames antes do período do exame</h3>
	<p>sso permite colocar todo o tipo de documentos proibidos no sistema para uso posterior. Idealmente o sistema é “limpo”, usando um sistema parecido com o SiGEX da FEUP.</p>
</blockquote>
<blockquote>
	<h3>Um estudante só pode fazer um login e esse terá obrigatoriamente de ser efectuado após o início do teste.</h3>
	<p>Salvo em computadores em que o IP é público (não é uma sala que faz NAT), todos os outros logins do mesmo utilizador, têm de ser considerados como uma tentativa de fraude, excepto os que forem identificados e relatados aos docentes responsáveis como falhas de sistema. O que se passa é que se não temos forma de controlar em qual computador é que o estudante está realmente sentado, não temos maneira de saber se não há trocas de utilizadores, ou se outro estudante está a fazer o exame de um colega. A única forma que temos de controlar isso, é identificar claramente qual é a sessão do utilizador que está a ser usada no exame (mediante a comprovação visual por parte do professor de um TAG ID+ fotografia do estudante como presente na folha de presenças.</p>
</blockquote>
<blockquote>
	<h3>Uma sala onde decorre um exame tem de ter acesso exclusivo aos estudantes que estão inscritos no curso que ministra o exame</h3>
	<p>Caso contrário, um estudante pode usar uma conta de uma terceira pessoa que não está inscrita no curso do exame, para aceder a todo o tipo de conteúdos ou mesmo falar com outras pessoas. Se a sala faz NAT, isso implica que podemos saber que “alguém” está a a fazer isso, mas não temos forma de saber “quem” é que o está a fazer.</p>
</blockquote>

<blockquote>
	<h3></h3>
	<p></p>
</blockquote>
<blockquote>
	<h3></h3>
	<p></p>
</blockquote>
<blockquote>
	<h3></h3>
	<p></p>
</blockquote>

<?php
include('footer.php');

?>
