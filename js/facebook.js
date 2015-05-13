window.fbAsyncInit = function() {
	console.log('Facebook Loaded - IDCF');
	// init the FB JS SDK
	FB.init({
	 	appId      : '238807482810727', // App ID from the App Dashboard
	  	xfbml      : true,
		version    : 'v2.0'
	});

	// Additional initialization code such as adding Event Listeners goes here
};

  (function(d, s, id){
   var js, fjs = d.getElementsByTagName(s)[0];
   if (d.getElementById(id)) {return;}
   js = d.createElement(s); js.id = id;
   js.src = "//connect.facebook.net/en_US/sdk.js";
   fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));