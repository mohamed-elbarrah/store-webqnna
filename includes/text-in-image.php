<?php
// Load the word2uni function
require_once QS_DIR . '/word2uni.php';

function save_gift($product_id, $from_name, $to_name, $image_filename){

    $thumbnail_id = get_post_meta( $product_id, '_thumbnail_id', true );
    $thumbnail_file = get_attached_file( $thumbnail_id );
    $full_size_file = str_replace( basename( $thumbnail_file ), basename( wp_get_attachment_url( $thumbnail_id ) ), $thumbnail_file );
    $thumbnail_dir = dirname( $full_size_file ) . '/' . basename( $full_size_file );

    $gift_tuning = get_post_meta($product_id, 'gift_tuning', true);

    $image = imagecreatefromjpeg($thumbnail_dir);
    $quality = 100;

    $text = $from_name;
    $params['color'] = $gift_tuning['name_1_color'] ?? '#000000';
    $params['size_ratio'] = $gift_tuning['name_1_font_size'] ?? 18;
    $params['right'] = $gift_tuning['name_1_right'] ?? 280;
    $params['top'] = $gift_tuning['name_1_top'] ?? 370;
    $params['line_space'] = 50;
    $params['font'] = $gift_tuning['name_1_font'] ?? 'DroidKufi-Regular.ttf';
    $params['quality'] = 100;
    writeTextToImage($text, $image, $params);

    $text = $to_name ?? 'خطأ';
    $params['color'] = $gift_tuning['name_2_color'] ?? '#000000';
    $params['size_ratio'] = $gift_tuning['name_2_font_size'] ?? 18;
    $params['right'] = $gift_tuning['name_2_right'] ?? 280;
    $params['top'] = $gift_tuning['name_2_top'] ?? 430;
    $params['line_space'] = 50;
    $params['font'] = $gift_tuning['name_2_font'] ?? 'DroidKufi-Regular.ttf';
    $params['quality'] = 100;

    writeTextToImage($text, $image, $params);
    
    if(!file_exists(ABSPATH . '/gifts/')){
        mkdir(ABSPATH . '/gifts/');
    }

    imagejpeg($image, ABSPATH . '/gifts/' . $image_filename, $quality);

    // Free up memory
    imagedestroy($image);
}

function preview_text_on_image(){
    
    $image_dir = $_GET['image_dir'];
    
    // Set the image path and load the image
    $image = imagecreatefromjpeg($image_dir);
    $quality = 50;

    $right = 29;

    $text = $_GET['name_1'] ?? 'خطأ';
    $params['color'] = $_GET['name_1_color'] ?? '#000000';
    $params['size_ratio'] = $_GET['name_1_font_size'] ?? 18;
    $params['right'] = $_GET['name_1_right'] ?? 280;
    $params['top'] = $_GET['name_1_top'] ?? 370;
    $params['line_space'] = 50;
    $params['font'] = $_GET['name_1_font'] ?? 'DroidKufi-Regular.ttf';
    $params['quality'] = 70;
    writeTextToImage($text, $image, $params);


    
    $text = $_GET['name_2'] ?? 'خطأ';
    $params['color'] = $_GET['name_2_color'] ?? '#000000';
    $params['size_ratio'] = $_GET['name_2_font_size'] ?? 18;
    $params['right'] = $_GET['name_2_right'] ?? 280;
    $params['top'] = $_GET['name_2_top'] ?? 430;
    $params['line_space'] = 50;
    $params['font'] = $_GET['name_2_font'] ?? 'DroidKufi-Regular.ttf';
    $params['quality'] = 70;

    writeTextToImage($text, $image, $params);


    

    header('Content-Type: image/jpeg');

    imagejpeg($image, null, $quality);
    
    // Free up memory
    imagedestroy($image);

    exit;
}

function writeTextToImage($text, &$image, $params = array()){
    

    // Set the font path and load the font
    $fontPath = QS_DIR . '/src/fonts/' . ($params['font'] ?? 'DroidKufi-Regular.ttf');
    $fontSizeRatio = ($params['size_ratio'] ?? 20) / 1000; // Font size as a ratio of image height or width
    $space = $params['line_space'] ?? 50; // Set line spacing
    $color = $params['color'] ?? '#000000';
    $position_top = $params['top'] ?? 100;
    $position_right = $params['right'] ?? 100;
    $quality = $params['quality'] ?? 70;

    $position_top /= 10;
    $position_right /= 10;
    
    list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
    $fontColor = imagecolorallocate($image, $r, $g, $b); // White color

    // Set the text to be written and convert it to Unicode
    $text = word2uni($text);

    // Calculate the font size based on the image size and font size ratio
    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);
    $fontSize = min($imageWidth, $imageHeight) * $fontSizeRatio;

    // Break up the text into multiple lines if it overflows
    $maxLineWidth = 0.9 * $imageWidth; // Maximum line width as a ratio of image width
    $lines = array('');
    $lineIndex = 0;
    $words = array_reverse(explode(' ', $text));
    foreach ($words as $word) {
        $line = $lines[$lineIndex];
        $testLine = $word . ' ' . $line;
        $textBoundingBox = imagettfbbox($fontSize, 0, $fontPath, $testLine);
        $textWidth = $textBoundingBox[2] - $textBoundingBox[0];
        if ($textWidth > $maxLineWidth) {
            $lineIndex++;
            $lines[] = $word;
        } else {
            $lines[$lineIndex] = $testLine;
        }
    }

    
    
    $textY = $imageHeight * $position_top / 100;
    
    foreach ($lines as $line) {
        // Get the bounding box of the text
        $textBoundingBox = imagettfbbox($fontSize, 0, $fontPath, $line);
        $textWidth = $textBoundingBox[2] - $textBoundingBox[0];
        $textHeight = $textBoundingBox[3] - $textBoundingBox[5];

        // $textX =  ($imageWidth - $textWidth) * (100 - $position_right) / 100;
        $textX =  ($imageWidth) * (100 - $position_right) / 100 - $textWidth;
        
        // Write the text on the image with the right-to-left direction and right alignment
        imagettftext($image, $fontSize, 0, $textX, $textY, $fontColor, $fontPath, $line);

        // Update the Y position for the next line
        $textY += $textHeight + $space;
    }
}


?>
