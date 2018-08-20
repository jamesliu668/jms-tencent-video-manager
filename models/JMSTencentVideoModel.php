<?php
    class JMSTencentVideoModel {
        private $tableName = "jms_tencent_video";

        function numberOfVideo($searchTerm) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );

            $sql = "SELECT count(*) AS total FROM $table_name";
            if($searchTerm != "") {
                $sql = "SELECT count(*) AS total FROM $table_name WHERE `title` LIKE '%".$searchTerm."%'";
            }
            $totalNumber = $wpdb->get_results($sql, OBJECT);
            return $totalNumber[0]->total;
        }

        function getVideoList($paged, $numberOfRecord, $searchTerm) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );

            $startIndex = ($paged - 1) * $numberOfRecord;
            $sql = "SELECT * FROM $table_name ORDER BY `id` ASC LIMIT $startIndex, $numberOfRecord";
            if($searchTerm != "") {
                $sql = "SELECT * FROM $table_name WHERE `title` LIKE '%".$searchTerm."%' ORDER BY `id` ASC LIMIT $startIndex,$numberOfRecord";
            }
            $result = $wpdb->get_results($sql, ARRAY_A);
            return $result;
        }

        function addVideo($title, $vid, $categoryid, $desc, $isPublish, $currentDate) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );

            //insert
            $query = $wpdb->prepare(
                "INSERT INTO $table_name (title, vid, category, create_date, update_date, published, description)
                    VALUES (%s, %s, %d, %s, %s, %d, %s)",
                array(
                    $title,
                    $vid,
                    $categoryid,
                    $currentDate,
                    $currentDate,
                    $isPublish,
                    $desc
                    )
            );

            $result = $wpdb->query($query);
            return $result;
        }

        function updateVideo($id, $title, $vid, $categoryid, $desc, $isPublish, $datetime, $filename) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );

            $query = $wpdb->prepare(
                "UPDATE $table_name SET title=\"%s\", vid=\"%s\", category=%d, update_date=\"%s\", published=%d,  description=\"%s\", thumb=\"%s\" WHERE id = %d",
                array(
                    $title,
                    $vid,
                    $categoryid,
                    $datetime,
                    $isPublish,
                    $desc,
                    $filename,
                    $id
                    )
            );
            $result = $wpdb->query($query);

            return $result; //true or false
        }

        function getVideoByID($id) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );
            return $wpdb->get_results("SELECT * FROM $table_name WHERE id=".(int)$id, ARRAY_A);
        }

        function deleteVideoByID($id) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->tableName;
            $wpdb->show_errors( true );
            $result = $wpdb->query($wpdb->prepare(
                "DELETE FROM $table_name WHERE `id` = %d",
                array($id)
            ));

            return $result;
        }
    }
?>