<?php
// Load environment configuration
require_once(__DIR__ . '/../env.php');

class clsketnoi{
    public function moKetNoi(){
        $host = config('database.host', 'localhost');
        $username = config('database.username', 'root');
        $password = config('database.password', '');
        $database = config('database.database', 'hanhphuc');
        $port = config('database.port', 3306);
        
        $con = mysqli_connect($host, $username, $password, $database, $port);
        if (!$con) {
            error_log("Database connection failed: " . mysqli_connect_error());
            return null;
        }
        mysqli_set_charset($con, 'utf8mb4');
        return $con;
    }
    public function dongKetNoi($con){
        if ($con) {
            mysqli_close($con);
        }
    }
}
?>