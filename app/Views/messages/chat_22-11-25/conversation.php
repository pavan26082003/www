<div class="chat-container">
    <!-- Conversation Header -->
    <div class="chat-header">
        <a href="<?= site_url('messages/chat_list') ?>" class="btn btn-back">
            <i data-feather="arrow-left"></i>
        </a>
        <div class="participant-info">
            <img src="<?= get_avatar($participant['image']) ?>" class="avatar">
            <h5><?= $participant['name'] ?></h5>
            <small>Node: <?= $node_id ?></small>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="messages-container">
        <?php foreach ($messages as $message): ?>
            <div class="message <?= $message->from_userid == $login_user_id ? 'outgoing' : 'incoming' ?>">
                <div class="message-content">
                    <?= nl2br(htmlspecialchars($message->message)) ?>
                </div>
                <div class="message-time">
                    <?= format_to_relative_time($message->created_at) ?>
                    <?php if ($message->from_userid == $login_user_id): ?>
                        <span class="status-icon">
                            <?php if ($message->status == 'read'): ?>
                                <i data-feather="check-circle"></i>
                            <?php else: ?>
                                <i data-feather="check"></i>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Message Composer -->
    <div class="message-composer">
        <form action="<?= site_url('messages/send_message') ?>" method="POST">
            <input type="hidden" name="node_id" value="<?= $node_id ?>">
            <textarea name="message" placeholder="Type your message..."></textarea>
            <button type="submit">
                <i data-feather="send"></i>
            </button>
        </form>
    </div>
</div>

<style>
.chat-container {
    display: flex;
    flex-direction: column;
    height: 100vh;
}

.chat-header {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
    background: #fff;
}

.participant-info {
    margin-left: 15px;
}

.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #f9f9f9;
}

.message {
    max-width: 70%;
    margin-bottom: 15px;
    padding: 10px 15px;
    border-radius: 18px;
}

.message.incoming {
    background: #fff;
    margin-right: auto;
    border: 1px solid #eee;
}

.message.outgoing {
    background: #007bff;
    color: white;
    margin-left: auto;
}

.message-time {
    font-size: 0.75rem;
    text-align: right;
    margin-top: 5px;
    opacity: 0.8;
}

.message.outgoing .message-time {
    color: rgba(255,255,255,0.7);
}

.message-composer {
    padding: 15px;
    background: #fff;
    border-top: 1px solid #eee;
}

.message-composer form {
    display: flex;
}

.message-composer textarea {
    flex: 1;
    border-radius: 20px;
    padding: 10px 15px;
    border: 1px solid #ddd;
    resize: none;
}

.btn-back {
    background: none;
    border: none;
    color: #333;
}
</style>

<script>
$(document).ready(function() {
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Scroll to bottom of messages
    const container = $('.messages-container');
    container.scrollTop(container[0].scrollHeight);
    
    // Mark messages as read
    $.post("<?= site_url('messages/mark_as_read') ?>", {
        node_id: "<?= $node_id ?>"
    });
});
</script>