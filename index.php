<?php
include 'assets/config/database.php';
include 'assets/config/membership-plans.php';

$machines = [];
$machineLoadError = false;
if ($conn instanceof PDO) {
    try {
        $machines = $conn->query('SELECT machine_name, category, total_units, available_units FROM machines ORDER BY machine_name')->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $machineLoadError = true;
    }
}

include 'includes/header.php';
?>

<main>
    <section class="hero">
        <div class="container hero-content">
            <p class="eyebrow">IFC fitness club / OVERVIEW</p>
            <h1>Train well.<br><span>Feel stronger.</span></h1>
            <p class="hero-copy">Explore membership options and check the equipment available at our gym.</p>
            <a class="button hero-button" href="#membership">View memberships <span aria-hidden="true">&darr;</span></a>
            <div class="hero-index"><span>01</span> / MEMBERSHIPS & EQUIPMENT</div>
        </div>
    </section>

    <section id="membership" class="container public-section">
        <div class="section-heading">
            <div>
                <p class="eyebrow">CHOOSE YOUR ROUTINE</p>
                <h2>Membership plans</h2>
            </div>
            <a class="section-link" href="pages/membership-plans.php">Plan details <span aria-hidden="true">&rarr;</span></a>
        </div>
        <div class="public-plan-grid">
            <?php foreach ($membershipPlans as $plan): ?>
                <article class="public-plan-card">
                    <p class="card-kicker">MEMBERSHIP</p>
                    <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
                    <p class="public-plan-price">₹<?php echo number_format($plan['price']); ?></p>
                    <p class="public-plan-duration"><?php echo htmlspecialchars($plan['description']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="container equipment-section public-section" aria-labelledby="equipment-title">
        <div class="section-heading">
            <div>
                <p class="eyebrow">LIVE GYM INVENTORY</p>
                <h2 id="equipment-title">Machines available</h2>
            </div>
            <?php if (is_admin_logged_in()): ?>
                <a class="button equipment-manage-link" href="pages/machines.php">Manage machines <span aria-hidden="true">&rarr;</span></a>
            <?php endif; ?>
        </div>

        <div class="equipment-grid">
            <?php if ($machineLoadError): ?>
                <p class="equipment-empty">Equipment availability is temporarily unavailable.</p>
            <?php elseif ($machines): ?>
                <?php foreach ($machines as $index => $machine): ?>
                    <article class="equipment-item">
                        <span class="equipment-number"><?php echo str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?></span>
                        <div class="equipment-copy">
                            <h3><?php echo htmlspecialchars($machine['machine_name']); ?></h3>
                            <p><?php echo htmlspecialchars($machine['category']); ?></p>
                        </div>
                        <span class="availability <?php echo (int) $machine['available_units'] > 0 ? '' : 'availability-none'; ?>">
                            <span></span><?php echo (int) $machine['available_units']; ?> / <?php echo (int) $machine['total_units']; ?> available
                        </span>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="equipment-empty">Machine availability will appear here when equipment is added.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>