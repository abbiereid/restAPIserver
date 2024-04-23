<?php 
    class API {
        private $conn;

        function __construct() {
            $host = 'localhost';
            $user = 'root';
            $password = '02122003';
            $database = 'api';

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
                $sql = "SELECT * FROM apitable WHERE oid = ?";
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
                    http_response_code(404);
                }
            }


        }

        function handlePost() {
            //accessing post parameters
            $oid = $_POST['oid'];
            $name = $_POST['name'];
            $comment = $_POST['comment'];
            //---------------------------------------------------

            //checking for valid parameters
            if(empty($oid) || empty($name) || empty($comment)) {
                http_response_code(400);
            } else {

                $sql = "INSERT INTO apitable (oid, name, comment) VALUES (?,?,?)";
                $result = $this->conn->prepare($sql); //sql statement sent to database separate from the parameters
                $stmt->bind_param('sss', $oid, $name, $comment); //binding parameters to placeholders , ensuring they're viewed as values now, not sql.
                $stmt->execute();


                if($stmt->affected_rows > 0) {
                    http_response_code(201); //created

                    //fetching id of newly created record
                    $sql = "SELECT id FROM apitable WHERE oid = ?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param('s', $oid);
                    $stmt->execute();

                    //-------------------------------------------
                    //returning id as JSON 

                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $response = array('id' => $row['id']);
                        
                        header('Content-Type: application/json');
                        echo json_encode($response);
                    } else {
                        http_response_code(500); // database error
                    }

                } else {
                    http_response_code(500); //database error
                }
            }
        }

    }

    $api = new API();
    $api->handleRequest();
?>