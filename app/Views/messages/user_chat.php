<div class="rise-chat-body clearfix"> 
<?php
session_start();
$user_id = $_SESSION['user_id'];  
if($user_id === '1'){
?>
<h2 style="text-align:center"><?php echo $user_id; ?></h2>
<?php
}else{
?>
 <h2 style="text-align:center">admin.io</h2>
<?php
}
?>
    <div id="js-chat-messages-container" class="clearfix">  
     <p>
     <?php echo app_lang("message"); ?>: <?php print_r($message); ?>
     </p>
    </div>  
    <div id="js-chat-reply-indicator"></div>  
</div>   

<div class="rise-chat-footer"> 
    <div id="chat-reply-form-dropzone" class="post-dropzone">  
        <?php echo form_open(get_uri("messages/UserBrand_reply/1"), array("id" => "chat-message-reply-form", "class" => "general-form", "role" => "form", "enctype" => "multipart/form-data")); ?>  
         
          <span class="chat-file-upload-icon upload-file-button">
            <i data-feather="camera" class="icon-16"></i>
            <input type="file" id="file-input" name="file" style="display:none;" accept="image/*, .pdf, .docx, .txt" />
         </span>  
         
         <!-- Textarea for Message -->
         <?php 
         echo form_textarea(array( 
             "id" => "js-chat-message-textarea", 
             "name" => "reply_message", 
             "data-rule-required" => true, 
             "autofocus" => true, 
             "data-msg-required" => "", 
             "placeholder" => app_lang('write_a_message') 
         )); 
         ?>
         
        <?php echo form_close(); ?> 
    </div> 
</div> 

<script>  
   document.querySelector('.upload-file-button').addEventListener('click', function() {
    document.getElementById('file-input').click();
});
document.getElementById('js-chat-message-textarea').addEventListener('keydown', function(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault(); // Prevents new line and submits form
        document.getElementById('chat-message-reply-form').submit();
    }
});
</script>

