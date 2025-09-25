<?php defined('BASEPATH') OR exit('No direct script access allowed');

	$this->load->view('header');
	$this->load->view('nav');
	
	$this->load->view($content_view);
	
	$this->load->view('footer');

?>