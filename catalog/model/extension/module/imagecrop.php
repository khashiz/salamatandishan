<?php
class ModelExtensionModuleImagecrop extends Model {
	private $image;
  private $width;
  private $height;
  private $bits;
  private $mime;
	
	function resize ($filename, $newWidth, $newHeight, $image_type,$degrees = false, $c = array()) 
	{

		if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
			return;
		}
		
		$info = pathinfo($filename);
		$extension = $info['extension'];
		
		if('svg' == $extension) {
			if ($this->request->server['HTTPS']) {
				return HTTPS_SERVER . 'image/' . $filename;
			} else {
				return HTTP_SERVER . 'image/' . $filename;
			}
		}
		
		$rgb = ($this->config->get('module_fs_imagecrop_bg')) ? $this->config->get('module_fs_imagecrop_bg') : 'FFFFFF';
		$quality = $this->config->get('module_fs_imagecrop_quality');
		
		$old_image = $filename;
		
		if($this->config->get('module_fs_imagecrop_hide_real_path') == 1) {
            $new_image = 'cache/' . md5(utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$newWidth . 'x' . (int)$newHeight . '-' . $image_type) . '.' . $extension;
        } else {
            $new_image = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . (int)$newWidth . 'x' . (int)$newHeight . '-' . $image_type . '.' . $extension;
		}
		
		if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {

			$path = '';
			
			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
			
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
				
				if (!file_exists(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}		
			}
			
			if ($this->img_tpl_resize(
						DIR_IMAGE . $filename, 
						DIR_IMAGE . $new_image, 
						$newWidth, 
						$newHeight, 
						$rgb, 
						$quality, 
						$image_type,
						$degrees) === false) {
			
				return 'error on creating thumbnail!';
				
			}
		}
		

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			return HTTPS_SERVER . 'image/' . $new_image;
		} else {
			return HTTP_SERVER . 'image/' . $new_image;
		}

	}
	
	private function img_tpl_resize($filePath, $dest, $width, $height, $rgb, $quality, $image_type, $degrees = false) {
	
		$size = getimagesize($filePath);
		
		if ($size === false) {
			return false;
		}
		
		$this->width  = $size[0];
        $this->height = $size[1];
        $this->bits = isset($size['bits']) ? $size['bits'] : '';
        $this->mime = isset($size['mime']) ? $size['mime'] : '';
		
		$types = array(
            'category_image' => 'category_image',
            'product_thumb' => 'product_thumb',
            'product_popup' => 'product_popup',
            'product_list' => 'product_list',
            'product_additional' => 'product_additional',
            'product_related' => 'product_related',
            'product_in_compare' => 'product_in_compare',
            'product_in_wish_list' => 'product_in_wish_list',
            'product_in_cart' => 'product_in_cart',
        );
	 
		$format = strtolower(substr($this->mime, strpos($this->mime, '/') + 1));
		$icfunc = 'imagecreatefrom'.$format;
	 
		if (function_exists($icfunc) === false) {
			return false;
		}
			
		$left_slide = 0;
		$top_slide = 0;
		
		
		$x_ratio = $width  / $this->width;
		$y_ratio = $height / $this->height;	
		if (in_array($image_type, $types) && $this->config->get('module_fs_imagecrop_ah_'.$image_type) == 1) {
			$height = 0;
		}
		if ($height == 0) { 
			# is height = 0, then thumbnail use width
			$y_ratio = $x_ratio;
			$height  = $y_ratio * $this->height;
		} elseif ($width == 0) { 
			# is width = 0, then thumbnail use height
			$x_ratio = $y_ratio;
			$width   = $x_ratio * $this->width;
		}
		
		if (in_array($image_type, $types) && $this->config->get('module_fs_imagecrop_cute_'.$image_type) == 1) {
			$ratio       = max($x_ratio, $y_ratio);
		} else {
			$ratio       = min($x_ratio, $y_ratio);
		}
		
		$use_x_ratio = ($x_ratio == $ratio);
		$new_width   = $use_x_ratio  ? $width  : floor($this->width * $ratio);
		$new_height  = !$use_x_ratio ? $height : floor($this->height * $ratio);
		
		
		if (in_array($image_type, $types) && $this->config->get('module_fs_imagecrop_cute_'.$image_type) == 1) {
			$new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width)   / 2);
			$new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);
			
		} else {
			$new_left    = (int)(($width - $new_width)   / 2);
			$new_top     = (int)(($height - $new_height) / 2);
		}
		
		$isrc  = $icfunc($filePath);
		$this->image = imagecreatetruecolor($width, $height);
		
		$fill_color = $this->hex2rgb($rgb);
		
		$rgb_fill = imagecolorallocate($this->image, $fill_color['red'], $fill_color['green'], $fill_color['blue']);
		imagefill($this->image, 0, 0, $rgb_fill);

		imagecopyresampled($this->image, $isrc, $new_left, $new_top, $left_slide, $top_slide, $new_width, $new_height, $this->width, $this->height);
		
		/* if (is_numeric($degrees)) {
			$this->image = imagerotate($this->image, $degrees, 0);
		} */
		
		
		if($this->config->get('module_fs_imagecrop_watermark') && in_array($image_type, $types) && $this->config->get('module_fs_imagecrop_w_'.$image_type) == 1) {
			$popup_width = $this->config->get('theme_' .$this->config->get('config_theme') . '_image_popup_width');
			$popup_height = $this->config->get('theme_' .$this->config->get('config_theme') . '_image_popup_height');
			$watermark_ratio = max($width,$height)/max($popup_width,$popup_height);
			$watermark_settings = array(
				'image'	=> 			$this->config->get('module_fs_imagecrop_w_image'),
				'zoom'	=> 			$this->config->get('module_fs_imagecrop_w_zoom'),
				'pos_x_center'	=> 	$this->config->get('module_fs_imagecrop_w_pos_x_center'),
				'pos_x'	=> 			$this->config->get('module_fs_imagecrop_w_pos_x'),
				'pos_y_center'	=> 	$this->config->get('module_fs_imagecrop_w_pos_y_center'),
				'pos_y'	=> 			$this->config->get('module_fs_imagecrop_w_pos_y'),
				'opacity'	=> 		$this->config->get('module_fs_imagecrop_w_opacity'),
			);
			$this->watermark($width,$height,$watermark_ratio,$watermark_settings);
		}
		
		if (imagejpeg($this->image, $dest, $quality) === false) {
			return false;
		}
		imagedestroy($isrc);
		imagedestroy($this->image);
	 
		return true;
	}
	
	public function watermark($image_width,$image_height,$watermark_ratio,$config) {
        $file = DIR_IMAGE . $config['image'];
		
		$info = getimagesize($file);
        $width  = $info[0];
        $height = $info[1];
        $bits = isset($info['bits']) ? $info['bits'] : '';
        $mime = isset($info['mime']) ? $info['mime'] : '';

        if ($mime == 'image/gif') {
            $watermark = imagecreatefromgif($file);
        } elseif ($mime == 'image/png') {
            $watermark = imagecreatefrompng($file);
        } elseif ($mime == 'image/jpeg') {
            $watermark = imagecreatefromjpeg($file);
        }
		
		if((float)$config['zoom'] !== 1 || $watermark_ratio !== 1) {
            $new_width = (int)($width * (float)$config['zoom'] * (float)$watermark_ratio);
            $new_height = (int)($height * (float)$config['zoom'] * (float)$watermark_ratio);

            $image_old = $watermark;
            $watermark = imagecreatetruecolor($new_width, $new_height);

            if ($mime == 'image/png') {
                imagealphablending($watermark, false);
                imagesavealpha($watermark, true);
                $background = imagecolorallocatealpha($watermark, 255, 255, 255, 0);
                imagecolortransparent($watermark, $background);
            } else {
                $background = imagecolorallocate($this->image, 255, 255, 255);
            }

            imagefilledrectangle($watermark, 0, 0, $width, $height, $background);

            imagecopyresampled($watermark, $image_old, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagedestroy($image_old);
            $width = $new_width;
            $height = $new_height;
        }
		
		

        $watermark_pos_x = 10;
        $watermark_pos_y = 10;

        if((int)$config['pos_x_center'] == 1) {
            $watermark_pos_x = (int)($image_width/2 - $width/2);
        } elseif((int)$config['pos_x'] < 0) {
            $watermark_pos_x = $image_width - $width + (int)$config['pos_x'];
        } else {
            $watermark_pos_x = (int)$config['pos_x'];
        }

		if((int)$config['pos_y_center'] == 1) {
            $watermark_pos_y = (int)($image_height/2 - $height/2);
        } elseif((int)$config['pos_y'] < 0) {
            $watermark_pos_y = $image_height - $height + (int)$config['pos_y'];
        } else {
            $watermark_pos_x = (int)$config['pos_y'];
        }
		
		$watermark_width = $width;
        $watermark_height = $height;
        $opacity = (int)((float)$config['opacity'] * 127);
		$this->imagecopymergealpha($this->image, $watermark, $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height,$opacity);

        imagedestroy($watermark);
    }
	
	public static function imagecopymergealpha(&$destImg, &$srcImg, $destX, $destY, $srcX, $srcY, $srcW, $srcH, $pct = 0) {
		
        $destX = (int) $destX;
        $destY = (int) $destY;
        $srcX = (int) $srcX;
        $srcY = (int) $srcY;
        $srcW = (int) $srcW;
        $srcH = (int) $srcH;
        $pct = (int) $pct;
        $destW = imageSX($destImg);
        $destH = imageSY($destImg);
		for ($y = 0; $y < $srcH + $srcY; $y++) {

            for ($x = 0; $x < $srcW + $srcX; $x++) {

                if ($x + $destX >= 0 && $x + $destX < $destW && $x + $srcX >= 0 && $x + $srcX < $srcW && $y + $destY >= 0 && $y + $destY < $destH && $y + $srcY >= 0 && $y + $srcY < $srcH) {

                    $destPixel = imageColorsForIndex($destImg, imageColorat($destImg, $x + $destX, $y + $destY));
                    $srcImgColorat = imageColorat($srcImg, $x + $srcX, $y + $srcY);
                    
                    if ($srcImgColorat >= 0) {
                    
                        $srcPixel = imageColorsForIndex($srcImg, $srcImgColorat);
    
                        $srcAlpha = 1 - ($srcPixel['alpha'] / 127);
                        $destAlpha = 1 - ($destPixel['alpha'] / 127);
                        $opacity = $srcAlpha * $pct / 100;
    
                        if ($destAlpha >= $opacity) {
                            $alpha = $destAlpha;
                        }
    
                        if ($destAlpha < $opacity) {
                            $alpha = $opacity;
                        }
    
                        if ($alpha > 1) {
                            $alpha = 1;
                        }
    
                        if ($opacity > 0) {
                            
                            $destRed = round((($destPixel['red'] * $destAlpha * (1 - $opacity))));
                            $destGreen = round((($destPixel['green'] * $destAlpha * (1 - $opacity))));
                            $destBlue = round((($destPixel['blue'] * $destAlpha * (1 - $opacity))));
                            $srcRed = round((($srcPixel['red'] * $opacity)));
                            $srcGreen = round((($srcPixel['green'] * $opacity)));
                            $srcBlue = round((($srcPixel['blue'] * $opacity)));
                            $red = round(($destRed + $srcRed  ) / ($destAlpha * (1 - $opacity) + $opacity));
                            $green = round(($destGreen + $srcGreen) / ($destAlpha * (1 - $opacity) + $opacity));
                            $blue = round(($destBlue + $srcBlue ) / ($destAlpha * (1 - $opacity) + $opacity));
    
                            if ($red   > 255) {
                                $red   = 255;
                            }
    
                            if ($green > 255) {
                                $green = 255;
                            }
    
                            if ($blue  > 255) {
                                $blue  = 255;
                            }
    
                            $alpha = round((1 - $alpha) * 127);
                            $color = imageColorAllocateAlpha($destImg, $red, $green, $blue, $alpha);
                            imageSetPixel($destImg, $x + $destX, $y + $destY, $color);
                        }
                    }
                }
            }
        }
    }
	
	private function hex2rgb( $colour ) {
        if ( $colour[0] == '#' ) {
                $colour = substr( $colour, 1 );
        }
        if(strlen( $colour ) == 8) {
			list( $r, $g, $b ) = array( $colour[2] . $colour[3], $colour[4] . $colour[5], $colour[6] . $colour[7] );
		} elseif ( strlen( $colour ) == 6 ) {
            list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        } elseif ( strlen( $colour ) == 3 ) {
            list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
        } else {
            return false;
        }
		
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
        return array( 'red' => $r, 'green' => $g, 'blue' => $b );
	}

}