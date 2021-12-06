<?php 
class ModelExtensionModuleErrorlogManager extends Model {
    public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "errorlog_manager (
            `id` INT NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (`id`),
            `filename` CHAR(32) NOT NULL,
            `message_hash` CHAR(32) NOT NULL,
            `message` TEXT NOT NULL,
            `first_timestamp` INT,
            `last_timestamp` INT,
            `occurances` INT,
            INDEX `filename` (`filename`),
            UNIQUE INDEX `message_hash` (`message_hash`),
            INDEX `first_timestamp` (`first_timestamp`),
            INDEX `last_timestamp` (`last_timestamp`),
            INDEX `occurances` (`occurances`)
        )");

        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "errorlog_manager_timestamps (
            `id` INT NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (`id`),
            `message_hash` CHAR(32) NOT NULL,
            `timestamp` INT,
            INDEX `message_hash` (`message_hash`),
            INDEX `timestamp` (`timestamp`)
        )");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "errorlog_manager");
        $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "errorlog_manager_timestamps");
    }

    public function truncate($file) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "errorlog_manager WHERE `filename`='" . $this->db->escape(md5($file)) . "'");
        $cache_file = DIR_CACHE . "last_row_hash_" . md5($file);
        if (file_exists($cache_file)) unlink($cache_file);
    }

    public function get_pages_count($filters) {
        $limit = 10;
        $messages_count = $this->get_messages_count($filters);

        return ceil($messages_count / $limit);
    }

    public function get_messages_count($filters) {
        $file_hash = !empty($filters['file']) ? md5($filters['file']) : "";
        $cache_key = "errorlogmanager.count." . ($file_hash ? $file_hash . "." : "") . md5(serialize($filters));

        $table = DB_PREFIX . "errorlog_manager";
        $table_timestamps = DB_PREFIX . "errorlog_manager_timestamps";

        $conditions = array();

        if (!empty($filters['file'])) {
            $conditions[] = "filename = '" . $this->db->escape(md5($filters['file'])) . "'";
        }

        if (!empty($filters['extension'])) {
            $conditions[] = "message LIKE '%" . $filters['extension'] . "%'";
        }

        if (!empty($filters['search'])) {
            $conditions[] = "message LIKE '%" . $filters['search'] . "%'";
        }

        if (!empty($filters['from']) || !empty($filters['to'])) {
            if (!empty($filters['from']) && preg_match('/\d{4}-\d{2}-\d{2}/', $filters['from'])) {
                $time_start = strtotime($filters['from']);
            } else {
                $time_start = 0;
            }

            if (!empty($filters['to']) && preg_match('/\d{4}-\d{2}-\d{2}/', $filters['to'])) {
                $time_end = strtotime($filters['to']);
            } else {
                $time_end = PHP_INT_MAX;
            }

            $result = $this->db->query("SELECT COUNT(*) as rows_total FROM (SELECT *, occurances as popularity, first_timestamp AS first_appeared, last_timestamp AS last_appeared FROM $table  WHERE " . implode(' AND ', $conditions) . ") as tmp LEFT JOIN (SELECT * FROM $table_timestamps WHERE timestamp BETWEEN '$time_start' AND '$time_end' GROUP BY message_hash) as tmp2 ON (tmp.message_hash = tmp2.message_hash) WHERE tmp.message_hash IS NOT NULL AND tmp2.message_hash IS NOT NULL");
        } else {
            $result = $this->db->query("SELECT COUNT(*) AS rows_total FROM $table WHERE " . implode(' AND ', $conditions));
        }

        return (int)$result->row['rows_total'];
    }

    public function get_errors($filters = array(), $page = 1) {
        $limit = 10;
        $start = ($page-1) * $limit;

        $filters["limit"] = $limit;
        $filters["start"] = $start;

        $file_hash = !empty($filters['file']) ? md5($filters['file']) : "";
        $cache_key = "errorlogmanager.errors." . ($file_hash ? $file_hash . "." : "") . md5(serialize($filters));

        $table = DB_PREFIX . "errorlog_manager";
        $table_timestamps = DB_PREFIX . "errorlog_manager_timestamps";

        $conditions = array();

        if (!empty($filters['file'])) {
            $conditions[] = "filename = '" . $this->db->escape(md5($filters['file'])) . "'";
        }

        if (!empty($filters['extension'])) {
            $conditions[] = "message LIKE '%" . $filters['extension'] . "%'";
        }

        if (!empty($filters['search'])) {
            $conditions[] = "message LIKE '%" . $filters['search'] . "%'";
        }

        $order = !empty($filters['sort_order']) ? $filters['sort_order'] : 'occurances desc';

        if (!empty($filters['from']) || !empty($filters['to'])) {
            if (!empty($filters['from']) && preg_match('/\d{4}-\d{2}-\d{2}/', $filters['from'])) {
                $time_start = strtotime($filters['from']);
            } else {
                $time_start = 0;
            }

            if (!empty($filters['to']) && preg_match('/\d{4}-\d{2}-\d{2}/', $filters['to'])) {
                $time_end = strtotime($filters['to']);
            } else {
                $time_end = PHP_INT_MAX;
            }

            $result = $this->db->query("SELECT * FROM (SELECT *, occurances as popularity, first_timestamp AS first_appeared, last_timestamp AS last_appeared FROM $table  WHERE " . implode(' AND ', $conditions) . ") as tmp LEFT JOIN (SELECT message_hash FROM $table_timestamps WHERE timestamp BETWEEN '$time_start' AND '$time_end' GROUP BY message_hash) as tmp2 ON (tmp.message_hash = tmp2.message_hash) WHERE tmp.message_hash IS NOT NULL AND tmp2.message_hash IS NOT NULL ORDER BY $order LIMIT $start, $limit");
        } else {
            $result = $this->db->query("SELECT *, occurances as popularity, first_timestamp AS first_appeared, last_timestamp AS last_appeared FROM $table  WHERE " . implode(' AND ', $conditions) . " ORDER BY $order LIMIT $start, $limit");
        }

        return $result->rows;
    }

    public function get_first_timestamp($file, $hash) {
        $result = $this->db->query("SELECT first_timestamp as timestamp FROM " . DB_PREFIX . "errorlog_manager WHERE filename='".$this->db->escape(md5($file))."' AND message_hash='$hash' LIMIT 1");
        return $result->row['timestamp'];
    }

    public function get_error_message($file, $hash) {
        $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "errorlog_manager WHERE filename='".$this->db->escape(md5($file))."' AND message_hash='$hash' LIMIT 1");
        return $result->row['message'];
    }

    public function clear_error($filename, $message_hash) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "errorlog_manager WHERE `filename`='" . $this->db->escape(md5($filename)) . "' AND `message_hash`='" . $this->db->escape($message_hash) . "'");
    }
}
