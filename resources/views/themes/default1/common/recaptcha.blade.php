<?php

        $siteKey = $version = $applyfor = NULL;

        $recaptchaSettings = \App\Model\helpdesk\Settings\CommonSettings::where('option_name','google')->pluck('option_value','optional_field')->toArray();

        if ($recaptchaSettings && $recaptchaSettings['recaptcha_status']) {

            $siteKey = $recaptchaSettings['service_key'];

            $version = $recaptchaSettings['recaptcha_type'];

            $applyfor = $recaptchaSettings['recaptcha_apply_for'];
        }
    
?>



<script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer></script>
<script type="text/javascript">
    
        //this script can be used to store data for specific pages
         sessionStorage.setItem('siteKey', '{{$siteKey}}' );
         
         sessionStorage.setItem('version', '{{$version}}' );

         sessionStorage.setItem('applyfor', '{{$applyfor}}');

</script>