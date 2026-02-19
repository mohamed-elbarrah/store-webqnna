<?php
add_shortcode('gifts', function($atts) {
    $atts = shortcode_atts(array(
        'count' => 6,
        'two_in_row' => 0,
        'categories' => array(),
        'not_in' => ''
    ), $atts, 'gifts');
    
    $args = array(
        'posts_per_page' => $atts['count'],
        'orderby' => 'post_date',
        'order' => 'DESC',
        'post_type' => 'product',
        'post_status' => 'publish',
        'post__not_in' => !empty($atts['not_in']) ? array_map('intval', explode(',', $atts['not_in'])) : array(),
        'meta_query' => array(
            array(
                'key' => 'is_gift',
                'value' => '1',
                'compare' => '='
            )
        )
    );

    if (!empty($atts['categories'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => is_array($atts['categories']) ? array_map('intval', $atts['categories']) : array_map('intval', explode(',', $atts['categories']))
            )
        );
    }

    ob_start();
    
    // تضمين ملف HTML إذا كان موجودًا
    if (defined('QS_DIR') && file_exists(QS_DIR . '/html/gifts.html')) {
        require_once QS_DIR . '/html/gifts.html';
    }
    
    $q = new WP_Query($args);
    if ($q->have_posts()) {
        ?>
        <div class="gifts-container g-container<?= ($atts['two_in_row']) ? ' two-in-a-row' : '' ?>">
        <?php
        while ($q->have_posts()) {
            $q->the_post();
            $post_id = get_the_ID();
            $product = wc_get_product($post_id);
            
            // جلب البيانات الأساسية
            $title = get_the_title();
            $permalink = get_permalink();
            $thumbnail_url = get_the_post_thumbnail_url();
            $share_value = (int)$product->get_price();
            if (!$share_value) $share_value = 20;
            
            // جلب خيارات الهدية المخصصة
            $has_custom_donate = get_post_meta($post_id, 'has_custom_donate', true);
            $custom_donate = json_decode(get_post_meta($post_id, 'custom_donate', true), true);
            $disable_input = $has_custom_donate ? 'disable-input' : '';
            
            // إنشاء أزرار الخيارات
            $btns_share = '';
            if ($has_custom_donate && is_array($custom_donate)) {
                $btns_share = '<div class="btns-share">';
                foreach ($custom_donate as $element) {
                    $btns_share .= '<div class="btn_share" data-share="' . esc_attr($element[1]) . '">' . esc_html($element[0]) . '</div>';
                }
                $btns_share .= '</div>';
            }
            
            // مشاركة اجتماعية
            $social_share_text = urlencode($title);
            $social_share_link = home_url() . '/?p=' . $post_id;
            ?>
            
            <div class="post-card" data-id="<?= esc_attr($post_id) ?>">
                <div class="g-card-image">
                    <div class="projectCardShare">
                        <span style="background: #0c6938;"><i class="fas fa-share-alt"></i></span>
                        <a href="https://twitter.com/intent/tweet?text=<?= $social_share_text ?>&url=<?= $social_share_link ?>" class="tws" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.facebook.com/sharer.php?t=<?= $social_share_text ?>&u=<?= $social_share_link ?>" class="fbs" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/?url=<?= $social_share_link ?>" class="gps" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="whatsapp://send?text=<?= $social_share_text ?> <?= $social_share_link ?>" class="was" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    </div>
                    <a href="<?= esc_url($permalink) ?>">
                        <img src="<?= esc_url($thumbnail_url) ?>" alt="<?= esc_attr($title) ?>">
                       
                    </a>
                </div>
                
                <div class="card-content">
                    <a href="<?= esc_url($permalink) ?>">
                        <div class="card-title"><h2><?= esc_html($title) ?></h2></div>
                    </a>
                    
                    <?php if ($btns_share) echo $btns_share; ?>
                    
                    <div class="input_spinner" data-share="<?= esc_attr($share_value) ?>">
                        <button class="spinner-btn btn btn-primary btn-sm" data-dir="down">-</button>
                        <input type="numeric"  name="shares" maxlength="3" value="1" placeholder="عدد الأسهم" min="1" max="200">
                        <button class="spinner-btn btn btn-primary btn-sm" data-dir="up">+</button>
                    </div>

                    
                    <div class="d-card-footer">
                        <div class="d-footer-btns">

                            <div class="amount">
                                <!-- <span>مبلغ الإهداء</span> -->
                                
                                <input type="numeric"  name="amount" maxlength="6" placeholder="0" 
                                    min="1" max="250000" value="<?= esc_attr($share_value) ?>"
                                    class="<?= $disable_input ?>"
                                    <?= $has_custom_donate ? 'readonly' : '' ?>>
                                <!-- <i>ر.س.</i> -->
                            </div>

                            
                            <div class="donate-btn">
                                <a href="<?= esc_url($permalink) ?>">إهداء الآن</a>
                            </div>
                            
                        </div>
                    </div>

                </div>
            </div>
            <?php
        }
        ?>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // اختيار خيار الهدية
            $(document).on('click', '.btn_share', function() {
                var shareValue = $(this).data('share');
                var $card = $(this).closest('.post-card');
                
                $card.find('.btn_share').removeClass('active');
                $(this).addClass('active');
                
                if (shareValue == 0) {
                    $card.find('.amount input')
                        .val('')
                        .addClass('enable-input')
                        .prop('readonly', false)
                        .focus();
                } else {
                    $card.find('.amount input')
                        .val(shareValue)
                        .removeClass('enable-input')
                        .prop('readonly', true);
                }
            });

            // زيادة/تقليل الكمية
            $(document).on('click', '.spinner-btn', function() {
                var $input = $(this).siblings('input');
                var currentVal = parseInt($input.val()) || 0;
                var dir = $(this).data('dir');
                
                if (dir === 'up') {
                    $input.val(currentVal + 1);
                } else if (dir === 'down' && currentVal > 1) {
                    $input.val(currentVal - 1);
                }
            });
        });


        // الغاء تحميل الصفحة
        jQuery(document).ready(function($) {
            $('.donate-btn a').on('click', function(e) {
                e.preventDefault(); // يمنع إعادة تحميل الصفحة أو الانتقال للرابط
            });
        });


        </script>

        <style>

            .gifts-container {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                padding: 10px;
            }

           .gifts-container .post-card {
                padding: 0; 
                position: relative; 
                overflow: hidden; 
                width: calc(33.3333% - 20px); 
                margin: 5px 10px 30px 10px; 
                background-color: #fff; 
                border: solid 1px #4c6c5b8a;
                border-radius: 8px;
                box-shadow: 0px 3px 5px 0px rgb(0 0 0 / 20%); 
                transition: all 0.4s ease; 
                position: relative; 
                padding-bottom: 65px; 
            }
            
            .gifts-container .post-card:hover {
                transform: translateY(-5px);
                box-shadow: 0px 5px 10px 0px rgb(0 0 0 / 30%);
            }

            .gifts-container .post-card .projectCardShare {
                position: absolute;
                right: 0;
                top: 0;
                background: rgba(0, 0, 0, 0.2);
                color: #fff;
                border-radius: 0 0 0 10px;
                overflow: hidden;
            }

            .gifts-container .btns-share{
                margin: 15px 0;
            }

            .gifts-container .card-title {
                text-align: center;
                padding: 5px 2px;
                margin-top: -10px;
                background-color: #15582e;
                color: white;
            }

            .gifts-container .card-title h2 {
                color: white;
                font-weight: 900;
                padding: 5px;
            }

            .gifts-container .d-card-footer {
                display: flex;
                padding: 0px;
                background: #f3f3f3;
                height: 65px;
                position: absolute;
                left: 0;
                bottom: 0;
                width: 100%;
            }

            .gifts-container .d-footer-btns {
                padding: 5px
            }

            .gifts-container .donate-btn a {
                background-color: rgb(21, 88, 46);
                border-color: rgb(57, 80, 44);
                position: relative;
                color: white !important;
                border-radius: 3px;
                padding: 0;
                transition: all 0.4s ease;
            }

            .gifts-container .donate-btn a:hover {
                background-color: #c59821;
                border-color: rgb(21, 88, 46);

            }

            .gifts-container .amount {
            
            }

            .gifts-container .amount input{
               
            }


        </style>
        <?php
    }
    
    wp_reset_postdata();
    return ob_get_clean();
});