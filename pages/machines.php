<?php
require_once '../includes/auth.php';
require_admin();
include '../assets/config/database.php';

$message = '';
$messageClass = 'alert-success';
$machines = [];
$loadError = false;
$categories = ['Cardio', 'Strength', 'Functional', 'Other'];

if ($conn instanceof PDO && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
        $message = 'Security check failed. Refresh the page and try again.';
        $messageClass = 'alert-error';
    } elseif ($action === 'delete') {
        $machineId = filter_var($_POST['machine_id'] ?? '', FILTER_VALIDATE_INT);
        if (!$machineId) {
            $message = 'Select a valid machine to delete.';
            $messageClass = 'alert-error';
        } else {
            $stmt = $conn->prepare('DELETE FROM machines WHERE id = ?');
            $stmt->execute([$machineId]);
            header('Location: machines.php?deleted=1');
            exit;
        }
    } elseif ($action === 'add' || $action === 'update') {
        $machineId = $action === 'update' ? filter_var($_POST['machine_id'] ?? '', FILTER_VALIDATE_INT) : null;
        $name = trim($_POST['machine_name'] ?? '');
        $category = $_POST['category'] ?? '';
        $totalUnits = filter_var($_POST['total_units'] ?? '', FILTER_VALIDATE_INT);
        $availableUnits = filter_var($_POST['available_units'] ?? '', FILTER_VALIDATE_INT);

        if (($action === 'update' && !$machineId) || $name === '' || strlen($name) > 100 || !in_array($category, $categories, true) || !$totalUnits || $totalUnits < 1 || $availableUnits === false || $availableUnits < 0 || $availableUnits > $totalUnits) {
            $message = 'Enter a machine name, category, total count, and available count within the total.';
            $messageClass = 'alert-error';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $conn->prepare('INSERT INTO machines (machine_name, category, total_units, available_units) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$name, $category, $totalUnits, $availableUnits]);
                    header('Location: machines.php?added=1');
                } else {
                    $stmt = $conn->prepare('UPDATE machines SET machine_name = ?, category = ?, total_units = ?, available_units = ? WHERE id = ?');
                    $stmt->execute([$name, $category, $totalUnits, $availableUnits, $machineId]);
                    header('Location: machines.php?updated=1');
                }
                exit;
            } catch (PDOException $e) {
                $message = $e->getCode() === '23000'
                    ? 'A machine with that name already exists.'
                    : 'Unable to save this machine. Check the database setup and try again.';
                $messageClass = 'alert-error';
            }
        }
    }
}

if (isset($_GET['added'])) {
    $message = 'Machine added.';
} elseif (isset($_GET['updated'])) {
    $message = 'Machine availability updated.';
} elseif (isset($_GET['deleted'])) {
    $message = 'Machine deleted.';
}

if ($conn instanceof PDO) {
    try {
        $machines = $conn->query('SELECT id, machine_name, category, total_units, available_units FROM machines ORDER BY machine_name')->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $loadError = true;
    }
}
?>

<?php include '../includes/header.php'; ?>

<main class="container machine-page">
    <a class="back-link" href="../index.php">&larr; Back to overview</a>
    <h1>Machine Inventory</h1>

    <?php if ($message): ?>
        <div class="alert <?php echo $messageClass; ?>" role="status"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if (!$conn instanceof PDO): ?>
        <div class="alert alert-error" role="status">The database is unavailable. Start MySQL in XAMPP and try again.</div>
    <?php elseif ($loadError): ?>
        <div class="alert alert-error" role="status">Machine inventory table is not ready. Apply the machines table from database/schema.sql.</div>
    <?php endif; ?>

    <section class="member-form-section">
        <h2>Add a Machine</h2>
        <form method="POST" class="machine-add-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="machine_name">Machine name</label>
                <input id="machine_name" name="machine_name" class="form-control" type="text" maxlength="100" required>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="total_units">Total units</label>
                <input id="total_units" name="total_units" class="form-control" type="number" min="1" value="1" required>
            </div>
            <div class="form-group">
                <label for="available_units">Available now</label>
                <input id="available_units" name="available_units" class="form-control" type="number" min="0" value="1" required>
            </div>
            <button type="submit" class="button">Add machine</button>
        </form>
    </section>

    <h2>Equipment List</h2>
    <table>
        <thead>
            <tr><th>No.</th><th>Machine</th><th>Category</th><th>Availability</th><th>Update</th><th>Delete</th></tr>
        </thead>
        <tbody>
            <?php if ($machines): ?>
                <?php foreach ($machines as $index => $machine): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($machine['machine_name']); ?></td>
                        <td><?php echo htmlspecialchars($machine['category']); ?></td>
                        <td><?php echo (int) $machine['available_units']; ?> / <?php echo (int) $machine['total_units']; ?></td>
                        <td>
                            <form method="POST" class="machine-update-form">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="machine_id" value="<?php echo htmlspecialchars($machine['id']); ?>">
                                <input class="form-control machine-count-input" type="number" name="total_units" min="1" value="<?php echo (int) $machine['total_units']; ?>" aria-label="Total units for <?php echo htmlspecialchars($machine['machine_name']); ?>" required>
                                <input class="form-control machine-count-input" type="number" name="available_units" min="0" max="<?php echo (int) $machine['total_units']; ?>" value="<?php echo (int) $machine['available_units']; ?>" aria-label="Available units for <?php echo htmlspecialchars($machine['machine_name']); ?>" required>
                                <input type="hidden" name="machine_name" value="<?php echo htmlspecialchars($machine['machine_name']); ?>">
                                <input type="hidden" name="category" value="<?php echo htmlspecialchars($machine['category']); ?>">
                                <button type="submit" class="text-button">Save</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" class="inline-delete-form" onsubmit="return confirm('Delete this machine from inventory?');">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="machine_id" value="<?php echo htmlspecialchars($machine['id']); ?>">
                                <button type="submit" class="text-button text-button-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No machines added yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php include '../includes/footer.php'; ?>