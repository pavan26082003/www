<?php
// This is the user list view
$title = "Admin Chat - User List";
include 'header.php';
?>

<div class="container">
    <h1>Chat Users</h1>
    
    <?php if (empty($users)): ?>
        <div class="alert alert-info">No users found with chat history.</div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($users as $user): ?>
                <a href="?action=viewChat&user_id=<?= htmlspecialchars($user['id']) ?>" 
                   class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?= htmlspecialchars($user['username']) ?></h5>
                    </div>
                    <p class="mb-1"><?= htmlspecialchars($user['email']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>