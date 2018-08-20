<?php
    global $wp;
    $currentURL = $wp->request."admin.php?page=jms-tencent-video-manager-top";
?>

<div class="wrap">
<h1>
<?php
    echo __('视频管理','jms-tencent-video-manager');
?> <a href="
<?php
echo $currentURL;
?>&action=new" class="page-title-action">
<?php
    echo __('添加视频','jms-tencent-video-manager');
?>
</a></h1>


<form id="jms-patient-profile-filter" method="get" action="<?php echo $currentURL?>">

<p class="search-box">
	<label class="screen-reader-text" for="post-search-input">搜索:</label>
	<input type="search" id="post-search-input" name="s" value="<?php echo $searchTerm; ?>">
	<input type="submit" id="search-submit" class="button" value="搜索">
</p>

<input type="hidden" id="page" name="page" value="jms-tencent-video-manager-top">

<div class="tablenav top">
    <div class="tablenav-pages">
        <span class="displaying-num"><?php echo $totalRecord."个视频"?></span>
        <span class="pagination-links">
        <?php
            if($paged == 1) {
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">«</span>';
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>';
            } else {
                echo '<a class="next-page" href="'.$currentURL.'&paged=1">';
                echo '<span class="screen-reader-text">首页</span><span aria-hidden="true">«</span>';
                echo '</a>';

                echo '<a class="next-page" href="'.$currentURL.'&paged='.($paged - 1).'">';
                echo '<span class="screen-reader-text">上一页</span><span aria-hidden="true">‹</span>';
                echo '</a>';
            }
        ?>

<span class="paging-input">第<label for="current-page-selector" class="screen-reader-text">当前页</label>
<input class="current-page" id="current-page-selector" name="paged" value="<?php echo $paged; ?>" size="1" aria-describedby="table-paging" type="text">
<span class="tablenav-paging-text">页，共<span class="total-pages"><?php echo $totalPage; ?></span>页</span></span>

        <?php
            if($paged == $totalPage) {
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">›</span>';
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">»</span>';
            } else {
                echo '<a class="next-page" href="'.$currentURL.'&paged='.($paged + 1).'">';
                echo '<span class="screen-reader-text">下一页</span><span aria-hidden="true">›</span>';
                echo '</a>';

                echo '<a class="last-page" href="'.$currentURL.'&paged='.$totalPage.'">';
                echo '<span class="screen-reader-text">尾页</span><span aria-hidden="true">»</span>';
                echo '</a>';
            }
        ?>


</span>
</div>
<br class="clear">
</div>




<h2 class="screen-reader-text">视频列表</h2>

<table class="wp-list-table widefat fixed striped posts">
	<thead>
	<tr>
		<td id="cb" class="manage-column column-cb check-column">
            <label class="screen-reader-text" for="cb-select-all-1">全选</label>
            <!--<input id="cb-select-all-1" type="checkbox">-->
        </td>
        <th scope="col" id="title" class="manage-column column-title column-primary">
            <?php echo __('视频名称','jms-tencent-video-manager');?>
        </th>
        <th scope="col" id="author" class="manage-column column-categories" style="width:180px;">
            <?php echo __('封面','jms-tencent-video-manager');?>
        </th>
        <th scope="col" id="author" class="manage-column column-author">
            <?php echo __('发布状态','jms-tencent-video-manager');?>
        </th>
        <th scope="col" id="categories" class="manage-column column-categories">
            <?php echo __('腾讯vid','jms-tencent-video-manager');?>
        </th>
        <th scope="col" id="categories" class="manage-column column-categories">
            <?php echo __('描述','jms-tencent-video-manager');?>
        </th>
        <th scope="col" id="categories" class="manage-column column-categories">
            <?php echo __('更新时间','jms-tencent-video-manager');?>
        </th>
    </tr>
	</thead>

	<tbody id="the-list">
    <?php
    if(isset($result)) {
        foreach($result as $data) {
    ?>
		<tr id="post-20" class="iedit author-self level-0 post-20 type-post status-publish format-standard hentry category-uncategorized">
			<th scope="row" class="check-column">
                <label class="screen-reader-text" for="cb-select-20">选择文章2</label>
                <!--<input id="cb-select-20" type="checkbox" name="post[]" value="20">-->
                <div class="locked-indicator"></div>
            </th>
            <td class="title column-title has-row-actions column-primary page-title">
                <strong>
                    <a class="row-title" href="<?php echo $wp->request; ?>admin.php?page=jms-tencent-video-manager-top&id=<?php echo $data["id"];?>&action=edit">
                        <?php echo $data["title"]; ?>
                    </a>
                </strong>
                <div class="row-actions">
                    <span class="edit"><a href="<?php echo $wp->request; ?>admin.php?page=jms-tencent-video-manager-top&id=<?php echo $data["id"];?>&action=edit">
                    <?php echo __('Edit','jms-tencent-video-manager'); ?>
                    </a> | </span>
                    <span class="trash"><a href="<?php echo $wp->request; ?>admin.php?page=jms-tencent-video-manager-top&id=<?php echo $data["id"];?>&action=delete&_wpnonce=<?php echo wp_create_nonce( 'delete-video-'.$data["id"] );?>" class="submitdelete">移至回收站</a> | </span>
                </div>
            </td>
            
            
            <td class="categories column-categories">
                <?php
                    if(empty($data["thumb"])) {
                        echo "<img src=\"".plugins_url( '/../thumb/image.jpg', __FILE__ )."\" width=\"160\" height=\"90\"/>";
                    } else {
                        echo "<img src=\"".plugins_url( '/../thumb/'.$data["thumb"], __FILE__ )."\" width=\"160\" height=\"90\"/>";
                    }
                ?>
            </td>
    
            <td class="author column-author">
                <?php
                    echo $data["published"];
                ?>
            </td>
            
            <td class="categories column-categories">
                <?php
                    echo "<a href=\"https://v.qq.com/x/page/".$data["vid"].".html\" target=\"_blank\">".$data["vid"]."</a>";
                ?>
            </td>
            
            <td class="categories column-categories">
                <?php
                    echo $data["description"];
                ?>
            </td>
            
            <td class="date column-date" data-colname="日期">
                <abbr title="2016/11/07 13:30:52">
                    <?php echo $data["update_date"]; ?>
                </abbr>
            </td>
        </tr>
    <?php
        }
    }
    ?>
	</tbody>

	<tfoot>
   	</tfoot>
</table>

<div class="tablenav bottom">
    <div class="tablenav-pages">
        <span class="displaying-num"><?php echo $totalRecord."个视频"?></span>
        <span class="pagination-links">
        <?php
            if($paged == 1) {
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">«</span>';
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>';
            } else {
                echo '<a class="next-page" href="'.$currentURL.'&paged=1">';
                echo '<span class="screen-reader-text">首页</span><span aria-hidden="true">«</span>';
                echo '</a>';

                echo '<a class="next-page" href="'.$currentURL.'&paged='.($paged - 1).'">';
                echo '<span class="screen-reader-text">上一页</span><span aria-hidden="true">‹</span>';
                echo '</a>';
            }
        ?>

<span class="paging-input">第<label for="current-page-selector" class="screen-reader-text">当前页</label>
<input class="current-page" id="current-page-selector" name="paged" value="<?php echo $paged; ?>" size="1" aria-describedby="table-paging" type="text">
<span class="tablenav-paging-text">页，共<span class="total-pages"><?php echo $totalPage; ?></span>页</span></span>

        <?php
            if($paged == $totalPage) {
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">›</span>';
                echo '<span class="tablenav-pages-navspan" aria-hidden="true">»</span>';
            } else {
                echo '<a class="next-page" href="'.$currentURL.'&paged='.($paged + 1).'">';
                echo '<span class="screen-reader-text">下一页</span><span aria-hidden="true">›</span>';
                echo '</a>';

                echo '<a class="last-page" href="'.$currentURL.'&paged='.$totalPage.'">';
                echo '<span class="screen-reader-text">尾页</span><span aria-hidden="true">»</span>';
                echo '</a>';
            }
        ?>


</span>
</div>
<br class="clear">
</div>

</form>
<br class="clear">
</div>