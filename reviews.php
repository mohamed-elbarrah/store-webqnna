<?php

echo '<div style="padding: 0 20px;max-width: 1100px;margin: 30px auto;">';
comments_template();
// comment_form();
echo '</div>';

return;

function better_comments( $comment, $args, $depth ) {

	// $tag       		= 'div';
	// $add_below 		= 'comment';
	global $current_user;
	$avatar_size 	= isset( $args['avatar_size'] ) ? $args['avatar_size'] : 100;
	$avatar 		= get_avatar( $comment, $avatar_size, '', 'صورة المستخدم');
	$author_link	= get_comment_author_link();
	$comment_ID 	= get_comment_ID();
	// $comment_type 	= $comment->comment_type;
	$comment_parent	= $comment->comment_parent;
	$comment_class 	= array();
	$comment_text	= trim(get_comment_text());
	// $comment_date 	= get_comment_date();
	// $comment_time 	= get_comment_time();
	if($current_user->ID == $comment->user_id)$owner = true; else $owner = false;

	$parent_id = $comment_parent;
	if($comment_parent){
		$parent_ = get_comment( intval( $comment_parent ) );
		if($parent_->comment_parent){
			$parent_id = $parent_->comment_parent;
			$parent_ = get_comment( intval( $parent_->comment_parent ) );
		}
		if($parent_->comment_parent){
			$parent_id = $parent_->comment_parent;
			$parent_ = get_comment( intval( $parent_->comment_parent ) );
		}
	}else{
		$parent_id = $comment_ID;
	}

	$user = get_user_by('id',$comment->user_id);
	$comment_class[] = 'cmnt_id_'.$comment_ID;
	$comment_class[] = 'cmnt_parent_'.$comment_parent;

	echo "<div ";
	echo "data-cmnt-id=\"" . $comment_ID . "\" ";
	echo "data-cmnt-parent=\"" . $comment_parent . "\" ";
	comment_class( $comment_class );
	echo " >"; 
	
	?>

	<div class="comment-container">
		<div class="comment-avatar">
			<?=$avatar?>
		</div>
		<div class="comment-content">
			<div class="comment-content-middle">
				<div class="comment-content-cmnt">
					<div class="comment-content-top">
						<b><a class="link" href="<?=home_url() . '/user/' . $user->user_login;?>"><?=$user->display_name?></a></b>
					</div>
					<span class="comment-content-txt"><?=htmlspecialchars($comment_text)?></span>
				</div>
			</div>
			<div class="comment-content-btm">
				<a class="comment-btm-links comment-like-link" data-id="<?=$comment_ID?>" data-comment_parent="<?=$parent_id?>" href="javascript:void(0);">أعجبني</a> . 
				<a class="comment-btm-links comment-replay-link" data-id="<?=$comment_ID?>" data-comment_parent="<?=$parent_id?>" href="javascript:void(0);">رد</a>
				<?php if($owner){ ?> . <a class="comment-btm-links comment-delete-link comment-like-link" data-action="<?=home_url() . '/?delete_comment&comment_id='.$comment_ID;?>" data-id="<?=$comment_ID?>" data-comment_parent="<?=$parent_id?>" href="javascript:void(0);">حذف</a><?php } ?>
			</div>
		</div>
	</div>
	<?php
}

?>
<style>

/*------------------------  [ comments  ]  ----------------------------*/

.block-1 {
    background-color: white;
    padding: 10px;
    border-radius: 10px;
    margin-top: 40px;
    margin-bottom: 20px;
    -webkit-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.16);
    -moz-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.16);
    -ms-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.16);
    -o-box-shadow: 0 3px 10px rgba(0, 0, 0, 0.16);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.16)
}

.block-1 .block-title {
    margin: 20px 18px;
    font-family: 'droid arabic kufi';
    font-size: 21px;
    font-weight: 500;
}

.block-1 .link {
    color: #6c3c96;
}

.block-1 .block-title span {
    border-bottom: solid 1px #4f436982;
}

.comment.depth-2 {
    /* display: none; */
    margin-right: 60px;
}

.comment.depth-1 {
    padding: 0;
    margin: 5px;
}

.comment-container {
    display: flex;
}

.comment.depth-1:not(:last-child) {
    /* border-bottom: solid 1px #7c4be31f; */
    margin-bottom: 5px;
    padding-bottom: 5px;
}

.comment-content {
    padding: 5px;
    flex: 1;
}

.comment-content-top {
    display: inline-block;
    height: 20px;
    line-height: 20px;
}

.comment-content-middle {
    min-height: 30px;
    line-height: 20px;
    padding-top: 5px;
    padding-bottom: 0;
}

.comment-content-cmnt {
    background-color: #3636690f;
    padding: 7px 10px 7px 15px;
    border-radius: 15px;
    display: inline-block;
}

.comment-content-txt {
    font-size: 14px;
    white-space: pre-line;
    word-wrap: break-word;
    overflow-wrap: break-word;
    display: inline;
}

.comment-content-btm {
    height: 20px;
    line-height: 20px;
    padding: 0px 15px;
}

.comment-avatar {
    width: 50px;
}

.comment-avatar img {
    border-radius: 50%;
    margin: 11px 5px 10px 5px;
    width: 40px !important;
    height: 40px !important;
}

.comment-form .comment-avatar img {
    margin: 3px 5px 10px 5px;
}

.comment-btm-links {
    color: #828080;
    font-family: 'droid arabic kufi';
    font-size: 12px;
}


/*********  depth-2 *********/

.comment.depth-2 .comment-content-top {
    height: 15px;
    line-height: 15px;
    font-size: 14px;
}

.comment.depth-2 .comment-content-middle {
    min-height: 20px;
    line-height: 15px;
    padding-top: 5px;
    padding-bottom: 0;
    font-size: 14px;
}

.comment.depth-2 .comment-content-btm {
    height: 20px;
    line-height: 20px;
    padding: 0px 15px;
    font-size: 14px;
}

.cmnt-textarea {
    width: calc(100%);
    line-height: 20px;
    padding: 10px 15px;
    border-radius: 17px;
    resize: none;
    outline: none;
    overflow: hidden;
    font-size: 14px;
    border: 0;
    background-color: white;
    background-color: #f1f1f1;
    transition: all 0.15s linear;
    border: solid 1px #d8d8d8;
}

textarea.cmnt-textarea:focus {
    box-shadow: 0 0 10px #0000001f;
    border-color: white!important;
}

.cmnt_btn {
    text-align: center;
    text-decoration: none;
    display: inline-block;
    margin: 4px 2px;
    transition-duration: 0.4s;
    border-radius: 7px;
    font-family: "droid arabic kufi", "changa", "flat-jooza";
    padding: 8px 23px;
    background-color: #79828c;
    border: solid 1px #ffffff;
    color: #ffffff;
    box-shadow: 0 2px 10px #8c47e259;
    font-size: 13px;
    cursor: pointer;
}

.cmnt_btn:hover {
    background-color: #4CAF50;
    color: white;
}

.cmnt_btn:active {
    position: relative;
    top: 1px;
    outline: none;
}

.opacity-halve {
    opacity: 0.5;
}

.opacity-halve .comment-btm-links {
    display: none;
}

@media screen and (max-width: 800px) {
    .comment-content {
        padding: 5px 0 5px 0;
        width: calc(100% - 65px);
        display: inline-block;
    }
    .comment.depth-1 {
        margin: 5px 0;
    }
    .comment-container {
        display: block;
    }
    .comment-avatar {
        display: inline-block;
        vertical-align: top;
    }
    .comment-content-btm {
        display: none;
    }
    .comment-content-btn {
        text-align: left;
    }
    .cmnt_btn {
        margin: 0 15px;
    }
}

</style>

<?php

global $current_user, $post;
$last_cid = '';

$comments_last = get_comments(array(
    'post_id' => get_the_ID(),
    'status' => 'approve',
    'type'=>'comment',
    'orderby'=>'comment_date',
    'order'=>'ASC',
));
$user_in = '0';
foreach($comments_last as $comment){
    $last_cid = $comment->comment_ID;
    if($current_user->ID && $current_user->ID == $comment->user_id){
        $user_in = '1';
    }
} 

?>

<input type="hidden" class="last_cid" value="<?=$last_cid;?>" />
<input type="hidden" class="post_id" value="<?=$post->ID;?>" />
<input type="hidden" class="user_in" value="<?=$user_in;?>" />

<div style="display:none;">
    <div class="u-avatar"><?=get_avatar($current_user->ID, 96, '', 'صورة المستخدم')?></div>
    <div class="u-profile-link"><?=home_url() . '/user/' . $current_user->user_login;?></div>
    <div class="u-name"><?=$current_user->display_name?></div>
</div>

<div class="comment-sample" style="display: none;">
    <div class="comment depth-[depth] cmnt_s-[cmnt_s] opacity-halve">
        <div class="comment-container">
            <div class="comment-avatar">
                [u_avatar]
            </div>
            <div class="comment-content">
                <div class="comment-content-middle">
                    <div class="comment-content-cmnt">
                        <div class="comment-content-top">
                            <b><a class="link" href="[u_profile_link]">[u_name]</a></b>
                        </div>
                        <span class="comment-content-txt">[comment_text]</span>
                    </div>
                </div>
                <div class="comment-content-btm">
                    <a class="comment-btm-links comment-like-link" data-id="[id]" data-comment_parent="[parent_id]" href="javascript:void(0);">أعجبني</a> . 
                    <a class="comment-btm-links comment-replay-link" data-id="[id]" data-comment_parent="[parent_id]" href="javascript:void(0);">رد</a> . 
                    <a class="comment-btm-links comment-delete-link" data-action="<?=home_url() . '/?delete_comment';?>" data-id="[id]" data-comment_parent="<?=$parent_id?>" href="javascript:void(0);">حذف</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="replay-form-sample" style="display: none;">
    <div class="comment comment-form cmnt-replay-form replay-to-[comment_id] comment-container depth-2">
        <div class="comment-avatar" style="padding-top: 10px;">
            <?=get_avatar($current_user->ID, 96, '', 'صورة المستخدم')?>
        </div>
        <div class="comment-content">
            <div class="comment-content-middle">
                    <form class="add_comment comment-f-[comment_id]" action="<?=home_url()."/?add_comment"?>"  data-insert-before="replay-to-[comment_id]" data-depth="[depth]">
                    <textarea class="cmnt-textarea" name="comment" rows = "2" placeholder="أكتب تعليقاً..."></textarea>
                    <input type="hidden" name="post_id" value="<?=get_the_ID()?>" />
                    <input type="hidden" class="cmnt-parent" name="cmnt-parent" value="[comment_id]" />
                    </form>
            </div>
        </div>
        <div style="padding-top: 10px;" class="comment-content-btn">
            <button data-cmnt-form="comment-f-[comment_id]" class="cmnt_btn">أرسل</button>
        </div>
    </div>
</div>




<div class="comments block-1">
    <h2 class="comments-title block-title">
        <span> التعليقات </span>
    </h2>
    <!-- list_comments -->
    <div class="list_comments">
<?php

$comments = get_comments( array('post_id' => get_the_id()) );

wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'better_comments', 'style' => 'div', 'avatar_size' => 50 ) ), $comments ); 


    // wp_list_comments( array(
    //     'callback'      => 'better_comments',
    //     'style'         => 'div',
    //     'avatar_size'   => 50,
    // ) ); 
?>
    </div><!-- list_comments -->

        <!-- comments form -->
        <div class="comment-container comments-form comment-form ">
            <div class="comment-avatar" style="padding-top: 10px;">
                <?=get_avatar($current_user->ID, 96, '', 'صورة المستخدم')?>
            </div>
            <div class="comment-content">
                <div class="comment-content-middle">
                        <form class="add_comment main-comment-form" action="<?=home_url()."/?add_comment"?>" data-insert-before="comments-form" data-depth="1">
                        <textarea class="cmnt-textarea" name="comment" rows = "3" placeholder="أكتب تعليقاً..."
                        style="
                        width: calc(100%);
                        line-height: 20px;
                        padding: 10px 15px;
                        border-radius: 17px;
                        resize: none;
                        outline: none;
                        overflow: hidden;
                        font-size: 14px;
                        "></textarea>
                        <input type="hidden" name="post_id" value="<?=get_the_ID()?>" />
                        <input type="hidden" class="cmnt-parent" name="cmnt-parent" value="0" />
                        </form>
                </div>
                <div class="comment-content-btm">
                    
                </div>
            </div>
            <div style="padding-top: 10px;" class="comment-content-btn">
                <button data-cmnt-form="main-comment-form" class="cmnt_btn">أرسل</button>
            </div>
        </div><!-- comments form -->

</div><!-- comments -->
    
<?php


