<?php echo form_open_multipart(get_uri("items/save_pages"), array("id" => "file-form2", "class" => "general-form", "role" => "form")); ?>

<input type="hidden" name="modelno" id="modelno" value="<?php echo $modelno; ?>" />
<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
<input type="hidden" name="modal_id" id="modal_id" value="<?php echo $modal_id; ?>" />

  <div id="form-items">
    <div class="form-group">
      <label for="input-box-1">Page Name</label>
      <input type="text" name="input_box[]" id="input-box-1" class="form-control" placeholder="Enter Page Name" required>
    </div>
<div class="form-group">
          <label for="seq_dropdown-${counter}">Select Page Sequence</label>
          <select name="seq_dropdown[]" id="seq_dropdown-${counter}" class="form-control" required> 
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
          </select>
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
 
#add-more {
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
          <label for="seq_dropdown-${counter}">Select Page Sequence</label>
          <select name="seq_dropdown[]" id="seq_dropdown-${counter}" class="form-control" required>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
          </select>
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

