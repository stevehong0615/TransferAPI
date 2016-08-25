<?php

//資料庫連線
class Connect
{
    public $db;

    public function __construct()
    {
        $this->db = new PDO (
            'mysql:host=localhost; dbname=Transfer; charset=utf8',
            'root',
            '',
            array (
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            )
        );
    }
}
