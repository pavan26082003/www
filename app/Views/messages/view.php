<style>
    .writebox {
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 14px;
        padding: 16px;
        -webkit-font-smoothing: auto;
        font-family: "Google Sans", Roboto, RobotoDraft, Helvetica, Arial, sans-serif;
    }

    
    .writebox input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 14px;
        color: #202124;
    }


    .writebox:focus-within input {
        box-shadow: 0 1px 6px rgba(32, 33, 36, 0.28);
    }

    .foot_btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 14px;



    }

    #replyBox {
        display: none;
    }

    .bottom_btns {
        display: flex;
    }


    .comment-image-box .download_icon {
        margin-left: 8px;
        margin-right: 6px;
        color: #5f6368;
        vertical-align: middle;
    }

    .comment-image-box .download_btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 7px 12px;
        border-radius: 999px;
        background: #0b61eb;
        color: #f8fcff;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.18s ease;
    }

    .comment-image-box .download_btn:hover {
        background: #e8f0fe;
        color: #0b57d0;
        text-decoration: none;
    }

    .comment-image-box .download_btn:focus,
    .comment-image-box .download_btn:active {
        outline: none;
        box-shadow: 0 0 0 3px rgba(11, 87, 208, 0.14);
    }

    .reply-image-only-btn {
        background: none;
        border: none;
        padding: 0;
        color: #7988a2;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .reply-image-only-btn:hover {
        color: #0b57d0;
    }

.forward_box {
    margin: 16px 12px 0 12px;
    width: auto;
    max-width: none;
    box-sizing: border-box;
    overflow: hidden;
}

.forward_to_row {
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 18px 0;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    overflow: hidden;
}

.forward_to_row label {
    min-width: 34px;
    margin: 0;
    font-weight: 600;
    color: #475467;
    flex-shrink: 0;
}

.forward_to_row .select2,
.forward_to_row select,
.forward_to_row .select2-container {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    box-sizing: border-box;
    flex: 1;
}

.forward_to_row .select2-selection {
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box;
}

.bottom_btns.hide-actions {
    display: none !important;
}

@media (min-width: 769px) {
    .forward_box {
        width: min(760px, calc(100% - 24px));
        margin-left: auto;
        margin-right: auto;
    }
}

@media (max-width: 768px) {
    .forward_box {
        width: auto;
        max-width: none;
        margin-left: 10px;
        margin-right: 10px;
        border-radius: 18px;
    }

    .forward_to_row {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }

    .forward_to_row label {
        min-width: 0;
        width: 100%;
    }
}

@media (max-width: 420px) {
    .forward_box {
        margin-left: 8px;
        margin-right: 8px;
    }
}
    
</style>
<div class="message-container-<?php echo $message_info->id; ?>" data-total_messages="<?php echo $found_rows ?>">
    <?php
    if ($mode === "inbox") {
        if ($is_reply) {
            $user_image = $login_user->image;
        } else {
            $user_image = $message_info->user_image;
        }
    }
    if ($mode === "sent_items") {
        if ($is_reply) {
            $user_image = $message_info->user_image;
        } else {
            $user_image = $login_user->image;
        }
    }
    ?>

    <div class="b-b p15 m0 bg-white">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex">
                    <div class="flex-shrink-0 pe-2">
                        <span class="avatar avatar-sm">
                            <img src="<?php echo get_avatar($user_image); ?>" alt="..." />
                        </span>
                    </div>
                    <div class="w-100">
                        <div class="clearfix">
                            <?php
                            $message_user_id = $message_info->from_user_id;
                            if ($mode === "sent_items" && $is_reply != "1" || $mode === "inbox" && $is_reply == "1") {
                                $message_user_id = $message_info->to_user_id;
                            ?>
                                <label class="badge bg-success"><?php echo app_lang("to"); ?></label><?php } ?>
                            <?php
                            if ($message_info->user_type == "client") {
                                echo get_client_contact_profile_link($message_user_id, $message_info->user_name, array("class" => "dark strong"));
                            } else {
                                echo get_team_member_profile_link($message_user_id, $message_info->user_name, array("class" => "dark strong"));
                            }
                            ?>
                            <span class="text-off float-end"><?php echo format_to_relative_time($message_info->created_at); ?></span>

                            <span class="float-end dropdown">
                                <!-- <div class="text-off dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="true" >
                                    <i data-feather="chevron-down" class="icon"></i>
                                </div> -->
                                <ul class="dropdown-menu" role="menu">
                                    <li role="presentation">
                                        <?php
                                        // Assuming $imageFilename is defined somewhere in your context
                                        $imageFilename = $message_info->files; // Replace with your actual filename or variable

                                        echo ajax_anchor(
                                            get_uri("messages/delete_my_messages/$message_info->id/$imageFilename"),
                                            "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'),
                                            array(
                                                "class" => "dropdown-item",
                                                "title" => app_lang('delete'),
                                                "data-fade-out-on-success" => ".message-container-$message_info->id"
                                            )
                                        );
                                        ?>
                                    </li>
                                </ul>
                            </span>

                        </div>
                        <p class="pt10 pb10 b-b">
                            <?php echo app_lang("subject"); ?>: <?php echo $message_info->subject; ?>
                        </p>

                        <?php
                        $clean_message = (string) $message_info->message;

                        if (!empty($message_info->files)) {
                            $clean_message = str_replace($message_info->files, "", $clean_message);
                        }

                        $clean_message = preg_replace('/a:\d+:\{.*?\}/s', '', $clean_message);
                        $clean_message = trim($clean_message);

                        echo nl2br(link_it($clean_message));
                        ?>


                        <!-- <div class="comment-image-box clearfix">
                            <?php
                            $files = array();

                            if (!empty($message_info->files)) {
                                $unserialized_files = @unserialize($message_info->files);

                                if ($unserialized_files !== false && is_array($unserialized_files)) {
                                    $files = $unserialized_files;
                                }
                            }

                            $total_files = count($files);

                            if ($total_files) {
                                echo view("includes/timeline_preview", array("files" => $files));

                                $download_caption = app_lang('download');

                                if ($total_files > 1) {
                                    $download_caption = sprintf(app_lang('download_files'), $total_files);
                                }

                                echo "<i class='bi bi-download icon-16 download_icon'></i>";
                                echo anchor(
                                    get_uri("messages/download_message_files/" . $message_info->id),
                                    $download_caption,
                                    array(
                                        "class" => "download_btn",
                                        "title" => $download_caption
                                    )
                                );
                            }
                            ?>
                        </div> -->


                        <div class="comment-image-box clearfix">
                            <?php
                            $files = array();

                            if (!empty($message_info->files)) {
                                $unserialized_files = @unserialize($message_info->files);

                                if ($unserialized_files !== false && is_array($unserialized_files)) {
                                    $files = $unserialized_files;
                                }
                            }

                            $total_files = count($files);

                            if ($total_files) {
                                echo view("includes/timeline_preview", array("files" => $files));

                                $download_caption = app_lang('download');

                                if ($total_files > 1) {
                                    $download_caption = sprintf(app_lang('download_files'), $total_files);
                                }

                                echo "<i class='bi bi-download icon-16 download_icon'></i>";
                                echo anchor(
                                    get_uri("messages/download_message_files/" . $message_info->id),
                                    $download_caption,
                                    array(
                                        "class" => "download_btn",
                                        "title" => $download_caption
                                    )
                                );
                            }
                            ?>
                        </div>





                    </div>
                </div>
            </div>
        </div>
    </div>



    <?php
    //if there are more then 5 messages, we'll show load more option.

    if ($found_rows > 5) {
    ?>
        <div id="load-messages" class="b-b">
            <?php
            echo js_anchor(app_lang("load_more"), array("class" => "btn btn-default w-100 mt15 spinning-btn", "title" => app_lang("load_more"), "id" => "load-more-messages-link"));
            ?>
        </div>
        <div id="load-more-messages-container"></div>
    <?php
    }




    foreach ($replies as $reply_info) {
    ?>
        <?php echo view("messages/reply_row", array("reply_info" => $reply_info)); ?>
    <?php } ?>

    <div id="reply-form-container">
        <div id="reply-form-dropzone" class="post-dropzone">
            <?php echo form_open(get_uri("messages/reply"), array("id" => "message-reply-form", "class" => "general-form", "role" => "form")); ?>
            <div class="p15 box b-b">
                <div class="box-content avatar avatar-md pr15 d-table-cell">
                    <img src="<?php echo get_avatar($login_user->image); ?>" alt="..." />
                </div>

                <!-- Reply Box -->
                <div class="reply_box " id="replyBox">
                    <div class="reply_header">
                        <span>Reply to: <span><?php
                                                if ($message_info->user_type == "client") {
                                                    echo get_client_contact_profile_link($message_user_id, $message_info->user_name, array("class" => "dark strong"));
                                                } else {
                                                    echo get_team_member_profile_link($message_user_id, $message_info->user_name, array("class" => "dark strong"));
                                                }
                                                ?></span>
                        </span>
                        <button type="button" class="close_reply" onclick="closeReply()">✕</button>
                    </div>

                    <!-- message id (HIDDEN FIELD) -->
                    <input type="hidden" name="message_id" value="<?php echo $message_info->id; ?>">

                    <?php
                    echo form_textarea(array(
                        "id" => "reply_message",
                        "name" => "reply_message",
                        "class" => "reply_input",
                        "placeholder" => app_lang('write_a_reply'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                        "data-rich-text-editor" => true,
                        "style" => "height: 6rem;"
                    ));
                    ?>

                    <?php echo view("includes/dropzone_preview"); ?>


                    <div class="reply_actions common-flex">
                        <button class="send_btn btn btn-primary float-end btn-sm " type="submit"><?php echo app_lang("send"); ?></button>
                        <button type="button" class="discard_btn" onclick="closeReply()">Discard</button>

                        <!-- <div class="text_formating"><svg xmlns="http://www.w3.org/2000/svg" width="20"
                                height="20" fill="currentColor" class="bi bi-alphabet-uppercase"
                                viewBox="0 0 16 16">
                                <path
                                    d="M1.226 10.88H0l2.056-6.26h1.42l2.047 6.26h-1.29l-.48-1.61H1.707l-.48 1.61ZM2.76 5.818h-.054l-.75 2.532H3.51zm3.217 5.062V4.62h2.56c1.09 0 1.808.582 1.808 1.54 0 .762-.444 1.22-1.05 1.372v.055c.736.074 1.365.587 1.365 1.528 0 1.119-.89 1.766-2.133 1.766zM7.18 5.55v1.675h.8c.812 0 1.171-.308 1.171-.853 0-.51-.328-.822-.898-.822zm0 2.537V9.95h.903c.951 0 1.342-.312 1.342-.909 0-.591-.382-.954-1.095-.954zm5.089-.711v.775c0 1.156.49 1.803 1.347 1.803.705 0 1.163-.454 1.212-1.096H16v.12C15.942 10.173 14.95 11 13.607 11c-1.648 0-2.573-1.073-2.573-2.849v-.78c0-1.775.934-2.871 2.573-2.871 1.347 0 2.34.849 2.393 2.087v.115h-1.172c-.05-.665-.516-1.156-1.212-1.156-.849 0-1.347.67-1.347 1.83" />
                            </svg>
                        </div> -->
                        <button type="button"
                            class="attachment_btn upload-file-button btn btn-default btn-sm"
                            style="background: none; border: none; padding: 0; color:#7988a2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-paperclip" viewBox="0 0 16 16">
                                <path
                                    d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z" />
                            </svg>
                        </button>

                        <div class="reactions">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-emoji-smile" viewBox="0 0 16 16">
                                <path
                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                <path
                                    d="M4.285 9.567a.5.5 0 0 1 .683.183A3.5 3.5 0 0 0 8 11.5a3.5 3.5 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5" />
                            </svg>
                        </div>
                        <button type="button"
                            class="image reply-image-only-btn"
                            title="Attach image"
                            onclick="openReplyImagePicker(event)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-image" viewBox="0 0 16 16">
                                <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                                <path
                                    d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1z" />
                            </svg>
                        </button>

                        <input type="file"
                            id="replyImageOnlyInput"
                            accept="image/*"
                            multiple
                            hidden
                            onchange="handleReplyImageOnlyUpload(this)">

                    </div>
                </div>

                <div class="bottom_btns  common-flex">
                    <div class="reply_btn2 common-flex" onclick="openReply(event)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-arrow-90deg-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M1.146 4.854a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H12.5A2.5 2.5 0 0 1 15 6.5v8a.5.5 0 0 1-1 0v-8A1.5 1.5 0 0 0 12.5 5H2.707l3.147 3.146a.5.5 0 1 1-.708.708z" />
                        </svg>
                        <p>Reply</p>
                    </div>

                    <div class="forward_btn common-flex"
                        data-forward-url="<?php echo get_uri("messages/modal_form/0") . "?forward_message_id=" . $message_info->id; ?>"
                        onclick="openForward(event)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-arrow-90deg-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M14.854 4.854a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 4H3.5A2.5 2.5 0 0 0 1 6.5v8a.5.5 0 0 0 1 0v-8A1.5 1.5 0 0 1 3.5 5h9.793l-3.147 3.146a.5.5 0 0 0 .708.708z" />
                        </svg>
                        <p>Forward</p>
                    </div>

                    <div class="reation_btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-emoji-smile" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path
                                d="M4.285 9.567a.5.5 0 0 1 .683.183A3.5 3.5 0 0 0 8 11.5a3.5 3.5 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5" />
                        </svg>
                    </div>




                </div>

                
            </div>
            <?php echo form_close(); ?>
            <div id="forward-message-box" class="reply_box forward_box" style="display:none;">
                    <?php echo form_open(get_uri("messages/forward_message"), array("id" => "message-forward-form", "class" => "general-form", "role" => "form")); ?>

                    <div class="reply_header">
                        <span>Forward message</span>
                        <button type="button" class="close_reply" onclick="closeForward()">✕</button>
                    </div>

                    <input type="hidden" name="forward_message_id" value="<?php echo $message_info->id; ?>" />

                    <div class="forward_to_row">
                        <label>To</label>
                        <?php
                        echo form_dropdown(
                            "to_user_id",
                            isset($forward_users_dropdown) ? $forward_users_dropdown : array("" => "- Select receiver -"),
                            "",
                            "class='select2 validate-hidden form-control' id='forward_to_user_id' data-rule-required='true' data-msg-required='" . app_lang('field_required') . "'"
                        );
                        ?>
                    </div>

                    <div class="reply_actions common-flex">
                        <button type="submit" class="send_btn btn btn-primary btn-sm">Forward</button>
                        <button type="button" class="discard_btn" onclick="closeForward()">Discard</button>
                    </div>

                    <?php echo form_close(); ?>
                </div>
        </div>
    </div>










    <script type="text/javascript">
        $(document).ready(function() {
            var uploadUrl = "<?php echo get_uri("messages/upload_file"); ?>";
            var validationUrl = "<?php echo get_uri("messages/validate_message_file"); ?>";

            var dropzone = attachDropzoneWithForm("#reply-form-dropzone", uploadUrl, validationUrl);
            window.replyDropzone = dropzone;

            $("#message-reply-form").appForm({
                isModal: false,
                onSuccess: function(result) {
                    $("#reply_message").val("");
                    $(result.data).insertBefore("#reply-form-container");
                    appAlert.success(result.message, {
                        duration: 10000
                    });
                    if (dropzone) {
                        dropzone.removeAllFiles();
                    }
                }
            });

            $("#load-more-messages-link").click(function() {
                loadMoreMessages();
            });

            $("#message-forward-form").appForm({
                isModal: false,
                onSuccess: function(result) {
                    if (result.success) {
                        appAlert.success(result.message, {
                            duration: 6000
                        });

                        closeForward();
                    } else {
                        appAlert.error(result.message);
                    }
                }
            });

        });

        function loadMoreMessages(callback) {
            $("#load-more-messages-link").addClass("spinning");
            var $topMessageDiv = $(".js-message-reply").first();

            $.ajax({
                url: "<?php echo get_uri('messages/view_messages'); ?>",
                type: "POST",
                data: {
                    message_id: "<?php echo $message_info->id; ?>",
                    top_message_id: $topMessageDiv.attr("data-message_id")
                },
                success: function(response) {
                    if (response) {
                        $("#load-more-messages-container").prepend(response);
                        $("#load-more-messages-link").removeClass("spinning");

                        if (callback) {
                            callback(); //has more data?
                        }
                    } else {
                        $("#load-more-messages-link").remove(); //no more messages left
                    }

                }
            });
        }
    </script>


    <script>
        function togglePreview(fileUrl, fileName) {
            var displayedImage = document.getElementById('displayedImage');

            // Check if the displayedImage is currently showing the same fileUrl
            if (displayedImage.src === fileUrl) {
                displayedImage.style.display = 'none'; // Hide the displayed image
            } else {
                displayedImage.src = fileUrl; // Set the source of the displayed image
                displayedImage.alt = fileName; // Set the alt attribute of the displayed image
                displayedImage.style.display = 'block'; // Show the displayed image
            }
        }
    </script>
    <script>
        function openReplyImagePicker(event) {
            event.preventDefault();
            event.stopPropagation();

            var input = document.getElementById("replyImageOnlyInput");

            if (input) {
                input.click();
            }
        }

        function handleReplyImageOnlyUpload(input) {
            var files = Array.prototype.slice.call(input.files || []);

            if (!files.length) {
                return;
            }

            files.forEach(function(file) {
                if (!file.type || !file.type.match(/^image\//)) {
                    if (window.appAlert) {
                        appAlert.error("Only image files are allowed.");
                    } else {
                        alert("Only image files are allowed.");
                    }
                    return;
                }

                if (window.replyDropzone) {
                    window.replyDropzone.addFile(file);
                } else {
                    if (window.appAlert) {
                        appAlert.error("Reply attachment uploader is not ready.");
                    } else {
                        alert("Reply attachment uploader is not ready.");
                    }
                }
            });

            input.value = "";
        }
    </script>

    <script>
        currentMessageId = <?= (int)$message_info->id ?>;



        // ------------reply function

        function openReply(event) {
            event.stopPropagation();

            const box = document.getElementById('replyBox');
            const bottomBtns = document.querySelector('.bottom_btns');

            box.style.display = 'block';
            bottomBtns.style.display = 'none'; //  HIDE buttons

            box.querySelector('.reply_input').focus();
        }

        function openForward(event) {
            if (event) event.stopPropagation();

            const replyBox = document.getElementById("replyBox");
            const forwardBox = document.getElementById("forward-message-box");
            const bottomBtns = document.querySelector(".bottom_btns");

            if (replyBox) replyBox.style.display = "none";
            if (bottomBtns) bottomBtns.style.display = "none";
            if (forwardBox) forwardBox.style.display = "block";

            const select = document.getElementById("forward_to_user_id");
            if (select) {
                setTimeout(function() {
                    if ($(select).hasClass("select2-hidden-accessible")) {
                        $(select).select2("open");
                    } else {
                        select.focus();
                    }
                }, 150);
            }
        }

        function closeForward(event) {
            if (event) event.preventDefault();

            const forwardBox = document.getElementById("forward-message-box");
            const bottomBtns = document.querySelector(".bottom_btns");

            if (forwardBox) forwardBox.style.display = "none";
            if (bottomBtns) bottomBtns.style.display = "flex";
        }


        function closeReply(event) {
            if (event) event.preventDefault();

            const box = document.getElementById('replyBox');
            const bottomBtns = document.querySelector('.bottom_btns');

            box.style.display = 'none';
            bottomBtns.style.display = 'flex';
        }
    </script>



</div>