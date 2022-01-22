<?php

namespace App\Controllers;

use App\Models\BairroModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Config\Database;

use App\Models\DistritoModel;

class Distrito extends ResourceController
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

        $this->model = new DistritoModel();
        $this->bairro_model = new BairroModel();
        $this->db = Database::connect();
    }

    public function index()
    {
        $data = json_decode(file_get_contents("php://input"));
        helper('funcao');
        
        if (isset($data)  && is_countable($data)) {
            foreach ($data as $value) {
                $data = [
                    'cod' => newGuid(),
                    'municipio_cod' => isset($value->municipio_cod) ? $value->municipio_cod : null,
                    'nome' => isset($value->nome) ? $value->nome : null,
                    'estado' => isset($value->estado) ? $value->estado : null
                ];

                $data = cadastronormal($this->model, $data, $this->db);
            }
        } else if (isset($data)) {
            $data = [
                'cod' => newGuid(),
                'municipio_cod' => isset($data->municipio_cod) ? $data->municipio_cod : null,
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
            'distrito' => $this->model->where('cod', $cod)->first(),
            'bairro' => $this->bairro_model->where('distrito_cod', $cod)->paginate(1000)
        ];
        return $this->respond($data);
    }
}
