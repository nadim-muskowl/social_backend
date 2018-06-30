<?php

class Custom_image {

    private $ci;
    private $source_image;
    private $new_image;
    private $new_image_path;
    private $width;
    private $height;
    private $code;
    private $size;

    public function __construct() {
        $this->ci = & get_instance();
        $this->ci->load->library('image_lib');
        $this->ci->image_lib->clear();
    }

    public function image_resize($image, $width, $height) {
        $this->ci->image_lib->clear();
        if (!$image) {
            return NULL;
        }
        $this->width = $width;
        $this->height = $height;
        $this->source_image = $this->get_path($image);

        $x = explode('/', $this->source_image);
        $source_image = end($x);

        $source_folder = str_replace($source_image, '', $this->source_image);

        $ext = strrchr($source_image, '.');
        $name = ($ext === FALSE) ? $source_image : substr($source_image, 0, -strlen($ext));

        $source_folder_url = 'cache/' . $source_folder;
        $source_folder_path = $this->ci->config->item('keycdn_path') . 'cache/' . $source_folder;


        if (!is_dir($source_folder_path)) {
            mkdir($source_folder_path, 0755, TRUE);
        }

        $this->new_image = $source_folder_url . $name . $this->width . 'X' . $this->height . $ext;

        $this->new_image_path = $source_folder_path . $name . $ext;

        if (!file_exists(base_url() . $this->new_image)) {

            $config['image_library'] = 'gd2';
            $config['source_image'] = $this->source_image;
            $config['new_image'] = $this->new_image_path;
            $config['maintain_ratio'] = TRUE;
            $config['quality'] = 50;
            $config['file_permissions'] = 0755;
            $config['width'] = $this->width;
            $config['height'] = $this->height;
            $config['create_thumb'] = TRUE;
            $config['thumb_marker'] = $this->width . 'X' . $this->height;

            $this->ci->image_lib->initialize($config);

            if (!$this->ci->image_lib->resize()) {
//                print_r($this->ci->image_lib);
//                exit;
                return base_url($this->source_image);
            } else {
//                print_r($this->ci->image_lib);
//                exit;
                $this->ci->image_lib->clear();
                return base_url() . $this->new_image;
            }
        } else {
            return base_url() . $this->new_image;
        }
    }

    public function get_path($image) {
        return str_replace(base_url(), '', $image);
    }

}
