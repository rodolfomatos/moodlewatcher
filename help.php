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
<h3>Help</h3>
	

<?php
include('footer.php');

?>
