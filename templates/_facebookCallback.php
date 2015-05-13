<?php if(isset ($accessToken)): ?>
<script>
    var data = {
        access_token: '<?php echo $accessToken; ?>'
    }
    window.opener.setFacebookCredentials(data);
    window.close();
</script>
<?php endif;?>