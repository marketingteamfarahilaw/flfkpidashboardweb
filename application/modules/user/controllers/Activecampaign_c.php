<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Activecampaign_c extends MX_Controller {
    function __construct() 
    {
        parent::__construct();

        $this->load->module('template');
    }

    function activecampaign()
    {
        $data['title']          =   'Active Campaign';
        $data['content_view']   =   'user/activecampaign';

        $this->template->dashboard($data);
    }
    
}