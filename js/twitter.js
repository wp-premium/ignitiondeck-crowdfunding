function PublishTwitter(from_url, text) {
	/*document.getElementById('twitter_link').innerHTML = '<a href="https://twitter.com/share?original_referer='+from_url+'%2F&amp;source=tweetbutton&amp;text='+text+'&amp;url='+from_url+'" id="btn_twitter_share" tabindex="1" aria-describedby="btn-desc"><span>Tweet</span></a>';*/
	
	document.getElementById('twitter_link').innerHTML = '<a id="btn_twitter_share" class="intenter" href="https://twitter.com/intent/tweet?original_referer='+from_url+'%2F&amp;text='+text+'&amp;url='+from_url+'" title="Just click to try this intent."></a>';
	
	document.getElementById('btn_twitter_share').click();
}

window.onload = function() {
	twttr.events.bind('tweet', function(event) {
		alert("tweet!!!");
		//console.log(event);
		var followed_user_id = event.data.user_id;
    	var followed_screen_name = event.data.screen_name;
	});
};