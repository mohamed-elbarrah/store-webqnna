<?php

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    add_shortcode( 'donates', function ( $atts ) {
        $atts = shortcode_atts( array(
            'count'         => 6,
            'two_in_row'    => 0,
            'categories'    => array()
        ), $atts, 'gifts' );

        $count      = $atts['count'];
        $categories = $atts['categories'];
        $two_in_row = $atts['two_in_row'];
        
        $categories = is_string($categories)?array_map('trim', explode(',', $categories)):false;


        $args = array(
            'posts_per_page'    => $count,
            'orderby'           => 'post_date',
            'order'             => 'DESC',
            'paged'             => $paged ?? 1,
            'post_type'         => 'product',
            'post_status'       => array('publish'),
            'caller_get_posts'  => 1,
            'meta_query' => array(
                  'relation' => 'AND',
                  array(
                      'key'     => 'is_gift',
                      'value'   => '1',
                      'compare' => '!=',
                  ),
                  array(
                      'key'     => 'is_project',
                      'value'   => '1',
                      'compare' => '!=',
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


        
        ob_start();

        $p = new WP_Query($args);
        if( $p->have_posts() ) {
            ?>
            <style>
                .sqd-progress-container {
                    margin: 20px 10px;
                }

                .sqd-progress-labels{
                    display: flex;
                    justify-content: space-between;
                    padding: 0 10px;
                }

                .sqd-progress {
                    width: 100%;
                    height: 17px;
                    border-radius: 10px;
                    border: 1px solid rgb(13 76 31 / 28%);
                    overflow: hidden;
                    position: relative;
                    box-shadow: inset #808080cf 0px 0px 17px -12px;
                    box-sizing: border-box;
                }
                
                .sqd-progress span {
                    font-size: 12px;
                    height: 100%;
                    display: block;
                    width: 0;
                    min-width: 30px;
                    padding-inline-end: 12px;
                    padding-inline-start: 5px;
                    color: rgb(255, 251, 251);
                    line-height: 14px;
                    position: absolute;
                    text-align: end;
                    box-sizing: border-box;
                    background-image: -webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);
                    background-size: 75px 75px;
                    box-shadow: 0 0 5px #00000033;
                    border-radius: 10px 0 0 10px;
                }

                .progress-green span {
                    background-color: #2a8165;
                }
                /* Donate option buttons: make full rounded rectangular layout (two columns)
                   Keep colors/background/hover behaviors unchanged; only adjust spacing, size and radius */
                .btns-share{
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 0px;
                    margin-top: 14px;
                    align-items: center;
                    padding: 6px 0;
                }

                .btns-share .btn_share{
                    box-sizing: border-box;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 28px;
                    font-size: 12px;
                    font-weight: 700;
                    text-align: center;
                    cursor: pointer;
                    white-space: normal;
                }
                    /* If a label is long, make it occupy the full row (span both columns) */
                    .btns-share .btn_share--full{ grid-column: 1 / -1; }

                /* Redesigned footer: amount box, quantity selector and action buttons */
                .donates-container .qscard .d-card-footer .d-footer-btns{ display: flex !important; flex-direction:column;}
                .donates-container .qscard .d-footer-btns .d-footer-row.top{ display: flex !important; gap:8px; align-items:center;  }

                .donation-box{ flex:1; background:#F5F5F5; border-radius:16px; padding:14px 16px; display:flex; flex-direction:column; justify-content:center; text-align:right; min-height:72px; border:1.5px dashed #e6eef3; }
                .donation-label{ font-size:13px; color:#9aa6ab; margin-bottom:6px; }
                .amount{ position:relative; display:flex; align-items:center; justify-content:flex-end; }
                .amount .amount-input{ font-size:24px; font-weight:700; border:0; background:transparent; text-align:right; padding:0; width:100%; color:#0f1720; }
                .amount .currency{ position:absolute; right:14px; color:#0f1720; font-weight:700; font-size:16px; }

                .qty-box{  background:#ffffff; border-radius:16px; display:flex; align-items:center; justify-content:center; gap:8px; padding:4px; box-shadow: 0 1px 4px rgba(0,0,0,0.03);  border:1px solid #f3f3f3; }
                .qty-box .spinner-btn{ background:transparent; border:0; font-size:22px; font-weight:700; padding:4px 8px; cursor:pointer; }
                .qty-box .spinner-btn[data-dir="up"]{ color:#1E6B3A; }
                .qty-box .spinner-btn[data-dir="down"]{ color:#222; }
                .qty-box .qty-input{ width:44px; text-align:center; border:0; background:transparent; font-weight:700; font-size:18px; }

                .donates-container .qscard .d-footer-row.buttons{ width: 100%; display:flex !important; gap:8px; }
                .donates-container .qscard .d-footer-row.buttons .donate-btn, .donates-container .qscard .d-footer-row.buttons .cart-donate-btn{ flex:1; }
                .donates-container .qscard .d-footer-row.buttons .donate-btn a, .donates-container .qscard .d-footer-row.buttons .cart-donate-btn a{ display:block; text-decoration:none; color:#fff; border-radius:16px; text-align:center; font-size:14px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
                .donates-container .qscard .d-footer-row.buttons .donate-btn a{ background:#1E6B3A; }
                .donates-container .qscard .d-footer-row.buttons .cart-donate-btn a{ background:#C15A00; }
                .donate-btn .btn-arrow{ margin-left:8px; }

                /* enforce order so green donate appears on the left visually */
                .donates-container .qscard .d-footer-row.buttons .donate-btn{ order:1 }
                .donates-container .qscard .d-footer-row.buttons .cart-donate-btn{ order:2 }

                /* ensure Arabic text inside buttons remains RTL */
                .donates-container .qscard .d-footer-row.buttons a{ direction: rtl; }

                @media (max-width:600px){
                    .donates-container .qscard .d-footer-row.top{ flex-direction:column-reverse; gap:12px; }
                    .qty-box{ width:100%; }
                    .d-footer-row.buttons{ flex-direction:column; }
                    .d-footer-row.buttons .donate-btn a, .d-footer-row.buttons .cart-donate-btn a{ height:50px; line-height:50px; }
                }
            </style>
                <script>
                    document.addEventListener('DOMContentLoaded', function(){
                        try{
                            document.querySelectorAll('.btns-share .btn_share').forEach(function(el){
                                var text = (el.textContent || '').trim();
                                if(text.length > 20) el.classList.add('btn_share--full');
                            });
                        }catch(e){/* fail silently */}
                    });
                </script>
            <div class="donates-container<?=($two_in_row)?' two-in-a-row':''?>">
            <?php
            while ($p->have_posts()){
                $p->the_post();
                $post_id = get_the_id();
                $permalink = get_the_permalink();
                $title = get_the_title();
                $thumbnail_url = get_the_post_thumbnail_url();
                $type = get_post_type();
                $excerpt = get_the_excerpt();
                $product = wc_get_product( $post_id );
                $share_value = $product->get_price();
                if(!$share_value)$share_value = 20;
                
                $social_share_text = urlencode($title);
                $social_share_link = home_url() . '/?p=' . $post_id;
                
                $project_goal = get_post_meta($post_id, 'project_goal', true);
                $has_custom_donate = get_post_meta($post_id, 'has_custom_donate', true);
                $custom_donate = get_post_meta($post_id, 'custom_donate', true);
                if(!$custom_donate)$custom_donate = json_encode(array());
                $custom_donate = json_decode($custom_donate, true);




                if($project_goal){
                    $received_donations = qs_get_product_net_revenue($post_id);
                    if($received_donations > $project_goal){
                        $percentage = 100;
                    }else{
                        if(!$project_goal) $project_goal = 1;
                        $percentage = round(($received_donations / $project_goal) * 100);
                    }
                }


                ?>

                <div class="qscard d-card" data-id="<?=$post_id?>">
                        <div class="d-card-image">
                            <div class="projectCardShare">
                                <span style="background: #0c6938;"><i class="fas fa-share-alt"></i></span>
                                <a href="https://twitter.com/intent/tweet?text=<?=$social_share_text?>&url=<?=$social_share_link?>" class="tws" target="_blank"><i class="fab fa-twitter"></i></a>
                                <a href="https://www.facebook.com/sharer.php?t=<?=$social_share_text?>&u=<?=$social_share_link?>" class="fbs" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                <a href="https://www.instagram.com/?url=<?=$social_share_link?>" class="gps" target="_blank"><i class="fab fa-instagram"></i></a>
                                <a href="whatsapp://send?text=<?=$social_share_text?> <?=$social_share_link?>" class="was" target="_blank"><i class="fab fa-whatsapp"></i></a>
                            </div>
                            <a href="<?=$permalink?>"><img src="<?=$thumbnail_url?>"></a>
                        </div>
                    
                    <div class="card-content">
                        <a href="<?=$permalink?>"><div class="card-title"><h2><?=$title?></h2></div></a>
                        <div class="d-cart-excerpt">
                            <?=wp_strip_all_tags($excerpt)?>
                        </div>
                        <div>
                            <?php
                                if($has_custom_donate){
                                    echo '<div class="btns-share">';
                                    if(is_array($custom_donate))
                                    foreach($custom_donate as $element){
                                        echo '<div class="btn_share" data-share="' . $element[1] . '">' . $element[0] . '</div>';
                                    }
                                    echo '</div>';
                                }
                            ?>
                        </div>
                        <?php if($project_goal){ ?>
                            <div class="sqd-progress-container">
                                <div class="sqd-progress-labels">
                                    <span><?php echo $received_donations;?> ريال</span>
                                    <span><?php echo $project_goal;?> ريال</span>
                                </div>
                                <div class="sqd-progress sqd-progress-animated- progress-green">
                                    <span data-progress="<?php echo $percentage;?>" style="width: <?php echo $percentage;?>%; background-color: #2a8165;"><?php echo $percentage;?>%</span>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="input_spinner" data-share="<?=$share_value?>" style="display: none">
                            <button class="spinner-btn btn btn-primary btn-sm" data-dir="down">-</button>
                            <input type="numeric" name="shares" maxlength="3" value="1" placeholder="عدد الأسهم" min="1" max="200">
                            <button class="spinner-btn btn btn-primary btn-sm" data-dir="up">+</button>
                        </div>
                        <div class="d-card-footer">
                            <div class="d-footer-btns">
                                <div class="d-footer-row top">
                                    <div class="donation-box">
                                        <label class="donation-label">قيمة التبرع</label>
                                        <div class="amount">
                                            <input type="numeric" class="<?=($has_custom_donate)?'disable-input':''?> amount-input" name="amount" maxlength="6" placeholder="الملبغ" min="1" max="250000" value="">
                                            <span class="currency">ر.س</span>
                                        </div>
                                    </div>

                                    <div class="qty-box" aria-label="quantity selector">
                                        <button class="spinner-btn" data-dir="up" type="button">+</button>
                                        <input type="numeric" name="shares" maxlength="3" value="1" class="qty-input">
                                        <button class="spinner-btn" data-dir="down" type="button">-</button>
                                    </div>
                                </div>

                                <div class="d-footer-row buttons">
                                    <div class="donate-btn"><a href="javascript:void(0)"><span class="btn-text">تبرع الآن</span></a></div>
                                    <div class="cart-donate-btn"><a href="javascript:void(0)"><span class="btn-text">إضافة</span> <i class="fas fa-shopping-cart"></i></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
            }
            ?>
            </div>
            <?php
            $r = ob_get_clean();
        }
        wp_reset_query();
        return ($gifts ?? '') . $r;
    });