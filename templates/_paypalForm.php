<form name="payform" action="<?php echo $url; ?>" method="post">
    <input name="bn" value="IgnitionDeck_SP" type="hidden">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="<?php echo esc_attr( $paypal_email ); ?>">
    <input type="hidden" name="item_name" value="<?php echo stripslashes(esc_html( get_the_title($post_id) )); ?>">
    <input type="hidden" name="item_number" value="<?php echo absint($_POST['project_id']); ?>">
    <input type="hidden" name="item_id" value="<?php echo esc_attr( absint( mysql_insert_id() ) ); ?>">
    <input type="hidden" name="amount" value="<?php echo esc_attr( $_POST['price'] ); ?>">
    
    <!--<input type="hidden" name="tax" value="">-->
    <input type="hidden" name="quantity" value="<?php echo esc_attr( $_POST['quantity'] ); ?>">
    <!--<input type="hidden" name="no_note" value="'.$_POST['notes'].'">-->
    <input type="hidden" name="no_shipping" value="1">
    <input type="hidden" name="currency_code" value="<?php echo esc_attr( $prod_settings->currency_code ); ?>">
    <!-- Enable override of payer’s stored PayPal address. -->
    <input type="hidden" name="address_override" value="<?php echo $paymentSettings->paypal_override == 1 ? '1' : '0'; ?>">
    <!-- Set prepopulation variables to override stored address. -->
    <input type="hidden" name="first_name" value="<?php echo esc_attr( $_POST['first_name'] ); ?>">
    <input type="hidden" name="last_name" value="<?php echo esc_attr( $_POST['last_name'] ); ?>">
    <input type="hidden" name="address1" value="<?php echo esc_attr( $_POST['address'] ); ?>">
    <input type="hidden" name="city" value="<?php echo esc_attr( $_POST['city'] ); ?>">
    <input type="hidden" name="state" value="<?php echo esc_attr( $_POST['state'] ); ?>">
    <input type="hidden" name="zip" value="<?php echo esc_attr( $_POST['zip'] ); ?>">
    <input type="hidden" name="country" value="<?php echo esc_attr( $_POST['country'] ); ?>">
    <!--<input type="hidden" name="return" value="<?php echo get_option('home')?>/">-->
    <input type="hidden" name="return" value="<?php echo esc_url( trailingslashit( home_url() ) ); ?>?payment_success=1&amp;product_id=<?php echo absint($_POST['project_id']); ?>">
    <input type="hidden" name="rm" value="2">
    <input type="hidden" name="notify_url" value="<?php echo esc_url( trailingslashit( home_url() ) ).'?ipn_handler=1&fname='.
    (isset($_POST['first_name']) ? urlencode(esc_attr($_POST['first_name'])) : '').
    '&lname='.(isset($_POST['last_name']) ? urlencode(esc_attr($_POST['last_name'])) : '').
    '&email='.(isset($_POST['email']) ? esc_attr($_POST['email']) : '').'&address='.
    (isset($_POST['address']) ? urlencode(esc_attr($_POST['address'])) : '').'&country='.
    (isset($_POST['country']) ? urlencode(esc_attr($_POST['country'])) : '').'&state='.
    (isset($_POST['state']) ? urlencode(esc_attr($_POST['state'])) : '').'&city='.
    (isset($_POST['city']) ? urlencode(esc_attr($_POST['city'])) : '').'&zip='.
    (isset($_POST['zip']) ? urlencode(esc_attr($_POST['zip'])) : '').
    '&product_id='.absint(esc_attr($_POST['project_id'])).'&level='.
    absint(esc_attr($_POST['level'])).'&prod_price='.str_replace(',', '', esc_attr($_POST['price'])); ?>">
    <!--<input type="hidden" name="ipn_test" value="1" />-->
</form>
<script>document.payform.submit();</script>
<?php
    // setup query arguments
    $query_args = array( 
        'payment_success'   => '1', 
        'fname'             => esc_attr($_POST['first_name']), 
        'lname'             => esc_attr($_POST['last_name']), 
        'email'             => esc_attr($_POST['email']), 
        'address'           => esc_attr($_POST['address']), 
        'country'           => esc_attr($_POST['country']), 
        'state'             => esc_attr($_POST['state']), 
        'country'           => esc_attr($_POST['country']), 
        'city'              => esc_attr($_POST['city']), 
        'zip'               => esc_attr($_POST['zip']), 
        'product_id'        => absint(esc_attr($_POST['project_id'])), 
        'level_select'      => absint(esc_attr($_POST['level_select'])), 
        'prod_price'        => absint(esc_attr($_POST['price']))
     );

// build query from the array
$query_string = esc_url( trailingslashit( home_url() ) ) . http_build_query( $query_args );
?>
<input type="hidden" value="<?php echo esc_attr( $query_string ); ?>" />
<?php exit; ?>