<?php

// Заголовки (headers)
header("Access-Control-Allow-Origin: http://authentication-jwt/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Подключение к БД
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/config/Database.php";
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/Objects/User.php";

// Соеденение к БД
$database = new Database();
$db = $database->getConnection();

// Объект User
$user = new User($db);

// Получение данных
$data = json_decode(file_get_contents("php://input"));

// Устанавливаем значение
$user->firstname = $data->firstname;
$user->lastname = $data->lastname;
$user->email = $data->email;
$user->password = $data->password;

// Метод Create и создание нового пользователя
if
(!empty($user->firstname) && !empty($user->email) && !empty($user->password) && $user->create()) {

    http_response_code(200);

    // Показываем, что пользователь создан
    echo json_encode(array("message" => "Пользователь был создан"));

} else {

    http_response_code(400);

    // Показываем, что пользователя не создали
    echo json_encode(array("message" => "Невозможно создать пользователя"));
}
