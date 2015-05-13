<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
<script src="<?php echo plugins_url('/js/twitter.js', dirname(__FILE__)); ?>" type="text/javascript"></script>

<div>
    <div id="social-sharing-options-wrapper">
        <div class="social-sharing-options-message">
            <?php echo $tr_share_experience_friends?>
        </div>
        <form id="share-with-friends-form">
            <input id="product_id" name="product_id" type="hidden" value="<?php echo $productId; ?>"/>
            <input id="share-with-friends" name="share-with-friends" type="hidden" />
            <input id="twitter_oauth_token" name="twitter_oauth_token" type="hidden" />
            <input id="twitter_oauth_token_secret" name="twitter_oauth_token_secret" type="hidden" />
            <input id="twitter_screen_name" name="twitter_screen_name" type="hidden" />
            <input id="facebook_access_token" name="facebook_access_token" type="hidden" />
            <ul>
                <?php if($showFacebookSharingForm): ?>
                <li>
                    <div class="post-share-message" id="facebook-post-share-message"></div>
                    <div class="sharing-btn-overlay" id="sharing-facebook-btn-overlay"></div>
                    <textarea class="share-on-facebook-text" name="share-on-facebook-text" id="share_on_fb_text"><?php echo $facebookSharingText; ?></textarea>
     				<?php
						if($post_image != '')
						{
							$post_image = $post_image;
						}
						else
						{
							$post_image = '';
													}
						?>
					<img src="<?php echo plugins_url('/images/help/facebook.png', dirname(__FILE__)); ?>" style="margin: 10px 20px 0 0"><a class="main-btn share-btn" href="#" onclick="var text=document.getElementById('share_on_fb_text').value; var product_id=document.getElementById('product_id').value; <?php ?>makepublishfb(<?php echo $facebookOptions->application_id; ?>,product_id, text);" id="share-on-facebook"><?php echo $tr_Facebook?></a>
                </li>
                <?php endif; ?>
                <?php if($showTwitterSharingForm): ?>
                <li>
                    <div class="post-share-message" id="twitter-post-share-message"></div>
                    <div class="sharing-btn-overlay" id="sharing-twitter-btn-overlay"></div>
                    <textarea class="share-on-twitter-text" name="share-on-twitter-text" id="share_on_twitter_text"><?php echo $twitterSharingText; ?></textarea>
                    <img src="<?php echo plugins_url('/images/help/twitter.png', dirname(__FILE__)); ?>" style="margin: 10px 20px 0 0"><a class="main-btn share-btn" href="#" onclick="app.popupwindow('<?php echo get_option('home'); ?>/?connect_social=twitter',
        'Connect Through Twitter', 800, 400)" id="share-on-twitter"><?php echo $tr_Twitter?></a>
					<div style="display:none;" id="twitter_link"></div>
                </li>
                <?php endif; ?>
            </ul>
        </form>
    </div>
</div>
<div id="fb-root"></div>
<script type="text/javascript">
function makepublishfb(appId,product_id, text_to_share)
{
	
	FB.init({
	  
    	
		appId : appId,
    	status : true, // check login status
    	cookie : true, // enable cookies to allow the server to access the session
    	xfbml  : true  // parse XFBML
  	});
  	FB.ui(
  	{
  	method: 'feed',
		name: 'IgnitionDeck',
		link: '<?php echo $product_url ?>',
		picture: '<?php echo $product_image1_path ?>',
		caption: '',
		description: text_to_share 
	},
  	function(response) {
    if (response && response.post_id) {
		console.log("Post published successfully :)");
     	// jQuery.facebox("Your Post has been Published");
    } else {
		console.log("Post not published :(");
      	//jQuery.facebox("Your Post is not Published");
	  
    }
  }
);
}


</script>