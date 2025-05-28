<?php
require_once "database.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    //selects users from table which match username and password from form
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);//stores result from sql query in variable

    if ($result->num_rows > 0) {
        //login successful
        echo "Login successful!";
		$_SESSION["username"] = $username; // Store username in session
        header("Location: search.php");    // Take you to search page
        exit();                           // Stops anymore code from running
    } else {
        //login failed
        echo "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="styles.css">
    <title>Login</title>
</head>
<body>
	<header>
        <nav>
			<ul>
				<li><a href="login.php">Login</a></li>
				<li><a href="register.php">Register</a></li>
			</ul>
		</nav>
    </header>
	<div>
    <h2>Login</h2>
		<form method="post">
			<label for="username">Username:</label>
			<input type="text" name="username" id="username" required><br>
			<label for="password">Password:</label>
			<input type="password" name="password" id="password" required><br>
			<input type="submit" value="Login">
		</form>
		<p>Don't have an account? <a href="register.php">Register Now</a></p>
	</div>
	<footer>
        <p> Library 2024 - Created by Joshua Ogunbare</p>
    </footer>
</body>
</html>