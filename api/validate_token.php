<?php
header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
header("Content-Type: application/json; charset = UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Требуется для декодирования JWT
use \Firebase\JWT\JWT;
use \Firebase\JWT\KEY;
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/config/Core.php";
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/libs/vendor/firebase/php-jwt/src/BeforeValidException.php";
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/libs/vendor/firebase/php-jwt/src/ExpiredException.php";
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/libs/vendor/firebase/php-jwt/src/SignatureInvalidException.php";
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/libs/vendor/firebase/php-jwt/src/JWT.php";
include_once "/Applications/MAMP/htdocs/authentication-jwt/api/libs/vendor/firebase/php-jwt/src/Key.php";

// Получение значения web-token JSON
$data = json_decode(file_get_contents("php://input"));

// Получение JWT
$jwt = isset($data->jwt) ? $data->jwt : "";

// Если JWT не пустой
if ($jwt) {

    // Если декодирование прошло успешно, показать данные пользователя
    try {
        // Декодируем jwt
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        // Код ответа
        http_response_code(200);

        echo json_encode(array("message" => "Доступ разрешен", "data" => $decoded->data));

        // Если декодирование не удалось, значит JWT недействительный
    } catch (Exception $e) {

        http_response_code(401);

        // Сообщаем пользователю, что ему отказано в доступе и выведем сообщение об ошибке
        echo json_encode(array("message" => "Вам доступ закрыт", "error" => $e->getMessage()));

// Показываем сообщение об ошибке, если JWT пустой
    }
} else {

    http_response_code(401);

    echo json_encode(array("message" => "Доступ запрещен"));
}
