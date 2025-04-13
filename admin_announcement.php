<?php
session_start();
include "connect.php"; // Database connection

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}


// Fetch announcements
$sql_announcements = "SELECT * FROM announcement ORDER BY CREATED_AT DESC";
$result_announcements = $conn->query($sql_announcements);



// Handle new announcements
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['announcement'])) {
    $content = $conn->real_escape_string($_POST['announcement']);
    $title = $conn->real_escape_string($_POST['announcement_title']);
    $date_posted = date("Y-m-d H:i:s");
    $conn->query("INSERT INTO announcement (TITLE, CONTENT, CREATED_AT) VALUES ('$title', '$content', '$date_posted')");
    header("Location: admin.php"); // Refresh page
    exit();
}


// Handle announcement deletion
if (isset($_GET['delete_announcement'])) {
    $id = $conn->real_escape_string($_GET['delete_announcement']);
    $conn->query("DELETE FROM announcement WHERE ID = '$id'");
    header("Location: admin.php");
    exit();
}

// Handle announcement update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_announcement'])) {
    $id = $conn->real_escape_string($_POST['announcement_id']);
    $content = $conn->real_escape_string($_POST['announcement_content']);
    $title = $conn->real_escape_string($_POST['announcement_title']);
    $conn->query("UPDATE announcement SET TITLE = '$title', CONTENT = '$content' WHERE ID = '$id'");
    header("Location: admin.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Basic dashboard styling - Customize as needed */
        body {
            background-color:rgba(218, 229, 240, 0.7);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background-color:hsl(249, 77.80%, 24.70%) !important;
        }

        .offcanvas {
            width: 250px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
        }

        .main {
            padding: 20px;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            margin-left: 250px;
            grid-gap: 20px;
        }
        .navbar {
        background-color: #0d6efd;
        position: sticky;
        top: 0;
        width: 100%;
        z-index: 1000; /* Ensures it stays on top of other elements */
        }

        section {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        h3 {
            margin-top: 0;
        }

        .stats-section {
            text-align: center;
        }

        .stats-section p {
            font-size: 1.2rem;
            font-weight: bold;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        button {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .announcement-item {
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .announcement-item strong {
            display: block;
            margin-bottom: 5px;
        }
        
        .modal-dialog {
            max-width: 500px;
        }
        
        .chart-container {
            width: 100%;
            height: 300px;
        }
        
        .logout-btn {
            background-color:rgba(189, 142, 12, 0.75);
            color: white !important;
            padding: 8px 20px !important;
            border-radius: 4px;
            transition: all 0.3s ease;
            margin-left: 20px;
            text-decoration: none;
        }

        .logout-btn:hover {
            background-color:rgb(201, 81, 1);
            color: white !important;
            text-decoration: none;
        }
        .navbar-brand {
        font-weight: bold;
        }
        .navbar {
    background-color: #0d6efd;
    position: sticky;
    top: 0;
    width: 100%;
    z-index: 1000; /* Ensures it stays on top of other elements */
}

.navbar-nav {
    margin-left: auto; /* Pushes navigation items to the right */
}




    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="admin.php">Home</a></li>
                <li class="nav-item"><b><a class="nav-link" href="admin_announcement.php">ANNOUNCEMENTS</a></b></li>
                <li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>
                <li class="nav-item"><a class="nav-link" href="current_sitin.php">Sit-In</a></li>
                <li class="nav-item"><a class="nav-link" href="sitinrecords.php">Sit-in Records</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Reports</a></li>
            </ul>
            <a href="login.php?logout=true" class="logout-btn ms-3">Log out</a>
        </div>
    </div>
</nav>


    <main class="container mt-4">
        
        <div class="row">
            <div class="col-md-12">
                <section>
                    <h3>Post Announcement</h3>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="announcement_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="announcement_title" name="announcement_title" required>
                        </div>
                        <div class="mb-3">
                            <label for="announcement" class="form-label">Content</label>
                            <textarea name="announcement" class="form-control" rows="3" placeholder="Enter new announcement..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Announcement</button>
                    </form>
                    <div class="mt-4">
                        <?php
                        if ($result_announcements->num_rows > 0) {
                            while ($announcement = $result_announcements->fetch_assoc()) {
                                echo "<div class='announcement-item'>";
                                echo "<div class='d-flex justify-content-between align-items-start'>";
                                echo "<div>";
                                echo "<strong>" . htmlspecialchars($announcement['TITLE']) . "</strong>";
                                echo "<p>" . htmlspecialchars($announcement['CONTENT']) . "</p>";
                                echo "<small class='text-muted d-block'>Posted on: " . date("Y-m-d H:i:s", strtotime($announcement['CREATED_AT'])) . "</small>";
                                echo "</div>";
                                echo "<div class='btn-group'>";
                                echo "<button type='button' class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editModal" . $announcement['ID'] . "'>Edit</button>";
                                echo "<a href='admin.php?delete_announcement=" . $announcement['ID'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this announcement?\")'>Delete</a>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";

                                // Edit Modal for each announcement
                                echo "<div class='modal fade' id='editModal" . $announcement['ID'] . "' tabindex='-1' aria-labelledby='editModalLabel" . $announcement['ID'] . "' aria-hidden='true'>";
                                echo "<div class='modal-dialog'>";
                                echo "<div class='modal-content'>";
                                echo "<div class='modal-header'>";
                                echo "<h5 class='modal-title' id='editModalLabel" . $announcement['ID'] . "'>Edit Announcement</h5>";
                                echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                                echo "</div>";
                                echo "<form method='POST' action=''>";
                                echo "<div class='modal-body'>";
                                echo "<input type='hidden' name='announcement_id' value='" . $announcement['ID'] . "'>";
                                echo "<div class='mb-3'>";
                                echo "<label for='edit_title" . $announcement['ID'] . "' class='form-label'>Title</label>";
                                echo "<input type='text' class='form-control' id='edit_title" . $announcement['ID'] . "' name='announcement_title' value='" . htmlspecialchars($announcement['TITLE']) . "' required>";
                                echo "</div>";
                                echo "<div class='mb-3'>";
                                echo "<label for='edit_content" . $announcement['ID'] . "' class='form-label'>Content</label>";
                                echo "<textarea name='announcement_content' class='form-control' rows='4' required>" . htmlspecialchars($announcement['CONTENT']) . "</textarea>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div class='modal-footer'>";
                                echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>";
                                echo "<button type='submit' name='update_announcement' class='btn btn-primary'>Update</button>";
                                echo "</div>";
                                echo "</form>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                        } else {
                            echo "<p>No announcements available.</p>";
                        }
                        ?>
                    </div>
                </section>
            </div>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    
</body>
</html>