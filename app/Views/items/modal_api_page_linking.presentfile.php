<!-- View File (CI4) -->
<!DOCTYPE html>
<html>
<head>
  <title>Parameter Selection with Editable Page Names</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f9f9f9;
      padding: 30px;
    }
    form {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
      max-width: 1000px;
      margin: auto;
    }
    select, input[type="text"], button {
      padding: 10px 14px;
      margin-right: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    button {
      background: #007bff;
      color: white;
      cursor: pointer;
      font-weight: 500;
    }
    button:hover {
      background: #0056b3;
    }
    #pageNameInput {
      display: none;
    }
    .selected-values-wrapper {
      margin-top: 30px;
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    .page-selected-values {
      flex: 1 1 30%;
      background: #fefefe;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 10px;
      min-width: 280px;
    }
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .page-title {
      font-weight: bold;
      color: #007bff;
    }
    .selected-value-line {
      background-color: #e7f4ea;
      padding: 6px 10px;
      border-radius: 6px;
      border: 1px solid #c1eac5;
      margin: 5px 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .serial-number {
      font-weight: bold;
      margin-right: 8px;
    }
    .crossButton {
      color: red;
      font-weight: bold;
      cursor: pointer;
    }
    button[type="submit"] {
      margin-top: 20px;
      background-color: #28a745;
    }
    button[type="submit"]:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>

<h2>Modal ID: <?= esc($modal_id) ?></h2>

<?php echo form_open(get_uri("items/submit_page_params"), array("id" => "file-form", "class" => "general-form")); ?>

<!-- ✅ Hidden input for model number -->
  <input type="hidden" name="modelno" value="<?= esc($modelno ?? 'EF-001') ?>">
  
  <?php if (!empty($params) && is_array($params)): ?>
  <select id="cmd_parameter">
    <option value="">-- Select --</option>
    <?php foreach ($params as $param): ?>
      <option value="<?= esc($param) ?>"><?= esc($param) ?></option>
    <?php endforeach; ?>
  </select>
<?php else: ?>
  <p>No parameters found.</p>
<?php endif; ?>







  <label for="uiPageNo">Page No:</label>
  <select id="uiPageNo" onchange="togglePageNameInput()">
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

<?php if (!empty($submittedData)): ?>
  <pre><?php print_r($submittedData); ?></pre>
<?php endif; ?>

<script>
  const pageNamesMap = {};
  const selectedParams = {}; // prevent duplicate params per page

  function togglePageNameInput() {
    const pageNo = document.getElementById("uiPageNo").value;
    const input = document.getElementById("pageNameInput");
    input.style.display = (pageNo !== "1" && !pageNamesMap[pageNo]) ? "inline-block" : "none";
  }

  function ensurePageBlock(pageNo, pageName = null) {
    const wrapper = document.getElementById("selectedValuesWrapper");
    if (document.getElementById(`page-${pageNo}`)) return;

    const title = (pageNo === "1") ? "Page 1" : (pageName || `Page ${pageNo}`);
    const div = document.createElement("div");
    div.className = "page-selected-values";
    div.id = `page-${pageNo}`;
    div.dataset.page = pageNo;

    div.innerHTML = `
      <div class="page-header">
        <span class="page-title" id="title-${pageNo}">${title}</span>
        <button type="button" onclick="editPageTitle('${pageNo}')">Edit</button>
      </div>
      <div class="param-list" id="list-${pageNo}"></div>
    `;

    wrapper.appendChild(div);
  }

  function addSelectedParameter() {
    const param = document.getElementById("cmd_parameter").value;
    const pageNo = document.getElementById("uiPageNo").value;
    const nameInput = document.getElementById("pageNameInput");

    if (!param || !pageNo) return alert("Select both parameter and page number");

    if (!selectedParams[pageNo]) selectedParams[pageNo] = new Set();

    if (selectedParams[pageNo].has(param)) {
      return alert("This parameter already exists on this page");
    }

    if (pageNo !== "1" && !pageNamesMap[pageNo]) {
      if (!nameInput.value.trim()) return alert("Enter a name for this page");
      pageNamesMap[pageNo] = nameInput.value.trim();
      nameInput.value = '';
      nameInput.style.display = "none";
    }

    ensurePageBlock(pageNo, pageNamesMap[pageNo]);

    const list = document.getElementById(`list-${pageNo}`);
    const count = list.children.length + 1;

    const line = document.createElement("div");
    line.className = "selected-value-line";
    line.dataset.param = param;
    line.innerHTML = `
      <span><span class="serial-number">${count}.</span>${param}</span>
      <span class="crossButton" onclick="removeParam(this, '${param}', '${pageNo}')">×</span>
    `;
    list.appendChild(line);

    selectedParams[pageNo].add(param);

    document.getElementById("hiddenInputs").insertAdjacentHTML("beforeend", `
      <input type="hidden" name="cmd_parameter[]" value="${param}" data-page="${pageNo}" data-param="${param}">
      <input type="hidden" name="uiPageNo_cmd[]" value="${pageNo}" data-page="${pageNo}" data-param="${param}">
      <input type="hidden" name="page_name[${pageNo}]" value="${pageNamesMap[pageNo] || 'Page ' + pageNo}" data-page="${pageNo}">
    `);
  }

  function removeParam(el, param, pageNo) {
    el.parentElement.remove();
    selectedParams[pageNo].delete(param);

    document.querySelectorAll(`#hiddenInputs input[data-page="${pageNo}"][data-param="${param}"]`).forEach(i => i.remove());

    const list = document.getElementById(`list-${pageNo}`);
    Array.from(list.children).forEach((child, i) => {
      child.querySelector(".serial-number").textContent = `${i + 1}.`;
    });

    if (list.children.length === 0) {
      document.getElementById(`page-${pageNo}`).remove();
      delete selectedParams[pageNo];
      delete pageNamesMap[pageNo];
      togglePageNameInput();
    }
  }

  function editPageTitle(pageNo) {
    const titleSpan = document.getElementById(`title-${pageNo}`);
    const oldName = titleSpan.textContent;
    titleSpan.classList.add("hidden");

    const container = titleSpan.parentElement;
    const input = document.createElement("input");
    input.type = "text";
    input.value = oldName;

    const saveBtn = document.createElement("button");
    saveBtn.textContent = "Save";
    saveBtn.onclick = () => {
      const newName = input.value.trim();
      if (!newName) return alert("Name can't be empty");
      pageNamesMap[pageNo] = newName;
      titleSpan.textContent = newName;
      cleanup();
    };

    const cancelBtn = document.createElement("button");
    cancelBtn.textContent = "Cancel";
    cancelBtn.onclick = cleanup;

    container.appendChild(input);
    container.appendChild(saveBtn);
    container.appendChild(cancelBtn);

    function cleanup() {
      input.remove();
      saveBtn.remove();
      cancelBtn.remove();
      titleSpan.classList.remove("hidden");
    }
  }
</script>

</body>
</html>
