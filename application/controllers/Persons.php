<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Person Controller
 *
 * @author       : Rodrigo Pereira da Luz
 * e-mail        : rodrigopluz@gmail.com
 * specification : Class Person Controller
 */
class Persons extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Person');
	}

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('person');
	}

	public function ajax_list()
	{
		$data = [];
		$list = $this->Person->get_datatables();

		$no = $_POST['start'];
		
		foreach ($list as $person) {
			$no++;
			$row = [];
			$row[] = $person->firstName;
			$row[] = $person->lastName;
			$row[] = ($person->gender == 'male') ? 'Masculino' : 'Feminino';
			$row[] = $person->address;
			$row[] = date('d-m-Y', strtotime(str_replace('-', '-', $person->dob)));

			$row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" onClick="edit_person('. $person->id .')"><i class="glyphicon glyphicon-pencil"></i> Edita</a>
				  	  <a class="btn btn-sm btn-danger" href="javascript:void(0)" onClick="delete_person('. $person->id .')"><i class="glyphicon glyphicon-trash"></i> Deleta</a>';
		
			$data[] = $row;
		}

		$output = [
			"data" => $data,
			// "draw" => $_POST['draw'],
			"recordsTotal" => $this->Person->count_all(),
			"recordsFiltered" => $this->Person->count_filtered(),
		];

		echo json_encode($output);
	}

	public function ajax_edit($id)
	{
		$data = $this->Person->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_add()
	{
		$data_nasc = date('Y-m-d', strtotime(str_replace('-', '-', $this->input->post('dob'))));

		$data = [
			'firstName' => $this->input->post('firstName'),
			'lastName' => $this->input->post('lastName'),
			'address' => $this->input->post('address'),
			'gender' => $this->input->post('gender'),
			'dob' => $data_nasc,
		];

		$this->Person->save($data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_update()
	{
		$data_nasc = date('Y-m-d', strtotime(str_replace('-', '-', $this->input->post('dob'))));

		$data = [
			'firstName' => $this->input->post('firstName'),
			'lastName' => $this->input->post('lastName'),
			'address' => $this->input->post('address'),
			'gender' => $this->input->post('gender'),
			'dob' => $data_nasc,
		];
		
		$this->Person->update(['id' => $this->input->post('id')], $data);
		echo json_encode(["status" => TRUE]);
	}

	public function ajax_delete($id)
	{
		$this->Person->delete_by_id($id);
		echo json_encode(["status" => TRUE]);
	}
}
