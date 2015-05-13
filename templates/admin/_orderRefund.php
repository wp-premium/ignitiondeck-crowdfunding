<?php
/***********************************************************
Refund.php

This page demonstrates Refund Operation/API.
Called by index.html.       

***********************************************************/
// clearing the session before starting new API Call
//session_unset();    
?>
<div class="wrap">
	<form id="Form1" name="Form1" action="RefundReceipt.php" method="post">
		<div class="icon32" id="icon-edit"><br>
		</div>
		<h2><?php echo $tr_Paypal_Refund; ?></h2>
		<table width="100%">
			<tr>
				<td width="20%"><?php echo $tr_Pay_Key; ?> (<?php echo $tr_Transaction_ID; ?>):</td>
				<td width="76%"><input type="text" size="50" maxlength="32" name="payKey"
            value="" /></td>
			</tr>
			<tr>
				<td><?php echo $tr_Currency_Code; ?>:</td>
				<td><select name="currencyCode">
						<option value="USD" selected>USD</option>
						<option value="GBP">GBP</option>
						<option value="EUR">EUR</option>
						<option value="JPY">JPY</option>
						<option value="CAD">CAD</option>
						<option value="AUD">AUD</option>
					</select></td>
			</tr>
			<tr>
				<td><P align="right">&nbsp;</P></td>
				<td><input class="submitbtn" type="hidden" name="receiveremail" size="50" value="<?php echo $paypal_settings->paypal_email?>"></td>
				<td width="4%"><input type="hidden" name="amount" size="5" maxlength="7" value="<?php echo $product_data->product_price?>"></td>
			</tr>
			<tr>
				<td><input class="submitbtn" name="btnRefundSubmit" type="submit" id="btnRefundSubmit" value="<?php echo $tr_Refund; ?>" /></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</form>
</div>