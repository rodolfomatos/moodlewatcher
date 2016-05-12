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

// Based on: https://github.com/codeforgeek/ajax-box-php

// direct access die!
//defined('MWPATH_BASE') or die();
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

	global $DB, $USER, $MWCFG, $_COOKIE;

	if(!user_is_allowed_to_see_moodlewatcher($USER->id)) {
		die;
	} else {		
		// ALLOWED_TO_SEE (start)

		if(!isset($_COOKIE[$MWCFG->cookie_name])) {
			//--------------------------------------------------------------
			$sql='SELECT q.id as id, q.name as name, q.course as course, c.shortname as coursename, cc.name as categoryname, c.category as category FROM {quiz} q, {course} c, {course_categories} cc WHERE c.category=cc.id AND q.course=c.id'; // AND q.timeclose > '.now();
			$debugue[]='search.php: 1. $sql='.$sql;
			$lista=$DB->get_records_sql($sql);
			$debugue[]='search.php: 2. $lista='.serialize($lista);
			//--------------------------------------------------------------
			foreach ($lista as $l) {
				$debugue[]='search.php: 3. $l='.serialize($l);
			
				$MWCFG->quiz[$l->id]=$l->id.':'.course_categories_as_string($l->course).'/'.$l->coursename.'/'.$l->name;			
			}
			
			setcookie($MWCFG->cookie_name,json_encode($MWCFG->quiz),time() + 60, "/");
		} else {

			$MWCFG->quiz=json_decode($_COOKIE[$MWCFG->cookie_name]);
		}
	
		$key= isset($_GET['key']) ? $_GET['key'] : '';

		// If fail redirect homepage
		if($key == '') redirect('index.php');
		$array = array();

		foreach ($MWCFG->quiz as $l) {
			$debugue[]='search.php: 3. $l='.serialize($l);

			if(user_is_allowed_to_see_quiz($USER->id, $l[0])) {

				if(strpos($l,$key) !== FALSE)
					$array[]=$MWCFG->quiz;
			}
		}		
		echo json_encode($array);
		// ALLOWED_TO_SEE (end)
	}

	$MWCFG->ERRORS[] = array('file: search.php',$debugue);
} catch (Exception $err) {
	$MWCFG->ERRORS[] = array('file: search.php',$err->getMessage(),$debugue);
}
?>
