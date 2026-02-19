<?php

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    add_action( 'admin_init', function() {
        $post_types = get_post_types( array('public' => true) );
        $post_types = 'product';
        add_meta_box( 'projects_meta_box', qs::_('Project Options'), 'display_projects_meta_box',$post_types, 'side', 'high' );
        // add_meta_box( 'gifts_meta_box', qs::_('Gifts Options'), 'display_gifts_meta_box',$post_types, 'side', 'high' );
    });
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function display_projects_meta_box( $post ) {
        $is_project = get_post_meta($post->ID, 'is_project', true);
        $share_value = get_post_meta($post->ID, 'share_value', true);
        $init_value = get_post_meta($post->ID, 'init_value', true);
        $project_goal = get_post_meta($post->ID, 'project_goal', true);
        $project_color = get_post_meta($post->ID, 'project_color', true);
        $project_style = get_post_meta($post->ID, 'project_style', true);
        $completed_donations = get_post_meta($post->ID, 'completed_donations', true);

        $is_project = $is_project?'checked':'';
        $completed_donations = $completed_donations?'checked':'';
        
        if(!$share_value)$share_value = 20;
        if(!$init_value)$init_value = 0;
        if(!$project_goal)$project_goal = 1000;

        $p = file_get_contents(QS_DIR . '/html/project_box_meta.html');
        $p = str_replace('[project_color]', $project_color, $p);

        echo '<div class="pcover"><div class="loader"></div></div><input type="hidden" name="product_i" value="1" >';
        echo "<table dir='".qs::_('ltr')."' style='width:100%;border: solid 1px green;border-radius:5px; padding:5px;'>";
        echo "<tr><td>".qs::_('Is it Project?')."</td><td style='text-align: end;padding: 0 10px;'> <input class='p_toggle' data-selector='.p_tr' type='checkbox' name ='is_project'  $is_project> </td></tr>";
        echo "<tr><td colspan='2'>";
        echo "  <table style='display: block;' class='p_tr'>";
        echo "      <tr><td>".qs::_('Goal')."</td><td> <input type='number' min='1' name ='project_goal' value='$project_goal'> </td></tr>";
        echo "      <tr><td>".qs::_('Share Value')."</td><td> <input type='number' min='1' name ='share_value' value='$share_value'> </td></tr>";
        echo "      <tr><td>".qs::_('Init Value')."</td><td> <input type='number' min='0' name ='init_value' value='$init_value'> </td></tr>";
        echo "      <tr><td>".qs::_('Style')."</td><td> <select name='project_style' style='width: 100%'><option value='1' " . ($project_style == 1?'selected':'') .">".qs::_('Style')." 1</option><option value='2' " . ($project_style == 2?'selected':'') .">".qs::_('Style')." 2</option></select> </td></tr>";
        echo "      <tr><td colspan='2'>$p</td></tr>";
        echo "  </table>";
        echo "<tr><td>".qs::_('Stop collecting donations when the project is completed?')."</td><td style='text-align: end;padding: 0 10px;'> <input type='checkbox' name ='completed_donations' $completed_donations> </td></tr>";
        echo "</td></tr>";
        echo "</table>";
       
        echo '<input type="hidden" value="'.$project_color.'" name="project_color" class="project_color"/>';



        //////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        /**
         * For gifts
         */

        $is_gift = get_post_meta($post->ID, 'is_gift', true);
        if($is_gift){
            $is_gift = 'checked';
        }else{
            $is_gift = '';
        }

        echo '<br><input type="hidden" name="gift_i" value="1" >';
        echo "<table dir='".qs::_('ltr')."' style='width:100%;border: solid 1px green;border-radius:5px; padding:5px;'>";
        echo "<tr><td>".qs::_('Is it Gift?')."</td><td style='text-align: end;padding: 0 10px;'> <input  type='checkbox' name ='is_gift'  $is_gift> </td></tr>";
        echo "</table>";
        
        //////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        /**
         * For Custom Donate
         */

        $has_custom_donate = get_post_meta($post->ID, 'has_custom_donate', true);
        $custom_donate = get_post_meta($post->ID, 'custom_donate', true);
        $init_value = get_post_meta($post->ID, 'init_value', true);
        $project_goal = get_post_meta($post->ID, 'project_goal', true);
        
        if($has_custom_donate){
            $has_custom_donate = 'checked';
            // $container_style = '';
        }else{
            $has_custom_donate = '';
            // $container_style = 'display: none;';
        }
        if(!$custom_donate)$custom_donate = json_encode(array());
        $container_style = '';
        echo "<script>var custom_donate = JSON.parse('$custom_donate');</script>";
        echo '<br><input type="hidden" name="has_custom_donate_i" value="1" >';
        echo '<input type="hidden" class="custom_donate_input" name="custom_donate" value="" >';
        echo "<table dir='".qs::_('ltr')."' style='width:100%;border: solid 1px green;border-radius:5px; padding:5px;'>";
        echo "<tr><td>".qs::_('Custom Donate?')."</td><td style='text-align: end;padding: 0 10px;'> <input  type='checkbox' class='has_custom_donate p_toggle'  data-selector='.custom_donate_container' name ='has_custom_donate'  $has_custom_donate> </td></tr>";
        echo "<tr><td colspan='2'><div class='custom_donate_container' style='$container_style'>
            <br><input type='text' placeholder='".qs::_('Init Value')."' name='init_value' value='$init_value'>
            <br><input style='margin-top: 10px' type='text' placeholder='".qs::_('Goal')."' name='project_goal' value='$project_goal'>
            <div class='custom_donate_items'></div><div class='custom_donate_addbtn'><a href='javascript:void(0)'>".qs::_('Add New')."</a></div></div></td></tr>";
        echo "</table>";

        $s = file_get_contents(QS_DIR . '/html/custom_donate_js.html');
        $s = str_replace('[name]', qs::_('Name'), $s);
        $s = str_replace('[price]', qs::_('Price'), $s);
        $s = str_replace('[save]', qs::_('Save'), $s);
        $s = str_replace('[cancel]', qs::_('Cancel'), $s);
        $s = str_replace('[delete]', qs::_('Delete'), $s);
        echo $s;
    }
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    add_action( 'save_post', function( $post_id, $Array ) {
        if(isset($_POST['product_i'])){
            $is_project = ($_POST['is_project'])?1:'';
            update_post_meta( $post_id, 'is_project',$is_project );
            update_post_meta( $post_id, 'project_goal',$_POST['project_goal'] );
            update_post_meta( $post_id, 'share_value',$_POST['share_value'] );
            update_post_meta( $post_id, 'init_value',$_POST['init_value'] );
            update_post_meta( $post_id, 'project_style',$_POST['project_style'] );
            update_post_meta( $post_id, 'project_color',$_POST['project_color'] );
            update_post_meta( $post_id, 'completed_donations',$_POST['completed_donations'] );
        }
        if(isset($_POST['gift_i'])){
            $is_gift = ($_POST['is_gift'])?1:'';
            update_post_meta( $post_id, 'is_gift',$is_gift );
        }
        if(isset($_POST['has_custom_donate_i'])){
            $has_custom_donate = ($_POST['has_custom_donate'])?1:'';
            update_post_meta( $post_id, 'has_custom_donate',$has_custom_donate );
            update_post_meta( $post_id, 'custom_donate',$_POST['custom_donate'] );
        }
    }, 10, 2 );