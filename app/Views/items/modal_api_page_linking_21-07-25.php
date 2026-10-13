<!DOCTYPE html>
<html>
<head>
  <title>Parameter Selection with Editable Page Names</title>
  <style>
    /* Styling (same as yours, kept intact) */
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f2f2f2;
      padding: 20px;
    }
    form {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
      max-width: 1000px;
      margin: auto;
    }
    select, button, input[type="text"] {
      padding: 10px 14px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-right: 10px;
      font-size: 14px;
    }
    button {
      background-color: #007bff;
      color: white;
      cursor: pointer;
      font-weight: 500;
    }
    button:hover {
      background-color: #0056b3;
    }
    .selected-values-wrapper {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 25px;
    }
    .page-selected-values {
      flex: 1 1 30%;
      background-color: #fefefe;
      border: 1px solid #ddd;
      padding: 15px;
      border-radius: 10px;
      min-width: 280px;
    }
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }
    .page-title {
      color: #007bff;
      font-size: 16px;
      font-weight: bold;
    }
    .edit-controls {
      display: flex;
      gap: 5px;
    }
    .selected-value-line {
      background-color: #e9f7ef;
      border: 1px solid #c1eac5;
      padding: 6px 12px;
      border-radius: 6px;
      color: #155724;
      font-weight: 500;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .serial-number {
      margin-right: 8px;
      font-weight: bold;
    }
    .crossButton {
      color: red;
      font-weight: bold;
      cursor: pointer;
    }
    button[type="submit"] {
      background-color: #28a745;
      margin-top: 20px;
    }
    button[type="submit"]:hover {
      background-color: #218838;
    }
    #pageNameInput {
      display: none;
      margin-top: 10px;
    }
    label {
      font-weight: 500;
    }
    .hidden {
      display: none;
    }
  </style>
</head>
<body>
<h2>Modal ID: <?= esc($modal_id) ?></h2>
<!-- ✅ FORM START -->
<!--form method="post" action="<?= site_url('items/submit_page_params') ?>"-->
<?php echo form_open(get_uri("items/submit_page_params"), array("id" => "file-form", "class" => "general-form")); ?>


  <label for="cmd_parameter">Select Parameter:</label>
 <select id="cmd_parameter" name="cmd_parameter[]">

    <option value="">-- Select --</option>
    <option value="Volume">Volume</option>
    <option value="Brightness">Brightness</option>
    <option value="Temperature">Temperature</option>
    <option value="Speed">Speed</option>
    <option value="Color">Color</option>
    <option value="Mode">Mode</option>
    <option value="Power">Power</option>
    <option value="Sound">Sound</option>
    <option value="Time">Time</option>
    <option value="Contrast">Contrast</option>
    <option value="Tone">Tone</option>
  </select>

  <label for="uiPageNo">Page No:</label>
  <select id="uiPageNo" onchange="togglePageNameInput()" name="uiPageNo_cmd[]">

    <?php for ($i = 1; $i <= 10; $i++): ?>
      <option value="<?= $i ?>"><?= $i ?></option>
    <?php endfor; ?>
  </select>

  <input type="text" id="pageNameInput" placeholder="Enter Page Name">
  <button type="button" onclick="addSelectedParameter()">Select</button>

  <div class="selected-values-wrapper" id="selectedValuesWrapper"></div>
  <div id="hiddenInputs"></div>
  <button type="submit">Submit</button>
</form>
<!-- ✅ FORM END -->

<!-- ✅ SHOW SUBMITTED DATA -->

    <?php if (!empty($submittedData)): ?>
    <pre><?php print_r($submittedData); ?></pre>
<?php endif; ?>



<!-- ✅ JAVASCRIPT -->
<script>
  const pageNamesMap = {};

  function togglePageNameInput() {
    const pageNo = document.getElementById("uiPageNo").value;
    const input = document.getElementById("pageNameInput");
    input.style.display = (pageNo !== "1" && !pageNamesMap[pageNo]) ? "inline-block" : "none";
  }

  function ensurePageBlock(pageNum, pageName = null) {
    const wrapper = document.getElementById("selectedValuesWrapper");
    const existing = document.getElementById(`page-${pageNum}-values`);
    if (existing) return;

    const displayName = (pageNum === "1") ? "Page 1" : (pageName || `Page ${pageNum}`);
    const pageBlock = document.createElement("div");
    pageBlock.className = "page-selected-values";
    pageBlock.id = `page-${pageNum}-values`;
    pageBlock.dataset.pageNum = pageNum;

    pageBlock.innerHTML = `
      <div class="page-header">
        <span class="page-title" id="pageTitle-${pageNum}">${displayName}</span>
        <div class="edit-controls">
          <button type="button" onclick="editPageTitle('${pageNum}')">Edit</button>
        </div>
      </div>
      <div class="selected-values-list" id="list-page-${pageNum}"></div>
    `;

    const blocks = Array.from(wrapper.children);
    const insertBefore = blocks.find(b => parseInt(b.dataset.pageNum) > parseInt(pageNum));
    if (insertBefore) {
      wrapper.insertBefore(pageBlock, insertBefore);
    } else {
      wrapper.appendChild(pageBlock);
    }
  }



  function addSelectedParameter() {
    const parameter = document.getElementById("cmd_parameter").value;
    const pageNum = document.getElementById("uiPageNo").value;
    const pageNameInput = document.getElementById("pageNameInput");

    if (!parameter || !pageNum) {
        alert("Please select both parameter and page number.");
        return;
    }

    if (pageNum !== "1" && !pageNamesMap[pageNum]) {
        if (!pageNameInput.value.trim()) {
            alert("Please enter a name for this page.");
            return;
        }
        pageNamesMap[pageNum] = pageNameInput.value.trim();
        pageNameInput.value = '';
        pageNameInput.style.display = "none";
    }

    ensurePageBlock(pageNum, pageNamesMap[pageNum]);

    // Check visually if parameter already exists in the list
    const list = document.getElementById(`list-page-${pageNum}`);
    const paramAlreadyExists = Array.from(list.children).some(
        child => child.dataset.param === parameter
    );

    if (paramAlreadyExists) {
        alert("This parameter already added on selected page.");
        return;
    }

    // Check if hidden input already exists for the same parameter and page
    const hiddenParamExists = document.querySelector(
        `#hiddenInputs input[name="cmd_parameter[]"][value="${parameter}"][data-page="${pageNum}"]`
    );
    if (hiddenParamExists) {
        alert("This parameter already linked internally, skipping.");
        return;
    }

    // Append visual list
    const index = list.children.length + 1;
    const div = document.createElement("div");
    div.className = "selected-value-line";
    div.dataset.param = parameter;
    div.innerHTML = `
      <span class="serial-number">${index}.</span>
      <span>${parameter}</span>
      <span class="crossButton" onclick="removeParam(this, '${parameter}', '${pageNum}')">×</span>
    `;
    list.appendChild(div);

    // Append hidden inputs
    document.getElementById("hiddenInputs").insertAdjacentHTML("beforeend", `
    <input type="hidden" name="cmd_parameter[]" value="${parameter}" data-page="${pageNum}" data-param="${parameter}">
    <input type="hidden" name="uiPageNo_cmd[]" value="${pageNum}" data-page="${pageNum}" data-param="${parameter}">
    <input type="hidden" name="page_name[${pageNum}]" value="${pageNamesMap[pageNum]}" data-page="${pageNum}">
`);


}












  function removeParam(el, parameter, pageNum) {
    el.parentElement.remove();

    const inputs = document.querySelectorAll(`#hiddenInputs input[data-page="${pageNum}"][data-param="${parameter}"]`);
    inputs.forEach(input => input.remove());

    const list = document.getElementById(`list-page-${pageNum}`);
    Array.from(list.children).forEach((child, idx) => {
      child.querySelector(".serial-number").textContent = `${idx + 1}.`;
    });

    if (list.children.length === 0) {
      const pageBlock = document.getElementById(`page-${pageNum}-values`);
      if (pageBlock) pageBlock.remove();
      delete pageNamesMap[pageNum];
      togglePageNameInput();
    }
  }

  function editPageTitle(pageNum) {
    const titleEl = document.getElementById(`pageTitle-${pageNum}`);
    const oldName = titleEl.textContent;
    const container = titleEl.parentElement;

    titleEl.classList.add("hidden");

    const input = document.createElement("input");
    input.type = "text";
    input.value = oldName;

    const saveBtn = document.createElement("button");
    saveBtn.type = "button";
    saveBtn.textContent = "Save";
    saveBtn.onclick = () => {
      const newName = input.value.trim();
      if (!newName) return alert("Name cannot be empty");
      titleEl.textContent = newName;
      pageNamesMap[pageNum] = newName;
      input.remove();
      saveBtn.remove();
      cancelBtn.remove();
      titleEl.classList.remove("hidden");
    };

    const cancelBtn = document.createElement("button");
    cancelBtn.type = "button";
    cancelBtn.textContent = "Cancel";
    cancelBtn.onclick = () => {
      input.remove();
      saveBtn.remove();
      cancelBtn.remove();
      titleEl.classList.remove("hidden");
    };

    container.appendChild(input);
    container.appendChild(saveBtn);
    container.appendChild(cancelBtn);
  }
</script>

</body>
</html>
