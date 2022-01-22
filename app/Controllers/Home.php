<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Config\Database;

use App\Models\BairroModel;
use App\Models\DistritoModel;
use App\Models\MunicipioModel;
use App\Models\ProvinciaModel;

class Home extends ResourceController
{
    public function __construct()
    {
        // headers
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            exit(0);
        }

        $this->bairro_model = new BairroModel();
        $this->distrito_model = new DistritoModel();
        $this->municipio_model = new MunicipioModel();
        $this->provincia_model = new ProvinciaModel();

        $this->db = Database::connect();
    }

    public function index()
    {
        return $this->respond([
            'Time' => date('Y-m-d H:i:s'),
            'Autor' => "Arsénio Muanda",
            'Company' => 'ArsenioVZM',
            'Project' => 'Angola API'
        ]);
    }

    public function all($cod)
    {
        $data = [
            'provincia' => $this->provincia_model->paginate(100),
            'municipio' => $this->municipio_model->paginate(1000),
            'distrito' => $this->distrito_model->paginate(1000),
            'bairro' => $this->bairro_model->paginate(1000)
        ];
        return $this->respond($data);
    }

    public function provincia($cod)
    {
        $data = [
            'provincia' => $this->provincia_model->where('cod', $cod)->first(),
            'municipio' => $this->municipio_model->where('provincia_cod', $cod)->paginate(1000)
        ];
        return $this->respond($data);
    }

    public function municipio($cod)
    {
        $data = [
            'municipio' => $this->municipio_model->where('cod', $cod)->first(),
            'distrito' => $this->distrito_model->where('municipio_cod', $cod)->paginate(1000)
        ];
        return $this->respond($data);
    }

    public function distrito($cod)
    {
        $data = [
            'distrito' => $this->distrito_model->where('cod', $cod)->first(),
            'bairro' => $this->bairro_model->where('distrito_cod', $cod)->paginate(1000)
        ];
        return $this->respond($data);
    }
}
