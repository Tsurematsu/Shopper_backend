<?php

class Database {
    private static $connection = null;
    
    /** @return PDO */
    public static function getConnection() {
        if (self::$connection === null) {
            self::connect();
        }
        return self::$connection;
    }
    
    /** @return PDO */
    private static function connect() {
        $host = getenv('POSTGRES_HOST');
        $db = getenv('POSTGRES_DB');
        $user = getenv('POSTGRES_USER');
        $pass = getenv('POSTGRES_PASSWORD');
        $port = getenv('POSTGRES_PORT');
        
        try {
            self::$connection = new PDO(
                "pgsql:host=$host;port=$port;dbname=$db",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            return self::$connection;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    /**
     * Ejecuta una consulta SELECT y retorna todos los resultados
     * @param string $sql
     * @param array $params
     * @return array
     */
    public static function query($sql, $params = []) {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error en query: " . $e->getMessage());
        }
    }
    
    /**
     * Ejecuta una consulta SELECT y retorna un solo resultado
     * @param string $sql
     * @param array $params
     * @return array|false
     */
    public static function queryOne($sql, $params = []) {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Error en queryOne: " . $e->getMessage());
        }
    }
    
    /**
     * Ejecuta una consulta INSERT, UPDATE o DELETE
     * @param string $sql
     * @param array $params
     * @return int Número de filas afectadas
     */
    public static function execute($sql, $params = []) {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error en execute: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene el último ID insertado
     * @return string
     */
    public static function lastInsertId() {
        return self::getConnection()->lastInsertId();
    }
    
    /**
     * Inicia una transacción
     */
    public static function beginTransaction() {
        self::getConnection()->beginTransaction();
    }
    
    /**
     * Confirma una transacción
     */
    public static function commit() {
        self::getConnection()->commit();
    }
    
    /**
     * Revierte una transacción
     */
    public static function rollback() {
        self::getConnection()->rollBack();
    }
    
    /**
     * Cierra la conexión
     */
    public static function close() {
        self::$connection = null;
    }
    
    /**
     * Prueba la conexión
     * @return bool
     */
    public static function testConnection() {
        try {
            self::getConnection();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}