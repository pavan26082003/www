<?php echo "Tabs"; ?>

<style>
    /* Professional Chat Interface Styles */
    .rise-chat-header.box {
        background-color: #ffffff;
        border-bottom: 1px solid #e0e3e7;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        padding: 12px 20px;
    }
    
    .rise-chat-body.long {
        background-color: #f8fafc;
        height: calc(100vh - 60px);
        overflow-y: auto;
    }
    
    .node-id-display {
    font-family: 'Courier New', monospace;
    font-size: 14px;
    color: #64748b;
    background-color: #f1f5f9;
    padding: 6px 12px;
    border-radius: 4px;
    display: inline-block;
    margin-left: 10px;
    word-break: break-all; /* Add this */
    white-space: normal;   /* Add this */
    max-width: 100%;       /* Optional: restrict width */
}
    
    .tab-content {
        background-color: #ffffff;
        border-radius: 8px;
        margin: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        height: calc(100% - 32px);
    }
    
    .tab-pane {
        padding: 16px;
        height: 100%;
    }
    
    .icon-16 {
        color: #3b82f6;
    }


.rise-chat-header.box {
        background-color: #ffffff;
        border-bottom: 1px solid #e0e3e7;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        padding: 0; /* Remove padding to let tabs fill header */
    }
    
    .chat-tab {
        display: flex;
        height: 60px; /* Increased tab container height */
    }
    
    .chat-tab .box-content {
        padding: 18px 24px; /* Increased padding for larger tabs */
        border-right: 1px solid #e0e3e7;
        display: flex;
        align-items: center;
        height: 100%;
    }
    
    .chat-tab a {
        color: #64748b;
        text-decoration: none;
        display: flex;
        align-items: center;
        font-size: 15px; /* Slightly larger font */
        gap: 8px; /* Space between icon and text */
    }
    
    .chat-tab .active {
        border-bottom: 3px solid #3b82f6; /* Thicker active indicator */
        background-color: #f8fafc; /* Light background for active tab */
    }
    
    .icon-16 {
        width: 20px; /* Slightly larger icon */
        height: 20px;
    }
</style>

<div class="rise-chat-header box">
    <div class="box-content">
        <ul class="chat-tab p0 m0 nav nav-tabs box" data-bs-toggle="ajax-tab" role="tablist">
            <li class="box-content active" id="chat-inbox-tab-button">
                <a role="presentation" href="#" data-bs-target="#chat-inbox-tab">
                    <i data-feather="message-circle" class="icon-16"></i>
                    <span>Inbox</span>
                    <span class="node-id-display"><?= $node_id ?></span>
                </a>
            </li>
        </ul> 
    </div>
</div>
<div class="rise-chat-body long clearfix">
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="chat-inbox-tab">
            <?php echo view("messages/chat/chat_list", array("messages" => $messages)); ?>
        </div>
    </div>
</div>