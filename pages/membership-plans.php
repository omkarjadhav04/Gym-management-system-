<?php
include '../config/membership-plans.php';
include '../includes/header.php';
?>

<div class="container">
    <h1>Membership Plans</h1>
    <table class="membership-plans">
        <thead>
            <tr>
                <th>Plan Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($membershipPlans as $plan): ?>
                <tr>
                    <td><?php echo htmlspecialchars($plan['name']); ?></td>
                    <td><?php echo htmlspecialchars($plan['description']); ?></td>
                    <td>₹<?php echo number_format($plan['price']); ?></td>
                    <td><?php echo $plan['duration_months']; ?> month<?php echo $plan['duration_months'] === 1 ? '' : 's'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include '../includes/footer.php';
?>