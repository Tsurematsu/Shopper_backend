<?php
function preventAdmin(){
    $sql = "SELECT * FROM usuarios WHERE rol = ?";
    $usuarios = Database::query($sql, ['admin']);

    if (count($usuarios)==0){
        $sql = "INSERT INTO usuarios (email, password, rol) VALUES (:email, :password, :rol)";
        Database::execute($sql, [
            ':email' => "test@test.com",
            ':password' => PasswordHelper::hash("root"),
            ':rol' => "admin"
        ]);
    }
}