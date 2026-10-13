<?php
$model_name = $_GET['model_number'] ?? '';
$user_id = $_GET['user_id'] ?? '';
$node_id = $_GET['node_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">    
    <title> Chat Support </title>
 
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        -webkit-tap-highlight-color: transparent;
    }

   

    body {
        font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        background-color: #f5f5f5;
        height: 100vh;
        margin: 0;
        overflow: hidden;
        position: relative;
        touch-action: manipulation;
    }

    #chat-container {
        width: 100%;
        height: 100vh;
        display: flex;
        flex-direction: column;
        background-color: #fff;
        position: relative;
        overflow: hidden;
    }

    #new-message-notification {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    #chat-header {
        background-color: #008CBA;
        color: white;
        padding: 15px;
        text-align: center;
        font-size: 1.2rem;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        position: relative;
        z-index: 10;
    }

    #chat-box {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
        padding-bottom: 80px;
        -webkit-overflow-scrolling: touch;
    }

    .chat-message {
        padding: 12px 16px;
        border-radius: 18px;
        margin-bottom: 12px;
        max-width: 85%;
        word-wrap: break-word;
        line-height: 1.4;
        position: relative;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

        .user-message {
        background-color: #008CBA;
        color: white;
        border-bottom-right-radius: 2px;
        margin-left: auto;
    }

    
    .bot-message {
        background-color: #4CAF50;   
        color: white;
        border-bottom-left-radius: 2px;
        margin-right: auto;
        border: 1px solid #388E3C;
    }

    
    .admin-message {
        background-color: #9C27B0;
        color: white;
        border-bottom-left-radius: 2px;
        margin-right: auto;
        border: 1px solid #7B1FA2;
    }

    .message-time {
        display: block;
        font-size: 0.7rem;
        opacity: 0.7;
        margin-top: 5px;
        text-align: right;
    }

    .user-message .message-time {
        color: rgba(255, 255, 255, 0.8);
    }

    .bot-message .message-time,
    .admin-message .message-time {
        color: rgba(255, 255, 255, 0.8);
    }

    #input-area {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #f5f5f5;
        padding: 10px;
        border-top: 1px solid #ddd;
        display: flex;
        align-items: center;
        z-index: 5;
    }

    #quick-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 10px;
        background-color: #f5f5f5;
        border-top: 1px solid #eee;
        transition: all 0.3s ease;
    }

    .quick-action-btn {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 8px 16px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .quick-action-btn:hover, .quick-action-btn:active {
        background-color: #008CBA;
        color: white;
        border-color: #008CBA;
    }

    #user-input-container {
        display: flex;
        width: 100%;
        gap: 8px;
    }

    #user-input {
        flex: 1;
        padding: 12px 15px;
        border-radius: 25px;
        border: 1px solid #ddd;
        font-size: 1rem;
        outline: none;
        resize: none;
        max-height: 120px;
    }

    #send-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #008CBA;
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    #menu-btn {
        position: absolute;
        left: 15px;
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
    }

    #back-btn {
        display: none;
        position: absolute;
        left: 15px;
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
        #chat-container {
            height: 100vh;
            height: -webkit-fill-available;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        #chat-box {
            padding-bottom: 120px;
        }

        #input-area {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px;
            background-color: #f5f5f5;
            border-top: 1px solid #ddd;
            padding-bottom: env(safe-area-inset-bottom);
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        #input-area {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px;
            background-color: #f5f5f5;
            border-top: 1px solid #ddd;
            padding-bottom: max(10px, env(safe-area-inset-bottom));
        }
        
        body.keyboard-visible #chat-box {
            padding-bottom: 300px;
        }

        body.keyboard-visible #input-area {
            position: absolute;
            bottom: -300px;
        }
    }

    /* App-like styles */
    .app-like {
        -webkit-user-select: none;
        -webkit-touch-callout: none;
    }
    
    /* Typing indicator */
    .typing-indicator {
        display: inline-block;
        padding: 10px 15px;
        background-color: #e9e9eb;
        border-radius: 18px;
        margin-bottom: 12px;
    }
    
    .typing-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #666;
        margin-right: 4px;
        animation: typingAnimation 1.4s infinite ease-in-out;
    }
    
    .typing-dot:nth-child(1) {
        animation-delay: 0s;
    }
    
    .typing-dot:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .typing-dot:nth-child(3) {
        animation-delay: 0.4s;
        margin-right: 0;
    }
    
    @keyframes typingAnimation {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-5px); }
    }

    .message-meta {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 5px;
        margin-top: 5px;
    }
    
    .message-status {
        display: inline-flex;
        align-items: center;
    }
    
    .tick {
        display: inline-block;
        width: 16px;
        height: 16px;
        background-size: contain;
        background-repeat: no-repeat;
    }
    
    .single-tick {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%2364758b"><path d="M18 7l-8 8-4-4-1.5 1.5L9 17l9.5-9.5z"/></svg>');
    }
    
    .double-tick {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%233b82f6"><path d="M18 7l-8 8-4-4-1.5 1.5L9 17l9.5-9.5L18 7zm-6 6l4-4-1.5-1.5L12 10l-1.5-1.5L9 10l3 3z"/></svg>');
    }
    
    .unseen-tick {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%2394a3b8"><path d="M18 7l-8 8-4-4-1.5 1.5L9 17l9.5-9.5L18 7zm-6 6l4-4-1.5-1.5L12 10l-1.5-1.5L9 10l3 3z"/></svg>');
    }
    
    /* Adjust existing time style */
    .message-time {
        font-size: 0.7rem;
        opacity: 0.7;
    }
    
    /* Color adjustments for different message types */
    .user-message .message-time {
        color: rgba(255, 255, 255, 0.8);
    }
    
    .bot-message .message-time,
    .admin-message .message-time {
        color: rgba(255, 255, 255, 0.8);
    }



    </style>
</head>
<body class="app-like">
    <div id="chat-container">
        <div id="new-message-notification" style="display: none; position: fixed; bottom: 70px; right: 20px; background: #008CBA; color: white; padding: 10px 15px; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); z-index: 100; cursor: pointer;">
            New message received!
        </div>

        <div id="chat-header">
            <button id="menu-btn">☰</button>
            <button id="back-btn" style="display: none;">←</button>
            <span>Chat Support</span>
        </div>
        
        <div id="chat-box">
            <div class="chat-message bot-message">
                Welcome to chat Support! How can we help you today?
                <span class="message-time"><?php echo date('h:i A'); ?></span>
            </div>
        </div>
        
        <div id="quick-actions">
            <button class="quick-action-btn" onclick="selectTopic('Equipment Startup')">Equipment Startup</button>
            <button class="quick-action-btn" onclick="selectTopic('Network Settings')">Network Settings</button>
            <button class="quick-action-btn" onclick="selectTopic('Password Settings')">Password Settings</button>
            <button class="quick-action-btn" onclick="showTicketForm()">Ticket Generation</button>
            <button class="quick-action-btn" onclick="selectTopic('Other Questions')">Other Questions</button>
        </div>
        
        <div id="input-area" style="display: none;">
            <div id="user-input-container">
                <textarea id="user-input" placeholder="Type your message..." rows="1" 
                         inputmode="text" enterkeyhint="send"></textarea>
                <button id="send-btn" onclick="sendMessage()">→</button>
            </div>
        </div>
    </div>

    <script>
        // DOM elements
        const chatBox = document.getElementById('chat-box');
        const userInput = document.getElementById('user-input');
        const quickActions = document.getElementById('quick-actions');
        const inputArea = document.getElementById('input-area');
        const backBtn = document.getElementById('back-btn');
        const menuBtn = document.getElementById('menu-btn');
        
        // State management
        let currentTopic = null;
        let currentSubTopic = null;
        let conversationHistory = [];
        let lastMessageTimestamp = null;
        let checkInterval = null;
        let isCheckingForMessages = false;
        
        // Initialize chat
        document.addEventListener('DOMContentLoaded', function() {
            loadChatHistory();
            setupMobileKeyboardHandling();
            startMessageChecking();
            startPeriodicRefresh(); 


            // Auto-resize textarea
            userInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Send message on Enter key
            userInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            
            // Touch event listeners
            document.querySelectorAll('.quick-action-btn').forEach(btn => {
                btn.addEventListener('touchstart', function() {
                    this.classList.add('active');
                });
                
                btn.addEventListener('touchend', function() {
                    this.classList.remove('active');
                });
            });
        });
        
        // Show typing indicator
        function showTyping() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'typing-indicator';
            typingDiv.id = 'typing-indicator';
            typingDiv.innerHTML = `
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
            `;
            chatBox.appendChild(typingDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        
        // Hide typing indicator
        function hideTyping() {
            const typing = document.getElementById('typing-indicator');
            if (typing) typing.remove();
        }
        
        // Format time for display
        function formatTime(timestamp) {
            const date = timestamp ? new Date(timestamp) : new Date();
            let hours = date.getHours();
            const minutes = date.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            const minutesStr = minutes < 10 ? '0'+minutes : minutes;
            return hours + ':' + minutesStr + ' ' + ampm;
        }
        
        // Append message to chat
        function appendMessage(sender, message, timestamp = null) {
            hideTyping();
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${sender}-message`;
            
            // Add timestamp to message
            const displayTime = formatTime(timestamp);
            messageDiv.innerHTML = `${message}<span class="message-time">${displayTime}</span>`;
            
            chatBox.appendChild(messageDiv);
            
            // Add to conversation history
            conversationHistory.push({
                sender: sender,
                message: message,
                timestamp: timestamp || new Date().toISOString()
            });
            
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        
        // Select a topic
        function selectTopic(topic) {
            if (topic === 'Other Questions') {
                appendMessage('user', topic);
                showTyping();
                setTimeout(() => {
                    appendMessage('bot', "Please describe your question and we'll help you.");
                    showInputBox();
                }, 1000);
                return;
            }
            
            currentTopic = topic;
            appendMessage('user', topic);
            
            showTyping();
            setTimeout(() => {
                let response = '';
                let actions = [];
                
                switch(topic) {
                    case 'Equipment Startup':
                        response = `For <strong>${topic}</strong>, please select your specific issue:`;
                        actions = [
                            'No response after the device is power on',
                            'Device does not respond after system start up',
                            'The device restarts repeatedly',
                            'How to check the device ID',
                            'Other questions'
                        ];
                        break;
                        
                    case 'Network Settings':
                        response = `For <strong>${topic}</strong>, please select your specific issue:`;
                        actions = [
                            'How to add the online device to your phone',
                            'What if can not add device to the device list',
                            'How to check the network status of device',
                            'How to add device to network',
                            'Other questions'
                        ];
                        break;
                        
                    case 'Password Settings':
                        response = `For <strong>${topic}</strong>, please select your specific issue:`;
                        actions = [
                            'How to signup and login to the app',
                            'Password recovery process',
                            'How to change your account password',
                            'Other questions'
                        ];
                        break;
                        
                    default:
                        response = `I can help with <strong>${topic}</strong>. Please provide more details about your issue.`;
                        actions = ['Back to Main Menu'];
                }
                
                appendMessage('bot', response);
                updateQuickActions(actions, 'subTopic');
            }, 1000);
        }
        
        // Handle subtopic selection
        function selectSubTopic(subTopic) {
            if (subTopic === 'Other questions') {
                appendMessage('user', subTopic);
                showTyping();
                setTimeout(() => {
                    appendMessage('bot', "Please describe your question and we'll help you.");
                    showInputBox();
                }, 1000);
                return;
            }
            
            currentSubTopic = subTopic;
            appendMessage('user', subTopic);
            
            showTyping();
            setTimeout(() => {
                let response = '';
                
                if (currentTopic === 'Equipment Startup') {
                    switch(subTopic) {
                        case 'No response after the device is power on':
                            response = `For <strong>${subTopic}</strong>:<br><br>
                            1. Check if the power cable is properly connected<br>
                            2. Verify the power outlet is working<br>
                            3. Try a different power cable if available<br>
                            4. Contact support if issue persists`;
                            break;
                            
                        case 'Device does not respond after system start up':
                            response = `For <strong>${subTopic}</strong>:<br><br>
                            1. Wait for 2-3 minutes as some devices take time to initialize<br>
                            2. Check all indicator lights<br>
                            3. Try restarting the device<br>
                            4. Contact support if issue persists`;
                            break;
                            
                        case 'The device restarts repeatedly':
                            response = `For <strong>${subTopic}</strong>:<br><br>
                            1. Check for overheating issues<br>
                            2. Ensure proper ventilation around the device<br>
                            3. Check if firmware updates are available<br>
                            4. Contact support for further diagnostics`;
                            break;
                            
                        case 'How to check the device ID':
                            response = `To check your device ID:<br><br>
                            1. Look on the back/bottom of the device for a label<br>
                            2. In the app, go to Device Settings > About<br>
                            3. The ID is typically a 12-digit alphanumeric code`;
                            break;
                            
                        default:
                            response = `Please describe your issue with the equipment startup and we'll help you resolve it.`;
                    }
                }
                else if (currentTopic === 'Network Settings') {
                    switch(subTopic) {
                        case 'How to add the online device to your phone':
                            response = `To add an online device:<br><br>
                            1. Open the app<br>
                            2. Tap the '+' icon in the top right<br>
                            3. Select 'Add Device'<br>
                            4. Follow the on-screen instructions`;
                            break;
                            
                        case 'What if can not add device to the device list':
                            response = `If you can't add a device:<br><br>
                            1. Ensure the device is powered on<br>
                            2. Check your phone is connected to the same network<br>
                            3. Restart both the device and your phone<br>
                            4. Try the manual setup option`;
                            break;
                            
                        case 'How to check the network status of device':
                            response = `To check network status:<br><br>
                            1. In the app, go to Device Settings<br>
                            2. Select 'Network Status'<br>
                            3. You'll see connection strength and IP details<br>
                            4. Green indicator means good connection`;
                            break;
                            
                        case 'How to add device to network':
                            response = `To add device to network:<br><br>
                            1. Connect device to power<br>
                            2. Press and hold the network button for 5 seconds<br>
                            3. Use the app to connect to the device's WiFi network<br>
                            4. Select your home network and enter password`;
                            break;
                            
                        default:
                            response = `Please describe your network settings issue and we'll help you resolve it.`;
                    }
                }
                else if (currentTopic === 'Password Settings') {
                    switch(subTopic) {
                        case 'How to signup and login to the app':
                            response = `To signup and login:<br><br>
                            1. Download the app from your app store<br>
                            2. Open app and select 'Create Account'<br>
                            3. Enter your email and create a password<br>
                            4. Verify your email through the link sent to you`;
                            break;
                            
                        case 'Password recovery process':
                            response = `Password recovery:<br><br>
                            1. On login screen, tap 'Forgot Password'<br>
                            2. Enter your registered email address<br>
                            3. Check your email for a reset link<br>
                            4. Create a new password (minimum 8 characters)`;
                            break;
                            
                        case 'How to change your account password':
                            response = `To change password:<br><br>
                            1. Log in to the app<br>
                            2. Go to Account Settings > Security<br>
                            3. Select 'Change Password'<br>
                            4. Enter current and new password`;
                            break;
                            
                        default:
                            response = `Please describe your password-related issue and we'll help you resolve it.`;
                    }
                }
                
                appendMessage('bot', response);
                updateQuickActions(['Back to Topics', 'Back to Main Menu', 'Generate Ticket'], 'postResponse');
            }, 1000);
        }
        
        // Show ticket generation message
        function showTicketForm() {
            appendMessage('user', 'Ticket Generation');
            
            showTyping();
            setTimeout(() => {
                appendMessage('bot', "Please describe your issue to generate a support ticket.");
                showInputBox();
            }, 1000);
        }
        
        function setupMobileKeyboardHandling() {
            if (!isMobile()) return;

            const input = document.getElementById('user-input');
            const inputArea = document.getElementById('input-area');
            
            input.addEventListener('focus', () => {
                const keyboardHeight = window.innerHeight * 0.5;
                document.documentElement.style.setProperty('--keyboard-height', `${keyboardHeight}px`);
                inputArea.classList.add('active');
                
                setTimeout(() => {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }, 300);
            });

            input.addEventListener('blur', () => {
                inputArea.classList.remove('active');
            });
        }

        function isMobile() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }


        // Add this with your other state variables at the top
let refreshInterval = null;

function startPeriodicRefresh() {
    // Clear any existing interval
    if (refreshInterval) clearInterval(refreshInterval);
    
    // Set up new interval (2000ms = 2 seconds)
    refreshInterval = setInterval(() => {
        loadChatHistory();
    }, 5000);
}

function stopPeriodicRefresh() {
    if (refreshInterval) clearInterval(refreshInterval);
}









        function startMessageChecking() {
            if (checkInterval) clearInterval(checkInterval);
            checkInterval = setInterval(checkForNewMessages, 10000);
            checkForNewMessages();
        }

        function stopMessageChecking() {
            if (checkInterval) clearInterval(checkInterval);
            isCheckingForMessages = false;
        }

        function checkForNewMessages() {
            if (isCheckingForMessages) return;
            isCheckingForMessages = true;
            
            const model_name = encodeURIComponent('<?php echo $model_name; ?>');
            const user_id = encodeURIComponent('<?php echo $user_id; ?>');
            const node_id = encodeURIComponent('<?php echo $node_id; ?>');
            
            const url = lastMessageTimestamp 
                ? `https://efactor.tech/v8/api.php?action=get_new_messages&node_id=${node_id}&user_id=${user_id}&since=${encodeURIComponent(lastMessageTimestamp)}`
                : `https://efactor.tech/v8/api.php?action=get_new_messages&node_id=${node_id}&user_id=${user_id}`;
            
            fetch(url)
                .then(response => response.json())
                .then(messages => {
                    if (messages.length > 0) {
                        lastMessageTimestamp = messages[messages.length - 1].timestamp;
                        
                        messages.forEach(msg => {
                            const senderType = msg.is_admin ? 'admin' : (msg.sender === 'user' ? 'user' : 'bot');
                            appendMessage(senderType, msg.message, msg.timestamp);
                        });
                        
                        if (!document.getElementById('new-message-notification').style.display || 
                            document.getElementById('new-message-notification').style.display === 'none') {
                            
                            const notification = document.getElementById('new-message-notification');
                            notification.style.display = 'block';
                            
                            notification.onclick = function() {
                                this.style.display = 'none';
                                chatBox.scrollTop = chatBox.scrollHeight;
                            };
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking for new messages:', error);
                })
                .finally(() => {
                    isCheckingForMessages = false;
                });
        }






        function loadChatHistory() {
    const model_name = encodeURIComponent('<?php echo $model_name; ?>');
    const user_id = encodeURIComponent('<?php echo $user_id; ?>');
    const node_id = encodeURIComponent('<?php echo $node_id; ?>');
    
    const apiUrl = `https://efactor.tech/v8/api.php?action=get_chat_history&node_id=${node_id}&user_id=${user_id}`;
    
    fetch(apiUrl)
        .then(response => response.json())
        .then(messages => {
            if (messages.length > 0) {
                // Only clear and reload if there are new messages
                const currentMessages = document.querySelectorAll('.chat-message');
                if (currentMessages.length !== messages.length) {
                    document.getElementById('chat-box').innerHTML = '';
                    lastMessageTimestamp = messages[messages.length - 1].timestamp;
                    
                    messages.forEach(msg => {
                        const senderType = msg.is_admin ? 'admin' : (msg.sender === 'user' ? 'user' : 'bot');
                        appendMessage(senderType, msg.message, msg.timestamp);
                    });
                    
                    chatBox.scrollTop = chatBox.scrollHeight;
                    document.getElementById('new-message-notification').style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error loading chat history:', error);
        });
}




        
        // Send user message
        function sendMessage() {
            const message = userInput.value.trim();
            if (!message) return;

            appendMessage('user', message);
            userInput.value = '';
            userInput.style.height = 'auto';
            userInput.blur();
            showTyping();
            
            const apiData = {
                message: message,
                model_name: '<?php echo $model_name; ?>',
                user_id: '<?php echo $user_id; ?>',
                node_id: '<?php echo $node_id; ?>',
                timestamp: new Date().toISOString()
            };

            callEfactorAPI(apiData)
                .then(response => {
                    hideTyping();
                    
                    if (response.error) {
                       // appendMessage('bot', "Thank you for contacting us. We'll get back to you soon.");
                    } else {
                        if (message.toLowerCase().includes('ticket') || 
                            message.toLowerCase().includes('generate')) {
                            appendMessage('bot', "Your ticket request has been received. Reference #: " + 
                                Math.floor(100000 + Math.random() * 900000));
                        } else {
                            const reply = response.message || "Thank you for your message.";
                            appendMessage('bot', reply);
                        }
                    }
                    
                    setTimeout(() => {
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }, 50);
                })
                .catch(error => {
                    hideTyping();
                  //  appendMessage('bot', "Thank you for your message. We'll respond shortly.");
                });
        }
        
        // Reset conversation to initial state
        function resetConversation() {
            currentTopic = null;
            currentSubTopic = null;
            
            updateQuickActions([
                'Equipment Startup',
                'Network Settings',
                'Password Settings',
                'Ticket Generation',
                'Other Questions'
            ], 'main');
        }
        
        // Update quick action buttons based on context
        function updateQuickActions(actions, context) {
            quickActions.innerHTML = '';
            
            actions.forEach(action => {
                const btn = document.createElement('button');
                btn.className = 'quick-action-btn';
                btn.textContent = action;
                
                if (context === 'subTopic') {
                    btn.onclick = function() {
                        if (action === 'Other questions') {
                            selectTopic('Other Questions');
                        } else {
                            selectSubTopic(action);
                        }
                    };
                } 
                else if (context === 'postResponse') {
                    btn.onclick = function() {
                        if (action === 'Back to Topics') {
                            selectTopic(currentTopic);
                        } 
                        else if (action === 'Generate Ticket') {
                            showTicketForm();
                        }
                        else {
                            resetConversation();
                        }
                    };
                }
                else {
                    if (action === 'Ticket Generation') {
                        btn.onclick = showTicketForm;
                    } 
                    else if (action === 'Other Questions') {
                        btn.onclick = function() { selectTopic('Other Questions'); };
                    }
                    else {
                        btn.onclick = function() { selectTopic(action); };
                    }
                }
                
                quickActions.appendChild(btn);
            });
            
            quickActions.style.display = 'flex';
            inputArea.style.display = 'none';
            backBtn.style.display = 'none';
            menuBtn.style.display = 'block';
        }
        
        // Show input box for custom messages
        function showInputBox() {
            quickActions.style.display = 'none';
            inputArea.style.display = 'block';
            userInput.focus();
            backBtn.style.display = 'block';
            menuBtn.style.display = 'none';
        }
        
        // Back button functionality
        backBtn.addEventListener('click', resetConversation);
        
        // Menu button functionality
        menuBtn.addEventListener('click', function() {
            appendMessage('bot', "Main menu options:<br><br>1. Account Settings<br>2. Device Management<br>3. Support Topics");
        });
        
        // Call Efactor API
        function callEfactorAPI(data) {
            const model_name = encodeURIComponent('<?php echo $model_name; ?>');
            const user_id = encodeURIComponent('<?php echo $user_id; ?>');
            const node_id = encodeURIComponent('<?php echo $node_id; ?>');
            
            const apiUrl = `https://efactor.tech/v8/api.php?action=call_services_web&node_id=${node_id}`;
            
            return fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .catch(error => {
                console.error('API Call Failed:', error);
                return { 
                    error: true, 
                    message: "Connection error. Please try again.",
                    details: error.message 
                };
            });
        }

document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopPeriodicRefresh();
    } else {
        startPeriodicRefresh();
    }
});
    </script>
</body>
</html>