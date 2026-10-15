<?php
require_once '../includes/auth.php';
require_admin();
ob_start();
include '../assets/config/database.php';
$databaseError = ob_get_clean();
include '../assets/config/membership-plans.php';
include '../includes/header.php';

$notice = '';
$noticeClass = 'alert-success';
$databaseAvailable = isset($conn) && $conn instanceof PDO;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberId = filter_var($_POST['member_id'] ?? '', FILTER_VALIDATE_INT);
    $planKey = $_POST['plan'] ?? '';

    if (!$databaseAvailable) {
        $notice = 'MySQL is unavailable. Start MySQL in XAMPP to record payments.';
        $noticeClass = 'alert-error';
    } elseif (!$memberId || !isset($membershipPlans[$planKey])) {
        $notice = 'Choose a valid member and membership duration.';
        $noticeClass = 'alert-error';
    } else {
        $memberCheck = $conn->prepare('SELECT id FROM members WHERE id = ?');
        $memberCheck->execute([$memberId]);

        if (!$memberCheck->fetchColumn()) {
            $notice = 'The selected member could not be found.';
            $noticeClass = 'alert-error';
        } else {
            $amount = $membershipPlans[$planKey]['price'];
            $paymentDate = date('Y-m-d');
            $stmt = $conn->prepare('INSERT INTO payments (member_id, amount, payment_date) VALUES (?, ?, ?)');
            $stmt->execute([$memberId, $amount, $paymentDate]);
            $notice = 'Payment recorded: ₹' . number_format($amount) . ' for ' . $membershipPlans[$planKey]['name'] . '.';
        }
    }
}

$members = [];
$payments = [];
if ($databaseAvailable) {
    $members = $conn->query('SELECT id, name FROM members ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
    $payments = $conn->query('SELECT payments.id, members.name AS member_name, payments.amount, payments.payment_date FROM payments JOIN members ON members.id = payments.member_id ORDER BY payments.payment_date DESC, payments.id DESC')->fetchAll(PDO::FETCH_ASSOC);
}

?>

<div class="container">
    <?php if (!$databaseAvailable): ?>
        <div class="alert alert-error" role="status">MySQL is unavailable. Start MySQL in XAMPP to load members and record payments.</div>
    <?php endif; ?>
    <?php if ($notice): ?>
        <div class="alert <?php echo $noticeClass; ?>" role="status"><?php echo htmlspecialchars($notice); ?></div>
    <?php endif; ?>

    <h2>Payment History</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Member</th>
                <th>Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($payments): ?>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['id']); ?></td>
                        <td><?php echo htmlspecialchars($payment['member_name']); ?></td>
                        <td>₹<?php echo number_format((float) $payment['amount']); ?></td>
                        <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No payments recorded.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Record a Payment</h2>
    <form method="POST">
        <div class="form-group">
            <label for="member_id">Member</label>
            <select id="member_id" name="member_id" class="form-control" required <?php echo $members ? '' : 'disabled'; ?>>
                <option value="">Select a member</option>
                <?php foreach ($members as $member): ?>
                    <option value="<?php echo htmlspecialchars($member['id']); ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="plan">Membership duration</label>
            <select id="plan" name="plan" class="form-control" required>
                <option value="">Select a duration</option>
                <?php foreach ($membershipPlans as $key => $plan): ?>
                    <option value="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($plan['name']); ?> - ₹<?php echo number_format($plan['price']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if (!$members && $databaseAvailable): ?>
            <p>Add a member before recording a payment.</p>
        <?php endif; ?>
        <button type="submit" class="button" <?php echo $members ? '' : 'disabled'; ?>>Record payment</button>
    </form>
</div>

<?php
include '../includes/footer.php';
?>