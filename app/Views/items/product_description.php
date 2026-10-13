<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product Description</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
<style>
    .form-check-input[type="radio"] {
    cursor: pointer;
    transform: scale(1.2);
    margin-right: 6px;
}
.form-check-label {
    cursor: pointer;
    font-weight: 700;
}
.form-check {
    margin-right: 30px; /* apne hisaab se badha/samjha le */
}
</style>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add Product Description</h4>
        </div>
        <div class="card-body">

            <!-- Sticker Details -->
            <div class="mb-4">
                <!-- <h5>Sticker Details:</h5> -->
                <form id="productDescForm" action="<?= site_url('items/product_description') ?>" method="POST" enctype="multipart/form-data">
    <!-- CSRF token -->
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />

    <!-- Hidden fields to send backend -->
    <input type="hidden" name="modelno" value="<?= esc($modelno ?? '') ?>">
    <input type="hidden" name="modelname" value="<?= esc($modelname ?? '') ?>">
    <input type="hidden" name="product_name" value="<?= esc($category ?? '') ?>">
    <input type="hidden" name="brand" value="<?= esc($brand ?? '') ?>">
    <input type="hidden" name="id" value="<?= esc($id ?? '') ?>">

    <!-- Display fields -->
    <div class="mb-3">
        <p><strong>Model No:</strong> <?= esc($modelno ?? '') ?></p>
        <p><strong>Model:</strong> <?= esc($modelname ?? '') ?></p>
        <p><strong>Product Name:</strong> <?= esc($category ?? '') ?></p>
        <p><strong>Brand:</strong> <?= esc($brand ?? '') ?></p>
    </div>

    <!-- Radio Options -->
    <div class="form-group mt-3">
        <div class="d-flex align-items-center gap-4">
            <div class="form-check">
                <input class="form-check-input" type="radio" id="per_id" name="option_type" value="per_id" <?= isset($option_type) && $option_type == 'per_id' ? 'checked' : '' ?>>
                <label class="form-check-label" for="per_id">Per ID</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="per_batch" name="option_type" value="per_batch" <?= isset($option_type) && $option_type == 'per_batch' ? 'checked' : '' ?>>
                <label class="form-check-label" for="per_batch">Per Batch</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" id="fixed" name="option_type" value="fixed" <?= isset($option_type) && $option_type == 'fixed' ? 'checked' : '' ?>>
                <label class="form-check-label" for="fixed">Fixed</label>
            </div>
        </div>
    </div>

    <!-- File Input -->
    <label for="itemImages">Upload Images</label>
    <input type="file" id="itemImages" name="item_images[]" class="d-none" accept="image/*" multiple>

    <!-- Image Preview -->
    <div id="imagePreview" class="mb-3 d-flex flex-wrap gap-2">
        <div id="addImageBox" class="add-box border d-flex align-items-center justify-content-center" style="width:100px; height:100px; cursor:pointer;">
            <span style="font-size:2rem;">+</span>
        </div>

        <!-- Existing Images Preview -->
        <?php if(!empty($item_images)): 
            $images = json_decode($item_images, true);
            foreach($images as $img): ?>
                <div class="position-relative me-2 mb-2" style="width:100px; height:100px;">
                    <img src="<?= base_url('uploads/products/'.$img) ?>" class="rounded border" style="width:100%; height:100%; object-fit:cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" style="padding:2px 5px;">×</button>
                </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- Short Description Toggle -->
    <div class="mb-3">
        <button type="button" id="showShortDesc" class="btn btn-outline-info btn-sm">Add Short Description</button>
    </div>

    <div class="form-group" id="shortDescBox" style="display:<?= !empty($short_description) ? 'block' : 'none' ?>;">
        <label for="short_description">Short Description</label>
        <textarea id="short_description" name="short_description" class="form-control" rows="2"><?= isset($short_description) ? esc($short_description) : '' ?></textarea>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="form-control" rows="4" required><?= isset($description) ? esc($description) : '' ?></textarea>
    </div>

    <button type="submit" class="btn btn-success btn-block">Save Product Description</button>
</form>

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    const itemImages = document.getElementById("itemImages");
    const imagePreview = document.getElementById("imagePreview");
    const addImageBox = document.getElementById("addImageBox");
    let selectedFiles = [];

    // Open file picker
    addImageBox.addEventListener("click", () => {
        if(selectedFiles.length < 10) itemImages.click();
        else alert("Maximum 10 images allowed!");
    });

    // Handle new file selection
    itemImages.addEventListener("change", function(e){
        const files = Array.from(e.target.files);
        if(selectedFiles.length + files.length > 10){ 
            alert("Max 10 images"); 
            return; 
        }

        files.forEach(file=>{
            selectedFiles.push(file);
            const reader = new FileReader();
            reader.onload = function(event){
                const imgDiv = document.createElement("div");
                imgDiv.classList.add("position-relative","me-2","mb-2");
                imgDiv.style.width = "100px"; 
                imgDiv.style.height = "100px";
                imgDiv.innerHTML = `
                    <img src="${event.target.result}" class="rounded border" style="width:100%; height:100%; object-fit:cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" style="padding:2px 5px;">×</button>
                `;
                imgDiv.querySelector("button").addEventListener("click", ()=>{
                    const index = selectedFiles.indexOf(file);
                    if(index > -1) selectedFiles.splice(index,1);
                    imgDiv.remove();
                });
                imagePreview.insertBefore(imgDiv, addImageBox);
            }
            reader.readAsDataURL(file);
        });
        e.target.value = "";
    });

    // Short description toggle
    $('#showShortDesc').click(function(){ 
        $('#shortDescBox').slideToggle(); 
    });
    $('#productDescForm').submit(function(e){
        e.preventDefault();
        const formData = new FormData(this);
        selectedFiles.forEach(f => formData.append('item_images[]', f));

        $.ajax({
            url: '<?= site_url("items/product_description") ?>',
            type:'POST',
            data: formData,
            processData:false,
            contentType:false,
            success:function(res){
                if(res.status==='success'){
                    alert(res.message);
                    location.reload();
                }else alert(res.message);
            },
            error:function(xhr){console.log(xhr.responseText);alert('Server error');}
        });
    });
});
</script>

</body>
</html>
