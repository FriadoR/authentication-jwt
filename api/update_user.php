<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset = UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Требуется для кодирования web-token JSON
use \Firebase\JWT\JWT;
use \Firebase\JWT\KEY;
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/config/Core.php";
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/libs/vendor/firebase/php-jwt/src/BeforeValidException.php";
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/libs/vendor/firebase/php-jwt/src/ExpiredException.php";
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/libs/vendor/firebase/php-jwt/src/SignatureInvalidException.php";
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/libs/vendor/firebase/php-jwt/src/JWT.php";
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/libs/vendor/firebase/php-jwt/src/Key.php";

// Файлы, необходимые для подключения к БД
include_once "./config/Database.php";
include_once "./Objects/User.php";

// Получаем соединение с БД
$database = new Database();
$db = $database->getConnection();

// Объект "User"
$user = new User($db);

// Получаем данные
$data = json_decode(file_get_contents("php://input"));

// Получаем JWT
$jwt = isset($data->jwt) ? $data->jwt : "";

// Если JWT не пуст
if ($jwt) {

    // Если декодирование выполнено успешно, показать данные пользвователя
    try {

        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        // Устанавливаем отправленные данные (через форму html) в свойствах объекта пользователя
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->id = $decoded->data->id;

        // Создание пользователя
        if ($user->update()) {

            // сгенерировать заново JWT здесь
        }
        // Сообщение, если не удается обновить пользователя
        else {

            http_response_code(401);

            echo json_encode(array("message" => "Невозможно обновить пользователя"));
        }
        // Если декодирование не удалось, то JWT недействительный
    } catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Доступ закрыт", "error" => $e->getMessage()));
    }
}
// Показать сообщение об ошибке, если JWT пуст
else {

    http_response_code(401);

    // Сообщаем пользователю, что доступ запрещен
    echo json_encode(array("message" => "Доступ закрыт"));

}
