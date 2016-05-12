<!DOCTYPE html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html;" charset="utf-8">
    <title>MoodleWatcher</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Rodolfo Matos">

    <!-- Le styles -->
<!--    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="css/calendar.css" rel="stylesheet">
    
        <!-- Le styles -->
    <!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->
<!--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"> -->
    <!-- <link href="css/bootstrap-theme.css" rel="stylesheet"> -->
    
    <link href="css/back2top.css" rel="stylesheet">
    <link href="css/school-logo.css" rel="stylesheet">
    <link href="css/search.css" rel="stylesheet">

    <link href="css/navbarsearches.css" rel="stylesheet">

    <style type="text/css">
		
      /* Page loader styles
      -------------------------------------------------- */
		/* This only works with JavaScript, 
		if it's not present, don't show loader */
			.no-js #loader { display: none;  }
			.js #loader { display: block; position: absolute; left: 100px; top: 0; }
			.se-pre-con {
			position: fixed;
			left: 0px;
			top: 0px;
			width: 100%;
			height: 100%;
			z-index: 9999;
			background: url(img/loader/Preloader_3.gif) center no-repeat #fff;
		}
      /* Sticky footer styles
      -------------------------------------------------- */

      html,
      body {
        height: 100%;
        /* The html and body elements cannot have any padding or margin. */
      }

      /* Wrapper for page content to push down footer */
      #wrap {
        min-height: 100%;
        height: auto !important;
        height: 100%;
        /* Negative indent footer by it's height */
        margin: 0 auto -60px;
      }

      /* Set the fixed height of the footer here */
      #push,
      #footer {
        height: 100px;
      }
      #footer {
        background-color: #f5f5f5;
      }

      /* Lastly, apply responsive CSS fixes as necessary */
      @media (max-width: 767px) {
        #footer {
          margin-left: -20px;
          margin-right: -20px;
          padding-left: 20px;
          padding-right: 20px;
        }
      }
      
	</style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->
    
    <!-- Fav and touch icons -->
    <link rel="shortcut icon" href="img/favicon.png">
    
    <script src="js/jquery.min.js"></script>
    
<!--    <script src="js/bootstrap-checkbox.min.js" defer></script> -->

    <script src="js/modernizr.js"></script>
    <script src="js/pageloader.js"></script>
    
  </head>

  <body>

<!--<div id='ajax_loader' style="position: fixed; left: 50%; top: 50%; display: none;"> -->
<div class="loading" style="position: fixed; left: 78px; top: 50px; z-index: 20;">
	<h1>A calcular...</h1>
	  <img src="img/loader/Preloader_3.gif" width="250" height="250"></img>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.loading').hide();
	});   
</script>

	  <div id="wrap">


<!-- nav (start)-->
<?php include('nav.php'); ?>
<!-- nav (end)-->
<div class="school-logo">
	<img src="img/school-logo-vertical.png" alt="School Logo" class="img-responsive">
</div>

    <div class="container content-container" id="top">
<!--    <div class="container"> -->
<?php
// IMPORTANT:
// since some queries are very slow, now() should be called once per page!

include_once('lib/lib.php');
global $MWCFG; 
$MWCFG->now=now();
?>
