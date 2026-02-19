<?php


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    add_shortcode( 'projects', function ( $atts ) {
        $atts = shortcode_atts( array(
            'count'         => 8,
            'two_in_row'    => 0,
            'style'         => 2,
            'not_in'        => '',
            'orderby'       => '',
            'categories'    => array()
        ), $atts, 'projects' );

        $style      = $atts['style'] ?? 1;
        $count      = $atts['count'] ?? 3;
        $orderby    = $atts['orderby'] ?? false;
        $order      = $atts['order'] ?? false;
        $categories = $atts['categories'];
        $not_in     = $atts['not_in'];
        $two_in_row = $atts['two_in_row'];
        
        $categories = is_string($categories) ? array_map('trim', explode(',', $categories)) : false;
        $not_in     = is_string($not_in) ? array_map('trim', explode(',', $not_in)) : false;
        
        $args=array(
            'posts_per_page'    => $count,
            'orderby'           => 'post_date',
            'order'             => 'DESC',
            'post_type'         => 'product',
            'post_status'       => array('publish'),
            'caller_get_posts'  => 1,
            'meta_query' => array(
                  'relation' => 'AND',
                  array(
                      'key'     => 'is_project',
                      'value'   => '1',
                      'compare' => '=',
                  ),
              ),
          );
          
        if($categories && is_array($categories) && !empty($categories)){
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $categories,
                )
            );
        }

        if($not_in && is_array($not_in) && !empty($not_in)){
            $args['post__not_in'] = $not_in;
        }

        if($order)$args['order'] = $order;
        if($orderby)$args['orderby'] = $orderby;
        
        $projects = '';

        if($style == 1){
            $project_temp = file_get_contents(QS_DIR . '/html/project-card-1.html');
        }else{
            $project_temp = file_get_contents(QS_DIR . '/html/project-card-2.html');
        }

        $p = new WP_Query($args);
        if( $p->have_posts() ) {

            while ($p->have_posts()){

                $p->the_post();

                $post_id        = get_the_id();
                $permalink      = get_the_permalink();
                $title          = get_the_title();
                $thumbnail_url  = get_the_post_thumbnail_url();
                $type           = get_post_type();
                $share_value    = get_post_meta($post_id, 'share_value', true);
                $project_goal   = get_post_meta($post_id, 'project_goal', true);
                $project_style  = get_post_meta($post_id, 'project_style', true);
                $project_color  = get_post_meta($post_id, 'project_color', true);
                $net_revenue    = qs_get_product_net_revenue($post_id);
                
                $social_share_text = urlencode($title);
                $social_share_link = home_url() . '/?p=' . $post_id;

                if(!$share_value)$share_value = 20;
                if(!$project_goal)$project_goal = 1000;
                if(!$project_color)$project_color = '#fe813a';

                $precentage = round(100 * $net_revenue / $project_goal);


                $project = $project_temp;
                $project = str_replace('[delete]', '', $project);
                $project = str_replace('[title]', $title, $project);
                $project = str_replace('[social_share_text]', $social_share_text, $project);
                $project = str_replace('[social_share_link]', $social_share_link, $project);
                $project = str_replace('[thumbnail_url]', $thumbnail_url, $project);
                $project = str_replace('[category]', $category, $project);
                $project = str_replace('[link]', $permalink, $project);
                $project = str_replace('[net_revenue]', $net_revenue, $project);
                $project = str_replace('[product_id]', $post_id, $project);
                $project = str_replace('[project_goal]', $project_goal, $project);
                $project = str_replace('[project_color]', $project_color, $project);
                $project = str_replace('[precentage]', $precentage, $project);
                $project = str_replace('[share_value]', $share_value, $project);
                $projects .= $project;
            }

            if($style == 1){
                $projects = '<div class="projects-container p-container p-container-shortcode' . ($two_in_row?' two-in-a-row':'') . '">' . $projects . '</div>';
            }else{
                $projects = '<div class="project-container-2' . ($two_in_row?' two-in-a-row':'') . '">' . $projects . '</div>';
            }

            $projects = '<div class="view_all_projects"><a href="/?projects">عرض جميع المشاريع</a></div>' . $projects;
            
            ?>
            <?php
        }else{
            $projects = '<span style="width: calc(100% - 20px);
            display: block;
            font-size: 23px;
            line-height: 80px;
            text-align: center;
            padding: 50px 0 50px 0;
            color: #0364332b;
            box-sizing: border-box;
            margin: 70px 10px;
            border-radius: 14px;
            "><span style="
            font-size: 70px;
        ">:(</span>
        <br>
        القائمة فارغة
        </span>';
        }
        wp_reset_query();
        return $projects;
    });