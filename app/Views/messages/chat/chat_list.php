<?php echo $user_id ?>

<style>
    :root {
        --primary-color: #3b82f6;
        --secondary-color: #64748b;
        --background-color: #f8fafc;
        --card-color: #ffffff;
        --text-color: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --unread-badge: #ef4444;
        --hover-color: #f1f5f9;
    }

    .message-list-container {
        background-color: var(--background-color);
        padding: 16px;
        border-radius: 12px;
        height: calc(100vh - 120px);
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--border-color) transparent;
    }

    .message-list-container::-webkit-scrollbar {
        width: 6px;
    }

    .message-list-container::-webkit-scrollbar-thumb {
        background-color: var(--border-color);
        border-radius: 3px;
    }

    .message-row {
        background-color: var(--card-color);
        border-radius: 10px;
        margin-bottom: 12px;
        padding: 16px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-color);
    }

    .message-row:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
        background-color: var(--hover-color);
    }

    .message-row.unread {
        border-left: 3px solid var(--primary-color);
    }

    .chat-no-messages {
        background-color: var(--card-color);
        padding: 40px;
        border-radius: 12px;
        text-align: center;
        color: var(--text-muted);
        border: 1px dashed var(--border-color);
    }

    .node-id-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    .node-id {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        color: var(--text-color);
        background-color: #f0f4f8;
        padding: 4px 8px;
        border-radius: 4px;
        max-width: 70%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: inline-block;
    }

    .chat-time {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
        white-space: nowrap;
    }

    .message-preview {
        color: var(--text-color);
        font-size: 14px;
        margin: 6px 0 4px;
        line-height: 1.4;
    }

    .ticket-number {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        color: var(--primary-color);
        background-color: rgba(59, 130, 246, 0.1);
        padding: 2px 6px;
        border-radius: 4px;
        display: inline-block;
        margin-top: 4px;
    }

    .search-container {
        margin-bottom: 16px;
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background-color: var(--card-color);
        font-size: 14px;
        transition: all 0.2s;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    @media (max-width: 768px) {
        .message-list-container {
            height: calc(100vh - 160px);
        }
        
        .node-id {
            max-width: 60%;
        }
    }
</style>

<div class="search-container">
    <i data-feather="search" class="search-icon"></i>
    <input type="text" class="search-input" placeholder="Search conversations...">
</div>

<div class="message-list-container">
    <?php if (!empty($grouped_messages)): ?>
        <?php 
        // Convert the grouped messages to an array we can sort
        $conversations = [];
        foreach ($grouped_messages as $node_id => $conversation) {
            $conversations[] = [
                'node_id' => $node_id,
                'conversation' => $conversation,
                'timestamp' => strtotime($conversation['latest_message']->created_at ?? 0)
            ];
        }
        
        // Sort conversations by timestamp (newest first)
        usort($conversations, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        foreach ($conversations as $item): 
            $node_id = $item['node_id'];
            $conversation = $item['conversation'];
            $latest_message = $conversation['latest_message'];
            $created_at = $latest_message->created_at ?? date('Y-m-d H:i:s');
            $message_preview = $latest_message->message ?? '';
            $ticket_number = $latest_message->ticket_number ?? '';
            $unread_count = $conversation['unread_count'] ?? 0;
            
            // Format date and time
            $date_display = date('M j', strtotime($created_at)); // e.g. "Jun 19"
            $time_display = date('g:i A', strtotime($created_at)); // e.g. "6:28 PM"
            $full_time_display = $date_display . ' • ' . $time_display; // e.g. "Jun 19 • 6:28 PM"
        ?>
            <div class='js-message-row message-row <?= $unread_count > 0 ? 'unread' : '' ?>' data-id='<?= $node_id ?>'>
                <div class="node-id-container">
                    <div class="node-id" title="<?= htmlspecialchars($node_id) ?>">
                        <?= substr($node_id, 0, 8) ?>...<?= substr($node_id, -3) ?>
                    </div>
                    <div class='chat-time' title="<?= $full_time_display ?>">
                        <?= $full_time_display ?>
                    </div>
                </div>
                
                <?php if (!empty($message_preview)): ?>
                    <div class='message-preview'>
                        <?= htmlspecialchars(strlen($message_preview) > 50 ? substr($message_preview, 0, 50).'...' : $message_preview) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($ticket_number)): ?>
                    <div class="ticket-number"><?= htmlspecialchars($ticket_number) ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="chat-no-messages">
            <i data-feather="message-circle" height="48" width="48" stroke-width="1"></i>
            <h5 class="mt-3 mb-1">No conversations yet</h5>
            <p class="text-muted mb-0"><?= app_lang("no_messages_text") ?></p>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    feather.replace();
    
    // Initialize search functionality
    $(".search-input").on("keyup", function() {
        const value = $(this).val().toLowerCase();
        $(".message-row").filter(function() {
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(value) > -1
            );
        });
    });
    
    // Message row click handler
    $(".js-message-row").click(function(e) {
        if (!$(e.target).is('a, input, button')) {
            const nodeId = $(this).data('id');
            loadConversation(nodeId);
        }
    });

    function loadConversation(nodeId) {
        window.location.href = "<?= site_url('messages/view_conversation') ?>/" + nodeId;
    }
});
</script>