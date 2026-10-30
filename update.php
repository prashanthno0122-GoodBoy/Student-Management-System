
<?php
include 'config.php';

$error = "";
$success = "";
$student = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (!$id || $id < 1) {
        $error = "Invalid student ID.";
    } elseif ($name === '' || $email === '' || $phone === '') {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match('/^\d{10}$/', $phone)) {
        $error = "Phone number must contain exactly 10 digits.";
    } else {
        $stmt = $conn->prepare(
            "UPDATE entries SET name = ?, email = ?, phone = ? WHERE id = ?"
        );
        $stmt->bind_param("sssi", $name, $email, $phone, $id);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: view.php");
            exit;
        } else {
            $error = "Unable to update student details.";
            $stmt->close();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id && $id > 0) {
        $stmt = $conn->prepare(
            "SELECT id, name, email, phone FROM entries WHERE id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();
    }

    if (!$student) {
        $error = "Student record not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body p-4">
            <h2 class="mb-4">Update Student</h2>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($student): ?>
                <form method="POST" action="update.php">
                    <input type="hidden" name="id"
                           value="<?= (int)$student['id'] ?>">

                    <div class="mb-3">
                        <label for="name" class="form-label">Student Name</label>
                        <input type="text" id="name" name="name"
                               class="form-control" required
                               value="<?= htmlspecialchars($student['name']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email"
                               class="form-control" required
                               value="<?= htmlspecialchars($student['email']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone"
                               class="form-control" required
                               pattern="[0-9]{10}"
                               value="<?= htmlspecialchars($student['phone']) ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>
                    <a href="view.php" class="btn btn-secondary">Cancel</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
