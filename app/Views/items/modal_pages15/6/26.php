 <?php echo form_open_multipart(get_uri("items/save_page_seq"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
 
 <input type="hidden" name="modelno" id="modelno" value="<?php echo $modelno; ?>" />
<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
<input type="hidden" name="modal_id" id="modal_id" value="<?php echo $modal_id; ?>" />

<div id="pageSequenceModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h4>Select a Page Sequence</h4>
         
        <div class="select-container"> 
            <select id="pageNamesDropdown">
                <?php 
                $page_names_string = $page_names[0]['page_names'];
                $pages = explode(", ", $page_names_string);   
                foreach ($pages as $page) {
                    echo "<option value='$page'>$page</option>";
                }
                ?>
            </select>
 
            <select id="sequenceDropdown"> 
            </select>
        </div>
 
        <button type="button" id="addButton">Add</button>
 
        <input type="text" id="addedValuesInput" name="addedValuesInput" readonly placeholder="Selected Page Sequence will appear here">

        <button id="submitPage">Submit</button>
    </div>
</div>
 <?php echo form_close(); ?>
<?php echo form_open_multipart(get_uri("items/save_pages"), array("id" => "file-form2", "class" => "general-form", "role" => "form")); ?>

<input type="hidden" name="modelno" id="modelno" value="<?php echo $modelno; ?>" />
<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
<input type="hidden" name="modal_id" id="modal_id" value="<?php echo $modal_id; ?>" />
 
<button type="button" id="view" class="btn btn-secondary">
    View Page 
</button>

<button type="button" id="sequence" class="btn btn-secondary">
    Add Page Sequence
</button>
<script> 
var modal = document.getElementById("pageSequenceModal");
 
var btn = document.getElementById("sequence");
 
var span = document.getElementsByClassName("close")[0];
 
btn.onclick = function() {
    modal.style.display = "block";
    populateSequenceDropdown();   
}
 
span.onclick = function() {
    modal.style.display = "none";
}
 
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
 
function populateSequenceDropdown() {
    var pageNamesDropdown = document.getElementById("pageNamesDropdown");
    var sequenceDropdown = document.getElementById("sequenceDropdown");
 
    sequenceDropdown.innerHTML = "";
 
    var numPages = pageNamesDropdown.options.length;
    for (var i = 1; i <= numPages; i++) {
        var option = document.createElement("option");
        option.value = i;
        option.text = i;
        sequenceDropdown.appendChild(option);
    }
}
 
document.getElementById("addButton").onclick = function() {
    var pageNamesDropdown = document.getElementById("pageNamesDropdown");
    var sequenceDropdown = document.getElementById("sequenceDropdown");
 
    var selectedPage = pageNamesDropdown.value;
    var selectedSequence = sequenceDropdown.value;
 
    var addedValuesInput = document.getElementById("addedValuesInput");
 
    addedValuesInput.value += selectedPage + " - Sequence: " + selectedSequence + "; ";
  
}
 
document.getElementById("submitPage").onclick = function() {
    var selectedPage = document.getElementById("pageNamesDropdown").value;
    var selectedSequence = document.getElementById("sequenceDropdown").value;
   // alert("Selected page: " + selectedPage + ", Sequence: " + selectedSequence);  
    modal.style.display = "none";  
}

</script> 
<div id="tab" class="tab-container" style="display:none;">
    <ul id="page-list"> 
    </ul>
</div>
 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script> 
var pages_names = <?php echo json_encode($page_names); ?>;

$(document).ready(function() { 
    $('#view').click(function() { 
        $('#tab').toggle();
 
        $('#page-list').empty();
 
        pages_names.forEach(function(item) { 
            var page_name = item.page_names;
            $('#page-list').append('<li>' + page_name + '</li>');
        });
    });
});
</script>

  <div id="form-items">
    <div class="form-group">
      <label for="input-box-1">Page Name</label>
      <input type="text" name="input_box[]" id="input-box-1" class="form-control" placeholder="Enter Page Name" required>
    </div>
 
    <div class="form-group">
      <label for="dropdown-1">Select Page Extension</label>
      <select name="dropdown[]" id="dropdown-1" class="form-control" required>
        <option value="test">.Test</option>
        <option value="config">.Config</option>
        <option value="paring">.Pairing</option>
        <option value="operation">.Operation</option>
      </select>
    </div>
  </div>

  <button type="button" id="add-more" class="btn btn-secondary">Add Page</button>

  <button type="submit" class="btn btn-primary">Submit</button>

<?php echo form_close(); ?>
 
<style> 
.body{
height:100vh;
}
.general-form {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: #f9f9f9;
}

.general-form .form-group {
  margin-bottom: 15px;
}

.general-form .form-group label {
  font-weight: 600;
  color: #333;
  display: block;
  margin-bottom: 8px;
}

.general-form .form-control {
  width: 100%;
  height:40px;
  padding: 10px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

.general-form .form-control:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
}

.general-form button {
  width: 100%;
  padding: 10px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.general-form button:hover {
  background-color: #0056b3;
}
 
.general-form .form-group {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.general-form .form-group input,
.general-form .form-group select {
  width: 48%;
}
 
#add-more, #view, #sequence {
  margin-top: 20px;
  margin-bottom: 20px;
  padding: 8px 12px;
  font-size: 20px;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  width:40%;
}

#add-more:hover {
  background-color: #218838;
}

.tab-container {
    background-color: #f4f4f4;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 300px;
    margin-top: 10px;
}

#page-list {
    list-style-type: none;
    padding: 0;
}

#page-list li {
    padding: 8px;
    margin-bottom: 5px;
    background-color: #ffffff;
    border: 1px solid #ddd;
    border-radius: 3px;
    cursor: pointer;
    transition: background-color 0.3s;
}

#page-list li:hover {
    background-color: #ddd;
}

.modal-content {
    display: flex;
    flex-direction: column;
    padding: 20px;
}

select {
    margin: 10px 0;
    padding: 5px;
    font-size: 16px;
}

.select-container {
    display: flex;
    justify-content: space-between;
    width: 100%;
}

#pageNamesDropdown, #sequenceDropdown {
    width: 48%; /* Each dropdown takes up 48% of the width, leaving space between them */
}

#submitPage {
    margin-top: 20px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
}

#submitPage:hover {
    background-color: #0056b3;
}

</style>
 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    var counter = 1;   
    $('#add-more').click(function() {
      counter++;  
      var newFormGroup = `
        <div class="form-group">
          <label for="input-box-${counter}">Page Name</label>
          <input type="text" name="input_box[]" id="input-box-${counter}" class="form-control" placeholder="Enter Page Name" required>
        </div>
        
        <div class="form-group">
          <label for="dropdown-${counter}">Select Page Extension</label>
          <select name="dropdown[]" id="dropdown-${counter}" class="form-control" required>
            <option value="test">.Test</option>
            <option value="config">.Config</option>
            <option value="paring">.Pairing</option>
            <option value="operation">.Operation</option>
          </select>
        </div>
      `;
 
      $('#form-items').append(newFormGroup);
    });
  });
</script>

