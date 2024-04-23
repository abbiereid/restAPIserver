<?php 
    class API {
        private $conn;

        function __construct() {
            $host = 'localhost';
            $user = 'ar1382_main';
            $password = 'myAPIkey';
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
            //accessing get parameters
            $oid = isset($_GET['oid']) ? $_GET['oid'] : null;
            //---------------------------------------------

            //checking if oid is empty
            if(empty($oid)) {
                http_response_code(400);
            } else {

                //forming the sql and binding parameters seperately, to avoid sql injection
                $sql = "SELECT * FROM apiTable WHERE oid = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param('s', $oid);
                $stmt->execute();

                $result = $stmt->get_result();
                if($result->num_rows > 0) { //if more than 0 rows are returned
                    $response = array();
                    while($row = $result->fetch_assoc()) {
                        $response[] = $row; //adding each row to the response array
                    }

                    header('Content-Type: application/json'); //setting the header to JSON
                    echo json_encode($response); //returning response array as JSON
                } else {
                    http_response_code(204); //no content 
                }
            }


        }

        function handlePost() {
            //accessing post parameters
            $oid = isset($_POST['oid']) ? $_POST['oid'] : null;
            $name = isset($_POST['name']) ? $_POST['name'] : null;
            $comment = isset($_POST['comment']) ? $_POST['comment'] : null;
            //---------------------------------------------------

            //checking for valid parameters
            if(empty($oid) || empty($name) || empty($comment)) {
                http_response_code(400);
            } else {

                $sql = "INSERT INTO apiTable (oid, name, comment) VALUES (?,?,?)";
                $stmt = $this->conn->prepare($sql); //sql statement sent to database separate from the parameters
                $stmt->bind_param('sss', $oid, $name, $comment); //binding parameters to placeholders , ensuring they're viewed as values now, not sql.
                $stmt->execute();


                if($stmt->affected_rows > 0) {
                    http_response_code(201); //created

                    //fetching id of newly created record
                    $sql = "SELECT id FROM apiTable WHERE oid = ?";
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