<?php


require_once QS_DIR . '/settings/class.settings-api.php';
require_once QS_DIR . '/settings/amli-settings.php';

new WeDevs_Settings();

function qs_get_settings($settigns, $name){
    if(!is_array($settigns))$settigns = get_option($settigns);
    return isset($settigns[$name])?$settigns[$name]:false;
}