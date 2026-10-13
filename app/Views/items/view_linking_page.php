<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token-name" content="<?= csrf_token() ?>">
<meta name="csrf-token-value" content="<?= csrf_hash() ?>">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <title>Model Linking Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .badge {
      font-size: 0.9rem;
      padding: 0.5em 0.75em;
      display: inline-flex;
      align-items: center;
    }
    .badge button {
      border: none;
      background: none;
      color: #fff;
      margin-left: 6px;
      font-weight: bold;
      line-height: 1;
    }
    .param-actions {
      display: flex;
      gap: 8px;
      margin-top: 0.5rem;
    }
    .card-header h4 { margin: 0; }
  </style>
</head>
<body>

<?php if (!empty($model_data)): ?>
<?php
  $page_names = json_decode($model_data['page_names'], true);
  $linking_data = json_decode($model_data['linking_data'], true);
?>
<div class="container mt-5">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h4>Model: <?= esc($model_data['model_name']) ?></h4>
    </div>
    <div class="card-body">
      <table class="table table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th style="width:10%">Page No.</th>
            <th style="width:30%">Page Name</th>
            <th>Parameters</th>
          </tr>
        </thead>
       <tbody>
  <?php foreach ($page_names as $page_no => $page_name): ?>
  <?php $params = $linking_data[$page_name] ?? []; ?>
  <tr data-page="<?= esc($page_no) ?>">
    <td><?= esc($page_no) ?></td>
    <td>
      <!-- Static text -->
      <span class="page-name-text"><?= esc($page_name) ?></span>
      <!-- Hidden input for editing -->
     <input 
  type="text"
  id="page_name_input_<?= esc($page_no) ?>"
  name="page_name_input_<?= esc($page_no) ?>"
  class="form-control page-name-input d-none"
  value="<?= esc($page_name) ?>"
  style="display:inline-block;width:auto;">

      <!-- Buttons -->
      <button class="btn btn-sm btn-primary btn-edit-name">✏️ Edit</button>
      <button class="btn btn-sm btn-success btn-save-name d-none">💾 Save</button>
      <button class="btn btn-sm btn-secondary btn-cancel-name d-none">❌ Cancel</button>
    </td>
    <td>
      <div class="existing-params mb-2">
        <?php foreach ($params as $p): ?>
          <span class="badge bg-secondary me-2 mb-2">
            <?= esc($p) ?>
            <button class="btn-delete-param" data-param="<?= esc($p) ?>">✖</button>
          </span>
        <?php endforeach; ?>
      </div>
      <div class="param-actions">
        <select class="form-select param-select" style="width:auto;">
          <option value="">-- Select Parameter --</option>
          <?php foreach ($all_params as $ap): ?>
  <option value="<?= esc($ap) ?>"><?= esc($ap) ?></option>
<?php endforeach; ?>
        </select>
        <button class="btn btn-success btn-add-param">+ Add</button>
      </div>
    </td>
  </tr>
  <?php endforeach; ?>
</tbody>

    </div>
  </div>
</div>
<?php else: ?>
<div class="container mt-5">
  <div class="alert alert-warning">No model data available.</div>
</div>
<?php endif; ?>

<script>
$(function() {
  // ---- CSRF ----
  const csrfName = $('meta[name="csrf-token-name"]').attr('content');
  let csrfHash = $('meta[name="csrf-token-value"]').attr('content');




  // ✅ DELETE PARAM
  $(document).on('click', '.btn-delete-param', function() {
    const btn = $(this);
    const row = btn.closest('tr');
    const pageNo = row.data('page');
    const paramName = btn.data('param');

console.log("Attempting to delete:", {
  model_name: "<?= esc($model_data['model_name']) ?>",
  page_no: pageNo,
  param: paramName,
  csrf: csrfHash
});

    $.post("<?= site_url('items/delete_param') ?>", {
      model_name: "<?= esc($model_data['model_name']) ?>",
      page_no: pageNo,
      param: paramName,
      [csrfName]: csrfHash
    }, function(res) {
      if (res.status === 'success') {
        btn.closest('.badge').remove();
        csrfHash = res.csrfHash;
        $('meta[name="csrf-token-value"]').attr('content', csrfHash);
      } else {
        alert(res.message);
      }
    }, 'json').fail(function() {
      alert('❌ Network error while deleting parameter.');
    });
  });




  // ✅ ADD PARAM (prevent duplicate)
  $(document).on('click', '.btn-add-param', function() {
    const row = $(this).closest('tr');
    const pageNo = row.data('page');
    const select = row.find('.param-select');
    const paramName = select.val();

    if (!paramName) {
      alert('⚠️ Please select a parameter.');
      return;
    }

    // duplicate check
    let duplicate = false;
    row.find('.existing-params .badge').each(function() {
      if ($(this).text().trim().includes(paramName)) {
        duplicate = true;
        return false;
      }
    });
    if (duplicate) {
      alert('⚠️ This parameter is already added.');
      return;
    }

    $.post("<?= site_url('items/add_param') ?>", {
      model_name: "<?= esc($model_data['model_name']) ?>",
      page_no: pageNo,
      param: paramName,
      [csrfName]: csrfHash
    }, function(res) {
      if (res.status === 'success') {
        row.find('.existing-params').append(`
          <span class="badge bg-secondary me-2 mb-2">
            ${paramName}
            <button type="button" class="btn-delete-param btn btn-sm btn-link text-danger" data-param="${paramName}">✖</button>
          </span>
        `);
        csrfHash = res.csrfHash;
        $('meta[name="csrf-token-value"]').attr('content', csrfHash);
        select.val('');
      } else {
        alert(res.message);
      }
    }, 'json').fail(function() {
      alert('❌ Network error while adding parameter.');
    });
  });

  // ✅ EDIT PAGE NAME
  $(document).on('click', '.btn-edit-name', function() {
    const td = $(this).closest('td');
    td.find('.page-name-text').addClass('d-none');
    td.find('.page-name-input').removeClass('d-none').focus();
    td.find('.btn-edit-name').addClass('d-none');
    td.find('.btn-save-name, .btn-cancel-name').removeClass('d-none');
  });

  // ✅ CANCEL EDIT
  $(document).on('click', '.btn-cancel-name', function() {
    const td = $(this).closest('td');
    const oldVal = td.find('.page-name-text').text().trim();
    td.find('.page-name-input').val(oldVal).addClass('d-none');
    td.find('.page-name-text').removeClass('d-none');
    td.find('.btn-save-name, .btn-cancel-name').addClass('d-none');
    td.find('.btn-edit-name').removeClass('d-none');
  });

  // ✅ SAVE NEW PAGE NAME
  $(document).on('click', '.btn-save-name', function() {
  const td = $(this).closest('td');
  const row = td.closest('tr');
  const pageNo = row.data('page');
  const newName = td.find('.page-name-input').val().trim();

  if(!newName){
    alert('⚠️ Page name cannot be empty!');
    return;
  }

  const csrfName = $('meta[name="csrf-token-name"]').attr('content');
  const csrfHash = $('meta[name="csrf-token-value"]').attr('content');

  $.ajax({
    url: "<?= site_url('items/update_page_name') ?>",
    method: "POST",
    dataType: "json",
    data: {
      model_name: "<?= esc($model_data['model_name']) ?>",
      page_no: pageNo,
      new_page_name: newName,
      [csrfName]: csrfHash
    },
    success: function(res) {
      console.log(res);
      if (res.status === 'success') {
        td.find('.page-name-text').text(newName).removeClass('d-none');
        td.find('.page-name-input').addClass('d-none');
        td.find('.btn-save-name, .btn-cancel-name').addClass('d-none');
        td.find('.btn-edit-name').removeClass('d-none');
        $('meta[name="csrf-token-value"]').attr('content', res.csrfHash);
        alert('✅ Updated!');
      } else {
        alert('❌ ' + res.message);
      }
    },
    error: function(xhr) {
      console.error(xhr.responseText);
      alert('❌ Network error.');
    }
  });
});


});
</script>

</body>
</html>
