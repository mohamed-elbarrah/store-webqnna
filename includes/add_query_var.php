<?php
    
    add_filter('query_vars', 'add_state_var', 0, 1);
    function add_state_var($vars){
        global $projects_slug;
        $vars[] = $projects_slug;
        return $vars;
    }
    
    function srv_userpage_rewrite_rule() {
        global $projects_slug;
        // $projects_slug = 'state';
        add_rewrite_tag( '%' . $projects_slug . '%', '([^&]+)' );
        add_rewrite_rule(
            '^' . $projects_slug . '/([^/]*)/?',
            'index.php?' . $projects_slug . '=$matches[1]',
            'top'
        );
        add_rewrite_rule(
            '^' . $projects_slug . '/?',
            'index.php?' . $projects_slug . '=$matches[1]',
            'top'
        );
    }
    add_action('init','srv_userpage_rewrite_rule');

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function srv_userpage_rewrite_catch() {
        global $wp,$wp_query,$current_user, $projects_slug;
        
        if ( array_key_exists( $projects_slug, $wp_query->query_vars ) ) {
            $wp_query->is_home = false;
            include (QS_DIR . '/projects_template.php');
            exit;
        }elseif($wp->request == $projects_slug){
            $wp_query->is_home = false;
            include (QS_DIR . '/projects_template.php');
            exit;
        }
        
    }
    add_action( 'template_redirect', 'srv_userpage_rewrite_catch' );

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////