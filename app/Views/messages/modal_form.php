<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .discard_btn:focus,
    .discard_btn:active {
        outline: none;
        box-shadow: none;
    }

    .send_btn:hover {
        background: #0b57d0d5;
    }

    .attachment-bar {
        padding: 8px 12px;
        margin-bottom: 8px;
        background: #f1f3f4;
        border-radius: 8px;
        /* display: flex;
        align-items: center; */
        max-width: 420px;
    }

    .attachment {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #202124;
        width: 100%;
    }

    .attachment-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: #e8eaed;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 11px;
        color: #444;
    }

    .attachment-name {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .attachment-remove {
        cursor: pointer;
        font-size: 16px;
        color: #5f6368;
    }

    .attachment-remove:hover {
        color: #d93025;
    }

    .attachment-thumb {
        width: 40px;
        height: 40px;
        border-radius: 6px;
        overflow: hidden;
        background: #e8eaed;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .attachment-thumb img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .attachment-file {
        font-size: 12px;
        font-weight: 600;
    }

    .attachment-popup {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #202124;
        color: #fff;
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.18);
        z-index: 999999;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
        max-width: 340px;
    }

    .attachment-popup.show {
        opacity: 1;
        transform: translateY(0);
    }


    .compose-tool-btn {
        background: none;
        border: none;
        padding: 0;
        color: #7988a2;
        cursor: pointer;
    }

    .reply-popover {
        position: fixed;
        z-index: 999999;
        display: none;
        gap: 6px;
        padding: 8px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, .18);
    }

    .reply-popover button {
        border: 0;
        background: #f8fafc;
        border-radius: 8px;
        padding: 6px 10px;
        cursor: pointer;
    }

    .compose-format-popover,
    .compose-emoji-popover {
        align-items: center;
        flex-wrap: wrap;
        max-width: 280px;
    }

    .compose-popover-divider {
        width: 1px;
        height: 24px;
        background: #e5e7eb;
        margin: 0 4px;
    }

    .compose-format-popover button,
    .compose-emoji-popover button {
        min-width: 34px;
        height: 32px;
        font-size: 13px;
        font-weight: 700;
    }

    .compose-emoji-popover button {
        font-size: 17px;
    }
</style>

<div id="new-message-dropzone" class="post-dropzone">

    <?php echo form_open_multipart(get_uri("messages/send_message"), array("id" => "message-form", "class" => "general-form", "role" => "form")); ?>

    <div class="modal-body clearfix">
        <div class="container-fluid">
            <div class="form-group">
                <div class="row">
                    <label for="to_user_id" class=" col-md-2"><?php echo app_lang('to'); ?></label>
                    <div class="col-md-10" style="font-weight:600;color:#000;">
                        <?php
                        if (isset($message_user_info)) {
                            $image_url = get_avatar($message_user_info->image);
                            echo "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span>" . $message_user_info->first_name . " " . $message_user_info->last_name;
                        ?>
                            <input type="hidden" name="to_user_id" value="<?php echo $message_user_info->id; ?>" />
                        <?php
                        } else {
                            echo form_dropdown("to_user_id", $users_dropdown, array(), "class='select2 validate-hidden' id='to_user_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="subject" class=" col-md-2"><?php echo app_lang('subject'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "subject",
                            "name" => "subject",
                            "class" => "form-control",
                            "placeholder" => app_lang('subject'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <?php
                    echo form_textarea(array(
                        "id" => "message",
                        "name" => "message",
                        "class" => "form-control",
                        "placeholder" => app_lang('write_a_message'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                        "style" => "min-height:200px;padding: 20px 16px 16px 26px;"
                    ));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <?php echo view("includes/dropzone_preview"); ?>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <!-- File Upload Button -->
        <div class="form-group">
            <label class=" hidden btn btn-default upload-file-button float-start me-auto btn-sm round" for="upload">
            </label>
            <!-- Attachment preview -->
            <div id="attachment-preview" class="attachment-bar hidden"></div>


            <div class="reply_actions common-flex">
                <button class="send_btn" type="submit"><?php echo app_lang('send'); ?></button>
                <button
                    type="button"
                    class="discard_btn"
                    data-bs-dismiss="modal">
                    <span data-feather="x" class="icon-16"></span>
                    <?php echo app_lang('close'); ?>
                </button>


                <!-- <button type="button" class="text_formating compose-tool-btn" id="composeFormatBtn" title="Formatting" onclick="toggleComposeFormatPopover(event)"><svg xmlns="http://www.w3.org/2000/svg" width="20"
                        height="20" fill="currentColor" class="bi bi-alphabet-uppercase"
                        viewBox="0 0 16 16">
                        <path
                            d="M1.226 10.88H0l2.056-6.26h1.42l2.047 6.26h-1.29l-.48-1.61H1.707l-.48 1.61ZM2.76 5.818h-.054l-.75 2.532H3.51zm3.217 5.062V4.62h2.56c1.09 0 1.808.582 1.808 1.54 0 .762-.444 1.22-1.05 1.372v.055c.736.074 1.365.587 1.365 1.528 0 1.119-.89 1.766-2.133 1.766zM7.18 5.55v1.675h.8c.812 0 1.171-.308 1.171-.853 0-.51-.328-.822-.898-.822zm0 2.537V9.95h.903c.951 0 1.342-.312 1.342-.909 0-.591-.382-.954-1.095-.954zm5.089-.711v.775c0 1.156.49 1.803 1.347 1.803.705 0 1.163-.454 1.212-1.096H16v.12C15.942 10.173 14.95 11 13.607 11c-1.648 0-2.573-1.073-2.573-2.849v-.78c0-1.775.934-2.871 2.573-2.871 1.347 0 2.34.849 2.393 2.087v.115h-1.172c-.05-.665-.516-1.156-1.212-1.156-.849 0-1.347.67-1.347 1.83" />
                    </svg>
                </button> -->
                <div class="attachment_btn">
                    <!-- hidden file input -->
                    <input type="file"
                        id="upload"
                        name="upload[]"
                        multiple
                        accept=".png,.jpg,.jpeg,.gif,.bmp,.tiff,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar,.mp3,.wav,.mp4,.mov,.avi"
                        onchange="handleAttachment(this)"
                        hidden>

                    <!-- label acts as button -->
                    <label for="upload" class="attach_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="currentColor" class="bi bi-paperclip" viewBox="0 0 16 16">
                            <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z" />
                        </svg>
                    </label>
                </div>




                <button type="button" class="reactions compose-tool-btn" id="composeEmojiBtn" title="Emoji" onclick="toggleComposeEmojiPopover(event)">

                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" class="bi bi-emoji-smile" viewBox="0 0 16 16">
                        <path
                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                        <path
                            d="M4.285 9.567a.5.5 0 0 1 .683.183A3.5 3.5 0 0 0 8 11.5a3.5 3.5 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5" />
                    </svg>
                </button>
                <button type="button" class="image compose-tool-btn" id="composeImageBtn" title="Attach image" onclick="openComposeImagePicker(event)">

                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" class="bi bi-image" viewBox="0 0 16 16">
                        <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                        <path
                            d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1z" />
                    </svg>
                </button>
                <input type="file" id="composeImageInput" accept="image/*" multiple hidden onchange="handleComposeImageUpload(this)">

            </div>

        </div>

    </div>
    <?php echo form_close(); ?>

    <div id="composeFormatPopover" class="reply-popover compose-format-popover">
        <button type="button" onclick="applyComposeFormat('bold')" title="Bold"><b>B</b></button>
        <button type="button" onclick="applyComposeFormat('italic')" title="Italic"><i>I</i></button>
        <button type="button" onclick="applyComposeFormat('underline')" title="Underline"><u>U</u></button>

        <span class="compose-popover-divider"></span>

        <button type="button" onclick="applyComposeFormat('upper')" title="Capital">AA</button>
        <button type="button" onclick="applyComposeFormat('lower')" title="Small">aa</button>

        <span class="compose-popover-divider"></span>

        <button type="button" onclick="applyComposeFormat('left')" title="Left">L</button>
        <button type="button" onclick="applyComposeFormat('center')" title="Center">C</button>
        <button type="button" onclick="applyComposeFormat('right')" title="Right">R</button>
    </div>

    <div id="composeEmojiPopover" class="reply-popover compose-emoji-popover">
        <button type="button" onclick="insertComposeEmoji('😊')">😊</button>
        <button type="button" onclick="insertComposeEmoji('😂')">😂</button>
        <button type="button" onclick="insertComposeEmoji('👍')">👍</button>
        <button type="button" onclick="insertComposeEmoji('🙏')">🙏</button>
        <button type="button" onclick="insertComposeEmoji('❤️')">❤️</button>
        <button type="button" onclick="insertComposeEmoji('🔥')">🔥</button>
        <button type="button" onclick="insertComposeEmoji('🎉')">🎉</button>
        <button type="button" onclick="insertComposeEmoji('✅')">✅</button>
        <button type="button" onclick="insertComposeEmoji('⭐')">⭐</button>
        <button type="button" onclick="insertComposeEmoji('📎')">📎</button>
        <button type="button" onclick="insertComposeEmoji('📌')">📌</button>
        <button type="button" onclick="insertComposeEmoji('💡')">💡</button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var uploadUrl = "<?php echo get_uri("messages/upload_file"); ?>";
        var validationUrl = "<?php echo get_uri("messages/validate_message_file"); ?>";

        var dropzone = attachDropzoneWithForm("#new-message-dropzone", uploadUrl, validationUrl);

        $("#message-form").appForm({
            onSuccess: function(result) {
                appAlert.success(result.message, {
                    duration: 10000
                });

                if ($("#message-table").length && $.fn.DataTable.isDataTable("#message-table")) {
                    $("#message-table").DataTable().ajax.reload(null, false);
                }

                if ($("#js-single-user-chat-list").is(":visible") && typeof window.triggerActiveChat !== "undefined") {
                    setTimeout(function() {
                        window.triggerActiveChat(result.id);
                    }, 1000);
                }
            }
        });

        $("#message-form .select2").select2();
    });
</script>

<script>
    function showImagePreview(input) {
        var preview = document.getElementById('upload-preview');
        var file = input.files[0];
        var reader = new FileReader();

        reader.onloadend = function() {
            preview.src = reader.result;
            preview.style.display = 'block';
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    }
</script>


<script>
    function hideComposePopovers() {
        $("#composeFormatPopover, #composeEmojiPopover").hide();
    }

    function showComposePopover(id, event) {
        event.preventDefault();
        event.stopPropagation();

        var rect = event.currentTarget.getBoundingClientRect();
        var $popover = $("#" + id);

        hideComposePopovers();

        $popover.css({
            left: rect.left + "px",
            top: (rect.top - 55) + "px",
            display: "flex"
        });
    }

    function toggleComposeFormatPopover(event) {
        showComposePopover("composeFormatPopover", event);
    }

    function toggleComposeEmojiPopover(event) {
        showComposePopover("composeEmojiPopover", event);
    }


    function getComposeTextareaSelection() {
        var field = document.getElementById("message");

        if (!field) {
            return null;
        }

        field.focus();

        return {
            field: field,
            start: field.selectionStart || 0,
            end: field.selectionEnd || 0,
            value: field.value || ""
        };
    }

    function replaceComposeSelection(newText, selectInsertedText) {
        var data = getComposeTextareaSelection();

        if (!data) {
            return;
        }

        data.field.value =
            data.value.substring(0, data.start) +
            newText +
            data.value.substring(data.end);

        if (selectInsertedText) {
            data.field.selectionStart = data.start;
            data.field.selectionEnd = data.start + newText.length;
        } else {
            data.field.selectionStart = data.field.selectionEnd = data.start + newText.length;
        }

        data.field.focus();
    }

    function applyComposeFormat(type) {
        var data = getComposeTextareaSelection();

        if (!data) {
            hideComposePopovers();
            return;
        }

        var selectedText = data.value.substring(data.start, data.end);

        if (!selectedText) {
            selectedText = "text";
        }

        var output = selectedText;

        if (type === "bold") {
            output = "<b>" + selectedText + "</b>";
        }

        if (type === "italic") {
            output = "<i>" + selectedText + "</i>";
        }

        if (type === "underline") {
            output = "<u>" + selectedText + "</u>";
        }

        if (type === "upper") {
            output = selectedText.toUpperCase();
        }

        if (type === "lower") {
            output = selectedText.toLowerCase();
        }

        if (type === "left") {
            output = '<div style="text-align:left;">' + selectedText + "</div>";
        }

        if (type === "center") {
            output = '<div style="text-align:center;">' + selectedText + "</div>";
        }

        if (type === "right") {
            output = '<div style="text-align:right;">' + selectedText + "</div>";
        }

        replaceComposeSelection(output, true);
        hideComposePopovers();
    }

    function insertComposeEmoji(emoji) {
        replaceComposeSelection(emoji, false);
        hideComposePopovers();
    }


    function openComposeImagePicker(event) {
        event.preventDefault();
        event.stopPropagation();

        document.getElementById("composeImageInput").click();
    }

    function handleComposeImageUpload(input) {
        var files = Array.prototype.slice.call(input.files || []);

        files.forEach(function(file) {
            if (!file.type || !file.type.match(/^image\//)) {
                showAttachmentPopup("Only image files are allowed.");
                return;
            }

            var attachmentInput = document.getElementById("upload");

            if (!attachmentInput) {
                showAttachmentPopup("Attachment input not found.");
                return;
            }

            var dataTransfer = new DataTransfer();

            Array.prototype.slice.call(attachmentInput.files || []).forEach(function(existingFile) {
                dataTransfer.items.add(existingFile);
            });

            dataTransfer.items.add(file);
            attachmentInput.files = dataTransfer.files;

            handleAttachment(attachmentInput);
        });

        input.value = "";
    }

    $(document).on("click", function() {
        hideComposePopovers();
    });
</script>


<script>
    function handleAttachment(input) {
        const preview = document.getElementById("attachment-preview");
        preview.classList.remove("hidden");

        const files = Array.from(input.files);
        if (!files.length) return;

        const maxFiles = 10;
        const maxFileSize = 25 * 1024 * 1024;

        if (input.files.length > maxFiles) {
            showAttachmentPopup("Maximum 10 attachments are allowed.");
            input.value = "";
            return;
        }

        for (const file of files) {
            if (file.size > maxFileSize) {
                showAttachmentPopup(file.name + " is too large. Maximum file size is 25 MB.");
                input.value = "";
                return;
            }
        }

        files.forEach(file => {
            const wrapper = document.createElement("div");
            wrapper.className = "attachment";

            const thumb = document.createElement("div");
            thumb.className = "attachment-thumb";

            // IMAGE PREVIEW
            if (file.type.startsWith("image/")) {
                const img = document.createElement("img");
                const reader = new FileReader();
                reader.onload = e => img.src = e.target.result;
                reader.readAsDataURL(file);
                thumb.appendChild(img);
            } else {
                // FILE ICON
                const icon = document.createElement("i");
                icon.className = `bi ${getBootstrapFileIcon(file)}`;
                icon.style.fontSize = "20px";
                thumb.appendChild(icon);
            }

            const info = document.createElement("div");
            info.className = "attachment-name";
            info.innerText = `${file.name} (${formatFileSize(file.size)})`;

            const remove = document.createElement("div");
            remove.className = "attachment-remove";
            remove.innerHTML = "&times;";
            remove.onclick = (e) => {
                e.preventDefault();

                wrapper.remove();

                if (!preview.querySelector(".attachment")) {
                    preview.classList.add("hidden");
                    preview.innerHTML = "";
                }
            };

            wrapper.append(thumb, info, remove);
            preview.appendChild(wrapper);
        });
    }


    function showAttachmentPopup(message) {
        const oldPopup = document.querySelector(".attachment-popup");

        if (oldPopup) {
            oldPopup.remove();
        }

        const popup = document.createElement("div");
        popup.className = "attachment-popup";
        popup.innerText = message;

        document.body.appendChild(popup);

        setTimeout(() => {
            popup.classList.add("show");
        }, 10);

        setTimeout(() => {
            popup.classList.remove("show");

            setTimeout(() => {
                popup.remove();
            }, 300);

        }, 3000);
    }



    function formatFileSize(bytes) {
        if (bytes < 1024 * 1024) {
            return (bytes / 1024).toFixed(0) + " KB";
        } else if (bytes < 1024 * 1024 * 1024) {
            return (bytes / (1024 * 1024)).toFixed(1) + " MB";
        } else {
            return (bytes / (1024 * 1024 * 1024)).toFixed(2) + " GB";
        }
    }

    function getBootstrapFileIcon(file) {
        const ext = file.name.split('.').pop().toLowerCase();

        const map = {
            pdf: 'bi-file-earmark-pdf',
            doc: 'bi-file-earmark-word',
            docx: 'bi-file-earmark-word',
            xls: 'bi-file-earmark-excel',
            xlsx: 'bi-file-earmark-excel',
            ppt: 'bi-file-earmark-ppt',
            pptx: 'bi-file-earmark-ppt',
            txt: 'bi-file-earmark-text',
            csv: 'bi-file-earmark-spreadsheet',
            zip: 'bi-file-earmark-zip',
            rar: 'bi-file-earmark-zip',
            mp3: 'bi-file-earmark-music',
            wav: 'bi-file-earmark-music',
            mp4: 'bi-file-earmark-play',
            mov: 'bi-file-earmark-play',
            avi: 'bi-file-earmark-play'
        };

        return map[ext] || 'bi-file-earmark';
    }
</script>