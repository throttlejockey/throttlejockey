<?php
class ControllerExtensionModuleMigrateToV3 extends Controller {
    private $error = array();

    public function index() {
        $this->load->model('extension/module/migratetov3');
        $this->load->language('extension/module/migratetov3');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->model_extension_module_migratetov3->setModuleStatus('0');

        if (!isset($this->error['nodb1']) && !isset($this->error['nodb2'])) {
            if (($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['input_exportdb'])) && $this->validate()) {
                $this->session->data['host1']         = $this->request->post['input_host1'];
                $this->session->data['user1']         = $this->request->post['input_user1'];
                $this->session->data['password1']     = $this->request->post['input_password1'];
                $this->session->data['port1']         = $this->request->post['input_port1'];
                $this->session->data['name1']         = $this->request->post['input_name1'];
                $this->session->data['prefix1']       = $this->request->post['input_prefix1'];
                if (!$results = $this->model_extension_module_migratetov3->checkDBs($this->request->post['input_host1'],$this->request->post['input_user1'],$this->request->post['input_password1'],$this->request->post['input_port1'],$this->request->post['input_name1'],$this->request->post['input_prefix1'])) {
                    $this->error['nodb1'] = $this->language->get('error_nodb1');
                }

                if (!isset($this->error['nodb1'])) {
                    $this->session->data['db']        = "db1";
                    $this->session->data['db1']       = $this->request->post['input_name1'];
                    $this->session->data['tab']       = $this->language->get('tab_exportdb');
                    $this->session->data['connected'] = $this->language->get('text_connected');
                    $this->response->redirect($this->url->link('extension/module/migratetov3', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
                }
            }
        }

        if (!isset($this->error['nodb1']) && !isset($this->error['nodb2'])) {
            if (($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['input_importdb'])) && $this->validate()) {
                $this->session->data['host2']         = $this->request->post['input_host2'];
                $this->session->data['user2']         = $this->request->post['input_user2'];
                $this->session->data['password2']     = $this->request->post['input_password2'];
                $this->session->data['port2']         = $this->request->post['input_port2'];
                $this->session->data['name2']         = $this->request->post['input_name2'];
                $this->session->data['prefix2']       = $this->request->post['input_prefix2'];
                if (!$results = $this->model_extension_module_migratetov3->checkDBs($this->request->post['input_host2'],$this->request->post['input_user2'],$this->request->post['input_password2'],$this->request->post['input_port2'],$this->request->post['input_name2'],$this->request->post['input_prefix2'])) {
                    $this->error['nodb2'] = $this->language->get('error_nodb2');
                }

                if (!isset($this->error['nodb2'])) {
                    $this->session->data['db']        = "db2";
                    $this->session->data['db2']       = $this->request->post['input_name2'];
                    $this->session->data['tab']       = $this->language->get('tab_importdb');
                    $this->session->data['connected'] = $this->language->get('text_connected');
                    $this->response->redirect($this->url->link('extension/module/migratetov3', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
                }
            }
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['input_customer'])) {
            include 'model/extension/module/migratecustomer.php';
            if (!isset($this->error['customer']) && !isset($this->error['address'])) {
                $this->session->data['migrated']      = $this->language->get('text_c_migrated');
                $this->response->redirect($this->url->link('extension/module/migratetov3', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
            }
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['input_category'])) {
            include 'model/extension/module/migratecategory.php';
            if (!isset($this->error['category'])) {
                $this->session->data['migrated']      = $this->language->get('text_p_migrated');
                $this->response->redirect($this->url->link('extension/module/migratetov3', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
            }
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['input_product'])) {
            include 'model/extension/module/migrateproduct.php';
            if (!isset($this->error['product'])) {
                $this->session->data['migrated']      = $this->language->get('text_p_migrated');
                $this->response->redirect($this->url->link('extension/module/migratetov3', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
            }
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['input_order'])) {
            include 'model/extension/module/migrateorder.php';
            if (!isset($this->error['order'])) {
                $this->session->data['migrated']      = $this->language->get('text_o_migrated');
                $this->response->redirect($this->url->link('extension/module/migratetov3', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
            }
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['input_other'])) {
            include 'model/extension/module/migrateother.php';
            if (!isset($this->error['other'])) {
                $this->session->data['migrated']      = $this->language->get('text_x_migrated');
                $this->response->redirect($this->url->link('extension/module/migratetov3', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
            }
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['input_migrate'])) {
            $this->session->data['tab']     = $this->language->get('tab_migrate');
            if (!isset($this->error['customer']) && !isset($this->error['address']) && !isset($this->error['category'])&& !isset($this->error['product'])&& !isset($this->error['order'])&& !isset($this->error['other'])) {
                $this->error['nomigrate']   = $this->language->get('error_nomigrate');
            }
        }

        if (isset($this->error['warning'])) {
            $data['error_warning']          = $this->error['warning'];
        } else {
            $data['error_warning']          = '';
        }

        if (isset($this->session->data['db'])) {
            $data['db']                     = $this->session->data['db'];
        }

        if (!isset($data['tab'])) {
            $data['tab']                    = $this->language->get('tab_tutorial');
            $data['error_tutorial']         = 'on';
            $data['error_exportdb']         = '';
            $data['error_importdb']         = '';
            $data['error_migrate']          = '';
        }

        if (isset($this->session->data['tab'])) {
            $data['tab']                    = $this->session->data['tab'];
            if ($this->session->data['tab'] == $this->language->get('tab_exportdb')) {
                $data['error_tutorial']     = '';
                $data['error_exportdb']     = 'on';
                $data['error_importdb']     = '';
                $data['error_migrate']      = '';
            } elseif ($this->session->data['tab'] == $this->language->get('tab_importdb')) {
                $data['error_tutorial']     = '';
                $data['error_exportdb']     = '';
                $data['error_importdb']     = 'on';
                $data['error_migrate']      = '';
            } else {
                $data['error_tutorial']     = '';
                $data['error_exportdb']     = '';
                $data['error_importdb']     = '';
                $data['error_migrate']      = 'on';
            }
        }

        if (isset($this->error['nodb1'])) {
            $data['db1']                = '';
            $this->session->data['db1'] = '';
            $data['error_nodb1']        = $this->error['nodb1'];
            $data['tab']                = $this->language->get('tab_exportdb');
            $data['error_tutorial']     = '';
            $data['error_exportdb']     = 'off';
            $data['error_importdb']     = '';
            $data['error_migrate']      = '';
        } else {
            $data['error_nodb1']        = '';
            if (isset($this->session->data['db1'])) {
                $data['db1']                = $this->session->data['db1'];
            }
        }

        if (isset($this->error['nohost1'])) {
            $data['error_nohost1']   = $this->error['nohost1'];
        } else {
            $data['error_nohost1']   = '';
        }

        if (isset($this->error['nouser1'])) {
            $data['error_nouser1']   = $this->error['nouser1'];
        } else {
            $data['error_nouser1']   = '';
        }

        if (isset($this->error['nopassword1'])) {
            $data['error_nopassword1']   = $this->error['nopassword1'];
        } else {
            $data['error_nopassword1']   = '';
        }

        if (isset($this->error['noport1'])) {
            $data['error_noport1']   = $this->error['noport1'];
        } else {
            $data['error_noport1']   = '';
        }

        if (isset($this->error['noname1'])) {
            $data['error_noname1']   = $this->error['noname1'];
        } else {
            $data['error_noname1']   = '';
        }

        // if (isset($this->error['noprefix1'])) {
        //  $data['error_noprefix1']   = $this->error['noprefix1'];
        // } else {
        //  $data['error_noprefix1']   = '';
        // }

        if (isset($this->session->data['host1'])) {
            $data['host1']             = $this->session->data['host1'];
            $data['user1']             = $this->session->data['user1'];
            $data['password1']         = $this->session->data['password1'];
            $data['port1']             = $this->session->data['port1'];
            $data['name1']             = $this->session->data['name1'];
            $data['prefix1']           = $this->session->data['prefix1'];
        }

        if (isset($this->session->data['host2'])) {
            $data['host2']             = $this->session->data['host2'];
            $data['user2']             = $this->session->data['user2'];
            $data['password2']         = $this->session->data['password2'];
            $data['port2']             = $this->session->data['port2'];
            $data['name2']             = $this->session->data['name2'];
            $data['prefix2']           = $this->session->data['prefix2'];
        }

        if (isset($this->error['nohost2'])) {
            $data['error_nohost2']   = $this->error['nohost2'];
        } else {
            $data['error_nohost2']   = '';
        }

        if (isset($this->error['nouser2'])) {
            $data['error_nouser2']   = $this->error['nouser2'];
        } else {
            $data['error_nouser2']   = '';
        }

        if (isset($this->error['nopassword2'])) {
            $data['error_nopassword2']   = $this->error['nopassword2'];
        } else {
            $data['error_nopassword2']   = '';
        }

        if (isset($this->error['noport2'])) {
            $data['error_noport2']   = $this->error['noport2'];
        } else {
            $data['error_noport2']   = '';
        }

        if (isset($this->error['noname2'])) {
            $data['error_noname2']   = $this->error['noname2'];
        } else {
            $data['error_noname2']   = '';
        }

        if (isset($this->error['noprefix2'])) {
            $data['error_noprefix2'] = $this->error['noprefix2'];
        } else {
            $data['error_noprefix2'] = '';
        }

        if (isset($this->error['nodb2'])) {
            $data['db2']                = '';
            $this->session->data['db2'] = '';
            $data['error_nodb2']        = $this->error['nodb2'];
            $data['tab']                = $this->language->get('tab_importdb');
            $data['error_tutorial']     = '';
            $data['error_importdb']     = 'off';
            $data['error_exportdb']     = '';
            $data['error_migrate']      = '';
        } else {
            $data['error_nodb2']        = '';
            if (isset($this->session->data['db2'])) {
                $data['db2']                = $this->session->data['db2'];
            }
        }

        if (isset($this->error['customer'])) {
            $data['error_customer']     = $this->error['customer'];
            $data['tab']                = $this->language->get('tab_migrate');
        } else {
            $data['error_customer']     = '';
        }

        if (isset($this->error['address'])) {
            $data['error_address']      = $this->error['address'];
            $data['tab']                = $this->language->get('tab_migrate');
        } else {
            $data['error_address']      = '';
        }

        if (isset($this->error['category'])) {
            $data['error_category']     = $this->error['category'];
            $data['tab']                = $this->language->get('tab_migrate');
        } else {
            $data['error_category']      = '';
        }

        if (isset($this->error['product'])) {
            $data['error_product']      = $this->error['product'];
            $data['tab']                = $this->language->get('tab_migrate');
        } else {
            $data['error_product']      = '';
        }

        if (isset($this->error['order'])) {
            $data['error_order']     = $this->error['order'];
            $data['tab']                = $this->language->get('tab_migrate');
        } else {
            $data['error_order']     = '';
        }

        if (isset($this->error['other'])) {
            $data['error_other']     = $this->error['other'];
            $data['tab']                = $this->language->get('tab_migrate');
        } else {
            $data['error_other']     = '';
        }

        if (isset($this->error['nomigrate'])) {
            $data['error_nomigrate']    = $this->error['nomigrate'];
            $data['tab']                = $this->language->get('tab_migrate');
        } else {
            $data['error_nomigrate']    = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success']            = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success']            = '';
        }

        if (isset($this->session->data['connected'])) {
            $data['connected']          = $this->session->data['connected'];
            unset($this->session->data['connected']);
        } else {
            $data['connected']          = '';
        }

        if (isset($this->session->data['migrated'])) {
            $data['migrated']           = $this->session->data['migrated'];
            unset($this->session->data['migrated']);
            $data['tab']                = $this->language->get('tab_migrate');
        } else {
            $data['migrated']           = '';
        }

        $data['breadcrumbs']   = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/migratetov3', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/migratetov3', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_migratetov3_status'])) {
            $data['module_migratetov3_status'] = $this->request->post['module_migratetov3_status'];
        } else {
            $data['module_migratetov3_status'] = $this->config->get('module_migratetov3_status');
        }

        $data['header']       = $this->load->controller('common/header');
        $data['column_left']  = $this->load->controller('common/column_left');
        $data['footer']       = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/migratetov3', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/migratetov3')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (isset($this->request->post['input_exportdb'])) {
            if ($this->request->post['input_host1'] == '') {
                $this->error['nohost1'] = $this->language->get('error_nohost1');
            }
            if ($this->request->post['input_user1'] == '') {
                $this->error['nouser1'] = $this->language->get('error_nouser1');
            }
            if ($this->request->post['input_password1'] == '') {
                $this->error['nopassword1'] = $this->language->get('error_nopassword1');
            }
            if ($this->request->post['input_port1'] == '') {
                $this->error['noport1'] = $this->language->get('error_noport1');
            }
            if ($this->request->post['input_name1'] == '') {
                $this->error['noname1'] = $this->language->get('error_noname1');
            }
            // if ($this->request->post['input_prefix1'] == '') {
            //     $this->error['noprefix1'] = $this->language->get('error_noprefix1');
            // }
            if($this->error) {
                $this->error['nodb1'] = $this->language->get('error_nodb1');
            }
        }

        if (isset($this->request->post['input_importdb'])) {
            if ($this->request->post['input_host2'] == '') {
                $this->error['nohost2'] = $this->language->get('error_nohost2');
            }
            if ($this->request->post['input_user2'] == '') {
                $this->error['nouser2'] = $this->language->get('error_nouser2');
            }
            if ($this->request->post['input_password2'] == '') {
                $this->error['nopassword2'] = $this->language->get('error_nopassword2');
            }
            if ($this->request->post['input_port2'] == '') {
                $this->error['noport2'] = $this->language->get('error_noport2');
            }
            if ($this->request->post['input_name2'] == '') {
                $this->error['noname2'] = $this->language->get('error_noname2');
            }
            if ($this->request->post['input_prefix2'] == '') {
                $this->error['noprefix2'] = $this->language->get('error_noprefix2');
            }
            if($this->error) {
                $this->error['nodb2'] = $this->language->get('error_nodb2');
            }
        }

        return !$this->error;
    }

    public function install() {
        $this->load->model('extension/module/migratetov3');
        $this->model_extension_module_migratetov3->setModuleStatus('0');
    }

    public function uninstall() {
        $this->load->model('extension/module/migratetov3');
        $this->model_extension_module_migratetov3->setModuleStatus('1');
    }

}