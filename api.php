<?php 
    class API {
        private $conn;

        function __construct() {
            $host = 'localhost';
            $user = 'root';
            $password = '02122003';
            $database = 'api';

            $conn = new mysqli($host, $user, $password, $database);

            if($conn->connect_error){
                die('Connection failed: ' . $conn->connect_error);
            }

        }

        function handleRequest() {
            $method = $_SERVER['REQUEST_METHOD'];
            switch($method) {
                case 'GET':
                    $this->handleGet();
                    break;
                case 'POST':
                    $this->handlePost();
                    break;
                default:
                    http_response_code(405);
                    break;
            }
        }


    }
?>