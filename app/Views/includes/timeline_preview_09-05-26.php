<?php
if (!$files) {
    return;
}

$image_extensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp"];
$doc_extensions = ["xls", "xlsx", "csv", "doc", "docx", "ppt", "pptx", "zip", "rar"];
?>

<div class="row mt-2">

<?php foreach ($files as $file_info) { 
    $file_name = $file_info['file_name'];
    $file_url  = get_file_uri(get_setting("timeline_file_path") . $file_name);
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
?>

    <div class="col-md-4 mb-3">

        <?php if (in_array($file_ext, $image_extensions)) { ?>

            <!-- IMAGE PREVIEW -->
            <img src="<?php echo $file_url; ?>" 
                 class="img-thumbnail" 
                 style="width:100%; max-height:200px; object-fit:cover; cursor:pointer;"
                 onclick="window.open('<?php echo $file_url; ?>','_blank')">

        <?php } elseif ($file_ext === "pdf") { ?>

            <!-- PDF PREVIEW -->
            <div class="p-3 bg-light" style="text-align:center; border-radius:8px; cursor:pointer;"
                 onclick="window.open('<?php echo $file_url; ?>','_blank')">
                <i class="fa fa-file-pdf-o" style="font-size:60px; color:#E53935;"></i>
                <p class="mt-2">PDF File</p>
            </div>

        <?php } elseif (in_array($file_ext, $doc_extensions)) { ?>

            <!-- EXCEL / WORD / ZIP / DOC PREVIEW -->
            <div class="p-3 bg-light" style="text-align:center; border-radius:8px; cursor:pointer;"
                 onclick="window.open('<?php echo $file_url; ?>','_blank')">
                <i class="fa fa-file" style="font-size:60px; color:#6c757d;"></i>
                <p class="mt-2"><?php echo strtoupper($file_ext); ?> File</p>
            </div>

        <?php } else { ?>

            <!-- UNKNOWN FILE -->
            <div class="p-3 bg-light" style="text-align:center; border-radius:8px;">
                <i class="fa fa-file-o" style="font-size:60px; color:#6c757d;"></i>
                <p class="mt-2">Unknown File</p>
            </div>

        <?php } ?>

        <!-- File name & download -->
        <div class="text-center mt-1">
            <a href="<?php echo $file_url; ?>" target="_blank"><?php echo $file_info['file_name']; ?></a>
        </div>

    </div>

<?php } ?>

</div>
