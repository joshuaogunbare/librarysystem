<?php
require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST["password"];
    $first_name = $_POST["first_name"];
    $surname = $_POST["surname"];
    $username = $_POST["username"];
    $address_line1 = $_POST["address_line1"];
    $address_line2 = $_POST["address_line2"];
    $city = $_POST["city"];
    $telephone = $_POST["telephone"];
    $mobile = $_POST["mobile"];
	$confirm_password = $_POST["confirm_password"];
	
	$errors = [];//array that stores error messages if any
	
	//makes sure mobile num has 10 numbers
	if (strlen($mobile) !== 10 || !is_numeric($mobile)) {
	  $errors[] = "Mobile number must be 10 digits.<br>";
    }
  
    //makes sure password is 6 characters long
    if (strlen($password) < 6) {
	  $errors[] = "Password must be at least 6 characters long.";
    }
  
    //makes sure passwords match
    if ($password !== $confirm_password) {
      $errors[] = "Passwords do not match.";
    }

	 //makes sure username doesnt exist
    $sql = "SELECT Username FROM users WHERE Username = '$username'";
    $result = $conn->query($sql);
	
    if ($result->num_rows > 0) {
		$errors[] = "Username already exists.";
    }
	
    // Insert data into the database if theres no errors
	if (count($errors) == 0){
		$sql = "INSERT INTO users (Username, Password, firstName, Surname, addressLine1, addressLine2, City, Telephone, Mobile)
				VALUES ('$username', '$password', '$first_name', '$surname', '$address_line1', '$address_line2', '$city', '$telephone', '$mobile')";

		if ($conn->query($sql) === TRUE) {
			echo "Registration successful!";
		} else {
			echo "Error: " . $conn->error;
		}
		header("Location: search.php" );
		exit();
		
	}
	else {
		echo "<ul>";
		foreach ($errors as $error) {
            echo "<li>$error</li>";//displays errors if any
        }
        echo "</ul>";
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="styles.css">
    <title>Registration</title>
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
		<h2>Registration</h2>
		<form method="post">
			<label for="username">Username:</label>
			<input type="text" name="username" id="username" required><br>
			<label for="password">Password:</label>
			<input type="password" name="password" id="password" required><br>
			<label for="confirm_password">Confirm Password:</label>
			<input type="password" name="confirm_password" id="confirm_password" required><br>
			<label for="first_name">First Name:</label>
			<input type="text" name="first_name" id="first_name" required><br>
			<label for="surname">Surname:</label>
			<input type="text" name="surname" id="surname" required><br>
			<label for="address_line1">Address Line 1:</label>
			<input type="text" name="address_line1" id="address_line1" required><br>
			<label for="address_line2">Address Line 2:</label>
			<input type="text" name="address_line2" id="address_line2"><br>
			<label for="city">City:</label>
			<input type="text" name="city" id="city" required><br>
			<label for="telephone">Telephone:</label>
			<input type="text" name="telephone" id="telephone" required><br>
			<label for="mobile">Mobile:</label>
			<input type="text" name="mobile" id="mobile" required><br>
			<input type="submit" value="Register"> <br>
			<a href="login.php"> Back to Login</a>
	</div>
    </form>
	<footer>
        <p> Library 2024 - Created by Joshua Ogunbare</p>
    </footer>
</body>
</html>