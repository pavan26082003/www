<html>
<head>
</head>
<style>
</style>
<body>
<h2> <?php echo $category_info->title; ?></h2>
<a href="#" class="mb20"><?php echo nl2br($category_info->description); ?></a>
<div id="pdfContainer" style="border:2px solid grey">
    <embed id="pdfEmbed" type="application/pdf" width="100%" height="400px" />
</div>
<?php
foreach ($articles as $article) {

    echo anchor(get_uri($category_info->type . "/view/" . $article->id), "<i data-feather='arrow-right-circle' class='icon-16 mr15'></i>" . $article->title, array("class" => "list-group-item"));
}
?>
<div class="mb20"></div>

<script>
$(document).ready(function() {
    var pdfURL = '<?php echo nl2br($category_info->description); ?>';
    loadPDF(pdfURL);
});

function loadPDF(pdfURL) {
    $.ajax({
        url: pdfURL,
        type: 'GET',
        dataType: 'text',
        success: function(data) {
            $('#pdfEmbed').attr('src', pdfURL);
        },
        error: function(xhr, status, error) {
            console.error('Error loading PDF:', error);
        }
    });
}
</script>
</body>
</html>
