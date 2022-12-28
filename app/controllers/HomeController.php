<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Helpers\Utils;
use App\Helpers\Redirect;
use App\Helpers\Session;



// controlador para manejar la lógica de negocio
class HomeController {
	// constructor de la clase
  	public function __construct() {
    	$this->userMmodel = new UserModel();
    	$this->session = new Session();
  	}

	public function index()
	{
		if (!$this->userMmodel->authCheck()) {
			Redirect::to('/iniciar-sesion');
		}

		//$this->session->destroy();
		$data['title'] = 'Bienvenido';
		$data['page'] = 'Bienvenido';
		$data['titlef'] = 'Bienvenido';
		$data['pagef'] = $dddd;
		$data['titleg'] = 'Bienvenido';
		$data['pageh'] = $this->userMmodel->isLoggedIn();

	    echo view('usuarios/list', $data);
	}

	public function login()
	{
		
	}
	
	public function error404()
	{
		$data['title'] = 'error404';
		$data['page'] = 'Bienvenido';
		$data['titlef'] = 'Bienvenido';
		$data['pagef'] = 'Bienvenido';
		$data['titleg'] = 'Bienvenido';
		$data['pageh'] = 'Bienvenido';
		
	    echo view('usuarios/list', $data);
	}

	
}
