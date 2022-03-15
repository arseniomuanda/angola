<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use Config\Database;

use App\Models\BairroModel;
use App\Models\RuaModel;

class Bairro extends ResourceController
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

        $this->model = new BairroModel();
        $this->rua_model = new RuaModel();
        $this->db = Database::connect();
    }

    public function index()
    {

        
        $data = json_decode(file_get_contents("php://input"));
        helper('funcao');
        if (isset($data) && (count($data) > 1)) {
            foreach ($data as $value) {
                $data = [
                    'cod' => newGuid(),
                    'distrito_cod' => isset($value->distrito_cod) ? $value->distrito_cod : null,
                    'nome' => isset($value->nome) ? $value->nome : null,
                    'estado' => isset($value->estado) ? $value->estado : null
                ];

                $data = cadastronormal($this->model, $data, $this->db);
            }
        } else if (isset($data)) {
            $data = [
                'cod' => newGuid(),
                'distrito_cod' => isset($data->distrito_cod) ? $data->distrito_cod : null,
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
            'bairro' => $this->model->where('cod', $cod)->first(),
            'rua' => $this->rua_model->where('bairro_cod', $cod)->paginate(1000)
        ];
        return $this->respond($data);
    }
}
