<?php
include 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student not found.");
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $reg_number = trim($_POST['reg_number']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);

    if (empty($fullname) || empty($reg_number) || empty($email) || empty($course)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $update_stmt = $conn->prepare("UPDATE students SET fullname=?, reg_number=?, email=?, course=? WHERE id=?");
        $update_stmt->bind_param("ssssi", $fullname, $reg_number, $email, $course, $id);
        
        if ($update_stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Update failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f9; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"] { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
        button:hover { background: #218838; }
        .alert-danger { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Student Record</h2>
    <?php if($error): ?><div class="alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($student['fullname']); ?>">
        </div>
        <div class="form-group">
            <label>Registration Number:</label>
            <input type="text" name="reg_number" value="<?php echo htmlspecialchars($student['reg_number']); ?>">
        </div>
        <div class="form-group">
            <label>Email Address:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>">
        </div>
        <div class="form-group">
            <label>Course:</label>
            <input type="text" name="course" value="<?php echo htmlspecialchars($student['course']); ?>">
        </div>
        <button type="submit">Update Student</button>
        <a href="index.php" style="margin-left: 10px; text-decoration: none; color: #666;">Cancel</a>
    </form>
</div>
</body>
</html>