<?php 
class ControllerExtensionModuleDevmanextensionsDownloadCenter extends Controller { 
    private $error = array();
    
    private $data_to_view = array();
    
    public function __construct($registry) {

        parent::__construct($registry);

        $this->extension_name = 'devmanextensions_download_center';
        $this->extension_group_config = 'devmanextensions_download_center';

        $this->oc_version = version_compare(VERSION, '3.0.0.0', '>=') ? 3 : (version_compare(VERSION, '2.0.0.0', '>=') ? 2 : 1);
        $this->api_url = defined('DEVMAN_SERVER_TEST') ? DEVMAN_SERVER_TEST : 'https://devmanextensions.com/';

        $this->is_oc_3x = $this->oc_version >= 3;
        $this->is_oc_2x = $this->oc_version >= 2 && $this->oc_version < 3;
        $this->is_oc_1x = $this->oc_version >= 1 && $this->oc_version < 2;

        //IMPORTANT - DEFINE TYPE EXTENSION - MODULE, PAYMENT, SHIPPING...
        $this->extension_type = 'module';
        $this->real_extension_type = version_compare(VERSION, '2.3.0.0', '>=') ? 'extension/'.$this->extension_type : $this->extension_type;

        $this->extension_url_cancel_oc_15x = 'extension/'.$this->extension_type;
        $this->extension_url_cancel_oc_20x = 'extension/'.$this->extension_type;
        $this->extension_url_cancel_oc_23x = 'extension/extension';
        $this->extension_url_after_save_oc_15x = 'extension/'.$this->extension_type;
        $this->extension_url_after_save_oc_20x = 'extension/'.$this->extension_type;
        $this->extension_url_after_save_oc_23x = 'extension/extension';
        $this->extension_url_after_save_error = 'extension/'.$this->extension_type.'/'.$this->extension_name;

        //Devman Extensions - info@devmanextensions.com - 2017-06-26 21:41:41 - Added Opencart 3.X Compatibility
            $this->token_name = version_compare(VERSION, '3.0.0.0', '<') ? 'token' : 'user_token';
            $this->token = $this->session->data[$this->token_name];
            $this->extension_view = version_compare(VERSION, '3.0.0.0', '<') ? $this->extension_name.'.tpl' : $this->extension_name;
        //END

        $loader = new Loader($registry);
        $loader->language($this->real_extension_type.'/'.$this->extension_name);
    }

    public function index() {
        //Set document title
            $this->document->setTitle($this->language->get('heading_title_2'));
            
        //Add custom js
            if(file_exists('view/javascript/devmanextensions_'.$this->extension_name.'.js'))
                $this->document->addScript('view/javascript/devmanextensions_'.$this->extension_name.'.js?'.date('Ymdhis'));

            if(version_compare(VERSION, '2.0.0.0', '>=') && file_exists('view/javascript/devmanextensions_'.$this->extension_name.'_oc2x.js'))
                $this->document->addScript('view/javascript/devmanextensions_'.$this->extension_name.'_oc2x.js?'.date('Ymdhis'));
            elseif(file_exists('view/javascript/devmanextensions_'.$this->extension_name.'_oc15x.js'))
                $this->document->addScript('view/javascript/devmanextensions_'.$this->extension_name.'_oc15x.js?'.date('Ymdhis'));

        //Add custom css
            if(file_exists('view/stylesheet/devmanextensions_'.$this->extension_name.'.css'))
                $this->document->addStyle('view/stylesheet/devmanextensions_'.$this->extension_name.'.css?'.date('Ymdhis'));

            if(version_compare(VERSION, '2.0.0.0', '>=') && file_exists('view/stylesheet/devmanextensions_'.$this->extension_name.'_oc2x.css'))
                $this->document->addStyle('view/stylesheet/devmanextensions_'.$this->extension_name.'_oc2x.css?'.date('Ymdhis'));
            elseif(file_exists('view/stylesheet/devmanextensions_'.$this->extension_name.'_oc15x.css'))
                $this->document->addStyle('view/stylesheet/devmanextensions_'.$this->extension_name.'_oc15x.css?'.date('Ymdhis'));
        
        //Devman Extensions - info@devmanextensions.com - 2016-10-21 18:57:30 - Custom functions
            if(
                !empty($this->request->post['ajax_function']) || !empty($this->request->get['ajax_function'])
                ||
                !empty($this->request->post[$this->extension_group_config.'_ajax_function']) || !empty($this->request->get[$this->extension_group_config.'ajax_function'])
            )
            {
                if(!empty($this->request->post['ajax_function']) || !empty($this->request->get['ajax_function']))
                    $index = 'ajax_function';
                else
                    $index = $this->extension_group_config.'_force_function';

                $post_get = !empty($this->request->post[$index]) ? 'post' : 'get';
                $this->{$this->request->{$post_get}[$index]}();
            }
        //END
            
        //Send token to view
            $this->data_to_view['token'] = $this->token;

        //Actions
            $this->data_to_view['action'] = $this->url->link($this->real_extension_type.'/'.$this->extension_name, $this->token_name.'=' . $this->token, 'SSL');
            $this->data_to_view['cancel'] = $this->url->link(version_compare(VERSION, '2.0.0.0', '>=') ? $this->extension_url_cancel_oc_20x : $this->extension_url_cancel_oc_15x, $this->token_name.'=' . $this->token, 'SSL');

        //Load extension languages
            $lang_array = array(
                'heading_title',
                'heading_title_2',
                'button_save',
                'button_cancel',
                'apply_changes',
                'text_image_manager',
                'text_browse',
                'text_clear',
                'text_validate_license',
                'text_license_id',
                'text_send',
                'text_download_identifier_recover'
            );

            foreach ($lang_array as $key => $value) {
                $this->data_to_view[$value] = $this->language->get($value);
            }
        //END Load extension languages
        

        //Devman Extensions - info@devmanextensions.com - 2016-11-19 14:43:03 - Send custom variables to view
            $this->_send_custom_variables_to_view();
        //END

        $this->data_to_view['breadcrumbs'] = array();
        $this->data_to_view['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', $this->token_name.'=' . $this->token, 'SSL'),
            'separator' => false
        );

        if(!in_array($this->extension_type, array('tool')))
        {
            $this->data_to_view['breadcrumbs'][] = array(
                'text'      => $this->language->get('text_'.$this->extension_type),
                'href'      => $this->url->link($this->extension_url_cancel, $this->token_name.'=' . $this->token, 'SSL'),
                'separator' => ' :: '
            );
        }

        $this->data_to_view['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title_2'),
            'href'      => $this->url->link($this->real_extension_type.'/'.$this->extension_name, $this->token_name.'=' . $this->token, 'SSL'),
            'separator' => ' :: '
        );

        $this->data_to_view['oc_version'] = $this->oc_version;

        //Devman Extensions - info@devmanextensions.com - 2017-06-28 20:47:35 - Opencart 3.x compatibility with alerts
            if(version_compare(VERSION, '3.0.0.0', '>='))
            {
                if(!empty($this->session->data['error']))
                {
                    $this->data_to_view['error_warning'] = $this->session->data['error'];
                    unset($this->session->data['error']);
                }

                if(!empty($this->session->data['error_expired']))
                {
                    $this->data_to_view['error_warning_expired'] = $this->session->data['error_expired'];
                    unset($this->session->data['error_expired']);
                }

                if(!empty($this->session->data['success']))
                {
                    $this->data_to_view['success_message'] = $this->session->data['success'];
                    unset($this->session->data['success']);
                }

                if(!empty($this->session->data['info']))
                {
                    $this->data_to_view['info_message'] = $this->session->data['info'];
                    unset($this->session->data['info']);
                }
            }
        //END

        //OC Versions compatibility
            if(version_compare(VERSION, '2.0.0.0', '>='))
            {
                $data = $this->data_to_view;
                $data['header'] = $this->load->controller('common/header');
                $data['column_left'] = $this->load->controller('common/column_left');
                $data['footer'] = $this->load->controller('common/footer');

                $this->response->setOutput($this->load->view($this->real_extension_type.'/'.$this->extension_view, $data));
            }
            else
            {
                $this->data = $this->data_to_view;
                $this->template = $this->extension_type.'/'.$this->extension_view;
                $this->children = array(
                    'common/header',
                    'common/footer'
                );

                $this->response->setOutput($this->render());
            }
    }

    public function _send_custom_variables_to_view()
    {
        $this->data_to_view['jquery_variables'] = array(
            'token' => $this->token,
            'token_name' => $this->token_name,
            'link_ajax_get_downloads' => htmlspecialchars_decode($this->url->link($this->real_extension_type.'/'.$this->extension_name.'&ajax_function=ajax_get_downloads', $this->token_name.'=' . $this->session->data[$this->token_name], 'SSL')),
            'opencart_version' => $this->oc_version
        );
    }

    public function ajax_get_downloads()
    {
        $download_id = array_key_exists('download_id', $this->request->post) && !empty($this->request->post['download_id']) ? $this->request->post['download_id'] : '';
        $result = $this->curl_call(array('download_id' => $download_id), $this->api_url.'opencart/ajax_get_extension_download_links');
        echo $result; die;
    }

    public function install()
    {
        if($this->is_oc_3x)
        {
            $this->load->model('user/user_group');
            $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/devmanextensions_download_center');
            $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/devmanextensions_download_center');

            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('module_'.$this->extension_group_config, array('module_'.$this->extension_group_config.'_status' => 1));   
        }
        return true;
    }

    public function uninstall()
    {
        if($this->is_oc_3x)
        {
            $this->load->model('user/user_group');
            $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/module/devmanextensions_download_center');
            $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/module/devmanextensions_download_center');

            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('module_'.$this->extension_group_config, array('module_'.$this->extension_group_config.'_status' => 0));   
        }
        return true;
    }

    public function curl_call($data, $url)
    {
        if (!function_exists('curl_init')){
            $result = json_encode(array(
                'error' => true,
                'message' => '<b>cURL PHP library</b> is not installed in your server!'
            ));
        }
        else
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
               $result = json_encode(array(
                    'error' => true,
                    'message' => '<b>cURL error number '.curl_errno($ch).'</b>'
                )); 
            }
            curl_close($ch);
        }
        return $result;
    }
}
?>