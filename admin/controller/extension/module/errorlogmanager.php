<?php
class ControllerExtensionModuleErrorlogManager extends Controller {
	private $moduleName = 'ErrorlogManager';
	private $moduleNameSmall = 'errorlogmanager';
	private $moduleData_module = 'errorlogmanager_module';
	private $moduleModel = 'model_extension_module_errorlogmanager';
    private $modulePath = 'extension/module/errorlogmanager';
	private $eventGroup = 'errorlogmanager';
    private $buffer = '';
    private $db_insert_buffer = '';
    private $db_insert_timestamps_buffer = '';
    private $db_insert_buf_count = 0;
    private $last_insert_row_hash = "";
    private $last_insert_file_hash = "";
    private $version = '3.2.5';

    private $db_update_string = " ON DUPLICATE KEY UPDATE first_timestamp=LEAST(first_timestamp, VALUES(first_timestamp)), last_timestamp=GREATEST(last_timestamp, VALUES(last_timestamp)), occurances=occurances+1";

    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->model('extension/module/'.$this->moduleNameSmall);
        $this->load->model("setting/setting");
    }

    public function index() {
        //upgrade check
        $struct_query = $this->db->query("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = '" . DB_DATABASE . "' AND table_name = '" . DB_PREFIX . "errorlog_manager' AND COLUMN_NAME = 'row_hash'");
        if ($struct_query->num_rows) {
            $this->{$this->moduleModel}->uninstall();
            $this->{$this->moduleModel}->install();
        }
        //end of upgrade check

		$data['moduleName'] = $this->moduleName;
		$data['moduleNameSmall'] = $this->moduleNameSmall;
		$data['moduleData_module'] = $this->moduleData_module;
		$data['moduleModel'] = $this->moduleModel;
		$data['modulePath'] = $this->modulePath;

        $this->load->language('extension/module/'.$this->moduleNameSmall);
        $this->load->model('setting/setting');
        $this->load->model('localisation/language');
        $this->load->model('design/layout');

        $this->document->addStyle('view/stylesheet/'.$this->moduleNameSmall.'/'.$this->moduleNameSmall.'.css');
        $this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

        $data['breadcrumbs']   = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL'),
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', 'type=module&user_token=' . $this->session->data['user_token'], 'SSL'),
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/'.$this->moduleNameSmall, 'user_token=' . $this->session->data['user_token'], 'SSL'),
        );

        $languageVariables = array(
		    // Main
			'heading_title',
			'error_permission',
			'text_success',
			'text_enabled',
			'text_disabled',
			'button_cancel',
			'save_changes',
			'text_default',
			'text_module',
			// Control panel
            'entry_code',
			'entry_code_help',
            'text_content_top',
            'text_content_bottom',
            'text_column_left',
            'text_column_right',
            'entry_layout',
            'entry_position',
            'entry_status',
            'entry_sort_order',
            'entry_layout_options',
            'entry_position_options',
			'entry_action_options',
            'button_add_module',
            'button_remove',
			// Custom CSS
			'custom_css',
            'custom_css_help',
            'custom_css_placeholder',
			// Module depending
			'wrap_widget',
			'wrap_widget_help',
			'text_products',
			'text_products_help',
			'text_image_dimensions',
			'text_image_dimensions_help',
			'text_pixels',
			'text_panel_name',
			'text_panel_name_help',
			'text_products_small',
			'show_add_to_cart',
			'show_add_to_cart_help'
        );

        foreach ($languageVariables as $languageVariable) {
            $data[$languageVariable] = $this->language->get($languageVariable);
        }

        $data['heading_title'] .= ' '.$this->version;

        $data['languages']              = $this->model_localisation_language->getLanguages();
        $data['user_token']                  = $this->session->data['user_token'];
        $data['action']                 = $this->url->link('extension/module/'.$this->moduleNameSmall, 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['cancel']                 = $this->url->link('extension/module', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['moduleSettings']			= $this->model_setting_setting->getSetting($this->moduleNameSmall);

		$data['moduleData']				= isset($data['moduleSettings'][$this->moduleNameSmall]) ? $data['moduleSettings'][$this->moduleNameSmall] : array ();

        $log_files = scandir(DIR_LOGS);
        $data['log_files'] = array();
        $data['main_log_file'] = basename($this->get_log_file());
        foreach ($log_files as $file) {
            if (in_array($file, array('.', '..'))) continue;

            $path = DIR_LOGS . $file;
            if (is_file($path)) {
                $data['log_files'][] = $file;
            }
        }

        $data['admin_mail'] = $this->config->get('config_email');
        $data['admin_name'] = $this->config->get('config_owner');

		$data['header']					= $this->load->controller('common/header');
		$data['column_left']			= $this->load->controller('common/column_left');
		$data['footer']					= $this->load->controller('common/footer');

        $data['initURL'] = html_entity_decode($this->url->link('extension/module/errorlogmanager/init', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        $data['updateURL'] = html_entity_decode($this->url->link('extension/module/errorlogmanager/update', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        $data['refreshURL'] = html_entity_decode($this->url->link('extension/module/errorlogmanager/clear_db', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        $data['getURL'] = html_entity_decode($this->url->link('extension/module/errorlogmanager/get_errors', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        $data['getPaginationURL'] = html_entity_decode($this->url->link('extension/module/errorlogmanager/get_pagination', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        $data['recentlyChangedURL'] = html_entity_decode($this->url->link('extension/module/errorlogmanager/recently_changed', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        $data['requestQuoteURL'] = html_entity_decode($this->url->link('extension/module/errorlogmanager/request_quote', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        $data['clearErrorURL'] = html_entity_decode($this->url->link('extension/module/errorlogmanager/clear_error', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        $data['getMsgURL'] = html_entity_decode($this->url->link('extension/module/errorlogmanager/get_msg', 'user_token=' . $this->session->data['user_token'], 'SSL'));

        $data['extensions'] = $this->getExtensions();

		$data['tab_controlpanel_content'] = $this->load->view('extension/module/'.$this->moduleNameSmall.'/tab_controlpanel', $data);

		$this->response->setOutput($this->load->view('extension/module/'.$this->moduleNameSmall, $data));
    }

    public function get_msg() {
        header('Content-Type: text/plain');
        $file = $this->request->get['file'];
        $hash = $this->request->get['hash'];
        echo $this->{$this->moduleModel}->get_error_message($file, $hash);
        exit;
    }

    public function request_quote() {
        $file = $this->request->post['file'];
        $hash = $this->request->post['hash'];
        $from = $this->request->post['from'];
        $name = !empty($this->request->post['name']) ? $this->request->post['name'] : $this->config->get('config_owner');
        $host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

        $json = array('message' => '[Error]: Failed to send message', 'success' => false);

        if (!empty($file) && !empty($file) && !empty($file)) {
            $error_msg = $this->{$this->moduleModel}->get_error_message($file, $hash);

            $mail = new Mail($this->config->get('config_mail'));
            $mail->setTo('sales@isenselabs.com');
            $mail->setFrom($from);
            $mail->setSender($name);
            $mail->setSubject("[Error Log Manager] Quote Request - " . $host);
            $mail->setText(html_entity_decode($error_msg, ENT_QUOTES, 'UTF-8'));
            $mail->send();

            $json['message'] = 'Your request has been submitted successfully';
            $json['success'] = true;
        }
        $this->return_json($json);
        exit;
    }

    public function get_errors() {
        session_write_close();
        $filters = !empty($this->request->get['filters']) ? json_decode(html_entity_decode($this->request->get['filters']), true) : array();
        $page = !empty($this->request->get['page']) ? $this->request->get['page'] : 1;

        $data['page'] = $page;
        $data['errors'] = $this->{$this->moduleModel}->get_errors($filters, $page);
        if (!empty($data['errors'])) {
            foreach ($data['errors'] as $k=>$error) {
                if (preg_match('/in\s([^\s]*)\son\sline\s(\d+)/', $error['message'], $matches)) {
                    $filename = $matches[1];
                    $line = (int)$matches[2];
                    $index = $line-1;
                    if (file_exists($filename)) {
                        $lines = file($filename);
                        $content = '<div style="border: 1px #000 dashed; padding: 5px;">';
                        if (isset($lines[$index-1])) {
                            $content .= '<div class="text-muted">' . ($line - 1) . ':&nbsp;' . htmlentities($lines[$index-1]) . '</div>';
                        }
                        if (isset($lines[$index])) {
                            $content .= '<div class="text-danger">' . ($line) . ':&nbsp;' . htmlentities($lines[$index]) . '</div>';
                        }
                        if (isset($lines[$index+1])) {
                            $content .= '<div class="text-muted">' . ($line + 1) . ':&nbsp;' . htmlentities($lines[$index+1]) . '</div>';
                        }
                        $content .= "</div>";
                        $data['errors'][$k]['code_preview'] = $content;
                    }
                }
            }
        }

        $this->template = 'extension/module/'.$this->moduleNameSmall.'/list';
        echo $this->load->view($this->template, $data);
        exit;
    }

    public function get_pagination() {
        session_write_close();
        $filters = !empty($this->request->get['filters']) ? json_decode(html_entity_decode($this->request->get['filters']), true) : array();
        $page = !empty($this->request->get['page']) ? $this->request->get['page'] : 1;

        $pagination = new Pagination();
        $pagination->total = $this->{$this->moduleModel}->get_messages_count($filters);
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = "javascript:gotoPage('{page}')";

        echo $pagination->render();
        exit;
    }

    public function clear_db() {
        $file = !empty($this->request->post['file']) ? $this->request->post['file'] : '';
        if (!empty($file)) {
            $this->{$this->moduleModel}->truncate($file);
        }
    }

    public function update() {
        session_write_close();

        $max_allowed_packet = $this->db->query("SHOW VARIABLES LIKE 'max_allowed_packet'");
        if ($max_allowed_packet->num_rows) {
            $this->max_allowed_packet = (int)$max_allowed_packet->row['Value'];
        } else {
            $this->max_allowed_packet = 1024 * 500;//500kb
        }

        $start_pos = !empty($this->request->post['start_byte']) ? (int)$this->request->post['start_byte'] : 0;
        $startTime = microtime(true);
        $max_exec_time = ini_get('max_execution_time');

        $json = array(
            "ready" => false,
            "cur_pos" => $start_pos,
            "total" => filesize($this->get_log_file())
        );

        $this->open_file($this->get_log_file());
        $cache_file = DIR_CACHE . "last_row_hash_" . md5(basename($this->get_log_file()));
        $last_row_hash = file_exists($cache_file) ? file_get_contents($cache_file) : null;

        if ($start_pos == 0) {//catch up to the last logged error, and start updating the db from that point on
            $this->clear_cache_for_file();

            if (!empty($last_row_hash)) {
                fseek($this->fh, 0, SEEK_END);
                while (false !== ($error = $this->get_prev_error())) {
                    $error_data = $this->get_row_data($error);
                    if ($error_data['row_hash'] == $last_row_hash) {
                        break;
                    }
                }
            }
        } else {
            fseek($this->fh, $start_pos);
        }

        while (false !== ($error = $this->get_next_error())) {
            $error_data = $this->get_row_data($error);
            if ($last_row_hash == $error_data['row_hash']) continue;

            $this->put_row_in_db($error);
            $json["cur_pos"] = ftell($this->fh);

            if ($max_exec_time - (microtime(true) - $startTime) < 5) {
                break;
            }
        }

        if (feof($this->fh)) {
            $json["ready"] = true;
        }

        $this->close_fh();
        $this->flush_to_db();
        $this->return_json($json);
    }

    public function init() {
        session_write_close();
        $json = array();
        $json['isUpdated'] = false;
        $this->open_file($this->get_log_file());
        $pos = -2;

        do {
            if(fseek($this->fh, $pos, SEEK_END) == -1) {
                break;
            }
            $pos--;
        } while (!$this->get_next_error());

        $row = $this->get_current_error();
        $this->close_fh();

        if ($row !== false) {
            $row_data = $this->get_row_data($row);
            $cache_file = DIR_CACHE . "last_row_hash_" . md5(basename($this->get_log_file()));
            $last_row_hash = file_exists($cache_file) ? file_get_contents($cache_file) : null;

            if (!empty($last_row_hash) && !empty($row_data)) {
                if ($row_data['row_hash'] == $last_row_hash) {
                    $json['isUpdated'] = true;
                }
            }
        }

        $this->return_json($json);
    }

    public function recently_changed() {
        $file = !empty($this->request->get['file']) ? $this->request->get['file'] : '';
        $hash = !empty($this->request->get['hash']) ? $this->request->get['hash'] : '';
        if (empty($file) || empty($hash)) {
            $response = "Couldn't find any files changed in that period";
        }

        $timestamp = (int)$this->{$this->moduleModel}->get_first_timestamp($file, $hash);
        $start = $timestamp - 86400;
        $end = $timestamp + 86400;

        $files = $this->scan_files(dirname(DIR_APPLICATION), $start, $end);

        $response = "<br /><p style=\"color: #f00;\">We couldn't find any files modified during this period :(</p>";
        if (!empty($files)) {
            $response = '<hr><table class="table table-bordered">';
            $response .= '<thead><tr><th>File</th><th>Modified on</th></tr></thead>';
            $response .= '<tbody>';
            foreach ($files as $file) {
                $response .= '<tr><td>' . $file . '</td><td>' . date('Y-m-d H:i:s', filemtime($file)) . '</td></tr>';
            }
            $response .= '</tbody>';
            $response .= '</table>';
        }

        echo $response;
        exit;
    }

    public function clear_error() {
        session_write_close();

        $msg_hash = $this->request->post['msg_hash'];
        $file_filter = $this->request->post['file'];
        $start_pos = !empty($this->request->post['start_byte']) ? (int)$this->request->post['start_byte'] : 0;
        $startTime = microtime(true);
        $max_exec_time = ini_get('max_execution_time');

        $file = $this->get_log_file();
        $file_tmp = $file . '.em_tmp';

        $json = array(
            "ready" => false,
            "cur_pos" => $start_pos,
            "total" => filesize($this->get_log_file())
        );

        $fh_tmp = fopen($file_tmp, 'w');
        $this->open_file($file);
        if ($this->fh !== false && $fh_tmp !== false) {
            fseek($this->fh, $start_pos);

            $counter = 0;
            while (false !== ($error = $this->get_next_error())) {
                $error_data = $this->get_row_data($error);
                if ($error_data['message_hash'] != $msg_hash) {
                    fwrite($fh_tmp, $error);
                    if (++$counter % 20 == 0) {//explicit flush every 20 messages
                        fflush($fh_tmp);
                    }
                }

                $json["cur_pos"] = ftell($this->fh);
                if ($max_exec_time - (microtime(true) - $startTime) < 5) {
                    break;
                }
            }

            if (feof($this->fh)) {
                $json["ready"] = true;
                $this->{$this->moduleModel}->clear_error($file_filter, $msg_hash);
            }

            fclose($fh_tmp);
            $this->close_fh();
            $file_bak = $file.'.bak_' . time();
            rename($file, $file_bak);
            rename($file_tmp, $file);
            clearstatcache();
            if (file_exists($file) && filesize($file) > 0) {
                unlink($file_bak);
            }
        }

        $this->return_json($json);
    }

    public function install() {
        $this->{$this->moduleModel}->install();
        $this->load->model("setting/event");

        // Admin events
        $this->model_setting_event->addEvent($this->eventGroup, "admin/view/common/column_left/before", $this->modulePath . "/injectAdminMenuItem");
    }

    public function uninstall() {
        $this->{$this->moduleModel}->uninstall();
        $this->model_setting_setting->deleteSetting("errorlogmanager");

        $this->load->model("setting/event");
        $this->model_setting_event->deleteEventByCode($this->eventGroup);
    }

    // Events
    public function injectAdminMenuItem($eventRoute, &$data) {
        if ($this->user->hasPermission('access', $this->modulePath)) {
            foreach ($data["menus"] as &$menu) {
                if ($menu["id"] == "menu-system") {
                    foreach ($menu["children"] as &$submenu) {
                        if (!empty($submenu["id"]) && $submenu["id"] == "menu-maintenance") {
                            foreach ($submenu["children"] as &$child) {
                                if (strpos($child["href"], "route=tool/log") !== false) {
                                    $child["href"] = $this->url->link($this->modulePath, 'user_token=' . $this->session->data['user_token'], true);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function getExtensions() {
        $ds = DIRECTORY_SEPARATOR;
        $dir_extensions = DIR_APPLICATION . 'controller' . $ds . 'extension' . $ds;

        $extension_entries = scandir($dir_extensions);
        $exts = array();

        foreach ($extension_entries as $ext_entry) {
            if (in_array($ext_entry, array(".", ".."))) continue;

            $entry_path = $dir_extensions . $ext_entry;
            if (is_dir($entry_path)) {
                $entry_name = preg_split("/[_\s]/", $ext_entry);
                $entry_name = array_map("strtolower", $entry_name);
                $entry_name = implode(" ", array_map("ucfirst", $entry_name));
                $exts[$entry_name] = $this->getExtensionsList($entry_path . $ds);
            }
        }

        return $exts;
    }

    private function getExtensionsList($dir) {
        $matches = array();
        $ds = DIRECTORY_SEPARATOR;
        $files = glob($dir . '*.php');
        foreach ($files as $file) {
            if (count(explode($ds, str_replace($dir, '', $file))) == 1) {
                $ext_title = $this->language->get('heading_title');
                $this->language->load(str_replace(DIR_APPLICATION . "controller" . $ds, "", preg_replace('/\.php$/', '', $file)));

                if ($this->language->get('heading_title') != $ext_title) {
                    $ext_title = $this->language->get('heading_title');
                } else {
                    $ext_title = basename(preg_replace('/\.php$/', '', $file));
                }

                $matches[] = array(
                    'file' => basename(preg_replace('/\.php$/', '', $file)),
                    'title' => $ext_title
                );
            }
        }
        return $matches;
    }

    private function return_json($json) {
        header('Content-Type: application/json');
        echo json_encode($json);
        exit;
    }

    private function flush_to_db() {
        if (empty($this->db_insert_buffer) || strlen($this->db_insert_buffer) + strlen($this->db_update_string) > $this->max_allowed_packet) return;

        $this->db->query(rtrim($this->db_insert_buffer, ',') . $this->db_update_string);
        $this->db->query(rtrim($this->db_insert_timestamps_buffer, ','));
        $this->db_insert_buffer = '';
        $this->db_insert_timestamps_buffer = '';
        $this->db_insert_buf_count = 0;

        file_put_contents(DIR_CACHE . "last_row_hash_" . $this->last_insert_file_hash, $this->last_insert_row_hash);
        $this->last_insert_row_hash = "";
        $this->last_insert_file_hash = "";
    }

    private function put_row_in_db($row) {
        if (empty($row) || strlen($row) > $this->max_allowed_packet) return;

        $row_data = $this->get_row_data($row);
        if (!$row_data) return false;

        extract($row_data);

        $file = md5(basename($this->get_log_file()));

        $new_item = " ('$file', '$message_hash', '".$this->db->escape($message)."', '".$this->db->escape($timestamp)."', '".$this->db->escape($timestamp)."', '1'),";
        $new_timestamp_item = " ('$message_hash', '".$this->db->escape($timestamp)."'),";

        if ($this->db_insert_buf_count >= 50 || (strlen($this->db_insert_buffer) + strlen($new_item) + strlen($this->db_update_string)) > $this->max_allowed_packet) {
            $this->flush_to_db();
        }

        if (empty($this->db_insert_buffer)) {
            $this->db_insert_buffer = "INSERT INTO " . DB_PREFIX . "errorlog_manager (`filename`, `message_hash`, `message`, `first_timestamp`, `last_timestamp`, `occurances`) VALUES";
        }

        if (empty($this->db_insert_timestamps_buffer)) {
            $this->db_insert_timestamps_buffer = "INSERT INTO " . DB_PREFIX . "errorlog_manager_timestamps (`message_hash`, `timestamp`) VALUES";
        }

        $this->db_insert_buffer .= $new_item;
        $this->db_insert_timestamps_buffer .= $new_timestamp_item;
        $this->db_insert_buf_count++;
        $this->last_insert_row_hash = $row_hash;
        $this->last_insert_file_hash = $file;
    }

    private function is_valid_row($row) {
        return preg_match('/(\d{4}-\d{2}-\d{2}\s\d{1,2}:\d{2}:\d{2})\s-\s(.*)/', $row);
    }

    private function get_log_file() {
        $file = 'error.log';

        if (!empty($this->request->post['file'])) {
            $file = $this->request->post['file'];
        }

        if (!empty($this->request->get['file'])) {
            $file = $this->request->get['file'];
        }

        return DIR_LOGS . $file;
    }

    private function clear_cache_for_file() {
        $file = md5(basename($this->get_log_file()));
    }

    private function open_file($file, $mode = 'r') {
        if (!file_exists($file)) return false;
        $this->fh = fopen($file, $mode);
    }

    private function close_fh() {
        fclose($this->fh);
    }

    private function get_row_data($row) {
        if (!$this->is_valid_row($row)) return false;

        $row = trim($row);
        $message = ltrim(substr($row, 21));
        return array(
            "timestamp" => strtotime(substr($row, 0, 19)),
            "message" => $message,
            "row_hash" => md5($row),
            "message_hash" => md5($message)
        );
    }

    private function go_next() {
        if (feof($this->fh)) return null;

        $this->buffer = '';

        // Buffer up to the beginning of the next error message or the end of the file
        while(false !== ($row = fgets($this->fh))) {
            if (!empty($this->buffer) && $this->is_valid_row($row)) {
                fseek($this->fh, $prev_pos, SEEK_SET);// Seek back to the beginnin of this error message
                break;
            }
            $this->buffer .= $row;
            $prev_pos = ftell($this->fh);
        }

        if ($this->is_valid_row($this->buffer)) {
            return true;
        } else {
            return false;
        }
    }

    private function go_prev() {
        if (ftell($this->fh) == 0) return null;

        $bytes = ftell($this->fh);
        $this->buffer = "";
        $line = "";

        // Buffer up to the beginning of the error message or the beginning of the file
        while ($bytes && !$line) {
            $seek_back = min($bytes, 1024);
            fseek($this->fh, -$seek_back, SEEK_CUR);
            $data = fread($this->fh, $seek_back);
            fseek($this->fh, -$seek_back, SEEK_CUR);
            $bytes -= $seek_back;

            $lines = explode("\n", $data);
            while ($lines) {
                $this->buffer = array_pop($lines) . "\n" . $this->buffer;
                if ($this->buffer && $this->is_valid_row($this->buffer)) {
                    $line = $this->buffer;
                    break;
                }
            }

            $remainder_len = strlen(implode("\n", $lines));
            $bytes += $remainder_len;
            fseek($this->fh, $remainder_len, SEEK_CUR);
        }

        if ($this->buffer && $this->is_valid_row($this->buffer)) {
            return true;
        } else {
            return false;
        }
    }

    private function get_current_error() {
        return $this->buffer;
    }

    private function get_next_error() {
        do {
            $result = $this->go_next();
        } while ($result === false);

        if ($result) {
            return $this->get_current_error();
        } else {
            return false;
        }
    }

    private function get_prev_error() {
        do {
            $result = $this->go_prev();
        } while ($result === false);

        if ($result) {
            return $this->get_current_error();
        } else {
            return false;
        }
    }

    private function scan_files($dir, $start, $end) {
        $files = array();
        if (!file_exists($dir) || !is_dir($dir)) return $files;

        $dh = opendir($dir);
        while(is_resource($dh) && false !== ($entry = readdir($dh))) {
            if (in_array($entry, array('.', '..'))) continue;
            if (preg_match('/\.(png|gif|jpe?g|zip|rar|txt|css|js)$/i', $entry)) continue;

            $path = $dir . DIRECTORY_SEPARATOR . $entry;
            if (is_file($path)) {
                $mod_time = filemtime($path);
                if ($mod_time > $start && $mod_time < $end) {
                    $files[] = $path;
                }
            } else if (is_dir($path)) {
                $files = array_merge($files, $this->scan_files($path, $start, $end));
            }
        }

        return $files;
    }
}
