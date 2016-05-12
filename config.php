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

unset($MWCFG);
global $MWCFG, $CFG;
$MWCFG = new stdClass();

$MWCFG->weburl = 'http://'.$_SERVER['SERVER_NAME'].'/moodlewatcher';

// Moodle configuration
//define ("CLI_SCRIPT", 1);
//require_once('/var/www/html/moodle/config.php');

// Show only quizzes with subnet restriction 
$MWCFG->checkonlywithsubnet = 1;

// Show only quizzes that allow only one attempt
$MWCFG->checkonlywithoneattempt = 1; 

// Tempo de visualização
$MWCFG->waybackpoint = 1307491200;

// um professor só pode ver exames até 15 dias...
// 15 dias * 24 horas * 60 minutos * 60 segundos...
$MWCFG->wayback 	 = 1296000;


// Timezone deve coincidir com a do Moodle
$MWCFG->timezone='Europe/Lisbon';

$MWCFG->cookie_name='geek2thebone';

// Debuging
$MWCFG->DEBUG = false;
// Identify server in case we use multiple moodle instances behind a load-balancer 
$MWCFG->server = 'moodle1';
$MWCFG->get_caller = false;

// Is Graphviz installed?
$MWCFG->GRAPHVIZ = false;

// LOGS
$MWCFG->logfile = '/var/www/html/moodlewatcher/logs/moodlewatcher.log';

// Authentication method: 'moodle', 'none'
$MWCFG->auth = 'moodle';
$MWCFG->admin = array('admin', 'rodolfo');
$MWCFG->urltoload = $MWCFG->weburl.'/index2.php';

$MWCFG->UOS = array('school1','school2');

require_once(dirname(__FILE__) . '/lib/lib.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
