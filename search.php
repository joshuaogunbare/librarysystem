<?php
// Include database connection
require_once "database.php";
session_start();

//makes sure the user is logged in to access search page
if (!isset($_SESSION["username"])) {
    echo "<p>You must log in to access this page.</p>";
    echo "<p><a href='login.php'>Click here to log in</a></p>";
    exit();
}

$username = $_SESSION["username"];

// initialising variables
$search_results = [];
$categories = [];

// Gets the categories from database for the dropdown menu
$categorysql_query = "SELECT * FROM categories";//stored the query in variable for simpler access later
$categorysql_query_result = $conn->query($categorysql_query);

if (!$categorysql_query_result) {
    die("Error getting categories: " . $conn->error);
}

$categories = [];
while ($category = $categorysql_query_result->fetch_assoc()) {
    $categories[] = $category;
}

//if user wants to reseve
if (isset($_POST['reserve'])) {
    $isbn = $_POST['reserve'];
    $current_date = date("Y-m-d");
    
    //check if the books already reserved
    $check_reservation = $conn->query("SELECT 1 FROM reservations WHERE ISBN = '$isbn' AND Username = '$username'");

    if ($check_reservation->num_rows > 0) {
        echo "This book is already reserved.";
    }
    else {
        $conn->query("INSERT INTO reservations (ISBN, Username, ReservedDate) VALUES ('$isbn', '$username', '$current_date')");
        echo "Reservation was succesful.";
    }    
}

// if user wants to remove a reservation
if (isset($_POST['remove_reservation'])) {
    $isbn = $_POST['remove_reservation'];
    
    $conn->query("DELETE FROM reservations WHERE ISBN = '$isbn' AND Username = '$username'");
    echo "Reservation has been removed.";
}

// Get all reserved books for the user 
$reserved_array = [];
$result = $conn->query("SELECT r.ISBN, b.BookTitle AS title, b.Author AS author 
                       FROM reservations r 
                       JOIN books b ON r.ISBN = b.ISBN 
                       WHERE r.Username = '$username'");

while ($book = $result->fetch_assoc()) {
    $reserved_array[] = $book;
}


// if user wants to search for a book
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $author = isset($_POST['author']) ? $_POST['author'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';

    $query = "SELECT ISBN, BookTitle AS title, Author AS author FROM books WHERE 1=1";
    if ($title != '') {  //makes sure it isnt empty
        $query .= " AND BookTitle LIKE '%$title%'"; //if its not empty, it adds a condition to the query 
    }
    if ($author != '') {
        $query .= " AND author LIKE '%$author%'";
    }
    if ($category != '') {
        $query .= " AND category = '$category'";
    }
    $result = $conn->query($query);
    $search_results = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
}

// logging out
if (isset($_POST['logout'])) {
    // Destroy the session and takes you to the login page
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Search Books</title>
</head>
<body>
    <header>
        <h1>Library</h1>
    </header>
    <div class>
        <h1>Search for a Book</h1>

        <form method="post">
            <label for="title">Book Title:</label>
            <input type="text" name="title" id="title"><br>

            <label for="author">Author:</label>
            <input type="text" name="author" id="author"><br>

            <label for="category">Category:</label>
            <select name="category" id="category">
                <option value=""> Select a Category </option>
                <?php 
                // gets the categories form category table for dropdown menu
                foreach ($categories as $category) {
                    echo "<option value=\"" . $category['CategoryID'] . "\">" . $category['CategoryDesc'] . "</option>";
                }
                ?>
            </select><br>

            <input type="submit" value="Search">
        </form>

        <?php 
        // Show search results in a table
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (count($search_results) > 0) {
                echo "<h2>Search Results:</h2>";
                echo "<table border='1'>";
                echo "<tr><th>Book Title</th><th>Author</th><th>Reserve</th></tr>";

                // Loop through each search result to display in a table
                foreach ($search_results as $book) {
                    $bookTitle = $book['title']; 
                    $bookAuthor = $book['author'];
                    $isbn = $book['ISBN'];

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($bookTitle) . "</td>";
                    echo "<td>" . htmlspecialchars($bookAuthor) . "</td>";
                    echo "<td>
                            <form method='post'>
                                <input type='hidden' name='reserve' value='$isbn'>
                                <input type='submit' value='Reserve'>
                            </form>
                          </td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>No books found.</p>";


            }
        }
        ?>

        <h2>Your Reserved Books:</h2>
        <?php
        // Show reserved books
        if (count($reserved_array) > 0) {
            echo "<table border='1'>";
            echo "<tr><th>Book Title</th><th>Author</th><th>Remove Reservation</th></tr>";

            // go through each reserved book
            foreach ($reserved_array as $book) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($book['title']) . "</td>";
                echo "<td>" . htmlspecialchars($book['author']) . "</td>";
                echo "<td>
                          <form method='post'>
                              <input type='hidden' name='remove_reservation' value='" . $book['ISBN'] . "'>
                              <input type='submit' value='Remove Reservation'>
                          </form>
                      </td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>You have no reserved books.</p>";
        }
        ?>

        <br>
        <form method="post">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
    <footer>
        <p> Library 2024 - Created by Joshua Ogunbare</p>
    </footer>


</body>
</html>

