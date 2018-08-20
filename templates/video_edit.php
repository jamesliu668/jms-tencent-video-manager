<?php
    $action = "update-save";
?>
<div class="wrap">
<h1>
<?php
    echo __('编辑视频','jms-tencent-video-manager');
?>
</h1>

<form method="post" novalidate="novalidate" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?php echo $action;?>">
    <?php wp_nonce_field( 'update_video' ); ?>
    <input type="hidden" name="id" value="<?php echo $result[0]["id"]; ?>"/>
    <input type="hidden" name="thumb_old" value="<?php echo $result[0]["thumb"]; ?>"/>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label for="video_title"><?php echo __('视频名称','jms-tencent-video-manager'); ?></label></th>
            <td>
                <input name="video_title" type="text" id="video_title" value="<?php echo stripslashes($result[0]["title"]); ?>" class="regular-text">
            </td>
        </tr>
        
        <tr>
            <th scope="row"><label for="tencent_vid"><?php echo __('腾讯视频vid','jms-tencent-video-manager'); ?></label></th>
            <td>
                <input name="tencent_vid" type="text" id="tencent_vid" value="<?php echo stripslashes($result[0]["vid"]); ?>" class="regular-text">
                <p class="description" id="tagline-description"><?php echo __('vid是腾讯视频的视频id标识，需要在腾讯视频的网页上才能找到。','jms-tencent-video-manager'); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="video_desc"><?php echo __('腾讯描述','jms-tencent-video-manager'); ?></label></th>
            <td>
                <textarea name="video_desc" id="video_desc" rows="5" cols="53"><?php echo stripslashes($result[0]["description"]); ?></textarea>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="video_thumb"><?php echo __('视频封面','jms-tencent-video-manager'); ?></label></th>
            <td>
                <p>
                <?php
                    if(empty($result[0]["thumb"])) {
                        echo "<img src=\"".plugins_url( '/../thumb/image.jpg', __FILE__ )."\" width=\"160\" height=\"90\"/>";
                    } else {
                        echo "<img src=\"".plugins_url( '/../thumb/'.$result[0]["thumb"], __FILE__ )."\" width=\"160\" height=\"90\"/>";
                    }
                ?>
                </p>
                <input name="video_thumb" type="file" id="video_thumb">
                <p class="description" id="tagline-description"><?php echo __('如果不修改封面图片，请勿选择文件；','jms-tencent-video-manager'); ?></p>
                <p class="description" id="tagline-description"><?php echo __('上传视频封面图片，尺寸为160x90，320x180，640x360，或者1600x900大小','jms-tencent-video-manager'); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="video_published"><?php echo __('是否发布','jms-tencent-video-manager'); ?></label></th>
            <td>
                <input name="video_published" type="checkbox" id="video_published" class="regular-text" <?php if($result[0]["published"]) echo "checked=\"checked\""; ?>><?php echo __('选中即表示能够在小程序中搜索到','jms-tencent-video-manager'); ?>
            </td>
        </tr>
	</tbody>
</table>
<p class="submit">
<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('保存','jms-tencent-video-manager');?>">
<a class="button" style="margin-left: 10px;" onclick="window.history.back();"><?php echo __('取消','jms-tencent-video-manager');?></a>
</p>

</form>

</div>