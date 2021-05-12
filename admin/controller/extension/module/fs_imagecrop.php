<?php
class ControllerExtensionModuleFsImagecrop extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/fs_imagecrop');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		$this->document->addStyle('view/javascript/jquery/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css');
		$this->document->addScript('view/javascript/jquery/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_fs_imagecrop', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->cache->delete('module_fs_imagecrop');
			$this->clear_images();

			if (isset($this->request->get['apply'])){
				$this->response->redirect($this->url->link('extension/module/fs_imagecrop', 'user_token=' . $this->session->data['user_token'], true));
			} else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}	
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_enabled'] = $this->language->get('text_enabled');

		$data['text_status'] = $this->language->get('text_status');
		$data['text_apply'] = $this->language->get('text_apply');
		
		$data['text_about'] = $this->language->get('text_about');
		$data['text_general_settings'] = $this->language->get('text_general_settings');
		$data['text_quality'] = $this->language->get('text_quality');
		$data['tooltip_quality'] = $this->language->get('tooltip_quality');
		$data['text_hide_real_path'] = $this->language->get('text_hide_real_path');
		$data['tooltip_hide_real_path'] = $this->language->get('tooltip_hide_real_path');
		$data['text_bg'] = $this->language->get('text_bg');
		$data['tooltip_bg'] = $this->language->get('tooltip_bg');
		$data['text_cuteborders'] = $this->language->get('text_cuteborders');
		$data['text_auto_height'] = $this->language->get('text_auto_height');
		$data['text_watermark_active'] = $this->language->get('text_watermark_active');
		$data['text_watermark'] = $this->language->get('text_watermark');
		$data['text_watermark_settings'] = $this->language->get('text_watermark_settings');
		$data['text_watermark_active'] = $this->language->get('text_watermark_active');
		$data['text_watermark_image'] = $this->language->get('text_watermark_image');
		$data['text_watermark_zoom'] = $this->language->get('text_watermark_zoom');
		$data['text_watermark_pos_x_center'] = $this->language->get('text_watermark_pos_x_center');
		$data['text_watermark_pos_x'] = $this->language->get('text_watermark_pos_x');
		$data['text_watermark_pos_y_center'] = $this->language->get('text_watermark_pos_y_center');
		$data['text_watermark_pos_y'] = $this->language->get('text_watermark_pos_y');
		$data['tooltip_watermark_pos'] = $this->language->get('tooltip_watermark_pos');
		$data['text_watermark_opacity'] = $this->language->get('text_watermark_opacity');
		$data['text_watermark_category_image'] = $this->language->get('text_watermark_category_image');
		$data['text_watermark_product_thumb'] = $this->language->get('text_watermark_product_thumb');
		$data['text_watermark_product_popup'] = $this->language->get('text_watermark_product_popup');
		$data['text_watermark_product_list'] = $this->language->get('text_watermark_product_list');
		$data['text_watermark_product_additional'] = $this->language->get('text_watermark_product_additional');
		$data['text_watermark_product_related'] = $this->language->get('text_watermark_product_related');
		$data['text_watermark_product_in_compare'] = $this->language->get('text_watermark_product_in_compare');
		$data['text_watermark_product_in_wish_list'] = $this->language->get('text_watermark_product_in_wish_list');
		$data['text_watermark_product_in_cart'] = $this->language->get('text_watermark_product_in_cart');
		$data['text_powered'] = $this->language->get('text_powered');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_clear'] = $this->language->get('button_clear');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_apply'] = $this->language->get('button_apply');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->error['bg'])) {
			$data['error_bg'] = $this->error['bg'];
		} else {
			$data['error_bg'] = '';
		}

		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/fs_imagecrop', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/fs_imagecrop', 'user_token=' . $this->session->data['user_token'], true);
		$data['apply'] = $this->url->link('extension/module/fs_imagecrop', 'user_token=' . $this->session->data['user_token'].'&apply', true);
		$data['clear'] = $this->url->link('extension/module/fs_imagecrop/clear', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_fs_imagecrop_active'])) {
			$data['module_fs_imagecrop_active'] = $this->request->post['module_fs_imagecrop_active'];
		} else {
			$data['module_fs_imagecrop_active'] = $this->config->get('module_fs_imagecrop_active');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_watermark'])) {
			$data['module_fs_imagecrop_watermark'] = $this->request->post['module_fs_imagecrop_watermark'];
		} else {
			$data['module_fs_imagecrop_watermark'] = $this->config->get('module_fs_imagecrop_watermark');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_hide_real_path'])) {
			$data['module_fs_imagecrop_hide_real_path'] = $this->request->post['module_fs_imagecrop_hide_real_path'];
		} else {
			$data['module_fs_imagecrop_hide_real_path'] = $this->config->get('module_fs_imagecrop_hide_real_path');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_bg'])) {
			$data['module_fs_imagecrop_bg'] = str_replace("#", "", $this->request->post['module_fs_imagecrop_bg']);
		} else if ($this->config->get('module_fs_imagecrop_bg')) {
			$data['module_fs_imagecrop_bg'] = $this->config->get('module_fs_imagecrop_bg');
		}else {
			$data['module_fs_imagecrop_bg'] = 'ffffff';
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_pos_x_center'])) {
			$data['module_fs_imagecrop_w_pos_x_center'] = $this->request->post['module_fs_imagecrop_w_pos_x_center'];
		} else {
			$data['module_fs_imagecrop_w_pos_x_center'] = $this->config->get('module_fs_imagecrop_w_pos_x_center');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_category_image'])) {
			$data['module_fs_imagecrop_w_category_image'] = $this->request->post['module_fs_imagecrop_w_category_image'];
		} else {
			$data['module_fs_imagecrop_w_category_image'] = $this->config->get('module_fs_imagecrop_w_category_image');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_product_thumb'])) {
			$data['module_fs_imagecrop_w_product_thumb'] = $this->request->post['module_fs_imagecrop_w_product_thumb'];
		} else {
			$data['module_fs_imagecrop_w_product_thumb'] = $this->config->get('module_fs_imagecrop_w_product_thumb');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_product_popup'])) {
			$data['module_fs_imagecrop_w_product_popup'] = $this->request->post['module_fs_imagecrop_w_product_popup'];
		} else {
			$data['module_fs_imagecrop_w_product_popup'] = $this->config->get('module_fs_imagecrop_w_product_popup');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_product_list'])) {
			$data['module_fs_imagecrop_w_product_list'] = $this->request->post['module_fs_imagecrop_w_product_list'];
		} else {
			$data['module_fs_imagecrop_w_product_list'] = $this->config->get('module_fs_imagecrop_w_product_list');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_product_additional'])) {
			$data['module_fs_imagecrop_w_product_additional'] = $this->request->post['module_fs_imagecrop_w_product_additional'];
		} else {
			$data['module_fs_imagecrop_w_product_additional'] = $this->config->get('module_fs_imagecrop_w_product_additional');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_product_related'])) {
			$data['module_fs_imagecrop_w_product_related'] = $this->request->post['module_fs_imagecrop_w_product_related'];
		} else {
			$data['module_fs_imagecrop_w_product_related'] = $this->config->get('module_fs_imagecrop_w_product_related');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_product_in_compare'])) {
			$data['module_fs_imagecrop_w_product_in_compare'] = $this->request->post['module_fs_imagecrop_w_product_in_compare'];
		} else {
			$data['module_fs_imagecrop_w_product_in_compare'] = $this->config->get('module_fs_imagecrop_w_product_in_compare');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_product_in_wish_list'])) {
			$data['module_fs_imagecrop_w_product_in_wish_list'] = $this->request->post['module_fs_imagecrop_w_product_in_wish_list'];
		} else {
			$data['module_fs_imagecrop_w_product_in_wish_list'] = $this->config->get('module_fs_imagecrop_w_product_in_wish_list');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_product_in_cart'])) {
			$data['module_fs_imagecrop_w_product_in_cart'] = $this->request->post['module_fs_imagecrop_w_product_in_cart'];
		} else {
			$data['module_fs_imagecrop_w_product_in_cart'] = $this->config->get('module_fs_imagecrop_w_product_in_cart');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_pos_x'])) {
			$data['module_fs_imagecrop_w_pos_x'] = $this->request->post['module_fs_imagecrop_w_pos_x'];
		} else if ($this->config->get('module_fs_imagecrop_w_pos_x')) {
			$data['module_fs_imagecrop_w_pos_x'] = $this->config->get('module_fs_imagecrop_w_pos_x');
		}else {
			$data['module_fs_imagecrop_w_pos_x'] = '-20';
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_pos_y_center'])) {
			$data['module_fs_imagecrop_w_pos_y_center'] = $this->request->post['module_fs_imagecrop_w_pos_y_center'];
		} else {
			$data['module_fs_imagecrop_w_pos_y_center'] = $this->config->get('module_fs_imagecrop_w_pos_y_center');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_pos_y'])) {
			$data['module_fs_imagecrop_w_pos_y'] = $this->request->post['module_fs_imagecrop_w_pos_y'];
		} else if ($this->config->get('module_fs_imagecrop_w_pos_y')) {
			$data['module_fs_imagecrop_w_pos_y'] = $this->config->get('module_fs_imagecrop_w_pos_y');
		}else {
			$data['module_fs_imagecrop_w_pos_y'] = '-20';
		}
		
		if (isset($this->request->post['module_fs_imagecrop_quality'])) {
			$data['module_fs_imagecrop_quality'] = $this->request->post['module_fs_imagecrop_quality'];
		} else if ($this->config->get('module_fs_imagecrop_quality')) {
			$data['module_fs_imagecrop_quality'] = $this->config->get('module_fs_imagecrop_quality');
		}else {
			$data['module_fs_imagecrop_quality'] = 100;
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_opacity'])) {
			$data['module_fs_imagecrop_w_opacity'] = $this->request->post['module_fs_imagecrop_w_opacity'];
		} else if ($this->config->get('module_fs_imagecrop_w_opacity')) {
			$data['module_fs_imagecrop_w_opacity'] = $this->config->get('module_fs_imagecrop_w_opacity');
		}else {
			$data['module_fs_imagecrop_w_opacity'] = 0.8;
		}
		
		if (isset($this->request->post['module_fs_imagecrop_w_zoom'])) {
			$data['module_fs_imagecrop_w_zoom'] = $this->request->post['module_fs_imagecrop_w_zoom'];
		} else if ($this->config->get('module_fs_imagecrop_w_zoom')) {
			$data['module_fs_imagecrop_w_zoom'] = $this->config->get('module_fs_imagecrop_w_zoom');
		} else {
			$data['module_fs_imagecrop_w_zoom'] = 0.5;
		}
		
		if (isset($this->request->post['module_fs_imagecrop_cute_category_image'])) {
			$data['module_fs_imagecrop_cute_category_image'] = $this->request->post['module_fs_imagecrop_cute_category_image'];
		} else {
			$data['module_fs_imagecrop_cute_category_image'] = $this->config->get('module_fs_imagecrop_cute_category_image');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_cute_product_thumb'])) {
			$data['module_fs_imagecrop_cute_product_thumb'] = $this->request->post['module_fs_imagecrop_cute_product_thumb'];
		} else {
			$data['module_fs_imagecrop_cute_product_thumb'] = $this->config->get('module_fs_imagecrop_cute_product_thumb');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_cute_product_popup'])) {
			$data['module_fs_imagecrop_cute_product_popup'] = $this->request->post['module_fs_imagecrop_cute_product_popup'];
		} else {
			$data['module_fs_imagecrop_cute_product_popup'] = $this->config->get('module_fs_imagecrop_cute_product_popup');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_cute_product_list'])) {
			$data['module_fs_imagecrop_cute_product_list'] = $this->request->post['module_fs_imagecrop_cute_product_list'];
		} else {
			$data['module_fs_imagecrop_cute_product_list'] = $this->config->get('module_fs_imagecrop_cute_product_list');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_cute_product_additional'])) {
			$data['module_fs_imagecrop_cute_product_additional'] = $this->request->post['module_fs_imagecrop_cute_product_additional'];
		} else {
			$data['module_fs_imagecrop_cute_product_additional'] = $this->config->get('module_fs_imagecrop_cute_product_additional');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_cute_product_related'])) {
			$data['module_fs_imagecrop_cute_product_related'] = $this->request->post['module_fs_imagecrop_cute_product_related'];
		} else {
			$data['module_fs_imagecrop_cute_product_related'] = $this->config->get('module_fs_imagecrop_cute_product_related');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_cute_product_in_compare'])) {
			$data['module_fs_imagecrop_cute_product_in_compare'] = $this->request->post['module_fs_imagecrop_cute_product_in_compare'];
		} else {
			$data['module_fs_imagecrop_cute_product_in_compare'] = $this->config->get('module_fs_imagecrop_cute_product_in_compare');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_cute_product_in_wish_list'])) {
			$data['module_fs_imagecrop_cute_product_in_wish_list'] = $this->request->post['module_fs_imagecrop_w_product_in_wish_list'];
		} else {
			$data['module_fs_imagecrop_cute_product_in_wish_list'] = $this->config->get('module_fs_imagecrop_cute_product_in_wish_list');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_cute_product_in_cart'])) {
			$data['module_fs_imagecrop_cute_product_in_cart'] = $this->request->post['module_fs_imagecrop_cute_product_in_cart'];
		} else {
			$data['module_fs_imagecrop_cute_product_in_cart'] = $this->config->get('module_fs_imagecrop_cute_product_in_cart');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_ah_category_image'])) {
			$data['module_fs_imagecrop_ah_category_image'] = $this->request->post['module_fs_imagecrop_ah_category_image'];
		} else {
			$data['module_fs_imagecrop_ah_category_image'] = $this->config->get('module_fs_imagecrop_ah_category_image');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_ah_product_thumb'])) {
			$data['module_fs_imagecrop_ah_product_thumb'] = $this->request->post['module_fs_imagecrop_ah_product_thumb'];
		} else {
			$data['module_fs_imagecrop_ah_product_thumb'] = $this->config->get('module_fs_imagecrop_ah_product_thumb');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_ah_product_popup'])) {
			$data['module_fs_imagecrop_ah_product_popup'] = $this->request->post['module_fs_imagecrop_ah_product_popup'];
		} else {
			$data['module_fs_imagecrop_ah_product_popup'] = $this->config->get('module_fs_imagecrop_ah_product_popup');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_ah_product_list'])) {
			$data['module_fs_imagecrop_ah_product_list'] = $this->request->post['module_fs_imagecrop_ah_product_list'];
		} else {
			$data['module_fs_imagecrop_ah_product_list'] = $this->config->get('module_fs_imagecrop_ah_product_list');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_ah_product_additional'])) {
			$data['module_fs_imagecrop_ah_product_additional'] = $this->request->post['module_fs_imagecrop_ah_product_additional'];
		} else {
			$data['module_fs_imagecrop_ah_product_additional'] = $this->config->get('module_fs_imagecrop_ah_product_additional');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_ah_product_related'])) {
			$data['module_fs_imagecrop_ah_product_related'] = $this->request->post['module_fs_imagecrop_ah_product_related'];
		} else {
			$data['module_fs_imagecrop_ah_product_related'] = $this->config->get('module_fs_imagecrop_ah_product_related');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_ah_product_in_compare'])) {
			$data['module_fs_imagecrop_ah_product_in_compare'] = $this->request->post['module_fs_imagecrop_ah_product_in_compare'];
		} else {
			$data['module_fs_imagecrop_ah_product_in_compare'] = $this->config->get('module_fs_imagecrop_ah_product_in_compare');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_ah_product_in_wish_list'])) {
			$data['module_fs_imagecrop_ah_product_in_wish_list'] = $this->request->post['module_fs_imagecrop_w_product_in_wish_list'];
		} else {
			$data['module_fs_imagecrop_ah_product_in_wish_list'] = $this->config->get('module_fs_imagecrop_ah_product_in_wish_list');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_ah_product_in_cart'])) {
			$data['module_fs_imagecrop_ah_product_in_cart'] = $this->request->post['module_fs_imagecrop_ah_product_in_cart'];
		} else {
			$data['module_fs_imagecrop_ah_product_in_cart'] = $this->config->get('module_fs_imagecrop_ah_product_in_cart');
		}
		
		$this->load->model('tool/image');
		if (isset($this->request->post['module_fs_imagecrop_w_image'])) {
			$watermark_image = $this->request->post['module_fs_imagecrop_w_image'];
		} else if ($this->config->get('module_fs_imagecrop_w_image')) {
			$watermark_image = $this->config->get('module_fs_imagecrop_w_image');
		} else {
			$watermark_image = 'catalog/watermark_default.png';
		}
		$data['module_fs_imagecrop_w_image'] = $watermark_image;
		
		$data['thumb'] = $this->model_tool_image->resize($watermark_image, 100, 100);
		$data['placeholder'] = $this->model_tool_image->resize($watermark_image, 250, 250);
		
		

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/fs_imagecrop', $data));
	}
	public function clear () {
		$this->load->language('extension/module/fs_imagecrop');
		$this->clear_images();
		$this->session->data['success'] = $this->language->get('text_success_clear_cache');
		$this->response->redirect($this->url->link('extension/module/fs_imagecrop', 'user_token=' . $this->session->data['user_token'], true));
	}
	
	
	private function clear_images() {
		if ($this->validate()) {
			// Clear cache
			$files = glob(DIR_CACHE . 'cache.*');
			
			foreach($files as $file){
				$this->deldir($file);
			}
				
			// Clear image cache
			$imgfiles = glob(DIR_IMAGE . 'cache/*');
			foreach($imgfiles as $imgfile){
				$this->deldir($imgfile);
			}
		}	
	}
	
	private function deldir($file){         
			if(file_exists($file)) {
				if(is_dir($file)){
					$dir = opendir($file);
					while($filename = readdir($dir)){
						if($filename != "." && $filename != "..") {

							$this->deldir($file . "/" . $filename); 
						}
					}
					closedir($dir);  
					rmdir($file);
				}else if (basename($file) != 'index.html') {
					@unlink($file);
				}			
			}
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/fs_imagecrop')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (isset($this->request->post['module_fs_imagecrop_bg']) && utf8_strlen($this->request->post['module_fs_imagecrop_bg']) <> 6) {
			$this->error['bg'] = $this->language->get('error_bg');
		}

		return !$this->error;
	}
}