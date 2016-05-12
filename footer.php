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

// Bibliotecas do MoodleWatcher
require_once('lib/lib.php');
?>
<div id="push"></div>
<hr>
<footer>
	<div class="row">
		<div class="col-md-8"><img src="img/UPdigital.png"></div>
		<div class="col-md-4 text-right"><img align="top-right" src="img/misc/geek_to_the_bone.png"><span class="glyphicon glyphicon-copyright-mark"></span></div>
	</div>

	<div class="row">
		<div class="col-md-8 text-left">
		</div>
		<div class="col-md-4 text-right">
			<strong><?php moodlewatcherversion(); ?></strong>
			<small><?php print "<br>Current Time: ".date('d/m/Y-H:i', now()); ?></small>
			<br>
			<small>
			<?php print 'Used '.getVirtualMemoryTaken().'% of total memory'; ?>
			</small>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12"><hr></div>
	</div>
</footer>
</div> <!-- /container -->

<!-- back2top (start) -->
<span id="top-link-block" class="hidden">
    <a href="#top" class="well well-sm"  onclick="$('html,body').animate({scrollTop:0},'slow');return false;">
        <i class="glyphicon glyphicon-chevron-up"></i> Voltar ao topo
    </a>
</span><!-- /top-link-block -->
<!-- back2top (end) -->

<?php 
//------------------------------
// DEBUG (start)
debugging_system();
// DEBUG (end)
//------------------------------
?>

 </div>  <!-- /wrap -->

<?php
 
 //-----------------------------
 ?>
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
 <!--   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> -->
 <!--   <script src="js/jquery.min.js"></script>  -->
    <script src="js/bootstrap.js"></script>

	<script src="js/back2top.js"></script>

	<script src="js/typeahead.min.js"></script>
    <script>
	$(document).ready(function(){
		$('input.typeahead').typeahead({
			name: 'typeahead',
			remote:'search.php?key=%QUERY',
			limit : 20
		});
	});
    </script>
    
	<script src="src/jquery.table2excel.js"></script>
	<script>
		$(function() {
			$("#export2excel").click(function(){
			$("#table2excel").table2excel({
				exclude: ".noExl",
				name: "Excel Document Name"
			}); 
			 });
		});
	</script>

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>

  </body>
</html>
