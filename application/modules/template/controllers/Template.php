<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Template extends MX_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	function template($data = NULL)
	{
		$this->load->view("public/template_v", $data);
	}

	function dashboard($data = NULL)
	{
		$this->load->view("dashboard/template_v", $data);
	}
}