<?php

$CI =& get_instance();
if($CI->config->item('is_admin_url'))
{ 
	///// admin paging ///////
	$config["full_tag_open"] = '<ul class="pagination">';
	$config["full_tag_close"] = '</ul>';	
	$config["first_link"] = '<i class="fa fa-angle-double-left"></i>';
	$config["first_tag_open"] = "<li>";
	$config["first_tag_close"] = "</li>";
	$config["last_link"] = '<i class="fa fa-angle-double-right"></i>';
	$config["last_tag_open"] = "<li>";
	$config["last_tag_close"] = "</li>";
	$config['next_link'] = '<i class="fa fa-angle-right"></i>';
	$config['next_tag_open'] = '<li>';
	$config['next_tag_close'] = '<li>';
	$config['prev_link'] = '<i class="fa fa-angle-left"></i>';
	$config['prev_tag_open'] = '<li>';
	$config['prev_tag_close'] = '<li>';
	$config['cur_tag_open'] = '<li class="active"><a class="active" href="javascript:">';
	$config['cur_tag_close'] = '</a></li>';
	$config['num_tag_open'] = '<li>';
	$config['num_tag_close'] = '</li>';
	$config['query_string_segment'] = 'page_no';
	$config['use_page_numbers'] = true;
	$config['page_query_string'] = true;
}
else
{
	///// front end paging ///////
	$config["full_tag_open"] = '<ul class="pagination">';
	$config["full_tag_close"] = '</ul>';	
	$config["first_link"] = '<i class="fa fa-angle-double-left"></i>';
	$config["first_tag_open"] = "<li>";
	$config["first_tag_close"] = "</li>";
	$config["last_link"] = '<i class="fa fa-angle-double-right"></i>';
	$config["last_tag_open"] = "<li>";
	$config["last_tag_close"] = "</li>";
	$config['next_link'] = '<i class="fa fa-angle-right"></i>';
	$config['next_tag_open'] = '<li>';
	$config['next_tag_close'] = '<li>';
	$config['prev_link'] = '<i class="fa fa-angle-left"></i>';
	$config['prev_tag_open'] = '<li>';
	$config['prev_tag_close'] = '<li>';
	$config['cur_tag_open'] = '<li class="active"><a class="active" href="javascript:">';
	$config['cur_tag_close'] = '</a></li>';
	$config['num_tag_open'] = '<li>';
	$config['num_tag_close'] = '</li>';
	$config['query_string_segment'] = 'page_no';
	$config['use_page_numbers'] = true;
	$config['page_query_string'] = true;
}

