<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <title><?= $title ?? 'MESSAGES' ?></title>
    <!-- Your existing stylesheets -->

    <link rel="stylesheet" href="<?php echo base_url('assets/css/message-style.css'); ?>">


    <style>

       
        /* FORCE override Bootstrap reboot for messages section */
        section.messages_sec,
        section.messages_sec *,
        section.messages_sec *::before,
        section.messages_sec *::after {
            box-sizing: unset !important;
            /* overflow-x:hidden; */
        }

        .datatable-tools:first-child {
            display: none;
        }

        /* default state */
        .left_controls .list-only {
            display: flex;
        }

        .left_controls .mail-only {
            display: none;
        }

        /* mail-open state */
        .left_controls.mail-open .list-only {
            display: none;
        }

        .left_controls.mail-open .mail-only {
            display: flex;
        }

        .bi-three-dots-vertical {
            display: none;
        }




        .search_bar button {
            outline: none;
            box-shadow: none;
        }

        .search_bar button:focus,
        .search_bar button:active {
            outline: none;
            box-shadow: none;
        }

        #refreshIcon.refresh-spinning {
            animation: refreshSpin 0.8s linear infinite;
            transform-origin: center;
            transform-box: fill-box;
        }

        @keyframes refreshSpin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Sidebar + compose collapsed override with animation */
        .side_bar {
            flex: 0 0 190px;
            width: 190px;
            min-width: 190px;
            transition:
                width 0.28s ease,
                min-width 0.28s ease,
                flex-basis 0.28s ease,
                padding 0.28s ease;
        }

        .side_bar.collapsed {
            flex: 0 0 72px;
            width: 72px;
            min-width: 72px;
        }

        .compose {
            display: flex !important;
            align-items: center;
            justify-content: flex-start;
            gap: 16px;
            box-sizing: border-box;
            white-space: nowrap;
            transition:
                width 0.28s ease,
                height 0.28s ease,
                padding 0.28s ease,
                margin 0.28s ease,
                border-radius 0.28s ease,
                gap 0.28s ease;
        }

        .side_bar.collapsed .compose {
            width: 48px !important;
            min-width: 48px;
            height: 48px;
            min-height: 48px;
            padding: 0 !important;
            margin-left: 12px;
            justify-content: center !important;
            gap: 0 !important;
            border-radius: 16px;
        }

        .compose_txt,
        .item_name,
        .unread_mail_count {
            transition: opacity 0.18s ease, max-width 0.28s ease;
            overflow: hidden;
            white-space: nowrap;
        }

        .side_bar.collapsed .compose_txt,
        .side_bar.collapsed .item_name,
        .side_bar.collapsed .unread_mail_count {
            opacity: 0;
            max-width: 0;
            pointer-events: none;
        }

        .side_bar.collapsed .menu_item {
            display: flex !important;
            align-items: center;
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            gap: 0 !important;
            transition:
                padding 0.28s ease,
                gap 0.28s ease;
        }


        /* Compact mobile sidebar only */
@media (max-width: 768px) {
    .side_bar {
        flex: 0 0 118px !important;
        width: 118px !important;
        min-width: 118px !important;
        max-width: 118px !important;
    }

    .side_bar.collapsed {
        flex: 0 0 46px !important;
        width: 46px !important;
        min-width: 46px !important;
        max-width: 46px !important;
    }

    .compose {
        width: calc(100% - 14px) !important;
        min-width: 0 !important;
        height: 36px !important;
        padding: 0 8px !important;
        margin-left: 7px !important;
        margin-right: 7px !important;
        gap: 8px !important;
        border-radius: 12px !important;
        font-size: 12px !important;
    }

    .compose_txt {
        max-width: 58px !important;
        font-size: 12px !important;
    }

    .menu_item {
        width: calc(100% - 10px) !important;
        min-width: 0 !important;
        height: 28px !important;
        margin-left: 5px !important;
        margin-right: 5px !important;
        padding: 0 6px !important;
        gap: 6px !important;
        border-radius: 7px !important;
        font-size: 11px !important;
    }

    .item_name {
        max-width: 70px !important;
        font-size: 11px !important;
    }

    .unread_mail_count {
        font-size: 10px !important;
        min-width: 16px !important;
        height: 16px !important;
        line-height: 16px !important;
    }

    .side_bar.collapsed .compose {
        width: 34px !important;
        min-width: 34px !important;
        height: 34px !important;
        min-height: 34px !important;
        margin-left: 6px !important;
        margin-right: 6px !important;
        padding: 0 !important;
    }

    .side_bar.collapsed .menu_item {
        width: 34px !important;
        min-width: 34px !important;
        height: 34px !important;
        margin-left: 6px !important;
        margin-right: 6px !important;
        padding: 0 !important;
    }
}



        /* .side_bar.collapsed .item_name,
.side_bar.collapsed .unread_mail_count {
    display: none !important;
} */
    </style>
</head>

<body>
    <section class="messages_sec">
        <div class="container">
            <div class="message_items">
                <div class="top_bar common-flex ">
                    <!-- ----side menu bar toggle button -->
                    <div class="menu_btn common-flex" onclick="ToggleSlidebar()">
                        <svg onclick="toggleMenuIcon(this)" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5" />
                        </svg>
                        <div class="mail_logo"><img src="<?= base_url('files/message_images/mail-blue.svg') ?>" alt="mail-image"></div>
                    </div>

                    <!-- ----Search bar  -->
                    <div class="search_bar">
                        <input type="text" id="search-messages" placeholder="<?php echo app_lang('search') ?>">
                        <button class="search_btn"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path
                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                            </svg></button>
                    </div>
                    <!-- ----- setting toggle button -->
                    <div class="setting_btn " onclick="ToggleSetting()">
                        <!-- <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-gear" viewBox="0 0 16 16">
                            <path
                                d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0" />
                            <path
                                d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z" />
                        </svg> -->

                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                            class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                            <path
                                d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                        </svg>
                    </div>
                </div>

                <!-- ----the main layout scroll-bar  -->
                <div class="main_layout">

                    <!-- ---left side bar -->
                    <div class="side_bar">
                        <div class="compose  common-flex" onclick="openCompose()">

                            <div class="compose_icon common-flex"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                    height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path
                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                    <path fill-rule="evenodd"
                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                </svg></div>
                            <div class="compose_txt" style="font-family: 'Google Sans', Roboto, RobotoDraft, Helvetica, Arial, sans-serif">
                                <?php echo modal_anchor(get_uri("messages/modal_form"), app_lang('compose'), array("title" => app_lang('send_message'))); ?></div>
                        </div>


                        <div class="menu_item common-flex <?php echo ($mode === 'inbox') ? 'active' : ''; ?>" data-label="inbox" onclick="setActive(this)">
                            <div class="item_icon common-flex"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                    height="16" fill="currentColor" class="bi bi-inboxes-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M4.98 1a.5.5 0 0 0-.39.188L1.54 5H6a.5.5 0 0 1 .5.5 1.5 1.5 0 0 0 3 0A.5.5 0 0 1 10 5h4.46l-3.05-3.812A.5.5 0 0 0 11.02 1zM3.81.563A1.5 1.5 0 0 1 4.98 0h6.04a1.5 1.5 0 0 1 1.17.563l3.7 4.625a.5.5 0 0 1 .106.374l-.39 3.124A1.5 1.5 0 0 1 14.117 10H1.883A1.5 1.5 0 0 1 .394 8.686l-.39-3.124a.5.5 0 0 1 .106-.374zM.125 11.17A.5.5 0 0 1 .5 11H6a.5.5 0 0 1 .5.5 1.5 1.5 0 0 0 3 0 .5.5 0 0 1 .5-.5h5.5a.5.5 0 0 1 .496.562l-.39 3.124A1.5 1.5 0 0 1 14.117 16H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .121-.393z" />
                                </svg></div>
                            <div class="item_name"><?php echo anchor(get_uri("messages/inbox"), app_lang('inbox'), array()); ?></div>
                            <!-- <div class="unread_mail_count hidden">20</div> -->
                            <div class="unread_mail_count hidden"></div>




                        </div>

                        <div class="menu_item common-flex <?php echo ($mode === 'sent_items') ? 'active' : ''; ?>" data-label="sent" onclick="setActive(this)">
                            <div class="item_icon common-flex"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                    height="16" fill="currentColor" class="bi bi-send-check" viewBox="0 0 16 16">
                                    <path
                                        d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855a.75.75 0 0 0-.124 1.329l4.995 3.178 1.531 2.406a.5.5 0 0 0 .844-.536L6.637 10.07l7.494-7.494-1.895 4.738a.5.5 0 1 0 .928.372zm-2.54 1.183L5.93 9.363 1.591 6.602z" />
                                    <path
                                        d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.774.773a.75.75 0 0 0 1.174-.144l1.335-2.226a.5.5 0 0 0-.172-.686" />
                                </svg></div>
                            <div class="item_name"><?php echo anchor(get_uri("messages/sent_items"), app_lang('sent_items'), array()); ?></div>
                        </div>

                        <div class="menu_item common-flex" data-label="starred" onclick="setActive(this)">
                            <div class="item_icon common-flex"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                    height="16" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
                                    <path
                                        d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z" />
                                </svg></div>
                            <div class="item_name">Starred</div>
                        </div>

                        <div class="menu_item common-flex" data-label="chats" onclick="setActive(this)">
                            <div class="item_icon common-flex"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                    height="16" fill="currentColor" class="bi bi-chat-square-text" viewBox="0 0 16 16">
                                    <path
                                        d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-2.5a2 2 0 0 0-1.6.8L8 14.333 6.1 11.8a2 2 0 0 0-1.6-.8H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2.5a1 1 0 0 1 .8.4l1.9 2.533a1 1 0 0 0 1.6 0l1.9-2.533a1 1 0 0 1 .8-.4H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                                    <path
                                        d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6m0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
                                </svg></div>
                            <div class="item_name">Chats</div>
                        </div>

                        <div class="menu_item common-flex <?php echo ($mode === 'all') ? 'active' : ''; ?>" data-label="all" onclick="setActive(this)">
                            <div class="item_icon common-flex"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                    height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                                    <path
                                        d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
                                </svg></div>
                            <div class="item_name"><?php echo anchor(get_uri("messages/inbox") . "?mode=all", "All mail", array()); ?></div>
                        </div>
                        <!-- you can other items here in side bar -->
                    </div>

                    <!-- ---the main scroll bar view section -->
                    <div class="scroll_bar">

                        <!-- ----- top controlsss -->
                        <div class="top_controls common-flex">

                            <!-- ---top left side controls -->
                            <div class="left_controls common-flex">

                                <div class="select_btn list-only"> <!--select check-box button-->
                                    <div class="select_checkbox"><input type="checkbox" id="selectAll"></div>
                                    <span class="dropdown_toggle"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                            height="16" fill="currentColor" class="bi bi-caret-down-fill"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z" />
                                        </svg></span>

                                    <div class="select_dropdown">
                                        <div onclick="selectAllMails()">All</div>
                                        <div onclick="selectNone()">None</div>
                                        <div onclick="selectRead()">Read</div>
                                        <div onclick="selectUnread()">Unread</div>
                                        <div onclick="selectStarred()">Starred</div>
                                        <div onclick="selectUnstarred()">Unstarred</div>
                                    </div>
                                </div>

                                <div class="refresh_btn list-only" onclick="refreshMessagesList()"> <!--refresh button-->

                                    <svg
                                        id="refreshIcon"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 48 48"
                                        width="20"
                                        height="20">
                                        <path
                                            d="M24 40c-4.4333 0 -8.2083 -1.5583 -11.325 -4.675C9.55833 32.2083 8 28.4333 8 24c0 -4.4333 1.55833 -8.2083 4.675 -11.325C15.7917 9.55833 19.5667 8 24 8c2.8333 0 5.3167 0.575 7.45 1.725 2.1333 1.15 3.9833 2.725 5.55 4.725V9.5c0 -0.43333 0.1417 -0.79167 0.425 -1.075 0.2833 -0.28333 0.6417 -0.425 1.075 -0.425 0.4333 0 0.7917 0.14167 1.075 0.425 0.2833 0.28333 0.425 0.64167 0.425 1.075v9.7c0 0.4333 -0.1417 0.7917 -0.425 1.075s-0.6417 0.425 -1.075 0.425h-9.7c-0.4333 0 -0.7917 -0.1417 -1.075 -0.425s-0.425 -0.6417 -0.425 -1.075c0 -0.4333 0.1417 -0.7917 0.425 -1.075s0.6417 -0.425 1.075 -0.425h6.9c-1.2667 -2 -2.8833 -3.6167 -4.85 -4.85C28.8833 11.6167 26.6 11 24 11c-3.6333 0 -6.7083 1.2583 -9.225 3.775C12.2583 17.2917 11 20.3667 11 24c0 3.6333 1.2583 6.7083 3.775 9.225C17.2917 35.7417 20.3667 37 24 37c2.5 0 4.8333 -0.6583 7 -1.975 2.1667 -1.3167 3.7833 -3.0917 4.85 -5.325 0.1667 -0.3667 0.4417 -0.6417 0.825 -0.825 0.3833 -0.1833 0.7583 -0.1917 1.125 -0.025 0.4 0.1667 0.6667 0.4417 0.8 0.825 0.1333 0.3833 0.1167 0.775 -0.05 1.175 -1.3 2.8 -3.2583 5.025 -5.875 6.675C30.0583 39.175 27.1667 40 24 40Z"
                                            fill="currentColor" />
                                    </svg>

                                </div>

                                <div class="back_btn mail-only " onclick="closeMailView()">
                                    <!--Back to index button-->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                                    </svg>
                                </div>

                                <div class="delete_btn mail-only" onclick="openDeletePopup()"> <!--delete button-->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-trash" viewBox="0 0 16 16">
                                        <path
                                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                        <path
                                            d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                    </svg>
                                </div>

                                <div class="unread_btn mail-only"> <!--Unread button-->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-envelope-slash" viewBox="0 0 16 16">
                                        <path
                                            d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2zm3.708 6.208L1 11.105V5.383zM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2z" />
                                        <path
                                            d="M14.975 10.025a3.5 3.5 0 1 0-4.95 4.95 3.5 3.5 0 0 0 4.95-4.95m-4.243.707a2.5 2.5 0 0 1 3.147-.318l-3.465 3.465a2.5 2.5 0 0 1 .318-3.147m.39 3.854 3.464-3.465a2.501 2.501 0 0 1-3.465 3.465Z" />
                                    </svg>
                                </div>

                                <div class="more_btn"> <!--more options button-->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                        <path
                                            d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                                    </svg>
                                </div>

                            </div>
                            <!-- ----top right controls -->
                            <div class="right_controls common-flex">
                                <div class="page_count hidden">1-50 of 200</div>
                                <div class="prev_btn" onclick="prevPage()"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor" class="bi bi-chevron-left"
                                        viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
                                    </svg>
                                </div>

                                <div class="next_btn" onclick="nextPage()"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor" class="bi bi-chevron-right"
                                        viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
                                    </svg>
                                </div>

                            </div>
                        </div>

                        <!-- ------mail list section  -->
                        <div class="scroll_sec">
                            <div class="mails_list_view" id="mailList">
                                <div class="table-responsive">
                                    <table id="message-table" class="display no-thead no-padding clickable" cellspacing="0" width="100%">

                                    </table>
                                </div>
                            </div>

                            <div id="message-details-section" class="card mail_view hidden">
                                <div id="empty-message" class="text-center mb15 box">
                                    <div class="box-content" style="vertical-align: middle; height: 100%">
                                        <div><?php echo app_lang("select_a_message"); ?></div>
                                        <i data-feather="mail" width="10rem" height="10rem" style="color:rgba(128, 128, 128, 0.1)"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="empty_state hidden" id="emptyState">
                                <p style="padding:40px; text-align:center; color:#666;">
                                    No mails to display
                                </p>
                            </div>

                            <div class="empty_state hidden" id="chatState">
                                <p style="padding:40px; text-align:center; color:#666;">
                                    Chats – Available soon
                                </p>
                            </div>

                            <!-- -----scroll footer -->
                            <div class="scroll_footer common-flex">
                                <div class="left_content">Efactor</div>

                                <div class="center_content">
                                    <a href="a">Terms</a>
                                    <span>|</span>
                                    <a href="b">privacy</a>
                                    <span>|</span>
                                    <a href="c">Program Policies</a>

                                </div>

                                <div class="last_activity">
                                    Last Account Activity:
                                    <span id="lastMailActivityTime">--</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Delete confirmation popup -->
    <div id="deleteConfirmPopup" class="delete-popup hidden">
        <div class="delete-popup-box">
            <p>Delete this message?</p>
            <div class="delete-actions">
                <button class="btn-cancel" onclick="closeDeletePopup()">Cancel</button>
                <button class="btn-confirm" onclick="confirmDeleteMail()">Delete</button>
            </div>
        </div>
    </div>











    <script type="text/javascript">
        $(document).ready(function() {
            var autoSelectIndex = "<?php echo $auto_select_index; ?>";
            $("#message-table").appTable({
                source: '<?php echo_uri("messages/list_data/" . $mode) ?>',
                order: [
                    [1, "desc"]
                ],
                columns: [{
                        title: '<?php echo app_lang("message") ?>'
                    },
                    {
                        targets: [1],
                        visible: false
                    },
                    {
                        targets: [2],
                        visible: false
                    }
                ],
                onInitComplete: function() {
                    if (autoSelectIndex) {
                        //automatically select the message
                        var $tr = $("[data-index=" + autoSelectIndex + "]").closest("tr");
                        if ($tr.length)
                            $tr.trigger("click");
                    }

                    var $message_list = $("#message-list-box"),
                        $empty_message = $("#empty-message");
                    if ($empty_message.length && $message_list.length) {
                        $empty_message.height($message_list.height());
                    }
                }
            });

            var messagesTable = $('#message-table').DataTable();

            function updateLastMailActivityTime() {
                var table = $('#message-table').DataTable();
                var data = table.rows({
                    search: 'applied'
                }).data();

                if (data.length) {
                    var rowHtml = data[0][0];
                    var tempDiv = document.createElement("div");
                    tempDiv.innerHTML = rowHtml;

                    var timeEl = tempDiv.querySelector(".text-off.float-end, .float-end.text-off, .text-off");

                    if (timeEl) {
                        $("#lastMailActivityTime").text(timeEl.innerText.trim());
                    }
                } else {
                    $("#lastMailActivityTime").text("--");
                }
            }

            updateLastMailActivityTime();

            $('#message-table').on('draw.dt xhr.dt', function() {
                setTimeout(updateLastMailActivityTime, 100);
            });
            $('#search-messages').keyup(function() {
                messagesTable.search($(this).val()).draw();
            });


            /*load a message details*/
            $("body").on("click", "#message-table tr", function() {

                // remove unread style
                $(this).find(".unread").removeClass("unread");

                if (!$(this).hasClass("active")) {

                    // const message_id = $(this).find(".message-row").data("id");
                    // const reply = $(this).find(".message-row").data("reply");

                    // window.currentMessageId = message_id; // <-- ADD
                    // window.currentMailbox = "<?php echo $mode ?>"; // inbox or sent_items

                    const $messageRow = $(this).find(".message-row");
                    const message_id = $messageRow.data("id");
                    const reply = $messageRow.data("reply");
                    const rowMode = $messageRow.data("mode") || "<?php echo $mode ?>";

                    window.currentMessageId = message_id;
                    window.currentMailbox = rowMode;
                    window.currentListMode = "<?php echo $mode ?>";


                    if (!message_id) return;

                    // UI toggle
                    $("#mailList").addClass("hidden");
                    $("#message-details-section").removeClass("hidden");

                    // toolbar switch
                    $(".left_controls").addClass("mail-open");

                    $("tr.active").removeClass("active");
                    $(this).addClass("active");

                    $("#message-details-section").html("<div style='padding:20px'>Loading...</div>");

                    $.ajax({
                        url: "<?php echo get_uri("messages/view"); ?>/" + message_id + "/" + rowMode + "/" + reply,
                        dataType: "json",
                        success: function(result) {
                            if (result.success) {

                                $("#message-details-section").html(result.data);

                                if ($("#message-table").length && $.fn.DataTable.isDataTable("#message-table")) {
                                    $("#message-table").DataTable().ajax.reload(null, false);
                                }

                            } else {
                                appAlert.error(result.message);
                            }
                        }
                    });
                }
            });

        });
    </script>



    <script>
        //-------------top-bar and sidebar functionality
        function ToggleSlidebar() {
            document.querySelector('.side_bar').classList.toggle('collapsed');
            document.querySelector('.top_bar').classList.toggle('collapsed');
        }

        function setActive(element) {
            document.querySelectorAll('.menu_item')
                .forEach(item => item.classList.remove('active'));

            element.classList.add('active');

            const label = element.dataset.label; // inbox | starred | sent | all
            currentFilter = label;

            currentPage = 0;
            applyFilters();
        }

        function toggleMenuIcon(svg) {
            svg.classList.toggle('active');
        }

        //------------scroll bar top control functions
        const selectBtn = document.querySelector('.select_btn');
        const selectAllBox = document.getElementById('selectAll');
        // const mailRows = document.querySelectorAll('.mail-row');

        // toggle dropdown
        document.querySelector('.dropdown_toggle').onclick = () => {
            selectBtn.classList.toggle('open');
        };

        // select all checkbox
        selectAllBox.onchange = () => {
            mailRows.forEach(row => {
                row.querySelector('.mail-checkbox').checked = selectAllBox.checked;
            });
        };

        // actions
        function selectAllMails() {
            mailRows.forEach(r => r.querySelector('.mail-checkbox').checked = true);
            closeDropdown();
        }

        function selectNone() {
            mailRows.forEach(r => r.querySelector('.mail-checkbox').checked = false);
            closeDropdown();
        }

        function selectRead() {
            filterSelect('read');
        }

        function selectUnread() {
            filterSelect('unread');
        }

        function selectStarred() {
            filterSelect('starred');
        }

        function selectUnstarred() {
            filterSelect('unstarred');
        }

        function filterSelect(cls) {
            mailRows.forEach(r => {
                r.querySelector('.mail-checkbox').checked = r.classList.contains(cls);
            });
            closeDropdown();
        }

        function closeDropdown() {
            selectBtn.classList.remove('open');
        }

        // close dropdown when clicked outside
        document.addEventListener('click', function(e) {
            const selectBtn = document.querySelector('.select_btn');

            if (!selectBtn.contains(e.target)) {
                selectBtn.classList.remove('open');
            }
        });

        // pagination for arrow buttons

        function nextPage() {
            $('#message-table')
                .DataTable()
                .page('next')
                .draw('page');
        }

        function prevPage() {
            $('#message-table')
                .DataTable()
                .page('previous')
                .draw('page');
        }

        function refreshMessagesList() {

            const refreshIcon = document.getElementById("refreshIcon");

            if (refreshIcon) {
                refreshIcon.classList.add("refresh-spinning");
            }

            if ($("#message-table").length && $.fn.DataTable.isDataTable("#message-table")) {

                $("#message-table").DataTable().ajax.reload(function() {

                    if (refreshIcon) {
                        refreshIcon.classList.remove("refresh-spinning");
                    }

                    appAlert.success("Messages refreshed");

                }, false);

            } else {

                if (refreshIcon) {
                    refreshIcon.classList.remove("refresh-spinning");
                }

            }
        }
        // compose clicking function
        function openCompose() {
            const composeLink = document.querySelector('.compose_txt a');
            if (composeLink) {
                composeLink.click();
            }
        };
        // set active menu_items 
        document.addEventListener("DOMContentLoaded", function() {
            const currentUrl = window.location.href;

            document.querySelectorAll(".menu_item").forEach(item => {
                const link = item.querySelector(".item_name a");
                if (!link) return;

                if (currentUrl.includes(link.getAttribute("href"))) {
                    item.classList.add("active");
                } else {
                    item.classList.remove("active");
                }
            });
        });

        function handleSidebarView(label) {

            // Hide all sections
            $("#mailList").addClass("hidden");
            $("#message-details-section").addClass("hidden");
            $("#emptyState").addClass("hidden");
            $("#chatState").addClass("hidden");

            if (label === "starred") {
                $("#emptyState").removeClass("hidden");
                return false; // stop navigation
            }

            if (label === "chats") {
                $("#chatState").removeClass("hidden");
                return false; // stop navigation
            }

            // Default: inbox / sent / all
            $("#mailList").removeClass("hidden");
            return true; // allow navigation
        }

        document.querySelectorAll(".menu_item").forEach(item => {
            item.addEventListener("click", function(e) {

                // active state
                document.querySelectorAll('.menu_item')
                    .forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                const label = this.dataset.label;

                // handle UI toggle
                const allowNavigation = handleSidebarView(label);

                // prevent anchor double click
                if (e.target.tagName.toLowerCase() === "a") return;

                // navigate only if allowed
                if (allowNavigation) {
                    const link = this.querySelector(".item_name a");
                    if (link) {
                        window.location.href = link.href;
                    }
                }
            });
        });



        function closeMailView() {
            $("#message-details-section").addClass("hidden");
            $("#mailList").removeClass("hidden");

            // restore toolbar
            $(".left_controls").removeClass("mail-open");

            // remove active row
            $("#message-table tr.active").removeClass("active");
        }





        // new delete function here 

        function openDeletePopup() {
            if (!window.currentMessageId) {
                return; // no message selected
            }
            document.getElementById("deleteConfirmPopup").classList.remove("hidden");
        }

        function closeDeletePopup() {
            document.getElementById("deleteConfirmPopup").classList.add("hidden");
        }




        function confirmDeleteMail() {
            if (!window.currentMessageId) return;

            var deleteUrl = "<?php echo get_uri('messages/delete_my_messages'); ?>/" +
                currentMessageId + "/";

            $.ajax({
                url: deleteUrl,
                type: "POST",
                success: function() {

                    closeDeletePopup();

                    // Close mail view
                    $("#message-details-section").addClass("hidden");
                    $("#message-details-section").html("");

                    // Restore list view + toolbar
                    $("#mailList").removeClass("hidden");
                    $(".left_controls").removeClass("mail-open");

                    // Reload correct list (Inbox / Sent)
                    // if (window.currentMailbox === "sent_items") {
                    //     window.location.href = "<?php echo get_uri('messages/sent_items'); ?>";
                    // } else {
                    //     window.location.href = "<?php echo get_uri('messages'); ?>";
                    // }
                    if (window.currentListMode === "all") {
                        window.location.href = "<?php echo get_uri('messages/inbox'); ?>?mode=all";
                    } else if (window.currentMailbox === "sent_items") {
                        window.location.href = "<?php echo get_uri('messages/sent_items'); ?>";
                    } else {
                        window.location.href = "<?php echo get_uri('messages/inbox'); ?>";
                    }
                },
                error: function() {
                    alert("Delete failed");
                    closeDeletePopup();
                }
            });
        }
    </script>













</body>

</html>