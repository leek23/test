<?php
require '../app/config/config.php';



$response['data']='nothing be found';
$response['message']='ok';


if (empty($_GET)) {
    $response['errors'][] = 'request is empty';
    echo json_encode($response);
    return;
}


//search in db

$con = mysql::get();
$mysqli = @new mysqli($con['host'],$con['user'], $con['password'], $con['db']);

if (mysqli_connect_errno()) {
    $response['errors'][] = 'connection with db error';
    echo json_encode($response);
    return;
}

if ($result = $mysqli->query("SELECT model FROM json GROUP BY model")) {

    while ($row = $result->fetch_object()){
        $models[] = $row->model;
    }

    $result->close();
}


//validation

$fields = array(
    'model' => 'string',
    'color' => 'string',
    'transmission' => 'string',
    'price'=> 'int',
    'km'=> 'int',
    'owners'=> 'int',
    'power'=> 'int',
    'engineCapacity'=> 'int',
);


foreach ($_GET as $key => $value) {

    if (array_key_exists($key, $fields)) {

        if (preg_match('/,/i', $value) && $fields[$key]=='int') {

            $value = explode(',',$value);


            if (preg_match('/^[0-9.]+$/', $value[0]) && (preg_match('/^[0-9.]+$/', $value[1]) || $value[1]=='inf')) {

//                echo $key.' '.$value[0].' '.gettype($value[0]).' '.$value[1].' '.gettype($value[1]).'<br>';

                if ($value[1]!='inf')
                    $sql[] = $key.' >= '.$value[0].' AND '.$key.' <= '.$value[1];

                else
                    $sql[] = $key.' >= '.$value[0];
            } else {
                $response['errors'][] = $key.' must be integer';
                $response['message'] =  "validation error";
            }

        }
        else {
            if ($fields[$key]=='int') {
                if (preg_match('/^[0-9.]+$/', $value)) {
//                    echo $key . ' ' . $value . ' ' . gettype($value) . '<br>';
                    $sql[] = $key . ' = ' . $value;
                }
                else {


                    $response['message'] = "validation error";
                    $response['errors'][] = $key . ' must be integer';
                }}
            else {
                if ($key == 'model' && !in_array($value, $models)) {
                    $response['errors'][] = 'model '.$value.' not found';
                    $response['message'] =  "validation error";
                }
//                echo $key . ' ' . $value . ' ' . gettype($value) . '<br>';
                $sql[] = $key . ' = "' . $value.'"';

            }
        }
    }


}

$sql = implode(' AND ', $sql);

//echo $sql;










if ($result = $mysqli->query("SELECT * FROM json WHERE " . $sql)) {

    while ($row = $result->fetch_object()){

        $data[] = $row;
    }

    $result->close();
}





$mysqli->close();

$response['data'] = $data;




echo json_encode($response, JSON_UNESCAPED_UNICODE);


