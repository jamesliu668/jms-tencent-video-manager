<?php
/* 
Plugin Name: JMS Tencent Video Manager
Plugin URI: http://www.jmsliu.com/products/jms-rss-feed
Description: It's a very simple tencent video manager for WordPress. You can use add vid, name, description of your videos which you have uploaded on tencent video platform.
Author: James Liu
Version: 1.0.0
Author URI: http://jmsliu.com/
License: GPL3

{Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
{Plugin Name} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {License URI}.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//ali sms
//include dirname(__FILE__)."/lib/TopSdk.php";
date_default_timezone_set('Asia/Shanghai');

global $jms_tencent_video_manager_version;
$jms_tencent_video_manager_version = '1.0';
    
//install database
register_activation_hook( __FILE__, 'installJMSTencentVideoManager' );

add_action( 'admin_menu', 'jmsTencentVideoAdminPage' );
add_action('wp_ajax_jms_tencent_video', 'jms_tencent_video_ajax');
add_action('wp_ajax_nopriv_jms_tencent_video', 'jms_tencent_video_ajax');


/**
 * Init database, current version 1.0
 */
function installJMSTencentVideoManager() {
    global $jms_tencent_video_manager_version;
    global $wpdb;
    

    $jms_tencent_video_manager_version = get_option( "jms_tencent_video_manager_version", null );
    if ( $jms_tencent_video_manager_version == null ) {
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_name = $wpdb->prefix . "jms_tencent_video";
        $sql = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `title` VARCHAR(255) NOT NULL,
                `vid` VARCHAR(255) NOT NULL,
                `category` INT UNSIGNED NOT NULL COMMENT 'category id from category table. Default value: 1',
                `create_date` DATETIME NOT NULL,
                `update_date` DATETIME NOT NULL,
                `published` TINYINT(1) NOT NULL,
                `description` TEXT(65535) NULL,
                `thumb` VARCHAR(255) NULL,
                PRIMARY KEY (`id`),
                UNIQUE INDEX `id_UNIQUE` (`id` ASC))
                ENGINE = InnoDB ".$charset_collate.";";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        
        add_option( "jms_tencent_video_manager_version", $jms_tencent_video_manager_version );
    }
}

function jmsTencentVideoAdminPage() {
    add_menu_page(
        __("视频管理", 'jms-tencent-video-manager' ),
        __("视频管理", 'jms-tencent-video-manager'),
        'manage_options',
        'jms-tencent-video-manager-top',
        'jmsTencentVideoAdminPageOptions' );
}

function jmsTencentVideoAdminPageOptions() {
    global $wpdb, $wp;
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    
    if( isset($_POST["action"]) ) {
        if($_POST[ "action" ] == "new-save") {
            if(check_admin_referer( 'new_video' )) {
                require_once(dirname(__FILE__)."/controllers/JMSTencentVideoController.php");
                $videoController = new JMSTencentVideoController();

                $title = trim($_POST[ "video_title" ]);
                $vid = trim($_POST[ "tencent_vid" ]);
                $desc = trim($_POST[ "video_desc" ]);
                $isPublish = 0;
                if(isset($_POST[ "video_published" ])) {
                    $isPublish = trim($_POST[ "video_published" ]) == "on" ? 1 : 0;
                }

                $videoController->addVideo($title, $vid, $desc, $isPublish);
            } else {
                echo __( '页面安全密钥已过期，请重新打开添加页面提交视频。' );
            }
        } else if($_POST[ "action" ] == "update-save") {
            if(check_admin_referer( 'update_video' )) {
                require_once(dirname(__FILE__)."/controllers/JMSTencentVideoController.php");
                $videoController = new JMSTencentVideoController();

                $id = trim($_POST[ "id" ]);
                $title = trim($_POST[ "video_title" ]);
                $vid = trim($_POST[ "tencent_vid" ]);
                $desc = trim($_POST[ "video_desc" ]);
                $thumb_old = trim($_POST[ "thumb_old" ]);
                $isPublish = 0;
                if(isset($_POST[ "video_published" ])) {
                    $isPublish = trim($_POST[ "video_published" ]) == "on" ? 1 : 0;
                }

                $videoController->updateVideo($id, $title, $vid, $desc, $isPublish, $thumb_old);
            } else {
                echo __( '页面安全密钥已过期，请重新打开编辑页面提交视频。' );
            }
        }
    } else if( isset($_GET[ "action" ]) ) {
        if($_GET[ "action" ] == 'new') {
            require_once(dirname(__FILE__)."/controllers/JMSTencentVideoController.php");
            $videoController = new JMSTencentVideoController();
            $videoController->showAddForm();
        } else if($_GET[ "action" ] == 'edit') {
            if(isset($_GET["id"])) {
                $videoID = trim($_GET["id"]);
                require_once(dirname(__FILE__)."/controllers/JMSTencentVideoController.php");
                $videoController = new JMSTencentVideoController();
                $videoController->showEditForm($videoID);
            } else {
                echo __('未找到制定的视频。', 'jms-tencent-video-manager');
            }
        } else if($_GET[ "action" ] == 'delete') {
            if(isset($_GET["id"])) {
                $videoID = trim($_GET["id"]);
                if(isset($_GET["_wpnonce"]) && wp_verify_nonce( trim($_GET["_wpnonce"]), 'delete-video-'.$videoID )) {
                    require_once(dirname(__FILE__)."/controllers/JMSTencentVideoController.php");
                    $videoController = new JMSTencentVideoController();
                    $videoController->deleteVideo($videoID);
                } else {
                    echo __('页面安全密钥已过期，无法删除指定视频。', 'jms-tencent-video-manager');
                }
            } else {
                echo __('未找到制定的视频。', 'jms-tencent-video-manager');
            }
        }
    } else {
        //show list
        require_once(dirname(__FILE__)."/controllers/JMSTencentVideoController.php");
        $videoController = new JMSTencentVideoController();

        $searchTerm = "";
        if( isset($_GET[ "s" ]) ) {
            $searchTerm = trim($_GET["s"]);
        }

        $paged = 1;
        if( isset($_GET[ "paged" ]) ) {
            $paged = (int)trim($_GET["paged"]);
        }

        $videoController->showVideoList($searchTerm, $paged);
    }
}

function jms_tencent_video_ajax($wp) {
    if($_REQUEST["task"] == "search") {
        require_once(dirname(__FILE__)."/controllers/JMSTencentVideoController.php");
        $controller = new JMSTencentVideoController();
        $controller->search();
    }
}
?>