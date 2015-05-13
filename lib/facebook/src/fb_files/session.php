<?php

	
    
    //facebook application
    $fbconfig['appid' ]     = "271679969517705";
    $fbconfig['secret']     = "3ca33b62c497951e8df0460f71caf9ae";
    $fbconfig['baseurl']    = "https://www.xintsolutions.com/cardealerfbapp/"; 
	
    //
    if (isset($_GET['request_ids'])){
        //user comes from invitation
        //track them if you need
    }
	
    //include_once("clsGeneral.php");
    $user            =   null; //facebook user uid
    try{
        include_once "facebook.php";
    }
    catch(Exception $o){
        error_log($o);
    }
	
    // Create our Application instance.
    $facebook = new Facebook(array(
      'appId'  => $facebookAppOptions->application_id/*$fbconfig['appid']*/,
      'secret' => $facebookAppOptions->application_secret/*$fbconfig['secret']*/,
      'cookie' => true,
    ));

    //Facebook Authentication part
    $user       = $facebook->getUser();    
    
    $loginUrl   = $facebook->getLoginUrl(
            array(
                'scope'         => 'email,publish_stream,user_about_me,friends_about_me',
                'redirect_uri'  => /*"http://virtuousgiant.com/idtest/"*/$fbconfig['baseurl']/*$facebookAppOptions->callback_url*/
            )
    );
    
    $logoutUrl  = $facebook->getLogoutUrl();
   
   if (!$user) {
        echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
        exit;
   }

    if ($user) {
      try {
        // Proceed knowing you have a logged in user who's authenticated.
        $user_profile = $facebook->api('/me');
      } catch (FacebookApiException $e) {
        //you should use error_log($e); instead of printing the info on browser
       // d($e);  // d is a debug function defined at the end of this file
        $user = null;
      }
    }
   
    
    //if user is logged in and session is valid.
    if ($user){
        //get user basic description
        $userInfo           = $facebook->api("/$user");
                
    
    
    $userinfo = $facebook->api( array( 'method' => 'fql.query', 'query' => "SELECT uid,pic_big,name FROM user WHERE uid = '$user'" ) );
	
	echo $name    = $userinfo['0']['name'];
	$uid      = $userinfo['0']['uid'];
	$pic      = $userinfo['0']['pic_big'];
		
	/*$qry_user =  "SELECT fbuid FROM `fbusers` WHERE `fbuid` = '$uid'"; //check user if exists!
	$rs_user  = db_execute($qry_user);
	
	if(mysql_num_rows($rs_user)>0){
		
		
	}
	else{
	
	
	 $qry_saveuser = "INSERT INTO fbusers SET   fbuid    	= '$uid',
	 											profile_pic = '$pic',
										        fb_name     = '$name'";
	          db_execute($qry_saveuser);							
	}
	*/
    
    }
    
    
?>
