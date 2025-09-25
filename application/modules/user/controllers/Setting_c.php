<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_c extends MX_Controller {
    function __construct() 
    {
        parent::__construct();

        $this->load->module('template');
    }

    function index()
    {
        $data['title']          =   'Settings';
        $data['content_view']   =   'user/settings/setting';

        $this->template->dashboard($data);
    }
}