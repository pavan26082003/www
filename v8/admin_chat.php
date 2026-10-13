<?php
$model_name = $_GET['model_number'] ?? '';
$user_id = $_GET['user_id'] ?? '';
$node_id = $_GET['node_id'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Support Chat</title>
    <style>
    /* Reuse most styles from user chat, with admin-specific modifications */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Roboto, sans-serif;
        background-color: #f5f5f5;
        height: 100vh;
        margin: 0;
    }

    #chat-container {
        width: 100%;
        height: 100vh;
        display: flex;
        flex-direction: column;
        background-color: #fff;
    }

    #chat-header {
        background-color: #9C27B0; /* Admin purple */
        color: white;
        padding: 15px;
        text-align: center;
        font-size: 1.2rem;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    #chat-box {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
        padding-bottom: 80px;
    }

    .chat-message {
        padding: 12px 16px;
        border-radius: 18px;
        margin-bottom: 12px;
        max-width: 85%;
        word-wrap: break-word;
        line-height: 1.4;
        position: relative;
    }

    .user-message {
        background-color: #9C27B0; /* Changed to admin purple */
        color: white;
        margin-left: auto; /* Changed to left side */
        border-bottom-left-radius: 2px;
    }

    .admin-message {
        background-color: #e1e1e1; /* Changed to user gray */
        color: #333;
        margin-right: auto; /* Changed to right side */
        border-bottom-right-radius: 2px;
    }

    .system-message {
        background-color: #f8f9fa;
        color: #666;
        margin: 10px auto;
        text-align: center;
        max-width: 100%;
        font-size: 0.9em;
        border-radius: 5px;
        padding: 8px 12px;
    }

    
    .user-label {
        font-weight: bold;
        font-size: 0.8rem;
        margin-bottom: 3px;
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
    }

    #user-input {
        flex: 1;
        padding: 12px 15px;
        border-radius: 25px;
        border: 1px solid #ddd;
        font-size: 1rem;
        outline: none;
        resize: none;
    }

    #send-btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #9C27B0;
        color: white;
        border: none;
        margin-left: 10px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    #send-btn:hover {
        background-color: #7B1FA2;
    }

    .user-info-panel {
        background: #f8f9fa;
        padding: 10px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
    }

    .status-badge {
        background: #4CAF50;
        color: white;
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 0.8rem;
    }

    .typing-indicator {
        display: inline-block;
        padding: 8px 12px;
        background-color: #f1f1f1;
        border-radius: 18px;
        margin-bottom: 12px;
        color: #666;
        font-size: 0.9em;
    }

   
.message-status.delivered {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%2364758b"><path d="M18 7l-8 8-4-4-1.5 1.5L9 17l9.5-9.5z"/></svg>');
}

.message-status.read {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%233b82f6"><path d="M18 7l-8 8-4-4-1.5 1.5L9 17l9.5-9.5L18 7zm-6 6l4-4-1.5-1.5L12 10l-1.5-1.5L9 10l3 3z"/></svg>');
}

.message-status-container {
        display: inline-flex;
        align-items: center;
        margin-left: 8px;
        vertical-align: middle;
    }
    
       
       .message-meta {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 5px;
        gap: 5px;
    }
    
    .message-status {
        display: inline-block;
        width: 16px;
        height: 16px;
    }
    
    .single-tick {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%2364758b"><path d="M18 7l-8 8-4-4-1.5 1.5L9 17l9.5-9.5z"/></svg>');
    }
    
    .double-tick {
    background-image: url("data:image/svg+xml;utf8,\
    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%233b82f6'>\
    <path d='M1.5 13.5l4.5 4.5L12 12l-1.5-1.5-4.5 4.5L3 13.5z'/>\
    <path d='M6 13.5l4.5 4.5L21 7.5l-1.5-1.5-9 9L7.5 15z'/>\
    </svg>");
}
    
    .double-tick.unseen {
    background-image: url("data:image/svg+xml;utf8,\
    <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2394a3b8'>\
    <path d='M1.5 13.5l4.5 4.5L12 12l-1.5-1.5-4.5 4.5L3 13.5z'/>\
    <path d='M6 13.5l4.5 4.5L21 7.5l-1.5-1.5-9 9L7.5 15z'/>\
    </svg>");
}
    
    .message-time {
        font-size: 0.7rem;
        opacity: 0.7;
        color: inherit;
    }
    </style>
   
</head>
<body>
    <div id="chat-container">
        <div class="user-info-panel">
            <div>
                <strong>Conversation with User #<?= $user_id ?></strong><br>
                <small>Node: <?= $node_id ?></small>
            </div>
            <div class="status-badge">Active</div>
        </div>
        
        <div id="chat-box">
            <!-- Messages will be loaded here -->
        </div>
        
        <div id="input-area">
            <textarea id="user-input" placeholder="Type your reply..." rows="1"></textarea>
            <button id="send-btn" onclick="sendAdminMessage()">→</button>
        </div>
    </div>

    <script>
        // DOM elements
        const chatBox = document.getElementById('chat-box');
        const userInput = document.getElementById('user-input');
        const nodeId = '<?= $node_id ?>';
        const userId = '<?= $user_id ?>';
        const modelName = '<?= $model_name ?>';
        
        // Initialize chat
        document.addEventListener('DOMContentLoaded', function() {
            loadChatHistory();
            
            // Auto-resize textarea
            userInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
            
            // Send message on Enter key (without Shift)
            userInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendAdminMessage();
                }
            });
        });
        
        // Format time for display
        function formatTime(timestamp) {
            if (!timestamp) return '';
            
            const date = new Date(timestamp);
            let hours = date.getHours();
            const minutes = date.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            const minutesStr = minutes < 10 ? '0'+minutes : minutes;
            return hours + ':' + minutesStr + ' ' + ampm;
        }


        
        // Append message to chat
       /* function appendMessage(sender, message, timestamp = null, senderName = null) {
            const messageDiv = document.createElement('div');
            
            if (sender === 'system') {
                messageDiv.className = 'system-message';
                messageDiv.textContent = message;
            } else {
                messageDiv.className = `chat-message ${sender}-message`;
                
                let messageContent = '';
                if (senderName) {
                    messageContent += `<div class="user-label">${senderName}</div>`;
                }
                messageContent += message;
                
                // Add timestamp to message
                const displayTime = formatTime(timestamp);
                messageContent += `<span class="message-time">${displayTime}</span>`;
                
                messageDiv.innerHTML = messageContent;
            }
            
            chatBox.appendChild(messageDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }*/





     function appendMessage(sender, message, timestamp = null, senderName = null, status = 'delivered') {
            const messageDiv = document.createElement('div');
            
            if (sender === 'system') {
                messageDiv.className = 'system-message';
                messageDiv.textContent = message;
            } else {
                messageDiv.className = `chat-message ${sender}-message`;
                
                let messageContent = '';
                if (senderName) {
                    messageContent += `<div class="user-label">${senderName}</div>`;
                }
                messageContent += message;
                
                // Create message meta container (for time and status)
                const displayTime = formatTime(timestamp);
                let statusIcon = '';
                
                // Add status indicator for admin messages
                if (sender === 'admin') {
                    if (status === 'delivered') {
                        statusIcon = '<span class="message-status single-tick"></span>';
                    } else if (status === 'read') {
                        statusIcon = '<span class="message-status double-tick"></span>';
                    } else {
                        statusIcon = '<span class="message-status double-tick unseen"></span>';
                    }
                }
                
                messageContent += `
                    <div class="message-meta">
                        ${statusIcon}
                        <span class="message-time">${displayTime}</span>
                    </div>
                `;
                
                messageDiv.innerHTML = messageContent;
            }
            
            chatBox.appendChild(messageDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }




        
        // Load chat history from database
     function loadChatHistory() {
            console.log('Loading chat history...');
            
            const params = new URLSearchParams({
                action: 'get_chat_history_admin',
                node_id: nodeId,
                user_id: userId
            });
            const url = `https://efactor.tech/v8/api.php?${params.toString()}`;
            
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    console.log('API response data:', data);
                    
                    if (!data || !Array.isArray(data)) {
                        throw new Error('Invalid data format received');
                    }

                    chatBox.innerHTML = '';
                    
                    if (data.length === 0) {
                        appendMessage('system', 'No messages found. Start the conversation!');
                        return;
                    }
                    
                    // Process each message
                    data.forEach(msg => {
                        if (!msg.message || msg.message.trim() === '') return;
                        
                        const isAdmin = msg.sender === 'admin';
                        const senderType = isAdmin ? 'admin' : 'user';
                        const senderName = isAdmin ? 'You' : `User ${userId}`;
                        
                        const timestamp = msg.timestamp ? 
                            msg.timestamp.replace(' ', 'T') + 'Z' : 
                            null;
                        
                        appendMessage(senderType, msg.message, timestamp, senderName, msg.status);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    appendMessage('system', `Error loading messages: ${error.message}`);
                });
        }







        
        // Send admin message to server
      /*  function sendAdminMessage() {
            const message = userInput.value.trim();
            if (!message) return;

            // Add the message to the chat immediately (optimistic UI)
            //appendMessage('admin', message, new Date().toISOString(), 'You');
            appendMessage('admin', message, new Date().toISOString(), 'Support Agent');

            userInput.value = '';
            userInput.style.height = 'auto';
            
            const apiData = {
                action: 'admin_send_message',
                node_id: nodeId,
                user_id: userId,
                model_name: modelName,
                message: message,
                is_admin: true
            };

      const apiUrl = `https://efactor.tech/v8/api.php?action=admin_services_web&node_id=${node_id}`;

            
            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(apiData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    console.error('Failed to save message:', data.error);
                    appendMessage('system', "Failed to send message. Please try again.");
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                appendMessage('system', "Network error. Message may not have been sent.");
            });
        }*/





function sendAdminMessage() {
            const message = userInput.value.trim();
            if (!message) return;

            // Add the message to the chat immediately (optimistic UI)
            appendMessage('admin', message, new Date().toISOString(), 'You', 'delivered');
            userInput.value = '';
            userInput.style.height = 'auto';
            
            const apiData = {
                action: 'admin_send_message',
                node_id: nodeId,
                user_id: userId,
                model_name: modelName,
                message: message,
                is_admin: true,
                status: 'delivered'
            };

            const apiUrl = `https://efactor.tech/v8/api.php?action=admin_services_web&node_id=${nodeId}`;

            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(apiData)
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    console.error('Failed to save message:', data.error);
                    appendMessage('system', "Failed to send message. Please try again.");
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
            //    appendMessage('system', "Network error. Message may not have been sent.");
            });
        }



   function updateMessageStatus(messageId, status) {
            const messages = document.querySelectorAll('.chat-message.admin-message');
            messages.forEach(msg => {
                if (msg.dataset.id === messageId) {
                    const statusIcon = msg.querySelector('.message-status');
                    if (statusIcon) {
                        statusIcon.classList.remove('single-tick', 'double-tick', 'unseen');
                        statusIcon.classList.add(status === 'read' ? 'double-tick' : 'single-tick');
                    }
                }
            });
        }




        
         // Periodically check for message read status
      /*  setInterval(() => {
            fetch(`https://efactor.tech/v8/api.php?action=check_message_status&node_id=${nodeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.messages) {
                        data.messages.forEach(msg => {
                            updateMessageStatus(msg.id, msg.status);
                        });
                    }
                });
        }, 3000); // Check every 3 seconds
*/

// Periodically check for new messages
        setInterval(loadChatHistory, 3000);


        
        // Show typing indicator when user is typing
        let typingTimeout;
        userInput.addEventListener('input', function() {
            // In a real app, you would send a "admin is typing" event to the server
            // For now, we'll just simulate it locally
            
            // Clear any existing timeout
            if (typingTimeout) clearTimeout(typingTimeout);
            
            // Find existing typing indicator
            let typingIndicator = document.querySelector('.typing-indicator');
            
            if (this.value.trim()) {
                if (!typingIndicator) {
                    typingIndicator = document.createElement('div');
                    typingIndicator.className = 'typing-indicator';
                    typingIndicator.textContent = 'admin is typing...';
                    chatBox.appendChild(typingIndicator);
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
                
                // Set timeout to remove typing indicator after 2 seconds of inactivity
                typingTimeout = setTimeout(() => {
                    if (typingIndicator) {
                        typingIndicator.remove();
                    }
                }, 2000);
            } else if (typingIndicator) {
                typingIndicator.remove();
            }
        });
    </script>
</body>
</html>