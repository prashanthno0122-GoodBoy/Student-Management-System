<?php
include 'config.php';

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $sql = "SELECT * FROM entries
            WHERE name LIKE ? OR email LIKE ?
            ORDER BY id DESC";

    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();

    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM entries ORDER BY id DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4 text-center">Student Records</h2>
     <form method="GET" action="view.php" class="row g-2 mb-4">
    <div class="col-md-10">
        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Search by Name or Email"
            value="<?php echo htmlspecialchars($search); ?>"
        >
    </div>

    <div class="col-md-2 d-grid">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<td>";
                        echo "<a href='update.php?id=" . (int)$row['id'] . "' class='btn btn-sm btn-primary me-2'>Edit</a>";
                        echo "<a href='delete.php?id=" . (int)$row['id'] . "' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure you want to delete this student?');\">Delete</a>";
                        echo "</td>";
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No entries found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>