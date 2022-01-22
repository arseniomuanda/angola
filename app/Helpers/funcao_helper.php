<?php
function cadastronormal($model, $data, $db)
{
    $query = $model->save($data);

    if ($query) {
        $id = $db->insertID();
        return [
            'message' => 'Sucesso!',
            'error' => false,
            'status' => 200,
            'data' => $model->where('id', $id)->paginate()
        ];
    } else if ($model->errors()) {
        $message = $model->errors();
    } else {
        $message = 'Sem sucesso';
    }

    return [
        'message' => $message,
        'error' => false,
        'status' => 400,
    ];
}


function newGuid()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}