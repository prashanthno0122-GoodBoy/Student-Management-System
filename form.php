<?php

include 'config.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Server-side validation
    if (empty($name) || empty($email) || empty($phone)) {

        $error = "All fields are required!";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Invalid email address!";

    } elseif (!preg_match('/^\d{10}$/', $phone)) {

        $error = "Phone number must contain exactly 10 digits!";

    } else {

        // Insert data using prepared statement
        $stmt = $conn->prepare(
            "INSERT INTO entries (name, email, phone) VALUES (?, ?, ?)"
        );

        $stmt->bind_param("sss", $name, $email, $phone);

        if ($stmt->execute()) {
            $success = "Student registered successfully!";
        } else {
            $error = "Error saving student details!";
        }

        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Add Student - Student Management System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                Student Management System
            </a>
            <a class="nav-link text-white" href="index.html">Home</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card shadow-sm mx-auto" style="max-width: 600px;">
            <div class="card-body p-4">

                <h2 class="mb-4">Register New Student</h2>
                <?php if (!empty($success)): ?>
               <div class="alert alert-success">
                <?php echo $success; ?>
               </div>
               <?php endif; ?>

               <?php if (!empty($error)): ?>
               <div class="alert alert-danger">
               <?php echo $error; ?>
               </div>
               <?php endif; ?>
                <form id="studentForm" method="POST" action="form.php">

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Student Name
                        </label>
                        <input type="text" id="name" name="name"
                               class="form-control"
                               placeholder="Enter student name"
                               oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Email
                        </label>
                        <input type="email" id="email" name="email"
                               class="form-control"
                               placeholder="Enter email">
                    </div>

                    <div class="mb-3">
                        Phone Number
                        <input type="tel" id="phone" name="phone"
                               class="form-control" required
                               maxlength="10"
                               oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                            
                        </label>
                        
                    </div>

                    <button type="submit" class="btn btn-success">
                        Register Student
                    </button>

                    <button type="reset" class="btn btn-secondary">
                        Clear
                    </button>

                </form>

            </div>
        </div>
    </div>

    <script>
        const studentForm = document.getElementById("studentForm");

        studentForm.addEventListener("submit", function(event) {
            

            const name = document.getElementById("name").value.trim();
            const email = document.getElementById("email").value.trim();
            const phone = document.getElementById("phone").value.trim();

            if (name === "" || email === "" || phone === "") {
                alert("Please fill in all fields!");
                return;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert("Please enter a valid email address!");
                return;
            }

            if (!/^\d{10}$/.test(phone)) {
                alert("Phone number must contain exactly 10 digits!");
                return;
            }

            alert("Student registered successfully!");
        });
    </script>
    <script>
     document.addEventListener("DOMContentLoaded", function () {
          const nameInput = document.getElementById("name");

           if (nameInput) {
              nameInput.addEventListener("input", function () {
            if (this.value.trim().length < 3) {
                this.style.borderColor = "red";
            } else {
                this.style.borderColor = "green";
            }
        });
    }
});
</script>
</body>
</html>
