<?php

namespace App\Controllers;

class Messages extends Security_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->init_permission_checker("message_permission");
    }

    private function is_my_message($message_info)
    {
        if ($message_info->from_user_id == $this->login_user->id || $message_info->to_user_id == $this->login_user->id) {
            return true;
        }
    }

    private function check_message_user_permission()
    {
        if (!$this->check_access_on_messages_for_this_user()) {
            app_redirect("forbidden");
        }
    }

    private function check_validate_sending_message($to_user_id)
    {
        if (!$this->validate_sending_message($to_user_id)) {
            echo json_encode(array("success" => false, 'message' => app_lang("message_sending_error_message")));
            exit;
        }
    }

    private function get_forward_users_dropdown()
    {
        $users_dropdown = array("" => "- Select receiver -");

        $users = $this->Messages_model->get_users_for_messaging($this->get_user_options_for_query())->getResult();

        foreach ($users as $user) {
            if ((int) $user->id === (int) $this->login_user->id) {
                continue;
            }

            $user_name = $user->email;

            if ($user_name === "admin@efactor.io") {
                if (!$this->login_user->is_admin) {
                    $users_dropdown[$user->id] = $user_name;
                }
            } else {
                if ($user->user_type === "client" && $user->company_name) {
                    if ($this->login_user->user_type == "User") {
                        $user_name .= " - " . app_lang("client") . ": " . $user->company_name;
                    } else {
                        $user_name = app_lang("contact") . ": " . $user_name;
                    }
                }

                if ($this->login_user->is_admin) {
                    $users_dropdown[$user->id] = $user_name;
                }
            }
        }

        return $users_dropdown;
    }

    function index()
    {
        $this->check_message_user_permission();
        app_redirect("messages/inbox");
    }

    /* show new message modal */

    function modal_form($user_id = 0)
    {
        $this->check_message_user_permission();





        if ($user_id) {
            $view_data['message_user_info'] = $this->Users_model->get_one($user_id);
        } else {
            $users = $this->Messages_model->get_users_for_messaging($this->get_user_options_for_query())->getResult();

            foreach ($users as $user) {
                $user_name = $user->email;

                if ($user_name === "admin@efactor.io") {

                    if (!$this->login_user->is_admin) {
                        $view_data['users_dropdown'][$user->id] = $user_name;
                    }
                } else {
                    // Handle other users
                    if ($user->user_type === "client" && $user->company_name) {
                        if ($this->login_user->user_type == "User") {
                            $user_name .= " - " . app_lang("client") . ": " . $user->company_name;
                        } else {
                            $user_name = app_lang("contact") . ": " . $user_name;
                        }
                    }

                    if ($this->login_user->is_admin) {
                        $view_data['users_dropdown'][$user->id] = $user_name;
                    }
                }
            }
        }

        return $this->template->view('messages/modal_form', $view_data);
    }
    // function inbox($auto_select_index = "")
    // {
    //     $this->check_message_user_permission();
    //     $this->check_module_availability("module_message");

    //     $view_data['mode'] = "inbox";
    //     $view_data['auto_select_index'] = $auto_select_index;
    //     return $this->template->rander("messages/index", $view_data);
    // }

    function inbox($auto_select_index = "")
    {
        $this->check_message_user_permission();
        $this->check_module_availability("module_message");

        $requested_mode = $this->request->getGet("mode");

        $view_data['mode'] = ($requested_mode === "all") ? "all" : "inbox";
        $view_data['auto_select_index'] = $auto_select_index;

        return $this->template->rander("messages/index", $view_data);
    }

    /* show sent items */

    function sent_items($auto_select_index = "")
    {
        $this->check_message_user_permission();
        $this->check_module_availability("module_message");

        $view_data['mode'] = "sent_items";
        $view_data['auto_select_index'] = $auto_select_index;
        return $this->template->rander("messages/index", $view_data);
    }

    // function get_unread_count()
    // {
    //     $this->check_message_user_permission();

    //     $db = db_connect();

    //     $count = $db->table('messages')
    //         ->where('to_user_id', $this->login_user->id)
    //         ->where('status', 'unread')
    //         ->countAllResults();

    //     echo json_encode(array(
    //         "success" => true,
    //         "unread_count" => $count
    //     ));
    // }

    private function get_allowed_user_ids()
    {
        $users = $this->Messages_model->get_users_for_messaging($this->get_user_options_for_query())->getResult();
        $users = json_decode(json_encode($users), true); //convert to array
        return implode(',', array_column($users, "id"));
    }

    /* list of messages, prepared for datatable  */

    // function list_data($mode = "inbox")
    // {
    //     $this->check_message_user_permission();
    //     if ($mode !== "inbox") {
    //         $mode = "sent_items";
    //     }

    //     $options = array("user_id" => $this->login_user->id, "mode" => $mode, "user_ids" => $this->get_allowed_user_ids());
    //     $list_data = $this->Messages_model->get_list($options)->getResult();

    //     $result = array();

    //     foreach ($list_data as $data) {

    //         $data->file_names = array();

    //         if (!empty($data->files)) {

    //             $unserialized = @unserialize($data->files);

    //             if ($unserialized !== false && is_array($unserialized)) {

    //                 $data->file_names = $unserialized;
    //             } else {

    //                 $data->file_names = array($data->files);
    //             }
    //         }
    //         $result[] = $this->_make_row($data, $mode);
    //     }

    //     echo json_encode(array("data" => $result));
    // }

    function list_data($mode = "inbox")
    {
        $this->check_message_user_permission();

        if (!in_array($mode, array("inbox", "sent_items", "all"))) {
            $mode = "inbox";
        }

        $options = array(
            "user_id" => $this->login_user->id,
            "mode" => $mode,
            "user_ids" => $this->get_allowed_user_ids()
        );

        $list_data = $this->Messages_model->get_list($options)->getResult();

        $result = array();

        foreach ($list_data as $data) {

            $data->file_names = array();

            if (!empty($data->files)) {
                $unserialized = @unserialize($data->files);

                if ($unserialized !== false && is_array($unserialized)) {
                    $data->file_names = $unserialized;
                } else {
                    $data->file_names = array($data->files);
                }
            }

            $result[] = $this->_make_row($data, $mode);
        }

        echo json_encode(array("data" => $result));
    }

    /* return a message details */

    function view($message_id = 0, $mode = "", $files = "", $reply = 0)
    {
        $this->check_message_user_permission();

        $message_mode = $mode;
        if ($reply == 1 && $mode == "inbox") {
            $message_mode = "sent_items";
        } else if ($reply == 1 && $mode == "sent_items") {
            $message_mode = "inbox";
        }

        $options = array("id" => $message_id, "user_id" => $this->login_user->id, "mode" => $message_mode);
        $view_data["message_info"] = $this->Messages_model->get_details($options)->row;

        if (!$this->is_my_message($view_data["message_info"])) {
            app_redirect("forbidden");
        }

        //change message status to read
        $this->Messages_model->set_message_status_as_read($view_data["message_info"]->id, $this->login_user->id);

        $replies_options = array("message_id" => $message_id, "user_id" => $this->login_user->id, "limit" => 4);
        $messages = $this->Messages_model->get_details($replies_options);

        $view_data["replies"] = $messages->result;
        $view_data["found_rows"] = $messages->found_rows;

        $image_filename = $files;
        // $image_url = '/factor_17-05/files/brandLogo/' . $image_filename;
        $image_url = 'https://efactor.tech/files/timeline_files/' . $image_filename;

        //$image_url = 'https://efactor.tech/files/brand_logo/' . $image_filename;

        $view_data['image'] = $image_url;



        $view_data["mode"] = $mode;
        $view_data["is_reply"] = $reply;
        $view_data["forward_users_dropdown"] = $this->get_forward_users_dropdown();

        echo json_encode(array("success" => true, "data" => $this->template->view("messages/view", $view_data), "message_id" => $message_id));
    }
    /* prepare a row of message list table */

    // private function _make_row($data, $mode = "", $return_only_message = false, $online_status = false)
    // {
    //     $image_url = get_avatar($data->user_image);
    //     $created_at = format_to_relative_time($data->created_at);
    //     $message_id = $data->main_message_id;
    //     $files = $data->file_names;
    //     $label = "";
    //     $reply = "";
    //     $status = "";
    //     $attachment_icon = "";
    //     $subject = $data->subject;
    //     if ($mode == "inbox") {
    //         $status = $data->status;
    //     }

    //     if ($data->reply_subject) {
    //         $label = " <label class='badge bg-success d-inline-block'>" . app_lang('reply') . "</label>";
    //         $reply = "1";
    //         $subject = $data->reply_subject;
    //     }

    //     $attachment_icon = "";

    //     if (!empty($data->file_names) && is_array($data->file_names)) {

    //         $total_files = count(array_filter($data->file_names));

    //         if ($total_files > 0) {

    //             $attachment_icon = "<i data-feather='paperclip' class='icon-14 mr15'></i>";
    //         }
    //     }



    //     //prepare online status
    //     $online = "";
    //     if ($online_status && is_online_user($data->last_online)) {
    //         $online = "<i class='online'></i>";
    //     }

    //     $message = "<div class='message-row $status' data-id='$message_id' data-index='$data->main_message_id' data-reply='$reply'><div class='d-flex'><div class='flex-shrink-0'>
    //                     <span class='avatar avatar-xs'>
    //                         <img src='$image_url' />
    //                             $online
    //                     </span>
    //                 </div>
    //                 <div class='w-100 ps-3'>
    //                     <div class='mb5'>
    //                         <strong> $data->user_name</strong>
    //                               <span class='text-off float-end time'>$attachment_icon $created_at</span>
    //                     </div>
    //                     $label $subject
    //                 </div></div></div>

    //             ";
    //     if ($return_only_message) {
    //         return $message;
    //     } else {
    //         return array(
    //             $message,
    //             $data->created_at,
    //             $status
    //         );
    //     }
    // }

    private function _make_row($data, $mode = "", $return_only_message = false, $online_status = false)
    {
        $image_url = get_avatar($data->user_image);
        $created_at = format_to_relative_time($data->created_at);
        $message_id = $data->main_message_id;

        $label = "";
        $reply = "";
        $status = "";
        $attachment_icon = "";
        $subject = $data->subject;

        /*
     * In All mail, each row must remember whether the latest message
     * belongs to inbox or sent. This prevents sent mails opening with inbox mode.
     */
        $row_mode = $mode;

        if ($mode === "all") {
            if (!empty($data->row_mode)) {
                $row_mode = $data->row_mode;
            } else {
                $row_mode = ((int) $data->from_user_id === (int) $this->login_user->id) ? "sent_items" : "inbox";
            }
        }

        if ($row_mode == "inbox") {
            $status = $data->status;
        }

        if ($data->reply_subject) {
            $label = " <label class='badge bg-success d-inline-block'>" . app_lang('reply') . "</label>";
            $reply = "1";
            $subject = $data->reply_subject;
        }

        if ($mode === "all") {
            if ($row_mode === "sent_items") {
                $label = " <label class='badge bg-primary d-inline-block'>To</label>" . $label;
            } else {
                $label = " <label class='badge bg-secondary d-inline-block'>From</label>" . $label;
            }
        }

        if (!empty($data->file_names) && is_array($data->file_names)) {
            $total_files = count(array_filter($data->file_names));

            if ($total_files > 0) {
                $attachment_icon = "<i data-feather='paperclip' class='icon-14 mr15'></i>";
            }
        }

        $online = "";
        if ($online_status && is_online_user($data->last_online)) {
            $online = "<i class='online'></i>";
        }

        $message = "<div class='message-row $status' data-id='$message_id' data-index='$data->main_message_id' data-reply='$reply' data-mode='$row_mode'>
                    <div class='d-flex'>
                        <div class='flex-shrink-0'>
                            <span class='avatar avatar-xs'>
                                <img src='$image_url' />
                                $online
                            </span>
                        </div>
                        <div class='w-100 ps-3'>
                            <div class='mb5'>
                                <strong> $data->user_name</strong>
                                <span class='text-off float-end time'>$attachment_icon $created_at</span>
                            </div>
                            $label $subject
                        </div>
                    </div>
                </div>";

        if ($return_only_message) {
            return $message;
        }

        return array(
            $message,
            $data->created_at,
            $status
        );
    }

    function send_message()
    {
        // $this->check_message_user_permission();

        $this->validate_submitted_data(array(
            "message" => "required",
            "to_user_id" => "required|numeric"
        ));

        $to_user_id = $this->request->getPost('to_user_id');

        $this->check_validate_sending_message($to_user_id);

        $uploadedFiles = array();


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_FILES["upload"]) && !empty($_FILES["upload"]["name"])) {

                $targetDirectory = rtrim(get_setting("timeline_file_path"), "/\\") . DIRECTORY_SEPARATOR;

                if (!is_dir($targetDirectory)) {
                    mkdir($targetDirectory, 0755, true);
                }

                // Multiple files: upload[]
                if (is_array($_FILES["upload"]["name"])) {

                    foreach ($_FILES["upload"]["name"] as $key => $name) {

                        if (empty($name)) {
                            continue;
                        }

                        if ($_FILES["upload"]["error"][$key] !== UPLOAD_ERR_OK) {
                            continue;
                        }

                        $originalFileName = basename($name);
                        $originalFileName = preg_replace('/[^A-Za-z0-9._-]/', '-', $originalFileName);

                        $uploadedFileName = "message_file" . uniqid() . "-" . $originalFileName;

                        $targetFile = $targetDirectory . $uploadedFileName;
                        $tempFile = $_FILES["upload"]["tmp_name"][$key];

                        if (move_uploaded_file($tempFile, $targetFile)) {
                            $uploadedFiles[] = array(
                                "file_name" => $uploadedFileName,
                                "file_size" => (string) filesize($targetFile),
                                "file_id" => null,
                                "service_type" => null
                            );
                        }
                    }
                } else {

                    // Fallback: single file upload
                    if ($_FILES["upload"]["error"] === UPLOAD_ERR_OK) {
                        $originalFileName = basename($_FILES["upload"]["name"]);
                        $originalFileName = preg_replace('/[^A-Za-z0-9._-]/', '-', $originalFileName);

                        $uploadedFileName = "message_file" . uniqid() . "-" . $originalFileName;

                        $targetFile = $targetDirectory . $uploadedFileName;
                        $tempFile = $_FILES["upload"]["tmp_name"];

                        if (move_uploaded_file($tempFile, $targetFile)) {
                            $uploadedFiles[] = array(
                                "file_name" => $uploadedFileName,
                                "file_size" => (string) filesize($targetFile),
                                "file_id" => null,
                                "service_type" => null
                            );
                        }
                    }
                }
            }

            $message_data = array(
                "from_user_id" => $this->login_user->id,
                "to_user_id" => $to_user_id,
                "subject" => $this->request->getPost('subject'),
                "files" => serialize($uploadedFiles),
                "message" => $this->request->getPost('message'),
                "created_at" => get_current_utc_time(),
                "deleted_by_users" => "",
            );
        }

        $message_data = clean_data($message_data);

        // Don't clean serialized attachment data.
        $message_data["files"] = serialize($uploadedFiles);

        $save_id = $this->Messages_model->saveapi($message_data);

        if ($save_id) {
            log_notification("new_message_sent", array("actual_message_id" => $save_id));
            echo json_encode(array("success" => true, "message" => app_lang("message_sent"), "id" => $save_id));
        } else {
            echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
        }
    }
    /* reply to an existing message */

    function reply($is_chat = 0)
    {
        $this->check_message_user_permission();
        $message_id = $this->request->getPost('message_id');

        $this->validate_submitted_data(array(
            "reply_message" => "required",
            "message_id" => "required|numeric"
        ));


        $message_info = $this->Messages_model->get_one($message_id);

        if (!$this->is_my_message($message_info)) {
            app_redirect("forbidden");
        }


        if ($message_info->id) {
            //check, where we have to send this message
            $to_user_id = 0;
            if ($message_info->from_user_id === $this->login_user->id) {
                $to_user_id = $message_info->to_user_id;
            } else {
                $to_user_id = $message_info->from_user_id;
            }

            $this->check_validate_sending_message($to_user_id);

            $target_path = get_setting("timeline_file_path");
            $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "message");

            $message = $this->request->getPost('reply_message');

            $message_data = array(
                "from_user_id" => $this->login_user->id,
                "to_user_id" => $to_user_id,
                "message_id" => $message_id,
                "subject" => "",
                "message" => $message,
                "created_at" => get_current_utc_time(),
                "deleted_by_users" => "",
            );

            $message_data = clean_data($message_data);
            $message_data["files"] = $files_data; //don't clean serilized data


            $save_id = $this->Messages_model->ci_save($message_data);

            if ($save_id) {

                //if chat via pusher is enabled, then send message data to pusher
                if (get_setting('enable_chat_via_pusher') && get_setting("enable_push_notification")) {
                    send_message_via_pusher($to_user_id, $message_data, $message_id);
                }

                //we'll not send notification, if the user is online

                if ($this->request->getPost("is_user_online") !== "1") {
                    log_notification("message_reply_sent", array("actual_message_id" => $save_id, "parent_message_id" => $message_id));
                }

                //clear the delete status, if the mail deleted
                $this->Messages_model->clear_deleted_status($message_id);

                if ($is_chat) {
                    echo json_encode(array("success" => true, 'data' => $this->_load_messages($message_id, $this->request->getPost("last_message_id"), 0, $to_user_id)));
                } else {
                    $options = array("id" => $save_id, "user_id" => $this->login_user->id);
                    $view_data['reply_info'] = $this->Messages_model->get_details($options)->row;
                    echo json_encode(array("success" => true, 'message' => app_lang('message_sent'), 'data' => $this->template->view("messages/reply_row", $view_data)));
                }

                return;
            }
        }
        echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
    }

    function forward_message()
    {
        $this->check_message_user_permission();

        $this->validate_submitted_data(array(
            "forward_message_id" => "required|numeric",
            "to_user_id" => "required|numeric"
        ));

        $forward_message_id = $this->request->getPost("forward_message_id");
        $to_user_id = $this->request->getPost("to_user_id");

        $this->check_validate_sending_message($to_user_id);

        $message_info = $this->Messages_model->get_one($forward_message_id);

        if (!$message_info || !$message_info->id || !$this->is_my_message($message_info)) {
            echo json_encode(array(
                "success" => false,
                "message" => app_lang("error_occurred")
            ));
            return;
        }

        $subject = trim((string) $message_info->subject);

        if (stripos($subject, "Fwd:") !== 0) {
            $subject = "Fwd: " . $subject;
        }

        $forward_body = "\n\n---------- Forwarded message ----------\n";
        $forward_body .= "From user ID: " . $message_info->from_user_id . "\n";
        $forward_body .= "Date: " . $message_info->created_at . "\n";
        $forward_body .= "Subject: " . $message_info->subject . "\n\n";
        $forward_body .= $message_info->message;

        $message_data = array(
            "from_user_id" => $this->login_user->id,
            "to_user_id" => $to_user_id,
            "subject" => $subject,
            "message" => $forward_body,
            "files" => $message_info->files,
            "created_at" => get_current_utc_time(),
            "deleted_by_users" => ""
        );

        $message_data = clean_data($message_data);

        $save_id = $this->Messages_model->saveapi($message_data);

        if ($save_id) {
            log_notification("new_message_sent", array("actual_message_id" => $save_id));

            echo json_encode(array(
                "success" => true,
                "message" => "Message forwarded successfully."
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => app_lang("error_occurred")
            ));
        }
    }


    //load messages right panel when clicking load more button
    function view_messages()
    {

        $this->check_message_user_permission();
        $this->validate_submitted_data(array(
            "message_id" => "required|numeric",
            "last_message_id" => "numeric",
            "top_message_id" => "numeric"
        ));

        $message_id = $this->request->getPost("message_id");

        echo $this->_load_more_messages($message_id, $this->request->getPost("last_message_id"), $this->request->getPost("top_message_id"));
    }

    //prepare the chat box messages 
    private function _load_more_messages($message_id, $last_message_id, $top_message_id)
    {

        $replies_options = array("message_id" => $message_id, "last_message_id" => $last_message_id, "top_message_id" => $top_message_id, "user_id" => $this->login_user->id, "limit" => 10);

        $view_data["replies"] = $this->Messages_model->get_details($replies_options)->result;
        $view_data["message_id"] = $message_id;

        $this->Messages_model->set_message_status_as_read($message_id, $this->login_user->id);

        return $this->template->view("messages/reply_rows", $view_data);
    }

    /* prepare notifications */

    function get_notifications()
    {
        $this->validate_submitted_data(array(
            "active_message_id" => "numeric"
        ));


        $notifiations = $this->Messages_model->get_notifications($this->login_user->id, $this->login_user->message_checked_at, $this->request->getPost("active_message_id"));
        $view_data['notifications'] = $notifiations->getResult();
        echo json_encode(array("success" => true, "active_message_id" => $this->request->getPost("active_message_id"), 'total_notifications' => $notifiations->resultID->num_rows, 'notification_list' => $this->template->view("messages/notifications", $view_data)));
    }

    function update_notification_checking_status()
    {
        $now = get_current_utc_time();
        $user_data = array("message_checked_at" => $now);
        $this->Users_model->ci_save($user_data, $this->login_user->id);
    }

    /* upload a file */

    function upload_file()
    {
        upload_file_to_temp();
    }

    /* check valid file for message */

    function validate_message_file()
    {
        return validate_post_file($this->request->getPost("file_name"));
    }

    /* download files by zip */

    function download_message_files($message_id = "")
    {
        $model_info = $this->Messages_model->get_one($message_id);

        if (!$this->is_my_message($model_info)) {
            app_redirect("forbidden");
        }

        $files = $model_info->files;

        if (!$files) {
            show_404();
        }

        $unserialized_files = @unserialize($files);

        if (is_array($unserialized_files)) {
            $normalized_files = array();

            foreach ($unserialized_files as $file) {
                if (is_array($file)) {
                    $file_name = get_array_value($file, "file_name");

                    if ($file_name) {
                        $normalized_files[] = $file;
                    }
                } else {
                    $file_name = trim((string) $file);

                    if ($file_name) {
                        $normalized_files[] = array(
                            "file_name" => $file_name
                        );
                    }
                }
            }

            if ($normalized_files) {
                $files = serialize($normalized_files);
            }
        }

        $timeline_file_path = get_setting("timeline_file_path");

        return $this->download_app_files($timeline_file_path, $files);
    }

    function delete_my_messages($id = 0, $imageFilename = '')
    {
        if ($imageFilename !== '') {
            // Code to handle deletion of messages and image
            if (!$id) {
                exit();
            }

            // Delete messages for current user.
            $this->Messages_model->delete_messages_for_user($id, $this->login_user->id);

            // $path = "/opt/lampp/htdocs/factor_17-05/files/brandLogo/" . $imageFilename;
            $path = "/var/www/html/files/timeline_files/" . $imageFilename;


            if (!unlink($path)) {
                echo "error";
            } else {
                header("Location: index.php?deletsuccess");
            }
        } else {
            // Code to handle deletion of messages only (no image deletion)
            if (!$id) {
                exit();
            }

            // Delete messages for current user.
            $this->Messages_model->delete_messages_for_user($id, $this->login_user->id);
        }
    }

    //prepare chat inbox list
    function chat_list()
    {
        $this->check_message_user_permission();

        $view_data['show_users_list'] = false;
        $view_data['show_clients_list'] = false;

        $client_message_users = get_setting("client_message_users");
        if ($this->login_user->user_type === "User") {
            //user is team member
            $client_message_users_array = explode(",", $client_message_users);
            if (in_array($this->login_user->id, $client_message_users_array)) {
                //user can send message to clients
                $view_data['show_clients_list'] = true;
            }

            if (get_array_value($this->login_user->permissions, "message_permission") !== "no") {
                //user can send message to team members
                $view_data['show_users_list'] = true;
            }
        } else {
            //user is a client contact and can send messages
            if ($client_message_users) {
                $view_data['show_users_list'] = true;
            }

            //user can send message to own client contacts
            if (get_setting("client_message_own_contacts")) {
                $view_data['show_clients_list'] = true;
            }
        }
        if ($this->login_user->is_admin) {
            $options = array("login_user_id" => $this->login_user->id, "user_ids" => $this->get_allowed_user_ids());

            $view_data['messages'] = $this->Messages_model->get_chat_list($options)->getResult();

            return $this->template->view("messages/chat/tabs", $view_data);
        } else {

            $view_data['show_users_list'] = false;
            $view_data['show_clients_list'] = false;

            $client_message_users = get_setting("client_message_users");


            if ($client_message_users) {
                $view_data['show_users_list'] = true;
            }


            if (get_setting("client_message_own_contacts")) {
                $view_data['show_clients_list'] = true;
            }


            $options = array(
                "login_user_id" => $this->login_user->id,
                "user_ids" => $this->get_allowed_user_ids()
            );

            $view_data['messages'] = $this->Messages_model->get_chat_list($options)->getResult();

            return $this->template->view("messages/chat/tabs", $view_data);
        }
    }

    function users_list($type)
    {
        $view_data["users"] = $this->Messages_model->get_users_for_messaging($this->get_user_options_for_query($type))->getResult();

        $page_type = "";
        if ($type === "User") {
            $page_type = "team-members-tab";
        } else {
            $page_type = "clients-tab";
        }

        $view_data["page_type"] = $page_type;

        return $this->template->view("messages/chat/team_members", $view_data);
    }

    //load messages in chat view
    function view_chat()
    {

        $this->check_message_user_permission();

        $this->validate_submitted_data(array(
            "message_id" => "required|numeric",
            "last_message_id" => "numeric",
            "top_message_id" => "numeric",
            "another_user_id" => "numeric"
        ));

        $message_id = $this->request->getPost("message_id");

        $another_user_id = $this->request->getPost("another_user_id");

        if ($this->request->getPost("is_first_load") == "1") {
            $view_data["first_message"] = $this->Messages_model->get_details(array("id" => $message_id, "user_id" => $this->login_user->id))->row;
            echo $this->template->view("messages/chat/message_title", $view_data);
        }

        echo $this->_load_messages($message_id, $this->request->getPost("last_message_id"), $this->request->getPost("top_message_id"), $another_user_id);
    }

    //prepare the chat box messages 
    private function _load_messages($message_id, $last_message_id, $top_message_id, $another_user_id = "")
    {

        $replies_options = array("message_id" => $message_id, "last_message_id" => $last_message_id, "top_message_id" => $top_message_id, "user_id" => $this->login_user->id);

        $view_data["replies"] = $this->Messages_model->get_details($replies_options)->result;
        $view_data["message_id"] = $message_id;

        $this->Messages_model->set_message_status_as_read($message_id, $this->login_user->id);

        $is_online = false;
        if ($another_user_id) {
            $last_online = $this->Users_model->get_one($another_user_id)->last_online;
            if ($last_online) {
                $is_online = is_online_user($last_online);
            }
        }

        $view_data['is_online'] = $is_online;

        return $this->template->view("messages/chat/message_items", $view_data);
    }

    function get_active_chat()
    {

        $this->validate_submitted_data(array(
            "message_id" => "required|numeric"
        ));

        $message_id = $this->request->getPost("message_id");

        $options = array("id" => $message_id, "user_id" => $this->login_user->id);
        $view_data["message_info"] = $this->Messages_model->get_details($options)->row;

        if (!$this->is_my_message($view_data["message_info"])) {
            app_redirect("forbidden");
        }

        //$this->Messages_model->set_message_status_as_read($view_data["message_info"]->id, $this->login_user->id);

        $view_data["message_id"] = $message_id;
        return $this->template->view("messages/chat/active_chat", $view_data);
    }

    function get_chatlist_of_user()
    {

        $this->check_message_user_permission();

        $this->validate_submitted_data(array(
            "user_id" => "required|numeric"
        ));

        $user_id = $this->request->getPost("user_id");

        $options = array("user_id" => $user_id, "login_user_id" => $this->login_user->id);
        $view_data["messages"] = $this->Messages_model->get_chat_list($options)->getResult();


        $user_info = $this->Users_model->get_one_where(array("id" => $user_id, "status" => "active", "deleted" => "0"));
        $view_data["user_name"] = $user_info->first_name . " " . $user_info->last_name;

        $view_data["user_id"] = $user_id;
        $view_data["tab_type"] = $this->request->getPost("tab_type");

        return $this->template->view("messages/chat/get_chatlist_of_user", $view_data);
    }

    function send_typing_indicator_to_pusher()
    {
        $message_id = $this->request->getPost("message_id");
        if (!$message_id) {
            show_404();
        }

        $message_info = $this->Messages_model->get_one($message_id);
        if (!$this->is_my_message($message_info)) {
            app_redirect("forbidden");
        }

        if ($message_info->id) {
            //check, where we have to send this message
            $to_user_id = 0;
            if ($message_info->from_user_id === $this->login_user->id) {
                $to_user_id = $message_info->to_user_id;
            } else {
                $to_user_id = $message_info->from_user_id;
            }

            $this->check_validate_sending_message($to_user_id);

            if (get_setting('enable_chat_via_pusher') && get_setting("enable_push_notification")) {
                send_message_via_pusher($to_user_id, "", $message_id, "typing");
            }
        } else {
            show_404();
        }
    }
}

/* End of file messages.php */
    /* Location: ./app/controllers/messages.php */
