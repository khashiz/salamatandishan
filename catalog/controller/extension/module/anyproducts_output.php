<?php  
class ControllerExtensionModuleAnyproductsOutput extends Controller {
	public function index($setting) {
		
		if ((float)VERSION >= 3.0) {
			$theme_prefix = 'theme_';
		} else {
			$theme_prefix = '';
		}
		
		// Add required sources
		$this->document->addStyle('catalog/view/javascript/anyproducts/anyproducts.css');
		if ($setting['carousel']) {
		$this->document->addScript('catalog/view/javascript/anyproducts/slick.min.js');
		}
		
    	$this->load->model('catalog/product');	
		$this->load->language('extension/module/latest'); // To get the text_tax text
		
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['text_tax'] = $this->language->get('text_tax');
		
		// Block title
		$data['block_title'] = $setting['use_title'];

		$data['title'] = false;
		if (!empty($setting['title_m'][$this->config->get('config_language_id')])) {
		$data['title'] = html_entity_decode($setting['title_m'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}

		$data['carousel'] = $setting['carousel'];
		$data['columns'] = $setting['columns'];
		$data['rows'] = $setting['rows'];
		$data['img_width'] = $setting['image_width'];
		$data['button_cart'] = $this->language->get('button_cart');
		
		static $module = 0;
		
		$data['tabs'] = array();

		$this->load->model('tool/image');
		
		$tabs = $this->config->get('showintabs_tab');
		
		$tabs = isset($tabs) ? $tabs : array();
		
	if (!empty($setting['selected_tabs'])) {
    	foreach ($tabs as $key => $tab) {
			if(in_array($key, $setting['selected_tabs']['tabs'])) {
				if (!empty($tab['title'][$this->config->get('config_language_id')])) {
					$title = $tab['title'][$this->config->get('config_language_id')];
				}else{
					$title = 'Tab';
				}	
	
				$products = array();
	
				switch ($tab['data_source']) {
					case 'SP': //Select Products
						$results = $this->getSelectProducts($tab,$setting['limit']);
						break;
					case 'PG': //Product Group
						$results = $this->getProductGroups($tab,$setting['limit']);
						break;
					case 'CQ': //Custom Query
						$results = $this->getCustomQuery($tab,$setting['limit']);
						break;
					default:
						$this->log->write('SHOW_IN_TAB::ERROR: The tab don\'t have product configured.');
						break;
				}
				
	
				foreach ($results as $result) {
					if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
					} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['image_width'], $setting['image_height']);
					}
					
					$images = $this->model_catalog_product->getProductImages($result['product_id']);
					if(isset($images[0]['image']) && !empty($images[0]['image'])){
					$images =$images[0]['image'];
				   	} else {
					$images = false;
					}
					
					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}
							
					if ((float)$result['special']) {
						$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}
					
					if ($this->config->get('config_review_status')) {
						$rating = $result['rating'];
					} else {
						$rating = false;
					}
					
					$products[] = array(
						'product_id' => $result['product_id'],
						'quantity'  => $result['quantity'],
						'thumb'   	 => $image,
						'name'    	 => $result['name'],
						'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get($theme_prefix . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'   	 => $price,
						'special' 	 => $special,
						'tax'        => $tax,
						'minimum'    => $result['minimum'] > 0 ? $result['minimum'] : 1,
						'rating'     => $rating,
						'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
						'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					);
				}	

				$data['tabs'][] = array(
					'title' => $title,
					'products' => $products
				);
			}
    	}
	}
		$data['module'] = $module++;

		return $this->load->view('extension/module/anyproducts_output', $data);
		
  	}

  	// Obtiene los productos de los grupos predefinidos de opencart
  	private function getProductGroups( $tabInfo , $limit ){
  		$results = array();

  		//Obtengo listado de productos en funcion del criterio
  		switch ( $tabInfo['product_group'] ) {
  			case 'BS':
  				$results = $this->model_catalog_product->getBestSellerProducts($limit);
  				break;
  			case 'LA':
  				$results = $this->model_catalog_product->getLatestProducts($limit);
  				break;
  			case 'SP':
  				$results = $this->model_catalog_product->getProductSpecials(array('start' => 0,'limit' => $limit));
  				break;
  			case 'PP':
  				$results = $this->model_catalog_product->getPopularProducts($limit);
  				break;
  		}
  		return $results;
  	}

	// Obtiene los productos seleccionados por el user con toda la info necesaria
  	private function getSelectProducts( $tabInfo , $limit ){
  		$results = array();

  		if(isset($tabInfo['products'])){
  			$limit_count = 0;
			foreach ( $tabInfo['products'] as $product ) {
				if ($limit_count++ == $limit) break;
				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
				if ($product_info) {
					$results[$product['product_id']] = $this->model_catalog_product->getProduct($product['product_id']);
				}
			}
		}

		return $results;

  	}

  	//Obtiene los productos segun los datos del custom query
  	private function getCustomQuery( $tabInfo , $limit){
  		$results = array();

  		if ( $tabInfo['sort'] == 'rating' || $tabInfo['sort'] == 'p.date_added') {
  			$order = 'DESC';
  		}else{
  			$order = 'ASC';
  		}

  		$data = array(
  			'filter_category_id' => $tabInfo['filter_category']=='ALL' ? '' : $tabInfo['filter_category'], 
  			'filter_manufacturer_id' => $tabInfo['filter_manufacturer']=='ALL' ? '' : $tabInfo['filter_manufacturer'], 
  			'sort' => $tabInfo['sort'], 
  			'order' => $order,
  			'start' => 0,
  			'limit' => $limit
  		);

  		$results = $this->model_catalog_product->getProducts($data);

		return $results;
  	}

}