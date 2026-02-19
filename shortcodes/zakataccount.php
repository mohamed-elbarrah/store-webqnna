<?php


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    add_shortcode( 'zakataccount', function ( $atts ) {
        $atts = shortcode_atts( array(
            'style' => 2
        ), $atts, 'zakataccount' );
        
        $style = $atts['style'];
        
        $page = file_get_contents(QS_DIR . '/html/zakat-account.html');
        return $page;
    });