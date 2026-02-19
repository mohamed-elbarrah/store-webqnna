<?php

get_header();

$product_id = get_the_ID();

$thumbnail_id = get_post_meta( $product_id, '_thumbnail_id', true );
$thumbnail_file = get_attached_file( $thumbnail_id );
$full_size_file = str_replace( basename( $thumbnail_file ), basename( wp_get_attachment_url( $thumbnail_id ) ), $thumbnail_file );
$thumbnail_dir = dirname( $full_size_file ) . '/' . basename( $full_size_file );



$tuning_params = array(
	"image_dir"			=> $thumbnail_dir,
	"name_1" 			=> "مثال لإسم الشخص الأول",
	"name_1_color" 		=> "#000000",
	"name_1_font_size"	=> "18",
	"name_1_right" 		=> "280",
	"name_1_top" 		=> "375",
	"name_1_font" 		=> "Amiri-Regular.ttf",

	"name_2" 			=> "مثال لإسم الشخص الثاني",
	"name_2_color" 		=> "#000000",
	"name_2_font_size" 	=> "18",
	"name_2_right" 		=> "280",
	"name_2_top" 		=> "430",
	"name_2_font"		=> "Amiri-Regular.ttf"
);
$gift_tuning = get_post_meta( $product_id, 'gift_tuning', true ) ?: array();
foreach($tuning_params as $key => $default_value){
	$gift_tuning[$key] = isset($gift_tuning[$key]) ? $gift_tuning[$key] : $default_value;
}

$font_dir = QS_DIR . '/src/fonts/';
$font_files = array_diff(scandir($font_dir), array('.', '..'));

$font_options = '';
foreach ($font_files as $font_file) {
	$font_options .= '<option value="' . $font_file  . '">' . $font_file . '</option>';
}

$tuning_page = file_get_contents(QS_DIR . '/html/tuning_gift.html');
$tuning_page = str_replace('[image-src]', get_the_post_thumbnail_url(), $tuning_page);
$tuning_page = str_replace('[image-dir]', $thumbnail_dir, $tuning_page);
$tuning_page = str_replace('[permalink]', get_the_permalink(), $tuning_page);
$tuning_page = str_replace('[product-id]', $product_id, $tuning_page);
$tuning_page = str_replace('[font-options]', $font_options, $tuning_page);
$tuning_page = str_replace('[home_url]', home_url(), $tuning_page);

foreach($tuning_params as $key => $default_value){
	$tuning_page = str_replace('['.$key.']', $gift_tuning[$key], $tuning_page);
}

echo $tuning_page;

get_footer();