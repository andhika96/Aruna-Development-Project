<?php

class home extends Aruna_Controller
{
	public function __construct()
	{
		parent::__construct();

		// $this->load->library('input');

		// $this->load->model('Home');

		$this->load->model('test');

		$this->load->model('test2');

		$this->offset = offset();

		$this->num_per_page = num_per_page();
	}

	public function index()
	{
		load_extend_view('default', ['header', 'footer']);

		// redirect(site_url('home/test'));

		// $this->form_validation->set_rules('fullname', 'Fullname', 'required|is_unique[default.ml_accounts.fullname]',
											// ['is_unique' => 'This %s already exists.']);

		// $res =  $this->db->sql_select("select * from ml_accounts order by id desc");
		// $row =  $this->db->sql_fetch($res);

		// print_r($row);

		// print_r($this->test->getListOfUsers());
		print_r($this->test2->getListOfUsers());

		// echo get_data_global('using_other_dblib');

		// if ($this->form_validation->run() == FALSE)
		// {
		// 	echo 'Error: '.$this->form_validation->validation_errors();
		// }
		
		// echo $this->input->get('name');

		// $data['row'] = 'data';

		// return view('index', $data, FALSE);

		// print_r($GLOBALS['segments']);

		
		$res_getTotal = $this->db->sql_select("select count(*) as num from ml_accounts");
		$row_getTotal = $this->db->sql_fetch_single($res_getTotal);

		$totalpage = ceil($row_getTotal['num']/$this->num_per_page);

		$currentpage = ($this->input->get('page') == 1) ? '' : $this->input->get('page');
		$currentpage = ($this->input->get('page') != null) ? $this->input->get('page') : 1;

		$res =  $this->db->sql_select("select * from ml_accounts order by id desc limit $this->offset, $this->num_per_page");
		$row =  $this->db->sql_fetch($res);
		

		// $pagination = load_lib('pagination', array($row_getTotal['num']));
		// $pagination->paras = site_url('home/index?status=success');

		// echo $pagination->whole_num_bar('justify-content-center');

		
		$config['base_url']		= 'home/index';
		$config['total_rows'] 	= $row_getTotal['num'];
		$config['style_class']	= 'justify-content-center';

		$data['row'] = $row;
		$data['config'] = $config;

		return view('index', $data, FALSE);
		

		// echo pagination($config);
	}

	public function test()
	{
		echo 'Kwkwkwkwkwk Land';
	}

	public function detail($category, $id)
	{
		// echo $category.' => '.$id;

		print_r($GLOBALS['segments']);
	}

	public function asd()
	{
		phpinfo();
	}
}

?>