<?php
include 'db.php';

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM tasks WHERE id = $id");
$row = $result->fetch_assoc();

if (isset($_POST['update'])) {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="py-5">
    <div class="container col-md-6">
        <div class="card card-custom p-4">
            <h3 class="fw-bold mb-3 text-white"><i class="bi bi-pencil-square"></i> Edit Task</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label text-secondary">Task Name</label>
                    <input type="text" name="task" class="form-control" value="<?php echo htmlspecialchars($row['task']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-secondary">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($row['description']); ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary">Deadline Date</label>
                        <input type="date" name="deadline_date" class="form-control" value="<?php echo $row['deadline_date']; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-secondary">Deadline Time</label>
                        <input type="time" name="deadline_time" class="form-control" value="<?php echo $row['deadline_time']; ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-secondary">Status</label>
                    <select name="status" class="form-select">
                        <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Completed" <?php echo ($row['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <button type="submit" name="update" class="btn btn-emerald w-100 py-2">Update Task</button>
                <a href="index.php" class="btn btn-outline-secondary w-100 py-2 mt-2">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>