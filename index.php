<?php
include 'db.php';

$errors = [];
$success = "";

// Handle Form Submission (Create)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $fullname = trim($_POST['fullname']);
    $reg_number = trim($_POST['reg_number']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);

    // Server-side Validation
    if (empty($fullname) || empty($reg_number) || empty($email) || empty($course)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address format.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO students (fullname, reg_number, email, course) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullname, $reg_number, $email, $course);
        
        if ($stmt->execute()) {
            $success = "Student registered successfully!";
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all students (Read)
$result = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management System</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"] { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #007BFF; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #007BFF; color: white; }
        .alert-success { color: green; margin-bottom: 15px; }
        .alert-danger { color: red; margin-bottom: 15px; }
        .actions a { text-decoration: none; margin-right: 10px; padding: 5px 8px; border-radius: 3px; color: white; }
        .btn-edit { background: #ffc107; }
        .btn-delete { background: #dc3545; }
    </style>
</head>
<body>

<div class="container">
    <h2>Student Management System</h2>

    <?php if(!empty($success)): ?>
        <div class="alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if(!empty($errors)): ?>
        <div class="alert-danger">
            <?php foreach($errors as $err) echo "<p>$err</p>"; ?>
        </div>
    <?php endif; ?>

    <!-- Registration Form -->
    <form id="studentForm" action="index.php" method="POST" onsubmit="return validateForm()">
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" id="fullname" name="fullname">
        </div>
        <div class="form-group">
            <label>Registration Number:</label>
            <input type="text" id="reg_number" name="reg_number">
        </div>
        <div class="form-group">
            <label>Email Address:</label>
            <input type="email" id="email" name="email">
        </div>
        <div class="form-group">
            <label>Course:</label>
            <input type="text" id="course" name="course">
        </div>
        <button type="submit" name="add_student">Register Student</button>
    </form>

    <hr style="margin: 30px 0;">

    <!-- View Registered Students -->
    <h2>Registered Students List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Reg Number</th>
                <th>Email</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($row['reg_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td class="actions">
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align: center;">No student records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Client-side JavaScript Validation -->
<script>
function validateForm() {
    let fullname = document.getElementById("fullname").value.trim();
    let regNumber = document.getElementById("reg_number").value.trim();
    let email = document.getElementById("email").value.trim();
    let course = document.getElementById("course").value.trim();

    if (fullname === "" || regNumber === "" || email === "" || course === "") {
        alert("All fields must be filled out!");
        return false;
    }

    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if (!email.match(emailPattern)) {
        alert("Please enter a valid email address.");
        return false;
    }

    return true;
}
</script>

</body>
</html>