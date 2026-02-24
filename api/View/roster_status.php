<?php
require_once("../config/db2.php");
require_once("../auth/roster.php");

//requireManager();

// Minimum volunteers required
$minVolunteers = 6;

// Get all future patrols
$stmt = $pdo->prepare("
    SELECT patrolNr, patrolDate, patrolDescription, SuperUserNr
    FROM cw_patrol_schedule
    WHERE patrolDate >= CURDATE()
    ORDER BY patrolDate ASC
");
$stmt->execute();
$patrols = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Roster Resourcing Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<?php include __DIR__ . "/../includes/nav.php"; ?>

<h2>Roster Resourcing Status</h2>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Patrol Date</th>
            <th>Description</th>
            <th>Volunteers Assigned</th>
            <th>SUPER Assigned</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>

<?php foreach ($patrols as $patrol): ?>

<?php
    // Count volunteers
    $volStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM cw_patrol_roster r
        JOIN cw_user u ON r.volunteer_ID_Nr = u.UserNr
        WHERE r.patrolNr = ?
        AND u.userEnabled = 1
    ");
    $volStmt->execute([$patrol['patrolNr']]);
    $volunteerCount = $volStmt->fetchColumn();

    // Check SUPER
    $superAssigned = false;

    if (!empty($patrol['SuperUserNr'])) {
        $superStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM cw_user
            WHERE UserNr = ?
            AND userEnabled = 1
        ");
        $superStmt->execute([$patrol['SuperUserNr']]);
        $superAssigned = $superStmt->fetchColumn() > 0;
    }

    // Business Rule
    if ($volunteerCount >= $minVolunteers && $superAssigned) {
        $status = "<span class='text-success fw-bold'>Sufficiently Resourced</span>";
    } else {
        $status = "<span class='text-danger fw-bold'>Under-Resourced</span>";
    }
?>

<tr>
    <td><?= htmlspecialchars($patrol['patrolDate']) ?></td>
    <td><?= htmlspecialchars($patrol['patrolDescription']) ?></td>
    <td><?= $volunteerCount ?></td>
    <td><?= $superAssigned ? "Yes" : "No" ?></td>
    <td><?= $status ?></td>
</tr>

<?php endforeach; ?>

    </tbody>
</table>

</body>
</html>
