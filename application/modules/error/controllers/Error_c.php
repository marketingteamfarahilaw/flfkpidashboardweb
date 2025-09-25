<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Error_c extends MX_Controller {
	function __construct() 
	{
		parent::__construct();
	}

	function error_404() 
	{
		$data['title']			=	'Home';
		show_404();
	}
}