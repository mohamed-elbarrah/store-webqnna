<?php

get_header();

if ( function_exists('yoast_breadcrumb') ) { ?>
	<div style="padding: 20px 10px; margin: 40px 0 -40px 0;">
		<div class="page-header" style="max-width: 1100px;margin: auto;line-height: 20px;height: 20px;">
			<?php yoast_breadcrumb('<p id="breadcrumbs">','</p>'); ?>
	
		</div>
	</div>
<?php }

$product_id = get_the_ID();

$content = get_the_content($product_id);
$content = apply_filters('the_content', $content);
$content = str_replace(']]>', ']]>', $content);

$excerpt = '';

if(has_excerpt($product_id)){
    $excerpt = get_the_excerpt($product_id);
}

$is_project = get_post_meta($product_id, 'is_project', true);
$is_gift    = get_post_meta($product_id, 'is_gift', true);

$project_goal           = get_post_meta($product_id, 'project_goal', true);
$has_custom_donate      = get_post_meta($product_id, 'has_custom_donate', true);
$custom_donate          = get_post_meta($product_id, 'custom_donate', true);
$project_color          = get_post_meta($product_id, 'project_color', true);
$project_style          = get_post_meta($product_id, 'project_style', true);
$completed_donations    = get_post_meta($product_id, 'completed_donations', true);

$product        = wc_get_product( $product_id );
$share_value    = (int)$product->get_price();

$spinner_style  = '';
$btns_share     = '';
$disable_input  = '';

if($has_custom_donate){
    $disable_input = 'disable-input';
    $custom_donate = json_decode($custom_donate, true);
    $btns_share = '<div class="btns-share">';
    if(is_array($custom_donate))
    foreach($custom_donate as $element){
        $btns_share .= '<div class="btn_share" data-share="' . $element[1] . '">' . $element[0] . '</div>';
    }
    $btns_share .= '</div>';
}

$related = do_shortcode('[projects orderby="rand" not_in="'.$product_id.'" two_in_row=1]');

$s = file_get_contents(QS_DIR . '/html/single-project-product-template.html');
$s = str_replace('[id]', $product_id, $s);
$s = str_replace('[title]', get_the_title(), $s);
$s = str_replace('[thumbnail-url]', get_the_post_thumbnail_url(), $s);
$s = str_replace('[btns-share]', $btns_share, $s);
$s = str_replace('[delete]', '', $s);
$s = str_replace('[share]', $share_value, $s);
$s = str_replace('[spinner-style]', $spinner_style, $s);
$s = str_replace('[disable-input]', $disable_input, $s);
$s = str_replace('[content]', $content, $s);
$s = str_replace('[excerpt]', $excerpt, $s);
// $s = str_replace('[reviews]', $reviews, $s);
$s = str_replace('[related]', $related, $s);
$s = str_replace('[share-url]', home_url() . '/?p=' . get_the_ID(), $s);
$s = str_replace('[share-title]', get_the_title(), $s);
echo $s;

require_once(QS_DIR . '/reviews.php');

get_footer();