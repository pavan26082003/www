<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .download_icon{
        color: #1a73e8;
        margin-right: 10px;
    }
    .download_btn {
        background: #1a73e8;
        color: #fff;
        border: none;
        border-radius: 16px;
        padding: 6px 12px;
        font-size: 12px;
        cursor: pointer;
    }
    .download_btn:hover{
         background: #1a73e8de;
         color: #fff;
    }
</style>
<div class="b-b p15 m0 bg-white js-message-reply" data-message_id="<?php echo $reply_info->id; ?>" href="#reply-<?php echo $reply_info->id; ?>" >
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <span class="avatar avatar-sm">
                        <img src="<?php echo get_avatar($reply_info->user_image); ?>" alt="..." />
                    </span>
                </div>
                <div class="w-100 ps-2">
                    <div>
                        <strong><?php
                            if ($reply_info->from_user_id === $login_user->id) {
                                echo app_lang("me");
                            } else {
                                if ($reply_info->user_type == "client") {
                                    echo get_client_contact_profile_link($reply_info->from_user_id, $reply_info->user_name, array("class" => "dark strong"));
                                } else {
                                    echo get_team_member_profile_link($reply_info->from_user_id, $reply_info->user_name, array("class" => "dark strong"));
                                }
                            }
                            ?>
                        </strong>
                        <span class="text-off float-end"><?php echo format_to_relative_time($reply_info->created_at); ?></span>
                    </div>
                    <p><?php echo nl2br(link_it($reply_info->message)); ?></p>

                    <div class="comment-image-box clearfix">
                        <?php
                        $files = unserialize($reply_info->files);
                        $total_files = count($files);

                        if ($total_files) {
                            echo view("includes/timeline_preview", array("files" => $files));

                            $download_caption = app_lang('download');
                            if ($total_files > 1) {
                                $download_caption = sprintf(app_lang('download_files'), $total_files);
                            }
                            echo "<i class='bi bi-download icon-16 download_icon'></i>";
                            echo anchor(get_uri("messages/download_message_files/" . $reply_info->id), $download_caption, array("class" => "download_btn", "title" => $download_caption));
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
