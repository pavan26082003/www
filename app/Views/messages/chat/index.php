




<?php
// Only load if chat module is enabled and not already loaded
$can_chat = can_access_messages_module();  //print in console unread_messgaes 

if ((get_setting("module_chat") || $can_chat) && !defined('CHAT_UI_LOADED')) {
    define('CHAT_UI_LOADED', true); // Prevent duplicate loading

$user_id=1;
    
    // Get unread message count
 //   $unread_count = $this->Messages_model->count_unread_chat($user_id); 
//$unread_count = '4';
  
    ?>





    <style>

        

        .init-chat-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
            cursor: pointer;
        }
        .chat-min-icon {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background-color: #7367F0;
            color: white;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            position: relative;
            transition: all 0.3s ease;
        }
        .chat-min-icon:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }
        .chat-notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #FF3B30;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid #fff;
        }
        .has-message .chat-min-icon {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .rise-chat-wrapper {
            position: fixed;
            bottom: 70px;
            right: 20px;
            width: 320px;
            height: 400px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            z-index: 998;
            overflow: hidden;
        }
        .move-chat-icon {
            right: 340px !important;
        }
        .chat-close {
            font-size: 20px;
            line-height: 40px;
        }
        @media (max-width: 767px) {
            .rise-chat-wrapper {
                width: 100%;
                height: 70vh;
                bottom: 0;
                right: 0;
                border-radius: 8px 8px 0 0;
            }
            .move-chat-icon {
                right: 20px !important;
                bottom: calc(70vh + 20px) !important;
            }
        }
    </style>

    <script type="text/javascript">

 if (typeof window.chatInitialized === 'undefined') {
            window.chatInitialized = true;
            
            $(document).ready(function () {
                // Your existing JavaScript code
            });
        }




        $(document).ready(function () {

            // Initialize chat icon content
            chatIconContent = {
                "open": "<i data-feather='message-circle' class='icon-18'></i>",
                "close": "<span class='chat-close'>&times;</span>",
                "unread": "<i data-feather='message-circle' class='icon-18'></i><span class='chat-notification-badge'></span>"
            };

            // Set chat icon state
            setChatIcon = function (type, count) {
                // Don't show count if notifications are temporarily disabled
                if ($("#js-chat-min-icon").attr("data-prevent-notification-count") === "1" && type === "unread") {
                    return false;
                }

                var iconHtml = chatIconContent[type];
                if (type === "unread" && count) {
                    var displayCount = count > 9 ? '9+' : count;
                    iconHtml = iconHtml.replace('</span>', displayCount + '</span>');
                }

                $("#js-chat-min-icon").attr("data-type", type).html(iconHtml);

                if (type === "open") {
                    $("#js-rise-chat-wrapper").addClass("hide");
                    $("#js-init-chat-icon").removeClass("has-message");
                } else if (type === "close") {
                    $("#js-rise-chat-wrapper").removeClass("hide");
                    $("#js-init-chat-icon").removeClass("has-message");
                } else if (type === "unread") {
                    $("#js-init-chat-icon").addClass("has-message");
                }

                feather.replace();
            };

            // Change chat icon position
            changeChatIconPosition = function (type) {
                if (type === "close") {
                    $("#js-init-chat-icon").addClass("move-chat-icon");
                } else if (type === "open") {
                    $("#js-init-chat-icon").removeClass("move-chat-icon");
                }
            };

            // Check for active chat
            var activeChatId = getCookie("active_chat_id"),
                isChatBoxOpen = getCookie("chatbox_open"),
                $chatIcon = $("#js-init-chat-icon");

            // Chat icon click handler
            $chatIcon.click(function () {
                $("#js-rise-chat-wrapper").html("");
                window.updateLastMessageCheckingStatus();

                var $chatIcon = $("#js-chat-min-icon");

                if ($chatIcon.attr("data-type") === "unread") {
                    $chatIcon.attr("data-prevent-notification-count", "1");
                    // Re-enable notifications after 11 seconds
                    setTimeout(function () {
                        $chatIcon.attr("data-prevent-notification-count", "0");
                    }, 11000);
                }

                var windowSize = window.matchMedia("(max-width: 767px)");

                if ($chatIcon.attr("data-type") !== "close") {
                    // Open chat
                    setTimeout(function () {
                        loadChatTabs();
                    }, 200);
                    setChatIcon("close");
                    setCookie("chatbox_open", "1");
                    if (windowSize.matches) {
                        changeChatIconPosition("close");
                    }
                } else {
                    // Close chat
                    setChatIcon("open");
                    setCookie("chatbox_open", "");
                    setCookie("active_chat_id", "");
                    if (windowSize.matches) {
                        changeChatIconPosition("open");
                    }
                }

                if (window.activeChatChecker) {
                    window.clearInterval(window.activeChatChecker);
                }

                if (typeof window.placeCartBox === "function") {
                    window.placeCartBox();
                }
                
                feather.replace();
            });

            // Open chat box if needed
            if (isChatBoxOpen) {
                if (activeChatId) {
                    getActiveChat(activeChatId);
                } else {
                    loadChatTabs();
                }
            }

            // Adjust position for mobile
            var windowSize = window.matchMedia("(max-width: 767px)");
            if (windowSize.matches && isChatBoxOpen) {
                $("#js-init-chat-icon").addClass("move-chat-icon");
            }

            // Event delegation for chat items
            $('body #js-rise-chat-wrapper').on('click', '.js-message-row', function () {
                getActiveChat($(this).attr("data-id"));
            });

            $('body #js-rise-chat-wrapper').on('click', '.js-message-row-of-team-members-tab', function () {
                getChatlistOfUser($(this).attr("data-id"), "team_members");
            });

            $('body #js-rise-chat-wrapper').on('click', '.js-message-row-of-clients-tab', function () {
                getChatlistOfUser($(this).attr("data-id"), "clients");
            });
        });

        // Load chat list for a specific user
        function getChatlistOfUser(user_id, tab_type) {
            setChatIcon("close");
            appLoader.show({container: "#js-rise-chat-wrapper", css: "bottom: 40%; right: 35%;"});
            $.ajax({
                url: "<?php echo get_uri("messages/get_chatlist_of_user"); ?>",
                type: "POST",
                data: {user_id: user_id, tab_type: tab_type},
                success: function (response) {
                    $("#js-rise-chat-wrapper").html(response);
                    appLoader.hide();
                }
            });
        }

        // Load chat tabs
        function loadChatTabs(trigger_from_user_chat) {
            setChatIcon("close");
            setCookie("active_chat_id", "");
            appLoader.show({container: "#js-rise-chat-wrapper", css: "bottom: 40%; right: 35%;"});
            $.ajax({
                url: "<?php echo get_uri("messages/chat_list"); ?>",
                data: { type: "inbox" },
                success: function (response) {
                    $("#js-rise-chat-wrapper").html(response);

                    if (!trigger_from_user_chat) {
                        $("#chat-inbox-tab-button a").trigger("click");
                    } else if (trigger_from_user_chat === "team_members") {
                        $("#chat-users-tab-button").find("a").trigger("click");
                    } else if (trigger_from_user_chat === "clients") {
                        $("#chat-clients-tab-button").find("a").trigger("click");
                    }
                    appLoader.hide();
                }
            });
        }

        // Load active chat
        function getActiveChat(message_id) {
            setChatIcon("close");
            appLoader.show({container: "#js-rise-chat-wrapper", css: "bottom: 40%; right: 35%;"});
            $.ajax({
                url: "<?php echo get_uri('messages/get_active_chat'); ?>",
                type: "POST",
                data: { message_id: message_id },
                success: function (response) {
                    $("#js-rise-chat-wrapper").html(response);
                    appLoader.hide();
                    setCookie("active_chat_id", message_id);
                    $("#js-chat-message-textarea").focus();
                }
            });
        }

        // Prepare unread message notification
        window.prepareUnreadMessageChatBox = function (totalMessages) {
            setChatIcon("unread", totalMessages);
        };

        // Trigger active chat
        window.triggerActiveChat = function (message_id) {
            getActiveChat(message_id);
        }
    </script>  
<?php } ?>