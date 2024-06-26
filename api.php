<?php 
    class API {
        private $conn;

        public function __construct() {
            $host = 'localhost';
            $user = 'ar1382_main';
            $password = '^fGrpYzM#+Y(';
            $database = 'ar1382_api';

            $this->conn = new mysqli($host, $user, $password, $database);

            if($this->conn->connect_error){
                http_response_code(500);
                die();
            }
        }

        public function handleRequest() {
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
        
        public function handleGet() {
            $oid = isset($_GET['oid']) ? $_GET['oid'] : null;

            if($oid == null) {
                http_response_code(400);
            } else {
                $sql = "SELECT id, DATE_FORMAT(date, '%d %M %Y') AS date, name, comment FROM apiTable WHERE oid = ? ORDER BY date ASC";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('s', $oid);
                $stmt->execute();

                $result = $stmt->get_result();
                if($result->num_rows > 0) { 
                    http_response_code(200);

                    $response = array();
                
                    while($row = $result->fetch_assoc()) {
                        $response[] = $row; 
                    }

                    $finalResponse = array('oid' => $oid, 'comments' => $response);
                    header('Content-Type: application/json'); 
                    echo json_encode($finalResponse);
                } else {
                    http_response_code(204);
                }
            }
        }

        public function handlePost() {
            $oid = isset($_POST['oid']) && trim($_POST['oid']) !== '' ? $_POST['oid'] : null;
            $name = isset($_POST['name']) && trim($_POST['name']) !== '' ? $_POST['name'] : null;
            $comment = isset($_POST['comment']) && trim($_POST['comment']) !== '' ? $_POST['comment'] : null;
        

            if($oid == null || $name == null || $comment == null) {
                http_response_code(400);
            } else {
                if (strlen($name) > 64 || strlen($oid) > 32) {
                    http_response_code(400);
                } else {
                    $sql = "INSERT INTO apiTable (oid, name, comment) VALUES (?,?,?)";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param('sss', $oid, $name, $comment); 
                    $stmt->execute();

                    if($stmt->affected_rows > 0) {
                        http_response_code(201);

                        $id = $this->conn->insert_id;
                        $response = array('id' => $id);
                
                        header('Content-Type: application/json');
                        echo json_encode($response);
                    } else {
                        http_response_code(500);
                    }
                }
            }
        }

        public function __destruct() {
            $this->conn->close();
        }

    }

    $api = new API();
    $api->handleRequest();

?>
