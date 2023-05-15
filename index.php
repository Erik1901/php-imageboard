<!DOCTYPE html>
<title>Board</title>
<link rel="stylesheet" href="style.css">

<body>
    <?php

    session_start();

    // koppla upp till databasen
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "phpmedadmin";
    $conn = mysqli_connect($host, $user, $password, $database);

    // hantera form-värden
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) && isset($_POST['content']) && isset($_POST['ingress'])) {
        // sätt alla värden
        $title = $_POST['title'];
        $ingress = $_POST['ingress'];
        $content = $_POST['content'];
        //om användaren inte vill ha namn är han anonymous
        if ($_POST['author'] == '') {
            $author = 'anonymous';
        } else {
            $author = $_POST['author'];
        }
        // Lägg in det nya inlägget i posts
        $stmt = $conn->prepare("INSERT INTO posts (title, content, ingress, author, `current_time`) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $title, $content, $ingress, $author);
        $stmt->execute();
        header("Location: index.php");
        exit();
    }

    // delete funktion
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $query = "DELETE FROM posts WHERE id = $delete_id";
        mysqli_query($conn, $query);
    }
    echo '<div class="container" style="background-image:linear-gradient(Plum,Aqua); height:40vh; border-radius: 40px; outline: 4px solid white;">';
    if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == true) {
        // Om användaren är inloggad visa form för inlägg
        echo '<form style="width:250px;" id="creation_form" method="POST" action="index.php">
            <label id="grid_item" for="author">Author</label>
            <input type="text" name="author" style="height: 100%;">
            <label id="grid_item" for="title">Title</label>
            <input type="text" name="title" style="height: 100%;" required>
            <label id="grid_item" for="ingress">Ingress</label>
            <textarea name="ingress" style="height: 100%;" required></textarea>
            <label id="grid_item" for="content">Content</label>
            <textarea name="content" style="height: 100%;" required></textarea>
            <button id="post_create" type="submit">Create Post</button>
        </form>';
    } else {
        // Annars visa login knapp
        echo '<form action="login.php">
            <input style="width:250px; outline: 2px solid white;" type="submit" value="Login">
        </form>';
    }
    // visa alltid logout knapp
    echo '
        <form action="logout.php">
            <input style="width:250px; outline: 2px solid white;" type="submit" value="Logout">
        </form>
    ';
    echo '</div>';

    // Visa inlägg
    $query = "SELECT * FROM `posts` ORDER BY `current_time` DESC";
    $result = mysqli_query($conn, $query); // för att inte få en vit rad där posts egentligen ska ligga när det är tomt
    if ($result->num_rows > 0) {
        echo "
        <div class='container'>

        <div class='postcontainer'>
    ";
    }
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='post' id='" . $row["id"] . "'>";
        echo "<b>" . $row["author"] . " @ " . $row["current_time"] . " Post No. " . $row["id"] . "</b>";
        echo "<h1>" . $row["title"] . "</h1>";
        echo "<h2>" . $row["ingress"] . "</h2>";
        echo "<p>" . $row["content"] . "</p>";
        // visa bara delete om användaren är admin
        if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == true && (isset($_SESSION['isadmin'])) && $_SESSION["isadmin"] == true) {
            echo "<form method='POST' action=''>";
            echo "<div>";
            echo "<input type='hidden' name='delete_id' value='" . $row["id"] . "'>";
            echo "<button type='submit'>Delete</button>";
            echo "</div>";
            echo "</form>";
        }
        echo "</div>";
    }
    if ($result->num_rows > 0) {

        echo "</div>
    </div>";
    }
    ?>
</body>