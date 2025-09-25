<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gbp_c extends MX_Controller {
    function __construct() 
    {
        parent::__construct();

        $this->load->module('template');
    }

    function gbpmarketing()
    {
        $data['title']          =   'GBP';
        $data['content_view']   =   'user/gbp/gbp';

        $this->template->dashboard($data);
    }
    
    
    function keywordranking()
    {
        $data['title']          =   'Keyword Ranking';
        $data['content_view']   =   'user/gbp/keywordranking';

        $this->template->dashboard($data);
    }
    
    function citation()
    {
        $data['title']          =   'Citations';
        $data['content_view']   =   'user/gbp/citation';

        $this->template->dashboard($data);
    }

    // function portfolio_detail()
    // {
    //     $data['title']          =   'Invoice Detail';
    //     $data['content_view']   =   'user/invoice/invoicepreview';

    //     $this->template->dashboard($data);
    // }
    
    // function create_portfolio() 
    // {
    //     $data['title']          =   'Create Portfolio';
    //     $data['content_view']   =   'user/invoice/createinvoice';

    //     $this->template->dashboard($data);
    // }
}