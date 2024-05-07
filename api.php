<?php 
    class API {
        private $conn;

        function __construct() {
            $host = 'localhost';
            $user = 'ar1382_main';
            $password = '^fGrpYzM#+Y(';
            $database = 'ar1382_api';

            $this->conn = new mysqli($host, $user, $password, $database);

            if($this->conn->connect_error){
                die('Connection failed: ' . $this->conn->connect_error);
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
            $oid = $_GET['oid'];

            if(empty($oid)) {
                http_response_code(400);
            } else {
                $sql = "SELECT * FROM apiTable WHERE oid = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('s', $oid);
                $stmt->execute();

                $result = $stmt->get_result();
                if($result->num_rows > 0) { 
                    $response = array();
                    while($row = $result->fetch_assoc()) {
                        $response[] = $row; 
                    }

                    header('Content-Type: application/json'); 
                    echo json_encode($response); 
                } else {
                    http_response_code(204);
                }
            }
        }

        function handlePost() {
            $oid = $_POST['oid'];
            $name = $_POST['name'];
            $comment = $_POST['comment'];
        
            if(empty($oid) || empty($name) || empty($comment)) {
                http_response_code(400);
            } else {
                $sql = "INSERT INTO apiTable (oid, name, comment) VALUES (?,?,?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('sss', $oid, $name, $comment); 
                $stmt->execute();

                if($stmt->affected_rows > 0) {
                    http_response_code(201);

                    $sql = "SELECT id FROM apiTable WHERE oid = ?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param('s', $oid);
                    $stmt->execute();

                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $response = array('id' => $row['id']);
                        
                        header('Content-Type: application/json');
                        echo json_encode($response);
                    } else {
                        http_response_code(500);
                    }

                } else {
                    http_response_code(500); 
                }
            }
        }

        function __destruct() {
            $this->conn->close();
        }

    }

    $api = new API();
    $api->handleRequest();

?>