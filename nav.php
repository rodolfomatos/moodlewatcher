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

include_once('config.php');
include_once('lib/lib.php');
global $CFG, $USER;
$GOD=user_is_GOD();
?>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php?"><img src="img/moodlewatcher-logo-83x32.png"></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
    <!--    <li><a href="#">Link</a></li> -->
	<li><a href="index.php"><span class="glyphicon glyphicon-search">Search</span></a></li>
    <?php
    if($GOD) {
    ?>
		<li><a href="quizzes_list.php"><span class="glyphicon glyphicon-th-list">List</span></a></li>
	<?php
	}
	?>
		<li><a href="calendar.php"><span class="glyphicon glyphicon-calendar">Calendar</span></a></li>
<?php 
// Menu Administração (acesso restrito) (start)
if($GOD) { 
//if(user_is_allowed_to_see_moodlewatcher($USER->id)) { 
?> 
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Administration <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu"> 
       <!--     <li><a href="quizzes_list.php">Lista de testes</a></li> -->
            <li><a href="reports.php"><span class="glyphicon glyphicon-stats">Reports</span></a></li>
       <!--     <li><a href="calendar.php">Calend&aacute;rios</a></li> -->
          <!--  <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li class="divider"></li>
            <li><a href="#">One more separated link</a></li> -->
            <?php
            if(user_is_GOD()) {
			?>
            <li><a href="keylogger.php"><span class="glyphicon glyphicon-record">&nbsp;Keylogger</span></a></li>
            <?php
			}
			?>
          </ul>
        </li>
 <?php
  } 
 // Menu Administração (acesso restrito) (end)
 ?>
 
      </ul>
<?php
if(0) { 
//if(user_is_allowed_to_see_moodlewatcher($USER->id)) { 
?> 

      <form class="navbar-form navbar-left" role="search" action="index2.php">
        <div class="form-group">
          <input type="text" name="quiz" class="form-control" placeholder="ID do teste">
        </div>
        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span>Quick Search</button>
      </form>
<?php
}
?>
      <ul class="nav navbar-nav navbar-right">
<?php
if(user_is_allowed_to_see_moodlewatcher($USER->id)) { 
?> 
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Help <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="help.php">Ajuda</a></li>
            <li><a href="tutorials.php">Tutorials</a></li>
            <li><a href="security.php">Prevention &amp; Security</a></li>
            <li><a href="faqs.php">FAQ's</a></li>
            <li class="divider"></li>
            <li><a href="contacts.php"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;Contacts</a></li>
            <li><a href="about.php"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;About</a></li>
          </ul>
        </li>
<?php
}
?>
        <li class="active"><a href="<?php print $CFG->wwwroot; ?>">Back to Moodle <span class="glyphicon glyphicon-share-alt"></span><span class="glyphicon glyphicon-education"></span><span class="sr-only">(current)</span></a></li>
        <li><a href="<?php print $CFG->wwwroot;?>/login/logout.php?sesskey=<?php print session_id(); ?>">EXIT&nbsp;<span class="glyphicon glyphicon-off"></a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
