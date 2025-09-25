<?php defined('BASEPATH') OR exit('No direct script access allowed');

	$this->load->view('template/public/header');
	$this->load->view('template/public/nav');
	
	$this->load->view($content_view);
	
	$this->load->view('template/public/footer');

?>