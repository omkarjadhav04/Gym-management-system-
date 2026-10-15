<?php
require_once 'includes/auth.php';
require_admin();
include 'assets/config/database.php';

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
            <p class="eyebrow">ADMIN WORKSPACE</p>
            <h1>Good to have<br><span>you back.</span></h1>
            <p class="hero-copy">Manage members, memberships, payments, and equipment in one place.</p>
            <a class="button hero-button" href="pages/members.php">Manage members <span aria-hidden="true">&rarr;</span></a>
            <div class="hero-index"><span>01</span> / GYM MANAGEMENT</div>
        </div>
    </section>

    <section class="container workspace-section">
        <div class="section-heading">
            <div>
                <p class="eyebrow">ADMIN TOOLS</p>
                <h2>Manage your gym</h2>
            </div>
            <span class="section-mark" aria-hidden="true">G / 01</span>
        </div>

        <div class="workspace-grid">
            <a class="workspace-card" href="pages/members.php">
                <div class="workspace-image">
                    <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&amp;fit=crop&amp;w=900&amp;q=85" alt="Athlete training in a gym" loading="lazy">
                    <span class="card-number">01</span>
                </div>
                <div class="workspace-card-copy">
                    <span class="card-kicker">PEOPLE</span>
                    <h3>Members</h3>
                    <span class="card-arrow" aria-hidden="true">&rarr;</span>
                </div>
            </a>

            <a class="workspace-card" href="pages/membership-plans.php">
                <div class="workspace-image">
                    <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&amp;fit=crop&amp;w=900&amp;q=85" alt="Bright, fully equipped gym floor" loading="lazy">
                    <span class="card-number">02</span>
                </div>
                <div class="workspace-card-copy">
                    <span class="card-kicker">PRICING</span>
                    <h3>Membership</h3>
                    <span class="card-arrow" aria-hidden="true">&rarr;</span>
                </div>
            </a>

            <a class="workspace-card" href="pages/machines.php">
                <div class="workspace-image">
                    <img src="https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?auto=format&amp;fit=crop&amp;w=900&amp;q=85" alt="Strength equipment in a gym" loading="lazy">
                    <span class="card-number">03</span>
                </div>
                <div class="workspace-card-copy">
                    <span class="card-kicker">EQUIPMENT</span>
                    <h3>Machines</h3>
                    <span class="card-arrow" aria-hidden="true">&rarr;</span>
                </div>
            </a>

            <a class="workspace-card" href="pages/payments.php">
                <div class="workspace-image">
                    <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&amp;fit=crop&amp;w=900&amp;q=85" alt="Athlete preparing for a strength workout" loading="lazy">
                    <span class="card-number">04</span>
                </div>
                <div class="workspace-card-copy">
                    <span class="card-kicker">BILLING</span>
                    <h3>Payments</h3>
                    <span class="card-arrow" aria-hidden="true">&rarr;</span>
                </div>
            </a>
        </div>
    </section>

    <section class="container equipment-section admin-equipment-section" aria-labelledby="equipment-title">
        <div class="section-heading">
            <div>
                <p class="eyebrow">FLOOR EQUIPMENT</p>
                <h2 id="equipment-title">Machines Available</h2>
            </div>
            <a class="button equipment-manage-link" href="pages/machines.php">Manage machines <span aria-hidden="true">&rarr;</span></a>
        </div>

        <div class="equipment-grid">
            <?php if ($machineLoadError): ?>
                <p class="equipment-empty">Machine inventory is unavailable. Check the database setup.</p>
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
                <p class="equipment-empty">No machines added yet. Add equipment to see availability here.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>