<?php

namespace App\Controllers;

use App\Models\MunicipioModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Config\Database;

use App\Models\ProvinciaModel;

class Provincia extends ResourceController
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

        $this->model = new ProvinciaModel();
        $this->municipio_model = new MunicipioModel();
        $this->db = Database::connect();
    }

    public function index()
    {
        $data = json_decode(file_get_contents("php://input"));
        helper('funcao');

        if (isset($data)  && (count($data) > 1)) {
            foreach ($data as $value) {
                $data = [
                    'cod' => newGuid(),
                    'nome' => isset($value->nome) ? $value->nome : null,
                    'estado' => isset($value->estado) ? $value->estado : null
                ];

                $data = cadastronormal($this->model, $data, $this->db);
            }
        } else if (isset($data)) {
            $data = [
                'cod' => newGuid(),
                'nome' => isset($data->nome) ? $data->nome : null,
                'estado' => isset($data->estado) ? $data->estado : null
            ];

            $data = cadastronormal($this->model, $data, $this->db);
        }

        $data = $this->model->paginate();
        return $this->respond($data);
    }

    public function perfil($cod)
    {
        $data = [
            'provincia' => $this->model->where('cod', $cod)->first(),
            'municipio' => $this->municipio_model->where('provincia_cod', $cod)->paginate(1000)
        ];
        return $this->respond($data);
    }
}
