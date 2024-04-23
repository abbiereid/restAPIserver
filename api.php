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
        
        function handleGet() {
           
        }

        function handlePost() {
            $oid = $_POST['oid'];
            $name = $_POST['name'];
            $comment = $_POST['comment'];

            if(empty($oid) || empty($name) || empty($comment)) {
                http_response_code(400);
            } else {
                $sql = "INSERT INTO apitable (oid, name, comment) VALUES (?,?,?)";
                $result = $this->conn->prepare($sql);
                $stmt->bind_param('sss', $oid, $name, $comment);
                $stmt->execute();

                if($stmt->affected_rows > 0) {
                    http_response_code(201);
                } else {
                    http_response_code(500);
                }
            }
        }

    }

    $api = new API();
    $api->handleRequest();
?>