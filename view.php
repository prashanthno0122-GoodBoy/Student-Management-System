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
        <table id="entriesTable" class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                   <th>ID</th>
                   <th onclick="sortTable(1)" style="cursor:pointer;">Name ↕</th>
                   <th onclick="sortTable(2)" style="cursor:pointer;">Email ↕</th>
                   <th onclick="sortTable(3)" style="cursor:pointer;">Phone ↕</th>
                  <th>Created At</th>
                  <th>Actions</th>
               </tr>
        </thead>

            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                     echo "<tr onmouseover=\"this.style.backgroundColor='#f2f2f2'\" onmouseout=\"this.style.backgroundColor=''\">";

                      echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";

                      echo "<td>";
                      echo "<a href='update.php?id=" . (int)$row['id'] . "' class='btn btn-sm btn-primary me-2'>Edit</a>";
                      echo "<button type='button' class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal' data-delete-url='delete.php?id=" . (int)$row['id'] . "'>Delete</button>";
                      echo "</td>";

                       echo "</tr>";
                       }
                    }
                 else {
                    echo "<tr><td colspan='6' class='text-center'>No entries found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
    <!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1"
     aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to delete this student?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <a href="#" id="confirmDeleteBtn"
                   class="btn btn-danger">Yes, Delete</a>
            </div>

        </div>
    </div>
</div>
    <script>
function sortTable(n) {
    const table = document.getElementById("entriesTable");
    const tbody = table.tBodies[0];
    const rows = Array.from(tbody.rows);

    if (rows.length === 0 || rows[0].cells.length < 5) return;

    const current = table.dataset.sortColumn;
    const direction =
        current === String(n) && table.dataset.sortDirection === "asc"
            ? "desc"
            : "asc";

    rows.sort((a, b) => {
        const x = a.cells[n].textContent.trim().toLowerCase();
        const y = b.cells[n].textContent.trim().toLowerCase();

        return direction === "asc"
            ? x.localeCompare(y, undefined, { numeric: true })
            : y.localeCompare(x, undefined, { numeric: true });
    });

    rows.forEach(row => tbody.appendChild(row));

    table.dataset.sortColumn = n;
    table.dataset.sortDirection = direction;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const deleteModal = document.getElementById('deleteModal');

deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const deleteUrl = button.getAttribute('data-delete-url');

    document.getElementById('confirmDeleteBtn').href = deleteUrl;
});
</script>
</body>
</html>