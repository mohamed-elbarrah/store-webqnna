<?php

get_header();

$cart_url = '';
if(function_exists('wc_get_cart_url'))
$cart_url = wc_get_cart_url();
?>
<input type="hidden" class="home_url" value ="<?=home_url()?>">
<input type="hidden" class="cart_url" value ="<?=$cart_url?>">



<div class="section_description">
    <h1>المشاريع</h1>
</div>
<div class="breadcrumb_container">
    <ul class="breadcrumb">
        <i aria-hidden="true" data-icon="" class="mhc-menu-icon"></i>
        <li><a href="/">الرئيسية</a></li><li>المشاريع</li>
    </ul>
</div>
<div class="qs-container">
    <div style="display: flex; flex-wrap: wrap; width: 100%">
        <div class="main">


<?php


$paged = (get_query_var('paged')) ? get_query_var('paged'): ((get_query_var('page')) ? get_query_var('page') : 1);
$args=array(
  'posts_per_page'    => 6,
  'orderby'           => 'post_date',
  'order'             => 'DESC',
  'paged'             => $paged,
  'post_type'         => 'product',
  'post_status'       => array('publish'),
  'caller_get_posts'  => 1,
  'meta_query' => array(
        'relation' => 'OR',
        array(
            'key'     => 'is_project',
            'value'   => '1',
            'compare' => '=',
        ),
    ),
);

if(isset($_GET['project']) && $_GET['project']){
    $args['tax_query']= array(
        array(
            'taxonomy'  => 'project',
            'field'     => 'term_id',
            'terms'     => array($_GET['project']),
        )
    );
}


query_posts($args);
if( have_posts() ) {
    ?>
    <div class="projects-container p-container">
    <?php
    while (have_posts()){
        the_post();
        $post_id = get_the_id();
        $permalink = get_the_permalink();
        $title = get_the_title();
        $thumbnail_url = get_the_post_thumbnail_url();
        $type = get_post_type();
        $share_value = get_post_meta($post_id, 'share_value', true);
        $project_goal = get_post_meta($post_id, 'project_goal', true);
        $net_revenue = qs_get_product_net_revenue($post_id);
        if(!$share_value)$share_value = 20;
        if(!$project_goal)$project_goal = 1000;
        
        $precentage = 100 * $net_revenue / $project_goal;
    ?>

        <div class="post-card" data-id="<?=$post_id?>">
            <a href="<?=$permalink?>">
                <div class="card-image"><img src="<?=$thumbnail_url?>">
                    <div class="share-view">قيمة السهم<br>
                        <span style="font-size: 20px;"><?=$share_value?></span>
                        <br>
                        ريال
                    </div>
                </div>
            </a>
            <div class="card-content">
                <a href="<?=$permalink?>"><div class="card-title"><h2><?=$title?></h2></div></a>
                <div class="input_spinner" data-share="<?=$share_value?>">
                    <button class="spinner-btn btn btn-primary btn-sm" data-dir="down">-</button>
                    <input type="number" name="shares" maxlength="3" value="1" placeholder="عدد الأسهم" min="1" max="200">
                    <button class="spinner-btn btn btn-primary btn-sm" data-dir="up">+</button>
                </div>
                <div class="progress_bar">
                    <div class="projcet-progress" style="width: <?=$precentage?>%"></div>
                    تم التبرع بـ <span><?=$net_revenue?></span> ريال من <span><?=$project_goal?></span>
                </div>
                <div class="amount">
                    <span>مبلغ التبرع</span>
                    <i>ر.س.</i>
                    <input type="number" name="amount" maxlength="6" placeholder="مبلغ التبرع" min="1" max="250000" value="<?=$share_value?>">
                </div>
                <div class="donate-btn">
                    <a href="javascript:void(0)">تبرع الآن</a>
                </div>
            </div>
        </div>

        <?php
    }
    ?>
    </div>
    <?php
}else{
    ?>
    <span style="width: calc(100% - 20px);
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
</span>
    <?php
}

?>
<?=zad_pagination();?>

        </div>

        <div class="sidebar">
            <ul class="list-unstyled list-No1">
                <?php
                    $params = $_GET;
                    unset($params['project']);
                    unset($params['page']);
                    $link = basename($_SERVER['PHP_SELF']).'?'.http_build_query($params);
                    if(!isset($_GET['project']) || !$_GET['project']){
                        echo '<li class="selected"><a href="'.$link.'">عرض جميع المشاريع</a></li>';
                    }else{
                        echo '<li><a href="'.$link.'">عرض جميع المشاريع</a></li>';
                    }
                    $terms = get_terms( array( 
                        'taxonomy'      => 'project',
                        'orderby'       => 'count',
                        'order'         => 'DESC',
                        'parent'        => 0,
                        'hide_empty'    => 0
                    ) );

                    foreach($terms as $term){
                        $params = $_GET;
                        unset($params['page']);
                        $params['project'] = $term->term_id;
                        $link = basename($_SERVER['PHP_SELF']).'?'.http_build_query($params);
                        if(isset($_GET['project']) && $_GET['project'] == $term->term_id){
                            echo '<li class="selected"><a href="'.$link.'">'.$term->name.'</a></li>';
                        }else{
                            echo '<li><a href="'.$link.'">'.$term->name.'</a></li>';
                        }
                    }
                ?>
            </ul>
        </div>
    </div>
</div>


<?php
get_footer();