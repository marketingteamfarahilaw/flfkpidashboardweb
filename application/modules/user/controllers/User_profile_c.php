<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_profile_c extends MX_Controller {
	function __construct() 
	{
		parent::__construct();

		$this->load->module('template');
	}

	function profile()
	{
		$data['title']			=	'My Profile';
		$data['content_view'] 	= 	'user/profile/profile';

		$this->template->dashboard($data);
	}

    function profile_user($id = null)
    {
        $data['title']          =   'Profile';
        $data['content_view']   =   'user/profile/profile_user';

        $this->template->dashboard($data);
    }

	function profile_update()
	{
		$this->load->library(['form_validation']);
		$this->form_validation->CI =& $this;

		$data['title']			=	'Update Profile';
		$data['content_view'] 	= 	'user/profile/profile_update';

		$this->template->dashboard($data);
	}
}