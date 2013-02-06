<?php

abstract class SaunterPHP_Framework_Providers_SQLServer {
    function __construct($server_or_source, $username, $password) {
        $os = strtolower(PHP_OS);

        if (substr($os, 0, 3) == 'win') {
            // http://msdn.microsoft.com/en-us/library/cc296182(v=sql.90).aspx
            $this->connection = new PDO("sqlsrv:server=$server_or_source", $username, $password);
        } elseif ($os === 'darwin') {
            // http://www.php.net/manual/en/ref.pdo-odbc.php
            $this->connection = new PDO("odbc:$server_or_source", $username, $password);
        } else {
            throw new Exception("{$PHP_OS} is not [yet] supported. patches welcome :)");
        }
    }

    function __destruct() {
        $this->connection = null;
    }

}
