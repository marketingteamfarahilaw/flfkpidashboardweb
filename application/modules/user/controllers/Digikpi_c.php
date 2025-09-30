<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Digikpi_c extends MX_Controller {
    function __construct() 
    {
        parent::__construct();

        $this->load->module('template');
    }

    function dashboard()
    {
        $data['title']          =   'Dashboard';
        $data['content_view']   =   'user/marketing_kpi/dashboard';

        $this->template->dashboard($data);
    }
    function eowmarketingkpi()
    {
        $data['title']          =   'EOW Digital Marketing KPI';
        $data['content_view']   =   'user/marketing_kpi/eowdigikpi';

        $this->template->dashboard($data);
    }
    function eommarketingkpi()
    {
        $data['title']          =   'EOM Digital Marketing KPI';
        $data['content_view']   =   'user/marketing_kpi/eomdigikpi';

        $this->template->dashboard($data);
    }
    function kpi()
    {
        $data['title']          =   'Department Marketing KPI';
        $data['content_view']   =   'user/marketing_kpi/kpi';

        $this->template->dashboard($data);
    }
    
    function asana()
    {
        $data['title']          =   'Asana';
        $data['content_view']   =   'user/asana';

        $this->template->dashboard($data);
    }
    function webdev()
    {
        $data['title']          =   'WebDev Team';
        $data['content_view']   =   'user/webdev';

        $this->template->dashboard($data);
    }
    function socmed()
    {
        $data['title']          =   'SocMed Team';
        $data['content_view']   =   'user/socmed';

        $this->template->dashboard($data);
    }
    function tlc()
    {
        $data['title']          =   'TLC';
        $data['content_view']   =   'user/tlc';

        $this->template->dashboard($data);
    }
    function content()
    {
        $data['title']          =   'Content Team';
        $data['content_view']   =   'user/content';

        $this->template->dashboard($data);
    }
    
    
    function analyticsengagement()
    {
        $data['title']          =   'Google Analytics Engagement';
        $data['content_view']   =   'user/analyticsengagement';

        $this->template->dashboard($data);
    }

    function analyticsacquisition()
    {
        $data['title']          =   'Google Analytics Acquisition';
        $data['content_view']   =   'user/analyticsacquisition';

        $this->template->dashboard($data);
    }

    function leaddocket()
    {
        $data['title']          =   'Lead Docker Tracker';
        $data['content_view']   =   'user/leaddocket';

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