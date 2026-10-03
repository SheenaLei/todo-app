<?php
include 'db.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';
if ($filter == 'Pending') {
    $result = $conn->query("SELECT * FROM tasks WHERE status = 'Pending' ORDER BY id DESC");
} elseif ($filter == 'Completed') {
    $result = $conn->query("SELECT * FROM tasks WHERE status = 'Completed' ORDER BY id DESC");
} else {
    $result = $conn->query("SELECT * FROM tasks ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="navbar-custom py-3 mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold"><i class="bi bi-check2-square"></i> My To-Do List</h4>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="card card-custom p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h3 class="fw-bold text-white mb-1">Task Dashboard</h3>
                            <p class="text-secondary small mb-0">Manage your pending and completed activities.</p>
                        </div>
                        <a href="create.php" class="btn btn-emerald px-4 py-2 rounded-3 text-decoration-none">
                            <i class="bi bi-plus-lg"></i> Add New Task
                        </a>
                    </div>
                </div>

                <div class="card card-custom p-4">
                    <h5 class="fw-bold mb-3 text-white">Task List</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-custom align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Task & Description</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php $i = 1; while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo $i++; ?></td>
                                            <td>
                                                <span class="fw-bold text-white d-block"><?php echo htmlspecialchars($row['task']); ?></span>
                                                <small class="text-secondary"><?php echo htmlspecialchars($row['description']); ?></small>
                                            </td>
                                            <td>
                                                <small class="text-white d-block fw-semibold"><?php echo $row['deadline_date']; ?></small>
                                                <small class="text-secondary"><?php echo $row['deadline_time']; ?></small>
                                            </td>
                                            <td>
                                                <?php if ($row['status'] == 'Completed'): ?>
                                                    <span class="badge badge-completed px-3 py-1">Completed</span>
                                                <?php else: ?>
                                                    <span class="badge badge-pending px-3 py-1">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success me-1" title="Edit">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this task?');" title="Delete">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-secondary py-4">No tasks found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>