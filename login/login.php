<?php
session_start();
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

   
    $sql = "SELECT * FROM usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($password === $user['password']) {
            
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            header("Location: ../user/dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Password incorrect";
        }
    } else {
        $_SESSION['error'] = "User not found";
    }

    $stmt->close();
}
$conn->close();
header("Location: ../index.php");
?>
