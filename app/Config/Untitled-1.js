 <div class="control-group">
<div>
  <h3>🖨 Printer Info</h3>
  <p id="printerStatus">Loading...</p>

  <select id="printerSelect" class="form-control" style="width: 300px;"></select>

  <button id="getInfoBtn" class="btn btn-primary" style="margin-left: 10px;">Get DPI Info</button>

  <pre id="dpiInfo" style="margin-top: 15px; background:#f5f5f5; padding:10px;"></pre>
</div>
</div><DIV>

  document.addEventListener("DOMContentLoaded", () => {
  const printerSelect = document.getElementById("printerSelect");
  const statusBox = document.getElementById("printerStatus");
  const dpiInfoBox = document.getElementById("dpiInfo");
  const getInfoBtn = document.getElementById("getInfoBtn");

  statusBox.textContent = "🔄 Loading printers...";

  fetch("/items/printer_list")
    .then(r => {
      console.log("Fetch response:", r);
      if (!r.ok) throw new Error("HTTP " + r.status);
      return r.json();
    })
    .then(data => {
      console.log("Printer list response:", data);
      if (data.success && data.printers.length > 0) {
        statusBox.textContent = "✅ Connected printers loaded.";
        printerSelect.innerHTML = '<option value="">Select Printer</option>';
        data.printers.forEach(name => {
          const opt = document.createElement("option");
          opt.value = name.trim();
          opt.textContent = name.trim();
          printerSelect.appendChild(opt);
        });
      } else {
        statusBox.textContent = "⚠️ No connected printers found.";
      }
    })
    .catch(err => {
      console.error("Printer fetch error:", err);
      statusBox.textContent = "❌ Error loading printers.";
    });

  getInfoBtn.addEventListener("click", () => {
    const printerName = printerSelect.value;
    if (!printerName) return alert("Please select a printer first!");
    dpiInfoBox.textContent = "⏳ Fetching printer info...";

    fetch("/items/get_printer_info", {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      body: JSON.stringify({printerName})
    })
      .then(r => r.json())
      .then(data => {
        console.log("Printer info:", data);
        if (data.success) {
          const i = data.info;
          dpiInfoBox.textContent = `🖨 Printer: ${i.printer}
📏 DPI: ${i.dpiX} x ${i.dpiY}
📄 Paper Size: ${i.paperSize}`;
        } else {
          dpiInfoBox.textContent = "⚠️ " + data.message;
        }
      })
      .catch(err => {
        console.error("Error fetching DPI info:", err);
        dpiInfoBox.textContent = "❌ Error fetching printer info.";
      });
  });


  // ✅ Get DPI Info button click
  getInfoBtn.addEventListener("click", async function () {
    const printerName = printerSelect.value;
    if (!printerName) {
      alert("Please select a printer first!");
      return;
    }

    console.log("🔍 Fetching info for:", printerName);
    dpiInfoBox.textContent = "⏳ Fetching printer info...";

    try {
      const res = await fetch("http://localhost/items/printer_info", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ printerName }),
      });

      if (!res.ok) throw new Error("HTTP error " + res.status);

      const data = await res.json();
      console.log("🖨 Printer Info Response:", data);

      if (data.success && data.info) {
        const info = data.info;
        dpiInfoBox.textContent =
          `🖨 Printer: ${info.printer}\n📏 DPI: ${info.dpiX} x ${info.dpiY}\n📄 Paper Size: ${info.paperSize}`;
      } else {
        dpiInfoBox.textContent = "⚠️ No info found for this printer.";
      }
    } catch (err) {
      console.error("🚨 Info fetch error:", err);
      dpiInfoBox.textContent = "❌ Error fetching printer info.";
    }
  });
});



<!-- FRONTED CODE ❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌ -->


public function listPrinters()
    {
        // PowerShell se printer info nikaalna
        $output = shell_exec('powershell "Get-CimInstance Win32_Printer | Select Name, WorkOffline, PrinterStatus"');
        $lines = array_filter(explode("\n", trim($output)));

        // Header aur separator line hata do
        $printers = [];
        foreach ($lines as $index => $line) {
            if ($index < 2) continue; // skip header + separator
            $cols = preg_split('/\s{2,}/', trim($line)); // split by 2+ spaces

            if (count($cols) >= 3) {
                $printers[] = [
                    'name' => trim($cols[0]),
                    'workOffline' => trim($cols[1]),
                    'status' => trim($cols[2])
                ];
            }
        }

        return $this->response->setJSON([
            'success' => !empty($printers),
            'printers' => $printers
        ]);
    }