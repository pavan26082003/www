<style>
    .input-group { margin-bottom: 12px; }
    label { font-weight: 600; }
</style>

<h3>API Frame Format Settings</h3>

<form id="frameForm">
    <div class="input-group">
        <label>1) No. of Commands</label>
        <select id="commands" class="form-control" required>
            <?php for($i=1;$i<=10;$i++) echo "<option value='$i'>$i</option>"; ?>
        </select>
    </div>

    <div class="input-group">
        <label>2) No. of Responses</label>
        <select id="responses" class="form-control" required>
            <?php for($i=1;$i<=10;$i++) echo "<option value='$i'>$i</option>"; ?>
        </select>
    </div>

    <div class="input-group">
        <label>3) No. of Errors</label>
        <select id="errors" class="form-control" required>
            <?php for($i=1;$i<=10;$i++) echo "<option value='$i'>$i</option>"; ?>
        </select>
    </div>

    <button type="submit" id="submitBtn" class="btn btn-primary btn-block">
        Proceed
    </button>
</form>

<script>
document.getElementById("frameForm").addEventListener("submit", function(e){
    e.preventDefault();

    const btn = document.getElementById("submitBtn");
    btn.disabled = true;
    btn.innerText = "Saving...";

    const payload = {
        item_id: "<?= $item_id ?>",
        no_of_commands: document.getElementById("commands").value,
        no_of_responses: document.getElementById("responses").value,
        no_of_errors: document.getElementById("errors").value
    };

    fetch("<?= get_uri('items/save_api_frame_size') ?>", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    })
    .catch(() => alert("Server error"))
    .finally(() => {
        btn.disabled = false;
        btn.innerText = "Proceed";
    });
});
</script>

