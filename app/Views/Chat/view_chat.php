<?php
// This is the chat interface view
$title = "Admin Chat - Conversation with " . htmlspecialchars($user['username']);
include 'header.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Chat with <?= htmlspecialchars($user['username']) ?></h1>
        <a href="?action=listUsers" class="btn btn-secondary">Back to Users</a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            User: <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['email']) ?>)
        </div>
        <div class="card-body chat-messages" style="height: 400px; overflow-y: auto;">
            <?php if (empty($messages)): ?>
                <div class="text-center text-muted py-4">No messages yet. Start the conversation!</div>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <div class="mb-3 <?= $message['from_user_id'] == $adminId ? 'text-end' : 'text-start' ?>">
                        <div class="d-inline-block p-3 rounded 
                            <?= $message['from_user_id'] == $adminId ? 'bg-primary text-white' : 'bg-light' ?>">
                            <?= htmlspecialchars($message['message']) ?>
                            <div class="small text-muted mt-1">
                                <?= date('M j, Y g:i A', strtotime($message['created_at'])) ?>
                                <?php if ($message['ticket_number']): ?>
                                    <br>Ticket: <?= htmlspecialchars($message['ticket_number']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <form method="post" action="?action=sendMessage">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
        <div class="mb-3">
            <label for="ticket_number" class="form-label">Ticket Number (optional)</label>
            <input type="text" class="form-control" id="ticket_number" name="ticket_number">
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>

<script>
// Scroll to bottom of chat messages
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.querySelector('.chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
});
</script>

<?php include 'footer.php'; ?>