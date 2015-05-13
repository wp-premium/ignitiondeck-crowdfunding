<?php
//error_reporting(0);


include_once "session.php";

if (isset($_GET['error_reason']) && $_GET['error_reason'] == "user_denied"){
   //header("Location: http://www.facebook.com" );
   exit;
  }
 
if (isset($_GET['code'])){
  // header("Location: http://www.facebook.com/roohwarefanpages?sk=app_111476838958639");
   exit;
 }

if (isset($_REQUEST['req'])){
	$page = $_REQUEST['req'].'.php';
	include_once($page);		
}

else{
	//include_once("admin_custom_design.php");
}
?>
Done