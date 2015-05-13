<?php
session_start();
//echo // get_bloginfo('url');

?>

<div style="display:none;">
	<div id="id-ask-question-form" class="pay-form-wrapper">
		<form id="formAskQuestion" name="formAskQuestion" method="post" action="">
			<table border="0" width="100%">
				<tr>
					<td colspan="2"><b><?php echo $GLOBALS['tr_Contact_Form']; ?></b></td>
				</tr>
				<tr>
					<td class="label"><?php echo $GLOBALS['tr_Name']; ?></td>
					<td class="field"><input id="ask_sender_fullname" name="ask_sender_fullname" value="" size="30" class="required" type="text"></td>
				</tr>
				<tr>
					<td class="label"><?php echo $GLOBALS['tr_Email']; ?></td>
					<td class="field"><input id="ask_sender_email" name="ask_sender_email" value="" size="30" type="text"></td>
				</tr>
				<tr>
					<td class="label"><?php echo $GLOBALS['tr_Subject']; ?></td>
					<td class="field"><input id="ask_sender_subject" name="ask_sender_subject" value="" size="30" class="required" type="text"></td>
				</tr>
				<tr>
					<td class="label"><?php echo $GLOBALS['tr_Message']; ?></td>
					<td class="field"><textarea id="ask_sender_comments" name="ask_sender_comments" cols="60" rows="10" class="required "></textarea></td>
				</tr>
				<tr>
					<td><?php echo $tr_Captcha_Type; ?></td>
					<td><input type="text" name="secure" value="what's the result?" onclick="this.value=''" /></td>
				</tr>
				<tr>
					<td class="field" colspan="2"><input value="<?php echo $GLOBALS['tr_Send_email']; ?>" type="submit" name="btnSubmitQuestion">
						<br>
						<input value="<?php echo $GLOBALS['tr_Clear_ignitiondeck']; ?>" type="reset"></td>
				</tr>
			</table>
		</form>
	</div>
</div>
<script type="text/javascript">
function reloadCaptcha()
{
	document.getElementById('captcha').src = document.getElementById('captcha').src+ '?' +new Date();
}
</script>