<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Public_page_c extends MX_Controller {
	function __construct() 
	{
		parent::__construct();

		$this->load->module('template');
	}

	function home() 
	{
		$data['title']			=	'Home';
		$data['content_view'] 	= 	'public/home';

		$this->template->template($data);
	}

	function about() 
	{
		$data['title']			=	'About Us';
		$data['content_view'] 	= 	'public/about';

		$this->template->template($data);
	}

	function login() 
	{
		$this->load->library(['form_validation']);
		$this->form_validation->CI =& $this;

		$data['title']			=	'Login';

		$this->load->view('public/login', $data);
	}

	function register() 
	{		
		$this->load->library(['form_validation']);
		$this->form_validation->CI =& $this;

		$data['title']			=	'Registration';

		$this->load->view('public/registration', $data);
	}
}