<?php
require_once '../includes/auth.php';
require_admin();
include '../config/database.php';

$memberId = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
$member = null;
$plans = [];
$formData = ['name' => '', 'email' => '', 'phone' => '', 'membership_plan_id' => ''];
$message = '';
$messageClass = 'alert-error';
$queryError = false;

if ($conn instanceof PDO && $memberId) {
    try {
        $plans = $conn->query('SELECT id, plan_name, price FROM membership_plans ORDER BY duration_months')->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $conn->prepare('SELECT id, name, email, phone, membership_plan_id FROM members WHERE id = ?');
        $stmt->execute([$memberId]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($member) {
            foreach ($formData as $field => $value) {
                $formData[$field] = $member[$field] === null ? '' : (string) $member[$field];
            }
        }
    } catch (PDOException $e) {
        $queryError = true;
    }

    if ($member && $_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($formData as $field => $value) {
            $formData[$field] = trim($_POST[$field] ?? '');
        }

        if ($formData['name'] === '' || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL) || $formData['phone'] === '' || strlen($formData['phone']) > 15) {
            $message = 'Enter a name, valid email, and phone number of 15 characters or fewer.';
        } else {
            $planId = $formData['membership_plan_id'] === '' ? null : filter_var($formData['membership_plan_id'], FILTER_VALIDATE_INT);
            $planIds = array_map('intval', array_column($plans, 'id'));

            if ($formData['membership_plan_id'] !== '' && (!$planId || !in_array($planId, $planIds, true))) {
                $message = 'Select a valid membership plan.';
            } else {
                try {
                    $stmt = $conn->prepare('UPDATE members SET name = ?, email = ?, phone = ?, membership_plan_id = ? WHERE id = ?');
                    $stmt->execute([$formData['name'], $formData['email'], $formData['phone'], $planId, $memberId]);
                    header('Location: members.php?updated=1');
                    exit;
                } catch (PDOException $e) {
                    $message = $e->getCode() === '23000'
                        ? 'That email address is already registered to another member.'
                        : 'Unable to update this member. Check the database setup and try again.';
                }
            }
        }
    }
} elseif ($conn instanceof PDO) {
    http_response_code(404);
}
?>

<?php include '../includes/header.php'; ?>

<main class="container">
    <a class="back-link" href="members.php">&larr; Back to members</a>
    <h1>Edit Member</h1>

    <?php if (!$conn instanceof PDO): ?>
        <div class="alert alert-error" role="status">The database is unavailable. Start MySQL in XAMPP and try again.</div>
    <?php elseif ($queryError): ?>
        <div class="alert alert-error" role="status">Unable to load this member. Check the database tables and try again.</div>
    <?php elseif (!$member): ?>
        <div class="alert alert-error" role="status">Member not found.</div>
    <?php else: ?>
        <?php if ($message): ?>
            <div class="alert <?php echo $messageClass; ?>" role="status"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <section class="member-form-section">
            <form method="POST" class="member-form">
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
                <button type="submit" class="button">Save changes</button>
            </form>
        </section>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>