<?php
require_once '../app/Mage.php';
umask(0);
Mage::app('default');
Mage::getSingleton('core/session', array('name' => 'frontend'));

$additional_js = "";
$additional_css = "";
$page_title = "VaporNation Product Guide";
$additional_body = "";
$show_hot_categories = false;
switch($CURRENT_PAGE) {
	case "index":
		$page_title .= "";
		$header_file = "default";
		$show_hot_categories = true;
		break;
	case "learning_center":
		$page_title .= " - Learning Center";
		$header_file = "learningcenter";
		break;
	case "quiz":
		//$additional_css .= '<link href="/prodguide/inc/slickquiz/css/reset.css" media="screen" rel="stylesheet" type="text/css">';
		$additional_css .= '<link href="/prodguide/inc/slickquiz/css/slickQuiz.css" media="screen" rel="stylesheet" type="text/css">';
		$additional_css .= '<link href="/prodguide/inc/slickquiz/css/master.css" media="screen" rel="stylesheet" type="text/css">';
		$page_title .= " - Learning Center - Quiz";
		$header_file = "learningcenter";
		//$additional_body .= ' id="slickQuiz"';
		break;
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="robots" content="NOINDEX,NOFOLLOW" />
<title><?=$page_title;?></title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<link rel="icon" href="http://www.vapornation.com/skin/frontend/vapornation/default/favicon.ico" type="image/x-icon" />
<link href="/prodguide/inc/lightbox/css/lightbox.css" rel="stylsheet" />
<link rel="shortcut icon" href="http://www.vapornation.com/skin/frontend/vapornation/default/favicon.ico" type="image/x-icon" />
<link href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/readable/bootstrap.min.css" rel="stylesheet">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha256-KXn5puMvxCw+dAYznun+drMdG1IFl3agK0p/pqT9KAo= sha512-2e8qq0ETcfWRI4HJBzQiA3UoyFk6tbNyG+qSaIBZLyW9Xf3sWZHN/lxe9fTh1U45DpPf07yj94KsUHHWe4Yk1A==" crossorigin="anonymous"></script>
<script src="inc/bootpag/jquery.bootpag.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylsheet" />
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<?=$additional_css;?>
<?=$additional_js;?>
<style>
/* Bootstrap updates */
li.dropdown{ font-size:22px; font-weight:bold; }
.dropdown-menu li a { font-size:14px }
.navbar li { font-size:20px; }
/*body > .container { margin-top:60px; }*/ /*Removed because we are no longer using a fixed topnav*/
body { margin-bottom:70px; } /* for Sticky Footer */
.footer { position:fixed; bottom:0; width:100%; height:60px; background-color:#f5f5f5; } /* for Sticky Footer */

/* for sub-menus */
.dropdown-submenu{position:relative;}
.dropdown-submenu>.dropdown-menu{top:0;left:100%;margin-top:-6px;margin-left:-1px;-webkit-border-radius:0 6px 6px 6px;-moz-border-radius:0 6px 6px 6px;border-radius:0 6px 6px 6px;}
.dropdown-submenu:hover>.dropdown-menu{display:block;}
.dropdown-submenu>a:after{display:block;content:" ";float:right;width:0;height:0;border-color:transparent;border-style:solid;border-width:5px 0 5px 5px;border-left-color:#cccccc;margin-top:5px;margin-right:-10px;}
.dropdown-submenu:hover>a:after{border-left-color:#ffffff;}
.dropdown-submenu.pull-left{float:none;}.dropdown-submenu.pull-left>.dropdown-menu{left:-100%;margin-left:10px;-webkit-border-radius:6px 0 6px 6px;-moz-border-radius:6px 0 6px 6px;border-radius:6px 0 6px 6px;}

/* Main CSS */
#search_results {  }
.searchterm { padding:3px 5px; border:1px solid #888; display:inline-block; margin: 0px 5px 5px 0px; }
.searchterm:hover { background-color:yellow; cursor:pointer; }
.activesearch { background-color:yellow; }
.imagebox { display:inline-block; margin-right:5px; margin-bottom:5px; padding:10px; border:1px solid #888; text-align:center; width:157px; vertical-align:top; }
.infodiv { margin-bottom:5px; font-size:18px; }
pre { padding:0px 2px; display:inline; background-color:#eee; border:1px solid #ddd; }
</style>
</head>
<body<?=$additional_body;?>>
<?php
require_once("nav_".$header_file.".php");
if($show_hot_categories) { require_once("hot_categories.php"); }
?>