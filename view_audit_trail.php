<?php
include 'functions.php';
$audit_logs = getAuditLogs();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Audit Trail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Audit Trail</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Item Type</th>
                    <th>Item ID</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while($log = $audit_logs->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $log['action']; ?></td>
                    <td><?php echo $log['item_type']; ?></td>
                    <td><?php echo $log['item_id']; ?></td>
                    <td><?php echo $log['timestamp']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-primary">Kembali ke Dashboard</a>
    </div>
</body>
</html>
