<?php

class User
{

    // Подключение к БД таблице "users"
    private $conn;
    private $table_name = "users";

    // Свойства
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $password;

    // Конструктор класса User
    public function __construct($db)
    {

        $this->conn = $db;
    }

    // Метод создания нового пользователя
    public function create()
    {

        // Запрос для добавления нового пользователя в БД
        $query = "INSERT INTO " . $this->table_name . " SET firstname = :firstname, lastname = :lastname, email = :email, password = :password";

        // Подготовка запроса
        $stmt = $this->conn->prepare($query);

        // Инъекция
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // Привязка значений
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":email", $this->email);

        // Для защиты пароля
        // Хешируем пароль перед сохранением в БД
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password_hash);

        // Выполняем запрос
        // Если выполнение успешно, то информация о пользователе будет сохранена в БД
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function emailExists()
    {
        // Проверка почты на существование
        $query = " SELECT id, firstname, lastname, password FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";

        // Подготовка запроса
        $stmt = $this->conn->prepare($query);

        // Инъекция
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Привязываем значение по email
        $stmt->bindParam(1, $this->email);

        // Выполнение запроса
        $stmt->execute();

        // Получаем кол-во строк
        $num = $stmt->rowCount();

        // Если почта существует, присвоим значения св-вам объекта для легкого доступа и использования для php сессий
        if ($num > 0) {

            // Получение значений
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Присвоение значения св-вам объекта
            $this->id = $row["id"];
            $this->firstname = $row["firstname"];
            $this->lastname = $row["lastname"];
            $this->password = $row["password"];

            // Вернем true, так как в БД есть электронная почта
            return true;
        }

        // Вернем false, если адрес не существует в БД
        return false;
    }

// Обновить запись пользователя

    public function update()
    {

        // Если в HTML-форме был введен пароль (необходимо обновить пароль)
        $password_set = !empty($this->password) ? ", password = :password" : "";

        // Если не введен пароль - не обновлять пароль
        $query = "UPDATE " . $this->table_name . "
        SET
        firstname = :firstname,
        lastname = :lastname,
        email = :email
        {$password_set}
        WHERE id = :id ";

        // Подготовка запроса
        $stmt = $this->conn->prepare($query);

        // Инъекция (очистка)
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Привязываем значения с HTML-формы
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":email", $this->email);

        // Метод password_hash() для защиты пароля пользователя в БД
        if (!empty($this->password)) {
            $this->password = htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $password_hash);
        }

        // Уникальный идентификатор записи для редактирования
        $stmt->bindParam(":id", $this->id);

        // Если выполнено успешно, то информация о пользователе будет сохранена в БД
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
