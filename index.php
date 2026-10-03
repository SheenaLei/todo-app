<?php
include 'db.php';

// Add Task
if (isset($_POST['add_task'])) {
    $task = trim($_POST['task']);
    $description = trim($_POST['description']);
    $deadline_date = $_POST['deadline_date'];
    $deadline_time = $_POST['deadline_time'];
    $status = $_POST['status'];

    if (!empty($task)) {
        $stmt = $conn->prepare("INSERT INTO tasks (task, description, deadline_date, deadline_time, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $task, $description, $deadline_date, $deadline_time, $status);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit();
    }
}

// Edit Task
if (isset($_POST['edit_task'])) {
    $id = intval($_POST['id']);
    $task = trim($_POST['task']);
    $description = trim($_POST['description']);
    $deadline_date = $_POST['deadline_date'];
    $deadline_time = $_POST['deadline_time'];
    $status = $_POST['status'];

    if (!empty($task)) {
        $stmt = $conn->prepare("UPDATE tasks SET task = ?, description = ?, deadline_date = ?, deadline_time = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $task, $description, $deadline_date, $deadline_time, $status, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit();
    }
}

// Delete Task
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// Filter Logic
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'All';
if ($filter == 'Pending') {
    $result = $conn->query("SELECT * FROM tasks WHERE status = 'Pending' ORDER BY id DESC");
} elseif ($filter == 'Completed') {
    $result = $conn->query("SELECT * FROM tasks WHERE status = 'Completed' ORDER BY id DESC");
} else {
    $result = $conn->query("SELECT * FROM tasks ORDER BY id DESC");
}

// Count statistics
$totalTasks = $conn->query("SELECT COUNT(*) as count FROM tasks")->fetch_assoc()['count'];
$pendingTasks = $conn->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'Pending'")->fetch_assoc()['count'];
$completedTasks = $conn->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'Completed'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #061a12; color: #ffffff; font-family: sans-serif; animation: fadeIn 0.6s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        
        .card-main { background-color: #092c1e; border: 1px solid #134e35; transition: transform 0.3s ease; }
        .btn-emerald { background-color: #046a38; color: white; border: none; transition: background-color 0.2s, transform 0.2s; }
        .btn-emerald:hover { background-color: #03532c; color: white; transform: scale(1.02); }
        
        .form-control, .form-select { background-color: #051f15; border: 1px solid #165b3e; color: #ffffff; }
        .form-control::placeholder { color: #a0b8ad; opacity: 1; }
        .form-control:focus, .form-select:focus { background-color: #051f15; border-color: #046a38; color: #ffffff; box-shadow: none; }
        
        /* Custom Dark Table Styles for Perfect Readability */
        .table { color: #ffffff; background-color: transparent; }
        .table th { background-color: #072217 !important; color: #2ecc71 !important; border-bottom: 2px solid #134e35; }
        .table td { background-color: #092c1e !important; color: #ffffff !important; border-bottom: 1px solid #134e35; vertical-align: middle; }
        .table-hover tbody tr:hover td { background-color: #0d3b27 !important; color: #ffffff !important; }
        
        .modal-content { background-color: #092c1e; color: #ffffff; border: 1px solid #165b3e; animation: modalPop 0.3s ease-out; }
        @keyframes modalPop { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        
        .badge-pending { background-color: #f1c40f; color: #000000; font-weight: 600; }
        .badge-completed { background-color: #2ecc71; color: #000000; font-weight: 600; }
        
        .filter-btn { background-color: #0d3b27; color: #b0d4c4; border: 1px solid #165b3e; transition: all 0.2s; }
        .filter-btn.active, .filter-btn:hover { background-color: #046a38; color: white; border-color: #046a38; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <!-- Header Card -->
                <div class="card card-main shadow-sm mb-4 p-4 rounded-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 rounded-3" style="background-color: #046a38;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="white" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="mb-0 fw-bold text-white">My To-Do List</h2>
                                <p class="mb-0 text-light small">Stay organized. Get things done.</p>
                            </div>
                        </div>
                        <button class="btn btn-emerald px-4 py-2 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                            + Add Task
                        </button>
                    </div>

                    <!-- Top Filter Bar -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-secondary flex-wrap gap-2">
                        <div class="btn-group" role="group">
                            <a href="index.php?filter=All" class="btn filter-btn <?php echo ($filter == 'All') ? 'active' : ''; ?>">All Tasks</a>
                            <a href="index.php?filter=Pending" class="btn filter-btn <?php echo ($filter == 'Pending') ? 'active' : ''; ?>">Pending</a>
                            <a href="index.php?filter=Completed" class="btn filter-btn <?php echo ($filter == 'Completed') ? 'active' : ''; ?>">Completed</a>
                        </div>
                        <div class="text-light small">
                            Total: <span class="text-white fw-bold"><?php echo $totalTasks; ?></span> | 
                            Pending: <span class="text-warning fw-bold"><?php echo $pendingTasks; ?></span> | 
                            Completed: <span class="text-success fw-bold"><?php echo $completedTasks; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Tasks Table Card -->
                <div class="card card-main shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3 fw-bold text-white">Tasks</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
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
                                                    <span class="fw-semibold text-white d-block"><?php echo htmlspecialchars($row['task']); ?></span>
                                                    <small class="text-light" style="opacity: 0.85;"><?php echo htmlspecialchars($row['description']); ?></small>
                                                </td>
                                                <td>
                                                    <small class="text-light d-block" style="opacity: 0.85;"><?php echo $row['deadline_date']; ?></small>
                                                    <small class="text-light" style="opacity: 0.85;"><?php echo $row['deadline_time']; ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($row['status'] == 'Completed'): ?>
                                                        <span class="badge badge-completed px-3 py-1">Completed</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-pending px-3 py-1">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <!-- Edit Button triggers Modal -->
                                                    <button class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>" title="Edit Task">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                                        </svg>
                                                    </button>
                                                    <!-- Delete with confirmation -->
                                                    <a href="index.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this task?');" title="Delete Task">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                                        </svg>
                                                    </a>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal for each task -->
                                            <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content p-3 rounded-4">
                                                        <div class="modal-header border-0">
                                                            <h5 class="modal-title text-white fw-bold">Edit Task</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form method="POST" action="">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                                <div class="mb-3">
                                                                    <label class="form-label text-light small">Task Name</label>
                                                                    <input type="text" name="task" class="form-control" value="<?php echo htmlspecialchars($row['task']); ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label text-light small">Description</label>
                                                                    <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($row['description']); ?></textarea>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label text-light small">Deadline Date</label>
                                                                        <input type="date" name="deadline_date" class="form-control" value="<?php echo $row['deadline_date']; ?>">
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label text-light small">Deadline Time</label>
                                                                        <input type="time" name="deadline_time" class="form-control" value="<?php echo $row['deadline_time']; ?>">
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label text-light small">Status</label>
                                                                    <select name="status" class="form-select">
                                                                        <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                                        <option value="Completed" <?php echo ($row['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0">
                                                                <button type="submit" name="edit_task" class="btn btn-emerald w-100 py-2 rounded-3">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-light py-4">No tasks found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 rounded-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white fw-bold">Add New Task</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-light small">Task Name</label>
                            <input type="text" name="task" class="form-control" placeholder="e.g. Finish IT report" required autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-light small">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Additional details..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light small">Deadline Date</label>
                                <input type="date" name="deadline_date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-light small">Deadline Time</label>
                                <input type="time" name="deadline_time" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-light small">Initial Status</label>
                            <select name="status" class="form-select">
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" name="add_task" class="btn btn-emerald w-100 py-2 rounded-3">Add Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>