<?php
class Users {
    public static function add_user_employee($email, $password):bool{
        $sql = "INSERT INTO usuarios (email, password, rol, activo) VALUES (:email, :password, :rol, false)";
        try {
            $result = Database::execute($sql, [
                ':email' => $email,
                ':password' => PasswordHelper::hash($password),
                ':rol' => "employee"
            ]);
            return $result==1 ? true : false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function get_user_by_email($email): array|bool {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $usuario = Database::queryOne($sql, [$email]);
        return $usuario;
    }

    public static function get_all_users_employees() {
        $sql = "SELECT * FROM usuarios WHERE rol = 'employee'";
        $usuarios = Database::query($sql);
        return $usuarios;
    }

    public static function delete_user_by_email($email) {
        try {
            $sql = "DELETE FROM usuarios WHERE email = :email";
            $result = Database::query($sql, [
                ":email"=> $email
            ]);
            return count($result) !== 0;
        } catch (\Throwable $th) {
            return false;
        }
    }
}