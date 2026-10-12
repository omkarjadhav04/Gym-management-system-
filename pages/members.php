<?php
require_once '../includes/auth.php';
require_admin();
include '../config/database.php';

$formData = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'membership_plan_id' => '',
];
$message = isset($_GET['added']) ? 'Member added successfully.' : (isset($_GET['updated']) ? 'Member details updated successfully.' : (isset($_GET['deleted']) ? 'Member deleted successfully.' : ''));
$messageClass = 'alert-success';
$plans = [];
$members = [];
$queryError = false;
if ($conn instanceof PDO) {
    try {
        $plans = $conn->query('SELECT id, plan_name, price FROM membership_plans ORDER BY duration_months')->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $queryError = true;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
        $memberId = filter_var($_POST['member_id'] ?? '', FILTER_VALIDATE_INT);
        if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
            $message = 'Security check failed. Refresh the page and try again.';
            $messageClass = 'alert-error';
        } elseif (!$memberId) {
            $message = 'Select a valid member to delete.';
            $messageClass = 'alert-error';
        } else {
            try {
                $stmt = $conn->prepare('DELETE FROM members WHERE id = ?');
                $stmt->execute([$memberId]);
                if ($stmt->rowCount() > 0) {
                    header('Location: members.php?deleted=1');
                    exit;
                }
                $message = 'Member not found.';
                $messageClass = 'alert-error';
            } catch (PDOException $e) {
                $message = $e->getCode() === '23000'
                    ? 'This member has payment records and cannot be deleted.'
                    : 'Unable to delete this member. Try again.';
                $messageClass = 'alert-error';
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($formData as $field => $value) {
            $formData[$field] = trim($_POST[$field] ?? '');
        }

        if ($formData['name'] === '' || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL) || $formData['phone'] === '' || strlen($formData['phone']) > 15) {
            $message = 'Enter a name, valid email, and phone number of 15 characters or fewer.';
            $messageClass = 'alert-error';
        } else {
            $planId = $formData['membership_plan_id'] === '' ? null : filter_var($formData['membership_plan_id'], FILTER_VALIDATE_INT);
            $planIds = array_map('intval', array_column($plans, 'id'));
            if ($formData['membership_plan_id'] !== '' && (!$planId || !in_array($planId, $planIds, true))) {
                $message = 'Select a valid membership plan.';
                $messageClass = 'alert-error';
            } else {
                try {
                    $stmt = $conn->prepare('INSERT INTO members (name, email, phone, membership_plan_id, join_date) VALUES (?, ?, ?, ?, ?)');
                    $stmt->execute([$formData['name'], $formData['email'], $formData['phone'], $planId, date('Y-m-d')]);
                    header('Location: members.php?added=1');
                    exit;
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000') {
                        $message = 'That email address is already registered.';
                    } else {
                        $message = 'Unable to add this member. Check the database setup and try again.';
                    }
                    $messageClass = 'alert-error';
                }
            }
        }
    }

    try {
        $query = 'SELECT members.id, members.name, members.email, members.phone, membership_plans.plan_name AS membership_plan FROM members LEFT JOIN membership_plans ON membership_plans.id = members.membership_plan_id ORDER BY members.id';
        $members = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $queryError = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Members - Gym Management System</title>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container">
    <h1>Members List</h1>
    <?php if ($message): ?>
        <div class="alert <?php echo $messageClass; ?>" role="status"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if (!$conn instanceof PDO): ?>
        <div class="alert alert-error" role="status">The database is unavailable. Create the gym_management database using database/schema.sql.</div>
    <?php elseif ($queryError): ?>
        <div class="alert alert-error" role="status">The member tables are missing or invalid. Re-import database/schema.sql into gym_management.</div>
    <?php endif; ?>

    <section class="member-form-section">
        <h2>Add a Member</h2>
        <form method="POST" class="member-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <div class="form-group">
                <label for="name">Full name</label>
                <input id="name" name="name" class="form-control" type="text" maxlength="100" value="<?php echo htmlspecialchars($formData['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" class="form-control" type="email" maxlength="100" value="<?php echo htmlspecialchars($formData['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input id="phone" name="phone" class="form-control" type="tel" maxlength="15" value="<?php echo htmlspecialchars($formData['phone']); ?>" required>
            </div>
            <div class="form-group">
                <label for="membership_plan_id">Membership plan (optional)</label>
                <select id="membership_plan_id" name="membership_plan_id" class="form-control">
                    <option value="">No plan selected</option>
                    <?php foreach ($plans as $plan): ?>
                        <option value="<?php echo htmlspecialchars($plan['id']); ?>" <?php echo $formData['membership_plan_id'] === (string) $plan['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($plan['plan_name']); ?> - ₹<?php echo number_format((float) $plan['price']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="button">Add member</button>
        </form>
    </section>

    <h2>Member Directory</h2>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Membership Plan</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($members): ?>
                <?php $rowNumber = 1; ?>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td><?php echo $rowNumber++; ?></td>
                        <td><?php echo htmlspecialchars($member['name']); ?></td>
                        <td><?php echo htmlspecialchars($member['email']); ?></td>
                        <td><?php echo htmlspecialchars($member['phone']); ?></td>
                        <td><?php echo htmlspecialchars($member['membership_plan'] ?? ''); ?></td>
                        <td>
                            <a href="edit_member.php?id=<?php echo $member['id']; ?>">Edit</a>
                            <form method="POST" class="inline-delete-form" onsubmit="return confirm('Delete this member? Members with payment history cannot be deleted.');">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="member_id" value="<?php echo htmlspecialchars($member['id']); ?>">
                                <button type="submit" class="text-button text-button-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No members found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>