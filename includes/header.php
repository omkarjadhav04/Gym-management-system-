<?php
require_once __DIR__ . '/auth.php';
$isSubpage = basename(dirname($_SERVER['SCRIPT_NAME'])) === 'pages';
$basePath = $isSubpage ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IFC fitness club | Membership Management</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="<?php echo $basePath; ?>index.php" aria-label="IFC fitness club home">
                <span class="brand-mark" aria-hidden="true">I</span>
                <span>IFC<span class="brand-light"> / fitness club</span></span>
            </a>
            <nav class="site-nav" aria-label="Main navigation">
                <ul>
                    <li><a href="<?php echo $basePath; ?>index.php">Overview</a></li>
                    <?php if (is_admin_logged_in()): ?>
                        <li><a href="<?php echo $basePath; ?>admin.php">Admin Workspace</a></li>
                        <li><a href="<?php echo $basePath; ?>pages/members.php">Members</a></li>
                        <li><a href="<?php echo $basePath; ?>pages/membership-plans.php">Plans</a></li>
                        <li><a href="<?php echo $basePath; ?>pages/machines.php">Machines</a></li>
                        <li><a href="<?php echo $basePath; ?>pages/payments.php">Payments</a></li>
                        <li><a href="<?php echo $basePath; ?>logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $basePath; ?>login.php">Admin Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <span class="header-status"><span></span> <?php echo is_admin_logged_in() ? 'ADMIN' : 'MANAGEMENT'; ?></span>
        </div>
    </header>