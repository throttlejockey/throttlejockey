<?php
class ControllerCustomDhlExpressApi extends Controller
{
    public function orders()
    {
        $json   = array();
        $status = intval($this->request->get['status']);
        $days   = $this->request->get['days'];
        $token  = $this->request->get['token'];
        if (isset($status) && !is_null($status)) {
            $status = intval($this->request->get['status']);
        } else {
            $status = 5;
        }
        $this->load->model('custom/dhlexpress_order');
        $json = $this->model_custom_dhlexpress_order->getOrdersByStatusId($status, $days, $token);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function update()
    {
        $json             = array();
        $order_id         = $this->request->post['order_id'];
        $order_status_id  = $this->request->post['order_status_id'];
        $tracking_details = $this->request->post['tracking_details'];
        $notify           = $this->request->post['notify'] === 'true' ? true : false;
        $override         = $this->request->post['override'] === 'true' ? true : false;
        $token            = $this->request->post['token'];
        $this->load->model('custom/dhlexpress_order');
        $json = $this->model_custom_dhlexpress_order->updateOrder($order_id, $tracking_details, $order_status_id, $notify, $override, $token);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}