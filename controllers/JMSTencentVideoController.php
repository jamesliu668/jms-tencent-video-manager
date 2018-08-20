<?php
    require_once(dirname(__FILE__)."/../models/JMSTencentVideoModel.php");
    class JMSTencentVideoController {
        private $model;

        function __construct() {
            $this->model = new JMSTencentVideoModel();
        }

        function showVideoList($searchTerm, $paged) {
            global $wpdb;

            $numberOfRecord = 10;
            $numberOfVideo = $this->model->numberOfVideo($searchTerm);
            $totalPage = ceil($numberOfVideo / $numberOfRecord) ;
    
            if($paged > $totalPage) {
                $paged = $totalPage > 0 ? $totalPage : 1;
            } else if($paged < 1) {
                $paged = 1;
            }

            $result = $this->model->getVideoList($paged, $numberOfRecord, $searchTerm);
            require_once(dirname(__FILE__)."/../templates/video_list.php");
        }

        function showAddForm() {
            require_once(dirname(__FILE__)."/../templates/video_new.php");
        }

        function showEditForm($videoID) {
            if(empty($videoID)) {
                echo __('未找到指定的视频', 'jms-tencent-video-manager');
            } else {
                $result = $this->model->getVideoByID($videoID);
                require_once(dirname(__FILE__)."/../templates/video_edit.php");
            }
        }

        function addVideo($title, $vid, $desc, $isPublish) {
            global $wpdb;
            if(empty($title)) {
                echo __('视频标题不能为空', 'jms-tencent-video-manager');
            } else if(empty($vid)) {
                echo __('视频vid不能为空', 'jms-tencent-video-manager');
            } else {
                $allowToAdd = true;
                if($_FILES["video_thumb"]["error"] == 0) {
                    $allowToAdd = $this->checkThumbnailFile();
                }

                if($allowToAdd) {
                    $currentDate = current_time('mysql', 0); //show local time
                    $categoryid = 1; # currently, the default category id is 1
                    $result = $this->model->addVideo($title, $vid, $categoryid, $desc, $isPublish, $currentDate);
                    if($result !== false) {
                        $lastid = $wpdb->insert_id;
                        if($_FILES["video_thumb"]["error"] == 0) {
                            $filename = $this->uploadThumbnail();
                            $result = $this->model->updateVideo($lastid, $title, $vid, $categoryid, $desc, $isPublish, $currentDate, $filename);
                        }
    
                        $message = sprintf(__('视频添加成功! <a href="%s">返回视频列表</a>', 'jms-tencent-video-manager'), $wp->request."admin.php?page=jms-tencent-video-manager-top");
                        echo "<h1>".$message."</h1>";
                    } else {
                        echo __('视频添加失败, 数据库操作失败!', 'jms-tencent-video-manager');
                    }
                } else {
                    echo __('视频添加失败, 请检查视频信息是否符合要求!', 'jms-tencent-video-manager');
                }
            }
        }

        function updateVideo($id, $title, $vid, $desc, $isPublish, $thumbPath) {
            if($_FILES["video_thumb"]["error"] == 0 && $this->checkThumbnailFile()) {
                $filename = $this->uploadThumbnail();
                if($filename != NULL) {
                    $thumbPath = $filename;
                } else {
                    echo __('视频封面图片上传失败', 'jms-tencent-video-manager');
                }
            }

            $currentDate = current_time('mysql', 0); //show local time
            $categoryid = 1; # currently, the default category id is 1
            $result = $this->model->updateVideo($id, $title, $vid, $categoryid, $desc, $isPublish, $currentDate, $thumbPath);
            if($result !== false) {
                $message = sprintf(__('视频更新成功! <a href="%s">返回视频列表</a>', 'jms-tencent-video-manager'), $wp->request."admin.php?page=jms-tencent-video-manager-top");
                echo "<h1>".$message."</h1>";
            } else {
                echo __('视频更新失败, 数据库操作失败!', 'jms-tencent-video-manager');
            }
        }

        function deleteVideo($videoID) {
            $result = $this->model->getVideoByID($videoID);
            if(count($result) > 0) {
                $thumbnail = trim($result[0]["thumb"]);
                if(!empty($thumbnail) && !$this->deleteThumbnail($result[0]["thumb"])) {
                    echo __('视频删除失败，找不到指定视频封面!', 'jms-tencent-video-manager');
                }

                $result = $this->model->deleteVideoByID($videoID);
                if($result !== false) {
                    $message = sprintf(__('视频删除成功! <a href="%s">返回视频列表</a>', 'jms-tencent-video-manager'), $wp->request."admin.php?page=jms-tencent-video-manager-top");
                    echo "<h1>".$message."</h1>";
                } else {
                    echo __('视频删除失败，找不到指定视频封面!', 'jms-tencent-video-manager');
                }
            } else {
                echo __('视频删除失败，找不到指定视频!', 'jms-tencent-video-manager');
            }
        }

        function mt_rand_str($length, $c = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
            $randomString = "";
            for($i = 0; $i < $length; $i++) {
                $randomString .= $c[mt_rand(0, strlen($c)-1)];
            }
            return $randomString;
        }

        function checkThumbnailFile() {
            $imageFileType = strtolower(pathinfo($_FILES["video_thumb"]["name"], PATHINFO_EXTENSION));
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" ) {
                echo __('视频封面的格式必须为图片格式，包括jpg，png，jpeg!', 'jms-tencent-video-manager');
                return false;
            }

            if ($_FILES["video_thumb"]["size"] > 5000000) {
                echo __('视频封面的大小必须小于5M!', 'jms-tencent-video-manager');
                return false;
            }

            return true;
        }

        function uploadThumbnail() {
            $targetFolder = dirname(__FILE__)."/../thumb/";
            $targetFileName = $this->mt_rand_str(32);
            $target_file = $targetFolder . $targetFileName;
            while (file_exists($target_file)) {
                $targetFileName = $this->mt_rand_str(32);
                $target_file = $targetFolder . $targetFileName;
            }
            
            if (move_uploaded_file($_FILES["video_thumb"]["tmp_name"], $target_file)) {
                return $targetFileName;
            } else {
                return NULL;
            }
        }

        function deleteThumbnail($fileName) {
            $targetFolder = dirname(__FILE__)."/../thumb/";
            $target_file = $targetFolder . $fileName;
            if(file_exists($target_file)) {
                return unlink($target_file);
            }

            return true;
        }

        function search() {
            $query = trim($_REQUEST['q']);
            $start = trim($_REQUEST['start']);
            $count = 10; # search for 10 items

            $result = $this->model->search($query, $start, $count);
            echo wp_json_encode($result);
        }
    }
?>