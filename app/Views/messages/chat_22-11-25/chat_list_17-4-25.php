
<style>
    /* Minimal Professional Chat Interface */
    .message-list-container {
        background-color: #f8fafc;
        padding: 15px;
        border-radius: 8px;
    }

    .message-row {
        background-color: #ffffff;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #e0e3e7;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .message-row:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
    }

    .node-id-display {
        font-family: 'Courier New', monospace;
        font-size: 14px;
        color: #334155;
        font-weight: 500;
    }

    .unread-indicator {
        background-color: #3b82f6;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: bold;
    }

    .chat-no-messages {
        padding: 30px;
        background-color: #ffffff;
        border-radius: 8px;
        color: #64748b;
        text-align: center;
    }
</style>

<div class="message-list-container">
    <?php if (!empty($grouped_messages)): ?>
        <?php foreach ($grouped_messages as $node_id => $conversation): ?>
            <?php
            $unread_count = $conversation['unread_count'] ?? 0;
            ?>
            <div class='js-message-row message-row' data-id='<?= htmlspecialchars($node_id, ENT_QUOTES) ?>'>
                <div class='node-id-display'>ID: <?= htmlspecialchars($node_id) ?></div>
                <?php if ($unread_count > 0): ?>
                    <span class='unread-indicator'><?= $unread_count ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="chat-no-messages">
            <i data-feather="message-circle" height="4rem" width="4rem"></i><br />
            <?= app_lang("no_messages_text") ?>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    $(".js-message-row").click(function(e) {
        e.preventDefault();
        const nodeId = $(this).data('id');
        window.location.href = "<?= site_url('messages/view_conversation') ?>/" + encodeURIComponent(nodeId);
    });
});
</script>