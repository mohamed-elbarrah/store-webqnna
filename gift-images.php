<?php

global $wp, $wp_query, $current_user;

$requests = explode('/', $wp->request);

$image_name = $requests[1] ?? '';
$image_number = pathinfo($image_name, PATHINFO_FILENAME);
$extension = pathinfo($image_name, PATHINFO_EXTENSION);

if(!file_exists(ABSPATH . '/gifts/' . $image_number . '.jpg')){
    echo 'no image';
    exit;
}
header('location: ' . home_url() . '/gifts/' . $image_number . '.jpg');