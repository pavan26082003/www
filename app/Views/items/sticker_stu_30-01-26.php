 <?php
// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON body
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No data received'
        ]);
        exit;
    }

    // Example: Save data into DB (here just printing)
    // In real project, insert into your database
    // foreach ($data as $field) { ... insert into DB ... }

    echo json_encode([
        'status' => 'success',
        'message' => 'Fields saved successfully!',
        'receivedData' => $data
    ]);
    exit;
}
?>


 <!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=0.8">

  <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0">  -->
    <title>Commander Label Designer</title>
   
<!-- <script defer src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script> -->
<script defer src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/bwip-js/dist/bwip-js-min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<!-- <script defer src="printer.js" defer></script> -->


<!-- <script src="your-script.js"></script> -->




    <style>
      /* General Styles */
body {
     zoom: 80%;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f7fa;
    margin: 0;
    padding: 20px;
    color: #333;
}
.extra-fields-container {
     border: 1px dashed #eeeeee !important; /* ✅ Ultra light gray */
      background-color: #fcfcfc;
  }
h1, h2 {
    color: #222;
    margin-bottom: 16px;
}

.labels-container {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 0.3cm !important;          /* thoda kam gap for alignment */
    margin-bottom: 20px !important;
    box-sizing: border-box !important;
}

.labels-container.horizontal {
    flex-direction: row !important;
}

.labels-container.vertical {
    flex-direction: column !important;
}

.label-box {
    width: 4.6cm !important;        /* thoda chhota (safe zone for border) */
    height: 4.6cm !important;
    margin: 0.1cm !important;       /* safe margin around label */
    padding: 0 !important;
    border: 2px solid #000 !important; /* ✅ 2px solid border, full visible */
    box-sizing: border-box !important;
    background: #fff !important;    /* safe white background */
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;    /* avoid cut */
}

.label-box canvas {
    width: calc(100% - 4px) !important; /* andar content border se chipke na */
    height: calc(100% - 4px) !important;
    display: block !important;
    background: #fff !important;
    box-sizing: border-box !important;
}

#labelsContainer {
  display: none;
}
@media print {
  #labelsContainer {
    display: block;
  }
}

@media print {
  #labelsContainer {
    display: none !important;  /* preview container hide only on print */
  }
}
/* Wrapper for form + canvas */
.designer-wrapper {
  display: flex !important;
  flex-direction: row !important;
  align-items: flex-start !important;
  justify-content: space-between !important;
  gap: 20px !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

/* .label-preview.no-print {
    position: sticky;   
    top: 10px;          
    background: #fff;   
    padding: 10px 20px;
    z-index: 999;     
    border-bottom: 2px solid #ccc;  
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
} */
.label-preview.no-print {
  position: sticky;
  top: 0;                      /* top se chipka rahega */
  align-self: flex-start;
  background: #fff;
  padding: 10px 20px;
  z-index: 999;
  border-bottom: 2px solid #ccc;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  height: 100vh;               /* full viewport height */
  overflow: hidden;            /* andar kuch scroll na ho */
}



/* Left Form */
.designer-form {
  flex: 1 !important;
  min-width: 400px !important;   /* itna chhota hoke bhi collapse nahi hoga */
  max-width: 50% !important;     /* half screen tak */
  overflow-y: auto !important;
  box-sizing: border-box !important;
}

/* Right Canvas */
.designer-canvas {
  flex: 1 !important;
  min-width: 400px !important;
  max-width: 50% !important;
  text-align: center !important;
  box-sizing: border-box !important;
}

/* Canvas itself responsive */
.designer-canvas canvas {
  max-width: 100% !important;
  height: auto !important;
}

/* ✅ Mobile view (stack) */
@media (max-width: 768px) {
  .designer-wrapper {
    flex-direction: column !important;
  }
  .designer-form, .designer-canvas {
    max-width: 100% !important;
    min-width: auto !important;
  }
}


  #editorToolbar {
  width: 100%;
  background: #f8f9fa;
  padding: 6px 10px;
  border-radius: 6px;
  margin-bottom: 8px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column; /* 2 rows */
  gap: 6px;
}

/* Each row styling */
.toolbar-row {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
}

/* Dropdowns compact */
#fontSizePicker { width: 55px; font-size: 12px; padding: 2px; }
#fontFamilyPicker { width: 110px; font-size: 12px; padding: 2px; }
#fontStylePicker { width: 90px; font-size: 12px; padding: 2px; }
#fontColorPicker { width: 35px; height: 28px; padding: 0; }

/* Buttons */
#editorToolbar button {
  padding: 4px 6px;
  font-size: 13px;
  border-radius: 6px;
  border: 1px solid #ddd;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
}
#editorToolbar button:hover { background: #e9ecef; }

.delete-btn { background: #f55 !important; color: #fff !important; }
.apply-btn { background: #28a745 !important; color: #fff !important; }


.app-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.controls {
    flex: 1 1 300px; /* Allow controls to grow and shrink */
    background: #fff;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
}



  @media (max-width: 768px) {
      .label-preview-container {
          width: 100%;
          padding: 10px; /* Adjust padding as needed */
          overflow: auto; /* Prevent overflow */
      }
      .label-preview {
          width: auto; /* or 100% */
          height: auto;
      }
      /* Adjust font size, buttons, etc. */
      .btn {
          width: 100%; /* Full-width buttons */
      }
  }
  
.label-box {
    flex: 1 1 calc(50% - 14px); /* Two per row on larger screens */
    max-width: calc(50% - 14px);
    height: auto;
    background: #fff;
    border: 1px solid #000000;
    box-sizing: border-box;
}

/* Control Group Styles */
.control-group {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.control-group label {
    font-weight: 600;
    margin-right: 10px;
}

/* Button Styles */
button {
    padding: 10px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button.print-btn {
    background-color: #2196F3;
    color: #fff;
}

button.lock-btn {
    background-color: #ff9800;
    color: #fff;
}

/* Media Queries for Responsiveness */
@media (max-width: 768px) {
    .app-container {
        flex-direction: column; /* Stack controls and preview */
    }

    .controls {
        width: 100%; /* Full width for controls */
    }

    .label-box {
        flex: 1 1 100%; /* Full width for label boxes */
        max-width: 100%;
    }

    .label-preview {
        width: 100%; /* Full width for label preview */
    }
}

@media print {
    .no-print {
        display: none;
    }

    body {
        margin: 0;
        padding: 0;
    }

    .labels-container {
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
        gap: 20px !important;
        padding: 0 !important;
    }

    .label-box {
        flex: 0 0 calc(50% - 20px) !important;
        max-width: calc(50% - 20px) !important;
        page-break-inside: avoid !important;
        border: 1px solid #000000 !important;
    }
}

      /* Main layout logic */
.labels-container.horizontal {
  flex-direction: row !important;
  flex-wrap: wrap !important;
}

.labels-container.vertical {
  flex-direction: column !important;
}

/* Print ke liye bhi same apply kar */
@media print {
  .labels-container.horizontal {
    flex-direction: row !important;
    flex-wrap: wrap !important;
  }

  .labels-container.vertical {
    flex-direction: column !important;
  }
}

      @media print {
  .no-print {
    display: none !important;
  }

  

  .labels-container {
    display: flex !important;
    flex-wrap: wrap !important;
    flex-direction: row !important;
    justify-content: center !important;
    gap: 12px !important;
    padding: 10px !important;
  }

  .label-box {
    flex: 0 0 calc(50% - 14px) !important;
    max-width: calc(50% - 14px) !important;
    page-break-inside: avoid !important;
    border: 1px solid #000000 !important;
    height: auto !important;
  }

  canvas {
    max-width: 100% !important;
    height: auto !important;
    box-sizing: border-box;
  }
}
        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .app-container {
            display: flex;
            gap: 20px;
        }
        .controls {
            width: 300px;
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            height: fit-content;
        }


/* Individual label box */
.label-box {
  padding: 0 !important;
  margin: 0 !important;
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  overflow: hidden !important;
}




        /* 🔥 Toolbar-like layout for control-group */
.control-group {
  display: flex !important;
  align-items: center !important;
  justify-content: flex-start !important;
  gap: 16px !important;
  flex-wrap: nowrap !important;
   background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  padding: 0 !important;
  border-radius: 0 !important;
  
}

/* ✅ Font size & color labels inline */
.control-group label {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  font-weight: 600 !important;
  font-size: 14px !important;
  color: #333 !important;
  margin: 0 !important;
}

/* ✅ Select and color input compact */
.control-group select,
.control-group input[type="color"] {
  padding: 6px 8px !important;
  font-size: 14px !important;
  border: 1px solid #ccc !important;
  border-radius: 6px !important;
  cursor: pointer !important;
  background: #f9f9f9 !important;
}

/* ✅ Move buttons inline */
#moveButtons {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  margin-left: auto !important; /* push them to right side */
}

#moveButtons button {
  background: #e0e0e0 !important;
  border: 1px solid #ccc !important;
  border-radius: 6px !important;
  width: 36px !important;
  height: 36px !important;
  font-size: 18px !important;
  cursor: pointer !important;
  transition: background-color 0.3s !important;
}

#moveButtons button:hover {
  background: #d5d5d5 !important;
}

/* ✅ Apply to All button aligned to right side like navbar action */
.control-group + button {
  margin-left: 16px !important;
  padding: 8px 14px !important;
  font-size: 14px !important;
  font-weight: 600 !important;
  border-radius: 6px !important;
  background: #007bff !important;
  color: #fff !important;
  border: none !important;
  cursor: pointer !important;
  transition: background-color 0.3s !important;
}

.control-group + button:hover {
  background: #0056b3 !important;
}

        /* Toolbar ko ek hi line me lane ke liye */
.label-preview .control-group {
  display: flex !important;
  flex-wrap: nowrap !important; /* ek hi line me rakhe */
  align-items: center !important;
  justify-content: center !important;
  gap: 10px !important;
  background: #ffffff !important;
  padding: 8px 15px !important;
  border-radius: 8px !important;
  border: 1px solid #ddd !important;
  margin-bottom: 15px !important;
}

/* Buttons ko ek jaisa look dene ke liye */
.label-preview .control-group button {
  min-width: 40px !important;
  min-height: 40px !important;
  font-size: 16px !important;
  border-radius: 8px !important;
}

/* Move buttons ko bhi same line me la */
#moveButtons {
  display: flex !important;
  flex-wrap: nowrap !important;
  gap: 8px !important;
  margin-top: 0 !important;
}

/* Move buttons bhi toolbar me chipka do */
.control-group #moveButtons {
  display: flex !important;
  flex-wrap: nowrap !important;
  gap: 8px !important;
  margin: 0 0 0 10px !important;
}


#previewCanvas {
  display: block !important;
  margin: 20px auto !important;
  border: 2px dashed #e0e0e0 !important;   /* 🔥 dark grey and light grey */
  border-radius: 8px !important;
  background: #fff !important;
  max-width: 100% !important;
  overflow: hidden !important;
}

#hiddenLabels canvas {
  border: none;  /* hidden labels pe koi border nahi */
}


/* Agar parent container me scroll aa raha hai to ye laga */
.label-preview {
  overflow: visible !important; /* preview section scroll na ho */
}

      

/* Button base style */
.label-preview button {
  padding: 8px 12px !important;
  font-size: 14px !important;
  font-weight: 600 !important;
  border: none !important;
  border-radius: 6px !important;
  cursor: pointer !important;
  transition: background 0.3s ease, transform 0.2s ease !important;
  color: #fff !important;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
}

/* Specific button colors */
.label-preview button:nth-of-type(1) { background: #007bff !important; } /* B */
.label-preview button:nth-of-type(2) { background: #28a745 !important; } /* I */
.label-preview button:nth-of-type(3) { background: #e83e8c !important; } /* U */
.label-preview button:nth-of-type(4) { background: #17a2b8 !important; } /* Apply */

/* Hover effects */
.label-preview button:hover {
  opacity: 0.9 !important;
  transform: translateY(-2px) !important;
}

/* Move buttons */
#moveButtons {
  display: flex !important;
  justify-content: center !important;
  gap: 10px !important;
  margin-top: 15px !important;
}

#moveButtons button {
  background: #ffc107 !important;
  color: #333 !important;
  font-size: 16px !important;
  border-radius: 50% !important;
  width: 40px !important;
  height: 40px !important;
  line-height: 40px !important;
  padding: 0 !important;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
}

#moveButtons button:hover {
  background: #e0a800 !important;
  transform: scale(1.1) !important;
}

/* Canvas styling */
#previewCanvas {
  display: block !important;
  margin: 20px auto !important;
  border: 2px dashed #e0e0e0 !important;
  border-radius: 8px !important;
  background: #fff !important;
} */

    
.label-box {
    width: 500px;
    height: 300px;
}

        .labels-container {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .labels-container.horizontal {
            flex-direction: row;
        }
        .labels-container.vertical {
            flex-direction: column;
        }
   
        .control-group {
            margin-bottom: 15px;
        }
        label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
        }
        input, select, textarea {
            padding: 5px;
            width: 150px;
        }
        textarea {
            height: 60px;
            resize: vertical;
        }
        button {
            padding: 8px 15px;
            margin: 5px 0;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        button.print-btn {
            background: #2196F3;
        }
        button.print-btn:hover {
            background: #0b7dda;
        }
        button.lock-btn {
            background: #ff9800;
        }
        button.lock-btn:hover {
            background: #e68a00;
        }
        @media print {
  .no-print {
    display: none;
  }

  body {
    margin: 0;
    padding: 0;
  }

  .print-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0 auto;
  }

  .print-table td {
    padding: 6px 4px; /* Less spacing for more labels */
    vertical-align: top;
  }

  .sticker-label {
    width: 300px;
    height: auto;
    border: 1px solid #000000;
    padding: 8px;
    font-size: 12px;
    box-sizing: border-box;
    display: inline-block;
    page-break-inside: avoid;
  }
}

    
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f7fa;
    margin: 0;
    padding: 20px;
    color: #333;
  }

h1, h2 {
  color: #222;
  margin-bottom: 16px;
}

.app-container {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.controls {
  width: 320px;
  background: #fff;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
}

.control-group {
  margin-bottom: 16px;
}

label {
  display: block;
  font-weight: 600;
  margin-bottom: 6px;
}

input[type="text"],
input[type="number"],
input[type="email"],
select,
textarea {
  padding: 10px 14px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
  width: 100%;
  box-sizing: border-box;
  transition: border-color 0.3s;
}

input:focus,
select:focus,
textarea:focus {
  border-color: #007bff;
  outline: none;
}

button {
  background-color: #007bff;
  color: #fff;
  padding: 10px 16px;
  margin-top: 6px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 15px;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #0056b3;
}

button.print-btn {
  background-color: #2196F3;
}

button.print-btn:hover {
  background-color: #0b7dda;
}

button.lock-btn {
  background-color: #ff9800;
}

button.lock-btn:hover {
  background-color: #e68a00;
}



.labels-container.horizontal {
  flex-direction: row;
}

.labels-container.vertical {
  flex-direction: column;
}


.label-box {
  flex: 0 0 calc(50% - 20px); /* two per row */
  max-width: calc(50% - 20px);
  height: auto;
  background: #fff;
  border: 1px solid #000000;
  box-sizing: border-box;
}

@media print {
  .labels-container {
    display: flex !important;
    flex-wrap: wrap !important;
    justify-content: center !important;
    gap: 20px !important;
    padding: 0 !important;
  }

  .label-box {
    flex: 0 0 calc(50% - 20px) !important;
    max-width: calc(50% - 20px) !important;
    page-break-inside: avoid !important;
    border: 1px solid #000000 !important;
  }
}

@media (max-width: 768px) {
  .app-container {
    flex-direction: column;
  }

  .controls {
    width: 100%;
  }


  h1, h2, h3 {
    color: #222;
    margin-bottom: 16px;
  }

  .container {
    max-width: 800px;
    margin: 20px auto;
    background-color: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
  }

  form {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  label {
    font-weight: 600;
    margin-bottom: 4px;
  }

  input[type="text"],
  input[type="number"],
  input[type="email"],
  select,
  textarea {
    padding: 10px 14px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s;
    width: 100%;
    box-sizing: border-box;
  }

  input:focus,
  select:focus,
  textarea:focus {
    border-color: #007bff;
    outline: none;
  }

  button {
    background-color: #007bff;
    color: #fff;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
  }

  button:hover {
    background-color: #0056b3;
  }

  .qr-display {
    text-align: center;
    margin-top: 30px;
    padding: 20px;
    border: 1px dashed #ccc;
    border-radius: 12px;
    background-color: #fafafa;
  }

  .qr-display img {
    max-width: 100%;
    height: auto;
  }

  .success-message, .error-message {
    padding: 12px 16px;
    border-radius: 8px;
    margin-top: 16px;
    font-weight: 500;
  }

  .success-message {
    background-color: #d4edda;
    color: #155724;
  }

  .error-message {
    background-color: #f8d7da;
    color: #721c24;
  }



#applyBox {
  transform: translateX(40px); /* 👈 Move right */
}

#canvasPreview {
  width: 600px !important;
  height: 400px !important;
  border: 1px solid #000 !important;
  margin-bottom: 20px !important;
}
.label-preview {
  width: 100%;
  max-width: 820px;
  padding: 20px;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.preview-box {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 320px; /* slightly taller than canvas */
  border: 1px dashed #eee;!important;
  background-color: #fafafa;
  border-radius: 10px;
  margin-bottom: 20px;
}
.control-group {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 40px;
  margin-bottom: 15px;
}

/* Left side controls */
.font-controls {
  display: flex;
  align-items: center;
  gap: 12x;
}

/* Right side buttons */
.button-controls {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-left: auto;
}

.control-group label {
  font-size: 14px;
  font-weight: 500;
  color: #333;
}

.control-group select,
.control-group input[type="color"] {
  padding: 4px 8px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: #f9f9f9;
  outline: none;
}

.control-group button {
  background-color: #e0e0e0;
  color: #333;
  border: 1px solid #ccc;
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.control-group button:hover {
  background-color: #d5d5d5;
}
#moveButtons button {
  background-color: #e0e0e0;
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 6px 10px;
  font-size: 16px;
  cursor: pointer;
}
#moveButtons button:hover {
  background-color: #d5d5d5;
}


 .delete-btn {
       margin-left: 8px;
       background: red;
       color: white;
       border: none;
       border-radius: 4px;
       cursor: pointer;
       padding: 2px 6px;
     }
.modal {
    position: fixed;
    top: 0; left:0; right:0; bottom:0;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.modal-content {
    position: relative;
}
.modal-field-row input, .modal-field-row select {
    padding: 4px 6px;
}
.modal-field-row input,
.modal-field-row select {
    font-size: 14px;
}

.modal-field-row button:hover {
    background: #c0392b;
}

.modal-content h3 {
    margin-bottom: 15px;
    font-size: 18px;
    color: #333;
}
/* Canvas ke parent ko sticky banado */
.preview-canvas-wrapper {
  position: sticky;
  top: 10px;   /* jitna gap chahiye upar se */
  align-self: flex-start;
}
/* .label-preview-box {
  width: 950px;
  margin: 20px auto;
  padding: 15px;
  border: 2px solid #333;
  border-radius: 8px;
  background: #fdfdfd;
  box-shadow: 0 3px 8px rgba(0,0,0,0.2);
} */


/* ============================
   FINAL RESPONSIVE LAYOUT
   ============================ */

.responsive-shell {
  display: flex;
  width: 100%;
  gap: 16px;
  box-sizing: border-box;
}

/* Left panel */
.responsive-shell .app-container {
  flex: 1;
  min-width: 380px;
  max-width: 50%;
}

/* Right panel */
.responsive-shell .label-preview {
  flex: 1;
  min-width: 380px;
  max-width: 50%;
}

/* Mobile & Tablet */
@media (max-width: 992px) {
  .responsive-shell {
    flex-direction: column;
  }

  .responsive-shell .app-container,
  .responsive-shell .label-preview {
    max-width: 100%;
    width: 100%;
  }
}


/* Ensure the container can be hidden effectively */
.designer-canvas, #labelsContainer {
    transition: all 0.3s ease;
}


.status-box {
  width: 20px;
  height: 20px;
  border: 2px solid #bbb;
  border-radius: 4px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  font-size: 14px;
  font-weight: bold;
  line-height: 1;
}

/* GREEN TICK INSIDE */
.status-box.active {
  background-color: #28a745;
  border-color: #28a745;
  color: #fff;
}

/* Add these styles to your existing CSS */

/* Printer Settings Modal */
.printer-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.printer-modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.printer-settings-tabs {
    display: flex;
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
}

.printer-tab {
    padding: 10px 20px;
    cursor: pointer;
    border: 1px solid transparent;
    border-bottom: none;
    background: #f5f5f5;
    margin-right: 5px;
    border-radius: 4px 4px 0 0;
}

.printer-tab.active {
    background: #fff;
    border-color: #ddd;
    border-bottom: 1px solid #fff;
    margin-bottom: -1px;
}

.printer-tab-content {
    display: none;
    padding: 20px 0;
}

.printer-tab-content.active {
    display: block;
}

.printer-setting-group {
    margin-bottom: 15px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 4px;
    border-left: 4px solid #007bff;
}

.printer-setting-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.printer-setting-group select,
.printer-setting-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

.printer-status {
    padding: 10px;
    margin: 10px 0;
    border-radius: 4px;
    display: none;
}

.printer-status.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.printer-status.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.printer-test-btn {
    background-color: #28a745;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
}

.printer-test-btn:hover {
    background-color: #218838;
}

/* Printer Driver Info */
.driver-info {
    background: #e9ecef;
    padding: 10px;
    border-radius: 4px;
    margin: 10px 0;
    font-size: 14px;
}

/* Advanced Settings */
.advanced-settings {
    display: none;
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 4px;
}

.advanced-toggle {
    color: #007bff;
    cursor: pointer;
    margin-top: 10px;
    display: inline-block;
}

/* Print Queue Status */
.print-queue {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    margin-top: 10px;
}

.queue-item {
    padding: 8px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.queue-item:last-child {
    border-bottom: none;
}

.queue-status {
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 3px;
}

.queue-status.queued {
    background: #ffc107;
    color: #212529;
}

.queue-status.printing {
    background: #17a2b8;
    color: white;
}

.queue-status.completed {
    background: #28a745;
    color: white;
}

.queue-status.error {
    background: #dc3545;
    color: white;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .printer-modal-content {
        width: 95%;
        margin: 10% auto;
    }
    
    .printer-settings-tabs {
        flex-wrap: wrap;
    }
    
    .printer-tab {
        flex: 1;
        min-width: 120px;
        text-align: center;
        margin-bottom: 5px;
    }
}
canvas#previewCanvas {
    position: relative !important;
    pointer-events: auto !important;
}
.canvas-container {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    overflow: visible !important;
}


    </style>
</head>
<body>
  


<div id="modalOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:999;"></div>
<div id="qrDataModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
     background:#fff; padding:20px; border-radius:8px; width:400px; max-width:90%; z-index:1000;">
    <h2>QR Data</h2>
    <div id="qrDataContent" style="margin-top:10px;"></div>
    <button id="closeQRDataModal" style="margin-top:15px;">Close</button>
</div>



<!-- <a href="<?= site_url('items/sticker_form?id='.$id.'&modelno='.$modelno.'&modelname='.$modelname.'&category='.$category.'&open=edit') ?>">
    <button type="button">Edit Field</button>
</a> -->


<!--  
   <center><h1 class="no-print">Commander Label Designer</h1></center>  -->

   <div class="responsive-shell">

    
    <div class="app-container">
        <div class="controls no-print">
          <h1 class="no-print">Commander Label Designer</h1>
                 <button type="button" onclick="saveModalFields()">Save Sticker Form</button>

        <button id="showQRDataBtn">Show QR Data</button>
        <a href="<?= site_url('items/sticker_form?id='.$id.'&modelno='.$modelno.'&modelname='.$modelname.'&category='.$category.'&open=edit') ?>">
    <button type="button">Edit Field</button>
</a>
<button type="button" 
        id="savePreviewBtn"
        onclick="saveAndUpdatePreview()" 
        style="background-color: #4CAF50; color: white; margin-left: 10px; padding: 10px 16px; border: none; border-radius: 4px; cursor: pointer;">
     Save 
</button>
 
  <div class="form-container">

<div id="stickerModal" class="modal" style="display:none;">
    <div class="modal-content" style="width:320px; padding:20px; background:#fff; border-radius:8px;">
        <!-- <h3>Sticker Form: <?= esc($modelname ?? '') ?></h3> -->
        <div id="modalFieldsContainer"></div>
        
    </div>
</div>
</div>
       

 <input type="hidden" id="capacityInput" placeholder="Enter capacity">
<input type="hidden" id="rangeInput" placeholder="Enter working range">
<input type="hidden" id="outputInput" placeholder="Enter output">
<input type="hidden" id="domInput" placeholder="Date of manufacture">
<input type="hidden" id="mrpInput" placeholder="Enter MRP">
<input type="hidden" id="serialInput" placeholder="Serial number">

    
              <h2>Product Specifications</h2>
           
<input type="hidden" id="companyName" value="Factor Enovations Pvt Ltd">
<input type="hidden" id="website" value="efactor.tech">
<input type="hidden" id="address" value="Hyderabad -62">
<input type="hidden" id="tagline" value="">
        <div class="control-group">

            
    <label for="productName">Product Name:</label>
    <input type="text" id="productName" value="<?php echo htmlspecialchars($category); ?>" placeholder="Enter Product name" readonly>
 <!-- <button class="delete-btn" onclick="deleteField(this)">❌DELETE</button> -->
  </div>

<div class="control-group">
    <label for="modelInput">Model:</label>
    <input type="text" id="modelInput" value="<?php echo htmlspecialchars($modelname); ?>" placeholder="Enter model name" readonly>
 <!-- <button class="delete-btn" onclick="deleteField(this)">❌DELETE</button> -->
  </div>

          
            <!-- <label for="borderOption">Border</label>

<select id="borderOption">
  <option value="no">No</option>
  <option value="yes">Yes</option>
  
</select> -->



 <BR><BR>
<div class="control-group">
  <label for="includeBarcode">Include Barcode:</label>
  <select id="includeBarcode">
    <option value="">-- Select Type --</option>
    <!-- <option value="yes" selected>Yes</option> -->
       <option value="yes">Yes</option>
    <option value="no">No</option>
  </select>
   <!-- <button class="delete-btn" onclick="deleteField(this)">❌DELETE</button> -->
</div>


<label for="barcodeType">Barcode Type:</label>
<select id="barcodeType">
  <option value="">-- Select Type --</option>
  <option value="code128">Code128</option>
  <option value="code39">Code39</option>
  <option value="ean13">EAN13</option>
  <option value="upc">UPC-A</option>
  <option value="itf">ITF</option>
  <option value="gs1databar">GS1 DataBar</option>
</select>
 <!-- <button class="delete-btn" onclick="deleteField(this)">❌DELETE</button> -->

<br><br>


<label for="barcodeValue">Barcode Value:</label>
<div style="display: flex; align-items: center; gap: 5px; max-width: 250px;">
  <input type="text" id="barcodeValue" placeholder="Enter value" style="flex: 1; padding: 5px;">
  <button type="button" onclick="generateBarcode()" style="padding: 5px 10px; cursor: pointer;">
    ➡️
  </button>
   <!-- <button class="delete-btn" onclick="deleteField(this)">❌DELETE</button> -->
</div>




<label for="nodeDropdown">Select Node ID:</label>
<select id="nodeDropdown" onchange="document.getElementById('Nodeinput').value=this.value">
    <option value="">--Select Node ID--</option>
    <?php if (!empty($nodeIds)): ?>
        <?php foreach ($nodeIds as $node): ?>
            <option value="<?= $node; ?>"><?= $node; ?></option>
        <?php endforeach; ?>
    <?php endif; ?>
</select><br>

<label for="qrSizeInput">QR Size (mm):</label>
<select id="qrSizeInput">
   <option value="12"selected>12 mm</option>
  <option value="14">14 mm</option>
  <option value="16">16 mm</option>
  <option value="18">18 mm</option>
  <option value="20">20 mm</option>
  <option value="22">22 mm</option>
  <option value="24">24 mm</option>
   <option value="26">26 mm</option>
  <option value="28">28 mm</option>
  <option value="30">30 mm</option>
  <option value="32">32 mm</option>
   <option value="34">34 mm</option>
  <option value="36">36 mm</option>
  <option value="38">38 mm</option>
  <option value="40">40 mm</option>
   <option value="42">42 mm</option>
  <option value="44">44 mm</option>
  <option value="46">46 mm</option>
  <option value="48">48 mm</option>
   <option value="50">50 mm</option>
 
</select>


            <h2>Print Settings</h2>
            <div class="control-group">
                <!-- <label for="labelCount">Number of Labels:</label>
                <select id="labelCount">
                    <option value="1">1</option>
                    <option value="2" selected>2</option>
                    <option value="4">4</option>
                    <option value="6">6</option>
                     <option value="8">8</option> -->
                      <!-- <option value="10">10</option> if needed -->
                <!-- </select> -->
                 <!-- <button class="delete-btn" onclick="deleteField(this)">❌DELETE</button> -->
            <!-- </div>
            <div class="control-group">
                <label for="printLayout">Print Layout:</label>
                <select id="printLayout">
                    <option value="horizontal">Horizontal</option>
                    <option value="vertical">Vertical</option>
                </select> -->
                 <!-- <button class="delete-btn" onclick="deleteField(this)">❌DELETE</button> -->
            </div>
            <div class="control-group">
                <label for="logoUpload">Upload Logo:</label>
                <input type="file" id="logoUpload" accept="image/*">
                 <!-- <button class="delete-btn" onclick="deleteField(this)">❌DELETE</button> -->
            </div>
            <!-- <button onclick="updateAllLabels()">Update Labels</button>
            <button class="print-btn" onclick="printLabels()">Print Labels</button>
            <button class="lock-btn" onclick="toggleLockAll()">Lock/Unlock All</button><br> -->
    
            
<!-- <div style="padding:20px;">
  <h3>🖨 Printer Info</h3>
  <p id="printerStatus">Loading...</p>

  <select id="printerSelect" class="form-control" style="width: 300px;">
    <option value="">Select Printer</option>
  </select>

  <button id="getInfoBtn" class="btn btn-primary" style="margin-left: 10px;">Get DPI Info</button>

  <pre id="dpiInfo" style="margin-top: 15px; background: #f5f5f5; padding: 10px;"></pre>
</div> -->

<!-- <div>
  <h3>🖨 Printer Info</h3>
  <p id="printerStatus">Loading...</p>

  <select id="printerSelect" class="form-control" style="width: 300px; margin-bottom: 10px;"></select>

  <button id="getInfoBtn" class="btn btn-primary" style="margin-left: 10px;">Get DPI Info</button>

  <pre id="dpiInfo" style="margin-top: 15px; background: #f5f5f5; padding: 10px;"></pre>
</div> -->


        </div>
<!-- Preview Editor Box -->
<div class="label-preview no-print">
  <center><h3>Label Preview Editor</h3></center>

 

  <div class="control-group">
    
     <label>Font Size: 
    <select id="fontSizePicker">
      <option value="8">8</option>
      <option value="10" selected>10</option>
      <option value="12">12</option>
      <option value="14">14</option>
      <option value="16">16</option>
      <option value="18">18</option>
      <option value="20">20</option>
      <option value="22">22</option>
      <option value="24">24</option>
      <option value="26">26</option>
        <option value="28">28</option>
          <option value="30">30</option>
            <option value="32">32</option>
    </select>
  </label><br>
  <label>Font Color:
    <input type="color" id="fontColorPicker" value="#000000">
  </label>

  <button onclick="toggleBold()">B</button>
  <button onclick="toggleItalic()">I</button>
  <button onclick="toggleUnderline()">U</button>

  <!-- 🔥 New Font Style dropdown -->
  <select id="fontFamilyPicker">
    <option value="Arial" style="font-family: Arial;">Arial</option>
    <option value="Verdana" style="font-family: Verdana;">Verdana</option>
    <option value="Tahoma" style="font-family: Tahoma;">Tahoma</option>
    <option value="Times New Roman" style="font-family: 'Times New Roman';">Times New Roman</option>
    <option value="Georgia" style="font-family: Georgia;">Georgia</option>
    <option value="Courier New" style="font-family: 'Courier New';">Courier New</option>
    <option value="Impact" style="font-family: Impact;">Impact</option>
  </select>

  <div id="moveButtons">
    <button onclick="moveSelected('up')">⬆️</button>
    <button onclick="moveSelected('left')">⬅️</button>
    <button onclick="moveSelected('down')">⬇️</button>
    <button onclick="moveSelected('right')">➡️</button>
  </div>

  <button onclick="deleteSelected()">🗑 Delete Selected</button>
  <button onclick="undoDelete()">↩ Undo</button>


  <button onclick="syncPreviewToAll()">Apply to All</button>
</div>

<!-- BUTTONS -->
<div class="main-controls">
<button id="printPageBtn" style="margin-left:10px;">Create Print Page</button>
 <span class="status-box" id="chk-create">⬜</span>
<button id="previewLabelBtn" style="margin-left:10px;">Preview Label (New)</button>
 <span class="status-box" id="chk-preview">⬜</span>
<button id="copyLabelBtn" style="margin:10px; padding:6px 12px;">Copy Label</button>
 <span class="status-box" id="chk-copy">⬜</span>
<!-- <button onclick="printFinalPage()">🖨️ Print Page</button> -->
<button onclick="printFinalPage()">🖨️ Print Page</button>
 <span class="status-box" id="chk-print">⬜</span>
<!-- Buttons outside printPageBox -->
<button id="deleteCanvasBtn">Delete Selected Label</button>
<button id="undoDeleteBtn">Undo Delete</button>
<button id="btn-delete-page" class="btn btn-danger" onclick="deletePrintPage()">Delete Print Page</button>
</div>

<!-- 🔹 MODAL for NEW LABEL (Preview Label) -->
<div id="sizeModalNew" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    z-index:9999;
">
  <div style="
      background:#fff;
      padding:20px 30px;
      border-radius:10px;
      box-shadow:0 5px 20px rgba(0,0,0,0.3);
      width:320px;
      text-align:center;
  ">
    <h3 style="margin-bottom:15px;">Set Label Size (MM)</h3>
    <label>Width:</label><br>
    <input type="number" id="labelWidthNew" style="width:100%; padding:6px; margin:6px 0;"><br>
    <label>Height:</label><br>
    <input type="number" id="labelHeightNew" style="width:100%; padding:6px; margin:6px 0;"><br>
    <div style="margin-top:15px;">
      <button id="confirmSizeNew" style="padding:6px 12px; margin-right:10px;">Confirm</button>
      <button id="cancelSizeNew" style="padding:6px 12px;">Cancel</button>
    </div>
  </div>
</div>

<!-- 🔹 MODAL for COPY LABEL -->
<div id="copyLabelModal" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    z-index:10000;
">
  <div style="
      background:#fff;
      padding:20px 30px;
      border-radius:10px;
      box-shadow:0 5px 20px rgba(0,0,0,0.3);
      width:320px;
      text-align:center;
  ">
    <h3 style="margin-bottom:15px;">Set Copy Label Size (MM)</h3>
    <label>Width:</label><br>
    <input type="number" id="copyLabelWidth" style="width:100%; padding:6px; margin:6px 0;"><br>
    <label>Height:</label><br>
    <input type="number" id="copyLabelHeight" style="width:100%; padding:6px; margin:6px 0;"><br>
    <div style="margin-top:15px;">
      <button id="confirmCopyLabel" style="padding:6px 12px; margin-right:10px;">Confirm</button>
      <button id="cancelCopyLabel" style="padding:6px 12px;">Cancel</button>
    </div>
  </div>
</div>

<!-- 🔹 MODAL for PRINT PAGE -->
<div id="printPageModal" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    z-index:10000;
">
  <div style="
      background:#fff;
      padding:20px 30px;
      border-radius:10px;
      box-shadow:0 5px 20px rgba(0,0,0,0.3);
      width:320px;
      text-align:center;
  ">
    <h3 style="margin-bottom:15px;">Set Print Page Size (MM)</h3>
    <label>Width:</label><br>
    <input type="number" id="printPageWidth" style="width:100%; padding:6px; margin:6px 0;"><br>
    <label>Height:</label><br>
    <input type="number" id="printPageHeight" style="width:100%; padding:6px; margin:6px 0;"><br>
    <div style="margin-top:15px;">
      <button id="confirmPrintPage" style="padding:6px 12px; margin-right:10px;">Confirm</button>
      <button id="cancelPrintPage" style="padding:6px 12px;">Cancel</button>
    </div>
  </div>
</div>

<!-- 🔹 LABEL PREVIEW BOX -->
<div class="label-preview-box" 
     style="
        width:1150px; 
        height:950px; 
        margin:30px auto; 
        border:2px solid #000; 
        background:#E6E6FA;
        position:relative;
        overflow:auto;
        box-shadow:0 5px 15px rgba(0,0,0,0.2);
     ">

  <div id="workspace" 
       style="position:relative; width:2000px; height:2000px;">
       
    <canvas id="rulerX" width="2000" height="20" 
            style="position: sticky; top:0; left:0; z-index:10; background:#fff;"></canvas>

    <canvas id="rulerY" width="20" height="2000" 
            style="position:absolute; top:0; left:0; z-index:1; background:#fff;"></canvas>
            
    <!-- Preview canvas will be added dynamically -->
  </div>
</div>
 <!-- <h3>🖨️ Select a Printer</h3>

  <select id="printerDropdown">
    <option value="">Loading printers...</option>
  </select> -->
<!-- <div>
  <h3>🖨 Printer Info</h3>
  <p id="printerStatus">Loading...</p>
  
  <select id="printerSelect" class="form-control" style="width: 300px;"></select>
  
  <button id="getInfoBtn" class="btn btn-primary" style="margin-left: 10px;">Get DPI Info</button>
  
  <pre id="dpiInfo" style="margin-top: 15px; background: #f5f5f5; padding: 10px;"></pre>
</div> -->


<div id="finalPrinterModal" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    z-index:30000;
">
  <div style="
      background:#fff;
      padding:25px;
      border-radius:15px;
      box-shadow:0 10px 40px rgba(0,0,0,0.4);
      width:500px;
      text-align:center;
      border: 2px solid #007bff;
  ">
    <h3 style="margin-bottom:5px; color:#333;">🖨️ Simulator Printer Selection</h3>
    <p id="printQtyStatus" style="color:#28a745; font-weight:bold; margin-bottom:15px;"></p>
    
    <label style="display:block; text-align:left; margin-bottom:5px; font-weight:bold;">Choose Printer from Simulator:</label>
<select id="finalPrinterSelect" class="form-control" style="width:100%; height:40px;">
    <option value="dymo450">DYMO LabelWriter 450</option>
    <option value="zebra-zt410">Zebra ZT410</option>
    <option value="brother-ql800">Brother QL-800</option>
    <option value="godex-g500">Godex G500</option>
    <option value="epson-tmc3500">Epson TM-C3500</option>
    <option value="rollo">Rollo Label Printer</option>
    <option value="zebra-zd420">Zebra ZD420</option>
    <option value="sato-cl4nx">Sato CL4NX</option>
    <option value="tsc-ttp247">TSC TTP-247</option>
    <option value="hp-laserjet-m404dn">HP LaserJet Pro M404dn</option>
    <option value="hp-m428fdw">HP LaserJet Pro MFP M428fdw</option>
    <option value="hp-officejet-9025e">HP OfficeJet Pro 9025e</option>
    <option value="hp-deskjet-2755e">HP DeskJet 2755e</option>
    <option value="hp-color-laserjet-m454dn">HP Color LaserJet Pro M454dn</option>
    <option value="hp-pagewide-477dw">HP PageWide Pro 477dw</option>
    <option value="hp-designjet-t730">HP DesignJet T730</option>
    <option value="hp-latex-315">HP Latex 315</option>
    <option value="hp-indigo-12000">HP Indigo 12000</option>
    <option value="brother-hl2395dw">Brother HL-L2395DW</option>
    <option value="canon-mf743cdw">Canon imageCLASS MF743Cdw</option>
    <option value="xerox-versalink-c405">Xerox VersaLink C405</option>
    <option value="ricoh-sp3610dn">Ricoh SP 3610DN</option>
    <option value="lexmark-cx431adwe">Lexmark CX431adwe</option>
    <option value="kyocera-m3655idn">Kyocera Ecosys M3655idn</option>
    <option value="oki-c650dn">OKI C650dn</option>
    <option value="konica-c368">Konica Minolta bizhub C368</option>
    <option value="epson-wf4820">Epson WorkForce Pro WF-4820</option>
    <option value="canon-pixma-tr8620">Canon PIXMA TR8620</option>
    <option value="brother-mfc-j995dw">Brother MFC-J995DW</option>
    <option value="epson-ecotank-et8500">Epson EcoTank ET-8500</option>
    <option value="canon-pro1000">Canon imagePROGRAF PRO-1000</option>
    <option value="epson-surecolor-p900">Epson SureColor P900</option>
    <option value="hp-designjet-z9">HP DesignJet Z9+</option>
    <option value="canon-tx5410">Canon imagePROGRAF TX-5410</option>
    <option value="star-tsp143iv">Star TSP143IV</option>
    <option value="epson-tm-t88vi">Epson TM-T88VI</option>
    <option value="custom-ttp345">Custom TTP-345</option>
    <option value="bixolon-srp350plus">Bixolon SRP-350plus</option>
    <option value="epson-tm-m30">Epson TM-m30</option>
    <option value="star-mpop">Star Micronics mPOP</option>
    <option value="hp-engage-go">HP Engage Go</option>
    <option value="custom-thermal">Custom Thermal Printer</option>
    <option value="canon-selphy-cp1300">Canon SELPHY CP1300</option>
    <option value="hp-sprocket-plus">HP Sprocket Plus</option>
    <option value="epson-picturemate-pm400">Epson PictureMate PM-400</option>
    <option value="custom">Custom Printer</option>
    <option value="custom-label">Custom Label Printer</option>
    <option value="custom-large-format">Custom Large Format</option>
</select>

    <div style="display:flex; justify-content:center; gap:20px;">
      <button id="finalConfirmPrint" style="background:#28a745; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">OK / Start Print</button>
      <button id="finalCancelPrint" style="background:#6c757d; color:white; border:none; padding:12px 25px; border-radius:8px; cursor:pointer; font-weight:bold;">Cancel</button>
    </div>
  </div>
</div>


  </div>



<!-- <script src="/printer.js"></script> -->


<script>
let labelFieldState = {}; 
let stickerModal = document.getElementById('stickerModal');
let modalFieldsContainer = document.getElementById('modalFieldsContainer');
let currentModelName = "<?= $modelname ?? '' ?>";
let showAdvancedFields = false;
let serialFieldName = null;
let serialStartValue = null;
let canvases = [];            
let previewCanvas = null;     
let isLocked = false;
let selectedObject = null;
let selectedNodeId = ""; // global
let keywordCount = 0;
const maxKeywords = 10;
let keywordsEnabled = false;
let deletedObjects = [];   
let isSelectMode = false; // 🔹 Global flag 
let printPageBox = null;
let printPageCanvas = null;
let printPageObject = null;
let deletedPrintPages = [];
let workspace = document.getElementById('workspace');
let selectedCanvasWrapper = null; // currently selected canvas wrapper
const labelPreviewBox = document.querySelector('.label-preview-box');


window.addEventListener("load", function() {
  const rulerX = document.getElementById('rulerX').getContext('2d');
  const rulerY = document.getElementById('rulerY').getContext('2d');
  const workspace = document.getElementById('workspace');

  // GC420t DPI
  const DPI = 203;
  const pxPerMm = DPI / 25.4; // 1mm ≈ 7.992 px


function setupLabelPreviewBox(widthMm = 200, heightMm = 200) {
    workspace.width = widthMm * pxPerMm;
    workspace.height = heightMm * pxPerMm;

    rulerX.clearRect(0, 0, workspace.width, 20);
    rulerY.clearRect(0, 0, 20, workspace.height);

    rulerX.strokeStyle = "#000";
    rulerY.strokeStyle = "#000";
    rulerX.fillStyle = "#000";
    rulerY.fillStyle = "#000";
    rulerX.font = "12px Arial";
    rulerY.font = "12px Arial";

    // ---------- X Ruler ----------
    for (let i = 0; i <= widthMm; i++) {
        const x = i * pxPerMm;
        rulerX.beginPath();

        // Calculate displayed number: -1 at i=0, 0 at i=10, 1 at i=20 ...
        const displayNumber = (i - 10)/10;

        if (i % 10 === 0) { // major tick every cm
            rulerX.lineWidth = 2;
            rulerX.moveTo(x, 20); rulerX.lineTo(x, 0);
            rulerX.stroke();
            rulerX.fillText(displayNumber + "cm", x + 2, 10);
        } else if (i % 5 === 0) { // mid tick
            rulerX.lineWidth = 1.5;
            rulerX.moveTo(x, 20); rulerX.lineTo(x, 12);
            rulerX.stroke();
        } else { // minor tick
            rulerX.lineWidth = 1;
            rulerX.moveTo(x, 20); rulerX.lineTo(x, 16);
            rulerX.stroke();
        }
    }

    // ---------- Y Ruler ----------
    for (let i = 0; i <= heightMm; i++) {
        const y = i * pxPerMm;
        rulerY.beginPath();

        const displayNumber = (i - 10)/10; // same adjustment

        if (i % 10 === 0) {
            rulerY.lineWidth = 2;
            rulerY.moveTo(20, y); rulerY.lineTo(0, y);
            rulerY.stroke();
            rulerY.fillText(displayNumber + "cm", 2, y + 10);
        } else if (i % 5 === 0) {
            rulerY.lineWidth = 1.5;
            rulerY.moveTo(20, y); rulerY.lineTo(12, y);
            rulerY.stroke();
        } else {
            rulerY.lineWidth = 1;
            rulerY.moveTo(20, y); rulerY.lineTo(16, y);
            rulerY.stroke();
        }
    }
}

setupLabelPreviewBox(200, 200);
});


// ---------------- PRINT PAGE SETUP ----------------
const printPageBtn = document.getElementById('printPageBtn');
const printPageModal = document.getElementById('printPageModal');
const confirmPrintPage = document.getElementById('confirmPrintPage');
const cancelPrintPage = document.getElementById('cancelPrintPage');
const printPageWidthInput = document.getElementById('printPageWidth');
const printPageHeightInput = document.getElementById('printPageHeight');

printPageBtn.addEventListener('click', () => {
  printPageWidthInput.value = '';
  printPageHeightInput.value = '';
  printPageModal.style.display = 'flex';
});

cancelPrintPage.addEventListener('click', () => {
  printPageModal.style.display = 'none';
});

window.addEventListener('load', async () => {
    // 🔥 Auto-open modal if needed
    openModal();

    // 🔥 Fetch last saved print page size
    try {
        const res = await fetch("<?= site_url('items/getLastPrintPage') ?>?modelname=" + currentModelName);
        const data = await res.json();

        if (data.status === 'success') {
            const widthPX = data.width_mm * 7.992;
            const heightPX = data.height_mm * 7.992;

            // ❌ Remove old if exists
            if (printPageBox) printPageBox.remove();

            // ✅ Render saved print page
            printPageBox = document.createElement('div');
            Object.assign(printPageBox.style, {
                width: widthPX + 'px',
                height: heightPX + 'px',
                background: '#fff',
                border: '2px solid #000',
                position: 'absolute',
                top: '80px',
                left: '80px',
                zIndex: '5',
                boxShadow: '0 0 10px rgba(0,0,0,0.3)',
                cursor: 'default',
                pointerEvents: 'auto'
            });
            printPageBox.id = "printPageBox";
            labelPreviewBox.appendChild(printPageBox);
        }
    } catch (err) {
        console.error('❌ Error loading saved print page size:', err);
    }
     markDone("chk-create"); // ✅ HERE
});


confirmPrintPage.addEventListener('click', async () => {
    const widthMM = parseFloat(printPageWidthInput.value);
    const heightMM = parseFloat(printPageHeightInput.value);

    if (isNaN(widthMM) || isNaN(heightMM) || widthMM <= 0 || heightMM <= 0) {
        alert('Please enter valid dimensions in mm');
        return;
    }

    const widthPX = widthMM * 7.992;
    const heightPX = heightMM * 7.992;

    // Remove old print page if exists
    if (printPageBox) printPageBox.remove();

    // Create new print page box
    printPageBox = document.createElement('div');
    Object.assign(printPageBox.style, {
        width: widthPX + 'px',
        height: heightPX + 'px',
        background: '#fff',
        border: '2px solid #000',
        position: 'absolute',
        top: '80px',
        left: '80px',
        zIndex: '5',
        boxShadow: '0 0 10px rgba(0,0,0,0.3)',
        cursor: 'default',
        pointerEvents: 'auto'
    });
    printPageBox.id = "printPageBox";
    labelPreviewBox.appendChild(printPageBox);

    // Hide modal
    printPageModal.style.display = 'none';

    // 🔹 Save fields first
    if (!previewCanvas) {
        alert("Canvas not initialized yet! Please wait for it to load.");
        return;
    }

    try {
        await saveModalFields(); // wait for async save
        // 🔹 Reload page only after successful save
        
    } catch (err) {
        console.error("Error saving fields:", err);
        alert("❌ Failed to save print page size. Reload canceled.");
    }
    
});


// ---------------- PREVIEW LABEL (NEW) ----------------
const previewLabelBtn = document.getElementById('previewLabelBtn');
const sizeModalNew = document.getElementById('sizeModalNew');
const confirmSizeNew = document.getElementById('confirmSizeNew');
const cancelSizeNew = document.getElementById('cancelSizeNew');
const labelWidthNew = document.getElementById('labelWidthNew');
const labelHeightNew = document.getElementById('labelHeightNew');

// 🔹 Open modal for new label size
previewLabelBtn.addEventListener('click', () => {
    if (!printPageBox) {
        alert('⚠️ Please create a print page first!');
        return;
    }
    labelWidthNew.value = '';
    labelHeightNew.value = '';
    sizeModalNew.style.display = 'flex';
});

cancelSizeNew.addEventListener('click', () => {
    sizeModalNew.style.display = 'none';
});


     function createPreviewLabelCanvas(wMM, hMM) {
    if (!printPageBox) {
        console.warn('⚠️ printPageBox not ready, skipping label preview.');
        return;
    }

    const pxPerMM = 7.992;
    const w = Math.round(wMM * pxPerMM);
    const h = Math.round(hMM * pxPerMM);

    // ✅ Remove any existing preview label before creating a new one
    const oldPreview = document.querySelector('#previewCanvas');
    if (oldPreview) {
        const oldWrapper = oldPreview.closest('div');
        if (oldWrapper && oldWrapper.parentNode) oldWrapper.remove();
    }
    if (previewCanvas) {
        try { previewCanvas.dispose(); } 
        catch (err) { console.warn('⚠️ Error disposing old previewCanvas:', err); }
        previewCanvas = null;
    }

    // 🔹 Right offset in px
    const rightOffset = 10;

    // ✅ Create wrapper div
    const wrapperDiv = document.createElement('div');
    Object.assign(wrapperDiv.style, {
        position: 'absolute',
        top: '0px',
        left: rightOffset + 'px',
        cursor: 'grab',
        zIndex: 10,
        overflow: 'visible'
    });

    // ✅ Create canvas element
    const canvasEl = document.createElement('canvas');
    canvasEl.id = 'previewCanvas';
    canvasEl.width = w;
    canvasEl.height = h;
    Object.assign(canvasEl.style, {
        position: 'absolute',
        top: '0px',
        left: '0px',
        background: 'transparent'
    });

    wrapperDiv.appendChild(canvasEl);
    printPageBox.appendChild(wrapperDiv);

    // ✅ Fabric canvas init
    previewCanvas = new fabric.Canvas(canvasEl, {
        selection: true,
        preserveObjectStacking: true,
        backgroundColor: 'transparent'
    });
    previewCanvas.wrapperEl = wrapperDiv;

    // 🔹 Background
    const bg = new fabric.Rect({
        left: 0,
        top: 0,
        width: w,
        height: h,
        fill: '#fff',
        selectable: false
    });
    previewCanvas.add(bg);
    bg.sendToBack();

   
// 🔹 Border (Blue Dotted Box)
    // 4 వైపులా ఖచ్చితంగా కనిపించడానికి strokeUniform మరియు stroke width సర్దుబాటు చేశాను
    const border = new fabric.Rect({
        left: 2,
        top: 2,
        width: w - 4,
        height: h - 4,
        stroke: '#00BFFF',          // బ్లూ కలర్
        strokeWidth: 2,             // బోర్డర్ మందం
        fill: 'transparent',
        strokeDashArray: [10, 5],    // డాటెడ్ లైన్ స్టైల్
        selectable: false,
        evented: false,
        strokeUniform: true,        // స్కేలింగ్ చేసినా బోర్డర్ మారకుండా ఉంటుంది
        paintFirst: 'stroke',       // బోర్డర్ అంచు బయట పడకుండా చేస్తుంది
        name: 'labelBorder'
    });
    
    // ఒకవేళ పాత బోర్డర్ ఉంటే తీసేయడానికి
    const objects = previewCanvas.getObjects();
    objects.forEach(obj => {
        if (obj.name === 'labelBorder') previewCanvas.remove(obj);
    });

    previewCanvas.add(border);
    border.center(); // కాన్వాస్ మధ్యలోకి సెట్ చేస్తుంది
    border.bringToFront();
    previewCanvas.renderAll();          // ఇది అన్నింటికంటే పైన కనిపిస్తుంది
    // 🔹 Make draggable
    makeWrapperDraggableSmart(wrapperDiv, printPageBox, previewCanvas);
    saveCanvasPositionToDB(wrapperDiv, true);
    restoreCanvasPosition(wrapperDiv, true);

    // 🔹 Outline selection logic
    wrapperDiv.addEventListener('click', e => {
        e.stopPropagation();
        if (selectedCanvasWrapper) selectedCanvasWrapper.style.outline = '';
        selectedCanvasWrapper = wrapperDiv;
        wrapperDiv.style.outline = '2px dashed red';
    });

    // ✅ 🔥 Attach selection + toolbar events here
    if (typeof attachSelectionEvents === 'function') {
        attachSelectionEvents(previewCanvas);
    } else {
        console.warn('⚠️ attachSelectionEvents() not found.');
    }

    // 🔹 Load form data
    if (typeof fetchStickerForm === 'function') fetchStickerForm();

    console.log(`✅ Preview Label created: ${wMM}mm × ${hMM}mm, shifted right by ${rightOffset}px`);
}

confirmSizeNew.addEventListener('click', () => {
    const wMM = parseFloat(labelWidthNew.value);
    const hMM = parseFloat(labelHeightNew.value);

    if (isNaN(wMM) || isNaN(hMM) || wMM <= 0 || hMM <= 0) {
        alert('Please enter valid label dimensions in mm');
        return;
    }

    // ✅ Validation against printPageBox size
    const printPageWidthMM = (printPageBox?.offsetWidth || 0) / 7.992;
    const printPageHeightMM = (printPageBox?.offsetHeight || 0) / 7.992;

    if (wMM > printPageWidthMM || hMM > printPageHeightMM) {
        alert(`❌ Label size exceeds Print Page Box! Max allowed: ${printPageWidthMM.toFixed(2)}mm × ${printPageHeightMM.toFixed(2)}mm`);
        return;
    }

    sizeModalNew.style.display = 'none';
    createPreviewLabelCanvas(wMM, hMM);
    saveModalFields(); // auto-save
    markDone("chk-preview");
});
// 🔹 Auto-load from DB once page + printPageBox ready
window.addEventListener('load', async () => {
    const checkExist = setInterval(async () => {
        if (printPageBox) {
            clearInterval(checkExist);
            try {
                const res = await fetch("<?= site_url('items/getLastLabelSize') ?>?modelname=" + currentModelName);
                const data = await res.json();
                if (data.status === 'success') {
                    createPreviewLabelCanvas(parseFloat(data.label_width_mm), parseFloat(data.label_height_mm));
                }
            } catch (err) {
                console.error('❌ Error loading saved label size:', err);
            }
        }
    }, 300);
});
// ---------------- SMART DRAG FUNCTION ----------------
function makeWrapperDraggableSmart(wrapper, container, fabricCanvas) {
    let isDragging = false;
    let startX, startY;

    wrapper.addEventListener('mousedown', (e) => {
        // 🧠 Check if click is on Fabric object (like text/image)
        const target = fabricCanvas.findTarget(e);

        // If user clicked on object (not blank background) → only move object
        if (target && target.name !== 'background' && target.name !== 'border') {
            // Let fabric handle drag
            return;
        }

        // ✅ Else: clicked on blank area → drag whole label
        e.preventDefault();
        isDragging = true;
        startX = e.clientX - wrapper.offsetLeft;
        startY = e.clientY - wrapper.offsetTop;
        wrapper.style.cursor = 'grabbing';
    });

    window.addEventListener('mousemove', (e) => {
        if (!isDragging) return;

        let x = e.clientX - startX;
        let y = e.clientY - startY;

        // Keep inside container bounds
        x = Math.max(0, Math.min(container.clientWidth - wrapper.offsetWidth, x));
        y = Math.max(0, Math.min(container.clientHeight - wrapper.offsetHeight, y));

        wrapper.style.left = x + 'px';
        wrapper.style.top = y + 'px';
    });

    window.addEventListener('mouseup', () => {
        if (isDragging) {
            isDragging = false;
            wrapper.style.cursor = 'grab';
        }
        
    });
}

document.getElementById('deleteCanvasBtn').addEventListener('click', () => {
    if (!selectedCanvasWrapper) {
        alert("⚠️ Please select a label first!");
        return;
    }

    // check if selected wrapper is the original preview canvas
    if (selectedCanvasWrapper.contains(document.getElementById('previewCanvas'))) {
        // original preview canvas
        deletedObjects.push({
            wrapperEl: selectedCanvasWrapper,
            fabricCanvas: previewCanvas
        });

        selectedCanvasWrapper.remove(); // remove wrapper div from DOM
        previewCanvas.dispose();
        previewCanvas = null;

        // remove from canvases array if present
        const idx = canvases.indexOf(previewCanvas);
        if(idx !== -1) canvases.splice(idx, 1);

        console.log("✅ Original preview canvas deleted");
    } else {
        // copied canvas
        const canvasInstance = canvases.find(c => c.wrapperEl === selectedCanvasWrapper);
        if (canvasInstance) {
            deletedObjects.push({
                wrapperEl: selectedCanvasWrapper,
                fabricCanvas: canvasInstance
            });

            selectedCanvasWrapper.remove();
            const idx = canvases.indexOf(canvasInstance);
            if (idx !== -1) canvases.splice(idx, 1);

            console.log("✅ Copy label deleted");
        }
    }

    selectedCanvasWrapper = null;
});
// ---------------- UNDO DELETE ----------------
document.getElementById('undoDeleteBtn').addEventListener('click', () => {
    if (!deletedObjects.length) {
        alert("⚠️ Nothing to undo!");
        return;
    }
    const lastDeleted = deletedObjects.pop();
    printPageBox.appendChild(lastDeleted.wrapperEl);
    canvases.push(lastDeleted.fabricCanvas);
    makeWrapperDraggable(lastDeleted.wrapperEl, printPageBox);
});


// ------------------- COPY LABEL SCRIPT (FULL UPDATED) -------------------
const copyLabelBtn = document.getElementById('copyLabelBtn');
const copyLabelModal = document.getElementById('copyLabelModal');
const confirmCopyLabel = document.getElementById('confirmCopyLabel');
const cancelCopyLabel = document.getElementById('cancelCopyLabel');
const copyLabelWidth = document.getElementById('copyLabelWidth');
const copyLabelHeight = document.getElementById('copyLabelHeight');

// 🔹 Open modal for copy label
copyLabelBtn.addEventListener('click', async () => {
    if (!previewCanvas) {
       
        return;
    }

    // ✅ Ensure Node ID exists
    if (!selectedNodeId) {
        try {
            const res = await fetch("<?= site_url('items/getFields') ?>?modelname=" + encodeURIComponent(currentModelName));
            const data = await res.json();
            const forms = data.fields.filter(f => f.modelname === currentModelName);

            if (forms.length > 0 && forms[0].qr_node_id) {
                selectedNodeId = forms[0].qr_node_id;

                const nodeDropdown = document.getElementById("nodeDropdown");
                if (nodeDropdown) nodeDropdown.value = selectedNodeId;
                const nodeInput = document.getElementById("Nodeinput");
                if (nodeInput) nodeInput.value = selectedNodeId;

                const qrSizeInput = document.getElementById("qrSizeInput");
                if (qrSizeInput && forms[0].qr_size_mm) qrSizeInput.value = forms[0].qr_size_mm;

                const model = currentModelName;
                const dom = document.getElementById("domInput")?.value || "";
                let qrContent = `EF/${model}/${dom}/${selectedNodeId}`;

                const fieldRows = document.querySelectorAll("#modalFieldsContainer .form-group");
                fieldRows.forEach((row) => {
                    const keywordCb = row.querySelector(".qr-keyword-checkbox");
                    const valueCb = row.querySelector(".qr-include-checkbox");
                    const fieldName = row.querySelector(".modal-field-name")?.value || "";
                    const fieldValue = row.querySelector(".modal-field-value")?.value || "";

                    let fieldContent = "";
                    if (keywordCb?.checked && fieldName) fieldContent += fieldName;
                    if (valueCb?.checked && fieldValue) fieldContent += (fieldContent ? ":" : "") + fieldValue;
                    if (fieldContent) qrContent += `/${fieldContent}`;
                });

                window.currentQRData = {
                    NodeID: selectedNodeId,
                    QRSizeMM: forms[0].qr_size_mm || 12,
                    Model: model,
                    DOM: dom
                };

                regenerateQR(qrContent);

            } else {
                alert("⚠️ Please select a Node ID / QR code in the original preview canvas first!");
                return;
            }
        } catch (err) {
            console.error("Failed to fetch saved Node ID:", err);
            alert("⚠️ Could not fetch Node ID from backend!");
            return;
        }
    }

    copyLabelWidth.value = '';
    copyLabelHeight.value = '';
    copyLabelModal.style.display = 'flex';
});

// 🔹 Cancel modal
cancelCopyLabel.addEventListener('click', () => {
    copyLabelModal.style.display = 'none';
});

confirmCopyLabel.addEventListener('click', () => {
    const wMM = parseFloat(copyLabelWidth.value);
    const hMM = parseFloat(copyLabelHeight.value);

    if (isNaN(wMM) || isNaN(hMM) || wMM <= 0 || hMM <= 0) {
        alert('Please enter valid label copy dimensions in mm');
        return;
    }

    // ✅ Validation against printPageBox size
    const printPageWidthMM = (printPageBox?.offsetWidth || 0) / 7.992;
    const printPageHeightMM = (printPageBox?.offsetHeight || 0) / 7.992;

    if (wMM > printPageWidthMM || hMM > printPageHeightMM) {
        alert(`❌ Copy label size exceeds Print Page Box! Max allowed: ${printPageWidthMM.toFixed(2)}mm × ${printPageHeightMM.toFixed(2)}mm`);
        return;
    }

    const pxPerMM = 7.992;
    const w = Math.round(wMM * pxPerMM);
    const h = Math.round(hMM * pxPerMM);

    copyLabelModal.style.display = 'none';
    createCanvasCopy(w, h);

    saveModalFields();
     markDone("chk-copy"); 
});
// 🔹 Update selected Node ID
document.getElementById('nodeDropdown').addEventListener('change', function() {
    selectedNodeId = this.value;
});


function createCanvasCopy(userWidth, userHeight, restoredPosition = null) {
    const baseCanvasEl = document.getElementById('previewCanvas');
    if (!baseCanvasEl || !previewCanvas) {
        console.error("❌ previewCanvas not found!");
        return null;
    }

    const offsetXCanvas = 45;
    const offsetYCanvas = 19;

    const wrapperDiv = document.createElement('div');
    Object.assign(wrapperDiv.style, {
        position: 'absolute',
        left: restoredPosition?.left !== undefined ? restoredPosition.left + 'px' : 
              (parseInt(baseCanvasEl.style.left) || 0) + previewCanvas.getWidth() + offsetXCanvas + 'px',
        top: restoredPosition?.top !== undefined ? restoredPosition.top + 'px' : 
             (parseInt(baseCanvasEl.style.top) || 0) + offsetYCanvas + 'px',
        cursor: 'grab',
        zIndex: 10,
        overflow: 'visible'
    });

    const newCanvasId = `previewCanvasCopy_${Date.now()}`;
    const newCanvasEl = document.createElement('canvas');
    newCanvasEl.id = newCanvasId;
    newCanvasEl.width = userWidth;
    newCanvasEl.height = userHeight;
    newCanvasEl.style.background = 'transparent';
    wrapperDiv.appendChild(newCanvasEl);
    printPageBox.appendChild(wrapperDiv);

    const newFabricCanvas = new fabric.Canvas(newCanvasId, {
       selection: false,             // 🔥 Multi-selection block chesthundhi
    preserveObjectStacking: true,
    backgroundColor: 'transparent',
    defaultCursor: 'default'      // Cursor normal ga unchuthundhi
    });

    const bg = new fabric.Rect({
        left: 0, top: 0,
        width: userWidth, height: userHeight,
        fill: '#fff', selectable: false, evented: false, name: 'background'
    });

// ✅ Copy Label Border (Preview Label లాగే సేమ్ రావడానికి)
    const strokeW = 2; // మీరు ప్రివ్యూలో వాడిన అదే మందం
    const border = new fabric.Rect({
        left: 2,           // ప్రివ్యూలో లాగే 2 పిక్సెల్స్ ఆఫ్‌సెట్
        top: 2,            // ప్రివ్యూలో లాగే 2 పిక్సెల్స్ ఆఫ్‌సెట్
        width: userWidth - 4,  // userWidth నుండి 4 తీసేయాలి (2 left + 2 right)
        height: userHeight - 4, // userHeight నుండి 4 తీసేయాలి (2 top + 2 bottom)
        stroke: '#00BFFF', 
        strokeWidth: strokeW,
        fill: 'transparent',
        strokeDashArray: [10, 5],
        selectable: false,
        evented: false,
        strokeUniform: true,
        paintFirst: 'stroke',
        name: 'border' // Copy label లో దీని పేరు 'border' అని ఉంచండి
    });

    // పాత బోర్డర్ ఏదైనా ఉంటే తీసేయడానికి
    const oldB = newFabricCanvas.getObjects().find(o => o.name === 'border');
    if (oldB) newFabricCanvas.remove(oldB);

    newFabricCanvas.add(border);
    border.bringToFront();
newFabricCanvas.add(border);
border.bringToFront();
    // ✅ Clone all non-QR objects as selectable in copy
    const sourceObjects = previewCanvas.getObjects().filter(o => !['background', 'border', 'qr'].includes(o.name));
    sourceObjects.forEach(obj => {
        obj.clone(clone => {
           clone.set({ 
            selectable: false,    // 🔥 Select avvadu
            evented: false        // 🔥 Click/Edit mode raadhu
        });
            newFabricCanvas.add(clone);
        });
    });

    // 🔹 Clone QR + sync with original
    const qrObj = previewCanvas.getObjects().find(o => o.name === 'qr');
    if (qrObj) {
        qrObj.clone(clone => {
          clone.set({ 
            selectable: false,    // 🔥 Select avvadu
            evented: false        // 🔥 Drag/Edit avvadu
        });
            newFabricCanvas.add(clone);
            newFabricCanvas.renderAll();

            function syncQR() {
                clone.set({
                    left: qrObj.left,
                    top: qrObj.top,
                    scaleX: qrObj.scaleX,
                    scaleY: qrObj.scaleY,
                    angle: qrObj.angle,
                    text: qrObj.text
                });
                clone.setCoords();
                newFabricCanvas.renderAll();
            }

            ['moving', 'scaling', 'rotating', 'modified'].forEach(ev =>
                previewCanvas.on(`object:${ev}`, e => { if (e.target === qrObj) syncQR(); })
            );
            previewCanvas.on('text:changed', e => { if (e.target === qrObj) syncQR(); });
        });
    }

    attachSelectionEvents(newFabricCanvas);

    newFabricCanvas.wrapperEl = wrapperDiv;
    canvases.push(newFabricCanvas);

    // ✅ Smart drag + object selection (like original)
    makeWrapperDraggableSmart(wrapperDiv, printPageBox, newFabricCanvas);

   // createCanvasCopy function lopala click event ni deenthlo replace cheyandi
wrapperDiv.addEventListener('click', e => {
    e.stopPropagation();
    
    // 🔥 De-select original preview or other copies
    if (selectedCanvasWrapper && selectedCanvasWrapper !== wrapperDiv) {
        selectedCanvasWrapper.style.outline = '';
    }
    
    selectedCanvasWrapper = wrapperDiv;
    wrapperDiv.style.outline = '2px dashed red';
    console.log("✅ Copy Label Selected");
});

    // 🔹 Auto-save initial position
    if (!restoredPosition) saveCanvasPositionToDB(wrapperDiv, true);

    console.log("✅ Copy label created with QR & rounded border (rest unchanged)");
    return newFabricCanvas;
}

// ---------------- RESTORE COPY LABEL ----------------
window.addEventListener('load', async () => {
    const waitForOriginal = setInterval(async () => {
        if (printPageBox && previewCanvas && document.getElementById('previewCanvas')) {
            clearInterval(waitForOriginal);

            try {
                const res = await fetch("<?= site_url('items/getLastCopyLabel') ?>?modelname=" + currentModelName);
                const data = await res.json();

                if (data.status === 'success' && data.copy_width_mm && data.copy_height_mm) {
                    // const pxPerMM = 7.992;
                       const pxPerMM = 8.2;
                    const copyW = data.copy_width_mm * pxPerMM;
                    const copyH = data.copy_height_mm * pxPerMM;
                    const origPositions = JSON.parse(data.positions || '[]');

                    if (document.getElementById('previewCanvas')) {
                        createCanvasCopy(copyW, copyH, origPositions);
                        console.log("✅ Copy Label Restored Successfully!");
                    }
                }
            } catch (err) {
                console.error("❌ Error loading copy label:", err);
            }
        }
    }, 400);
});


// Deselect on outer click
document.addEventListener('click', (e) => {
    if (selectedCanvasWrapper && !selectedCanvasWrapper.contains(e.target)) {
        selectedCanvasWrapper.style.outline = '';
        selectedCanvasWrapper = null;
    }
});




// ✅ make draggable helper (used for copies & preview)
function makeWrapperDraggable(wrapperEl, container) {
  let isDrag = false, startX, startY;

  wrapperEl.addEventListener('mousedown', e => {
    isDrag = true;
    startX = e.clientX - wrapperEl.offsetLeft;
    startY = e.clientY - wrapperEl.offsetTop;
    wrapperEl.style.cursor = 'grabbing';
  });

  document.addEventListener('mousemove', e => {
    if (!isDrag) return;
    const rect = container.getBoundingClientRect();
    let x = e.clientX - rect.left - startX;
    let y = e.clientY - rect.top - startY;

    // stay within container
    x = Math.max(0, Math.min(x, rect.width - wrapperEl.offsetWidth));
    y = Math.max(0, Math.min(y, rect.height - wrapperEl.offsetHeight));

    wrapperEl.style.left = x + 'px';
    wrapperEl.style.top = y + 'px';
  });

  document.addEventListener('mouseup', () => {
    if (isDrag) {
      isDrag = false;
      wrapperEl.style.cursor = 'grab';
    }
  });
}

// ---------------- DRAG HELPERS ----------------
function makeDraggableWithin(parent, element) {
  let offsetX, offsetY, isDragging = false;
  element.addEventListener('mousedown', (e) => {
    isDragging = true;
    const rect = element.getBoundingClientRect();
    offsetX = e.clientX - rect.left;
    offsetY = e.clientY - rect.top;
    element.style.cursor = 'grabbing';
  });

  window.addEventListener('mouseup', () => {
    isDragging = false;
    element.style.cursor = 'move';
  });

  window.addEventListener('mousemove', (e) => {
    if (!isDragging) return;
    const parentRect = parent.getBoundingClientRect();
    let x = e.clientX - parentRect.left - offsetX;
    let y = e.clientY - parentRect.top - offsetY;

    if (x < -element.width + 10) x = -element.width + 10;
    if (y < -element.height + 10) y = -element.height + 10;
    if (x > parent.clientWidth - 10) x = parent.clientWidth - 10;
    if (y > parent.clientHeight - 10) y = parent.clientHeight - 10;

    element.style.left = x + 'px';
    element.style.top = y + 'px';
  });
}

function makeWrapperDraggable(wrapperEl, container) {
  let isDrag = false, startX, startY;
  wrapperEl.addEventListener('mousedown', e => {
    isDrag = true;
    startX = e.clientX - wrapperEl.offsetLeft;
    startY = e.clientY - wrapperEl.offsetTop;
    wrapperEl.style.cursor = 'grabbing';
  });
  document.addEventListener('mousemove', e => {
    if (!isDrag) return;
    let x = e.clientX - startX;
    let y = e.clientY - startY;
    wrapperEl.style.left = x + 'px';
    wrapperEl.style.top = y + 'px';
  });
  document.addEventListener('mouseup', () => {
    if (isDrag) {
      isDrag = false;
      wrapperEl.style.cursor = 'grab';
    }
  });
}

function makeWrapperDraggableAndSelectable(wrapperEl, fabricCanvas, container) {
  let isDragging = false;
  let startX, startY;

  // ✅ Handle single click for drag start
  wrapperEl.addEventListener('mousedown', (e) => {
    if (isSelectMode) return; // if in select mode, skip dragging
    isDragging = true;
    startX = e.clientX - wrapperEl.offsetLeft;
    startY = e.clientY - wrapperEl.offsetTop;
    wrapperEl.style.cursor = 'grabbing';
  });

  document.addEventListener('mousemove', (e) => {
    if (!isDragging || isSelectMode) return;
    const rect = container.getBoundingClientRect();
    let x = e.clientX - rect.left - startX;
    let y = e.clientY - rect.top - startY;

    // Keep within container
    x = Math.max(0, Math.min(x, rect.width - wrapperEl.offsetWidth));
    y = Math.max(0, Math.min(y, rect.height - wrapperEl.offsetHeight));

    wrapperEl.style.left = `${x}px`;
    wrapperEl.style.top = `${y}px`;
  });

  document.addEventListener('mouseup', () => {
    if (isDragging) {
      isDragging = false;
      wrapperEl.style.cursor = 'grab';
    }
  });

  // ✅ Double-click toggles between drag/select mode
  wrapperEl.addEventListener('dblclick', (e) => {
    e.stopPropagation();
    isSelectMode = !isSelectMode;

    if (isSelectMode) {
      // enable selecting/editing inside canvas
      fabricCanvas.selection = true;
      fabricCanvas.forEachObject(obj => (obj.selectable = true));
      wrapperEl.style.outline = '2px dashed red';
      wrapperEl.style.cursor = 'default';
    } else {
      // disable selecting/editing, back to drag
      fabricCanvas.discardActiveObject().renderAll();
      fabricCanvas.selection = false;
      fabricCanvas.forEachObject(obj => (obj.selectable = false));
      wrapperEl.style.outline = '';
      wrapperEl.style.cursor = 'grab';
    }
  });
}
// 🔹 Save canvas wrapper position (mm)
function saveCanvasPositionToDB(wrapperDiv, isPreview = true) {
    if (!wrapperDiv) return;

    const pxPerMM = 7.992;
    const leftMM = parseInt(wrapperDiv.style.left || 0) / pxPerMM;
    const topMM = parseInt(wrapperDiv.style.top || 0) / pxPerMM;

    const payload = {
        modelname: currentModelName,
        user_id: "<?= session()->get('user_id') ?>",
        preview_left_mm: isPreview ? leftMM : null,
        preview_top_mm: isPreview ? topMM : null,
        copy_left_mm: !isPreview ? leftMM : null,
        copy_top_mm: !isPreview ? topMM : null
    };

    fetch("<?= site_url('items/saveCanvasPosition') ?>", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => console.log('✅ Canvas position saved:', data))
    .catch(err => console.error('❌ Error saving canvas position:', err));
}
async function restoreCanvasPosition(wrapperDiv, isPreview = true) {
    try {
        const type = isPreview ? 'preview' : 'copy';
        const res = await fetch(`<?= site_url('items/getCanvasPosition') ?>?modelname=${currentModelName}&canvasType=${type}`);
        const data = await res.json();
        if (data.status === 'success') {
            const pxPerMM = 7.992;
            if (isPreview && data.preview_left_mm !== undefined && data.preview_top_mm !== undefined) {
                wrapperDiv.style.left = data.preview_left_mm * pxPerMM + 'px';
                wrapperDiv.style.top = data.preview_top_mm * pxPerMM + 'px';
            } else if (!isPreview && data.copy_left_mm !== undefined && data.copy_top_mm !== undefined) {
                wrapperDiv.style.left = data.copy_left_mm * pxPerMM + 'px';
                wrapperDiv.style.top = data.copy_top_mm * pxPerMM + 'px';
            }
        }
    } catch (err) {
        console.error('❌ Error restoring canvas position:', err);
    }
}


function openModal() {
    stickerModal.style.display = 'block';
    fetchStickerForm();
}

function closeModal() {
    stickerModal.style.display = 'none';
}

function addModalField(name = '', value = '', counting = '', savedData = null) {
    if (!modalFieldsContainer || !previewCanvas) return;

    // Decode values if they were URL-encoded
    try {
        name = decodeURIComponent(name);
        value = decodeURIComponent(value);
    } catch (e) {
        console.warn("Decoding failed, using raw value:", e);
    }

    const lowerName = name.trim().toLowerCase();
    const isTitle = lowerName === 'title';

    // Prevent duplicate non-title fields in modal
    if (!isTitle && Array.from(modalFieldsContainer.querySelectorAll('.modal-field-name'))
        .some(f => f.value === name)) return;

    // Header (once)
    if (!document.querySelector('#qr-fields-heading')) {
        const heading = document.createElement('h5');
        heading.id = 'qr-fields-heading';
        heading.innerHTML = `
            <div style="display:flex; justify-content:flex-end; gap:0px; font-weight:600; color:#333;">
                <span>Count</span>&nbsp;&nbsp;&nbsp;
                <span>Show in QR</span>
            </div>`;
        heading.style.margin = '15px 0';
        modalFieldsContainer.prepend(heading);
    }

    // Row container
    const row = document.createElement('div');
    row.className = 'form-group';
    row.style.marginBottom = '10px';

    const nameBg = savedData?.fontWeight ? '#e0e0e0' : '#fff';
    const valueBg = savedData?.fontSize ? '#e0e0e0' : '#fff';

    // Encode for safety
    const safeName = name.replace(/"/g, '&quot;');
    const safeValue = value.replace(/"/g, '&quot;');

    // Row HTML
    row.innerHTML = `
        <div style="display:flex; align-items:center; gap:8px;">
            <input type="text" class="modal-field-name form-control" placeholder="Field Name" 
                   value="${safeName}" 
                   style="flex:0.6; min-width:120px; max-width:160px; padding:7px 10px; 
                   border:1px solid #ccc; border-radius:4px; background:${nameBg};">

            <input type="text" class="modal-field-value form-control" placeholder="Field Value" 
                   value="${safeValue}" 
                   style="flex:1.2; min-width:100px; max-width:150px; padding:7px 10px; 
                   border:1px solid #ccc; border-radius:4px; background:${valueBg};">

            <input type="text" class="modal-field-counting form-control" 
                   value="${counting}" readonly 
                   style="flex:0.4; min-width:50px; max-width:80px; padding:7px 10px; 
                   border:1px solid #ccc; border-radius:4px; text-align:center; background:#e0e0e0;">

            <input type="checkbox" class="qr-keyword-checkbox" title="Include Field Name in QR" 
                   style="transform:scale(1.2); cursor:pointer;">
            <input type="checkbox" class="qr-include-checkbox" title="Include Value in QR" 
                   style="transform:scale(1.2); cursor:pointer;">
        </div>`;

    modalFieldsContainer.appendChild(row);

    // -----------------------------
    // 🔹 Clamp positions within canvas
    // -----------------------------
    const canvasWidth = previewCanvas.getWidth();
    const canvasHeight = previewCanvas.getHeight();

    let left = parseFloat(savedData?.left) || 10 + modalFieldsContainer.children.length * 10;
    let top = parseFloat(savedData?.top) || 10 + modalFieldsContainer.children.length * 10;
    let scaleX = parseFloat(savedData?.scaleX) || 1;
    let scaleY = parseFloat(savedData?.scaleY) || 1;
    let fontSize = parseInt(savedData?.fontSize) || 20;
    let fontWeight = savedData?.fontWeight || 'bold';
    let fontFamily = savedData?.fontFamily || 'Verdana'; // ✅ fontFamily support added
    let rotation = parseFloat(savedData?.rotation) || 0;

    // Create temporary fabric text for size calculations
    const tempObj = new fabric.Textbox(value || name, {
        fontSize,
        fontWeight,
        scaleX,
        scaleY,
        fontFamily // ✅ apply font family here too
    });

    tempObj.set({ left: 0, top: 0 });
    tempObj.setCoords();

    const objWidth = tempObj.getScaledWidth();
    const objHeight = tempObj.getScaledHeight();

    left = Math.min(Math.max(0, left), canvasWidth - objWidth);
    top = Math.max(0, top);

    // Save all dataset info in row
    row.dataset.left = left;
    row.dataset.top = top;
    row.dataset.scaleX = scaleX;
    row.dataset.scaleY = scaleY;
    row.dataset.fontSize = fontSize;
    row.dataset.fontWeight = fontWeight;
    row.dataset.fontFamily = fontFamily; // ✅ store fontFamily in dataset
    row.dataset.rotation = rotation;

    // -----------------------------
    // 🖋️ Create canvas text object
    // -----------------------------
    const textObj = new fabric.Textbox(value || name, {
        left,
        top,
        fontSize,
        fontWeight,
        fontFamily, // ✅ apply saved font
        scaleX,
        scaleY,
        angle: rotation,
        fill: '#000',
        originX: 'left',
        originY: 'top',
        hasControls: true,
        editable: true
    });

    textObj.fieldName = name;
    textObj.fieldValue = value;
    textObj.counting = counting;

    previewCanvas.add(textObj);
    previewCanvas.renderAll();
}

// function addModalField(name = '', value = '', counting = '', savedData = null) {
//     if (!modalFieldsContainer || !previewCanvas) return;

//     // Decode values if they were URL-encoded before
//     try {
//         name = decodeURIComponent(name);
//         value = decodeURIComponent(value);
//     } catch (e) {
//         console.warn("Decoding failed, using raw value:", e);
//     }

//     const lowerName = name.trim().toLowerCase();
//     const isTitle = lowerName === 'title';

//     // Prevent duplicate non-title fields in modal
//     if (!isTitle && Array.from(modalFieldsContainer.querySelectorAll('.modal-field-name'))
//         .some(f => f.value === name)) return;

//     // Header (once)
//     if (!document.querySelector('#qr-fields-heading')) {
//         const heading = document.createElement('h5');
//         heading.id = 'qr-fields-heading';
//         heading.innerHTML = `
//             <div style="display:flex; justify-content:flex-end; gap:0px; font-weight:600; color:#333;">
//                 <span>Count</span>&nbsp;&nbsp;&nbsp;
//                 <span>Show in QR</span>
//             </div>`;
//         heading.style.margin = '15px 0';
//         modalFieldsContainer.prepend(heading);
//     }

//     // Row container
//     const row = document.createElement('div');
//     row.className = 'form-group';
//     row.style.marginBottom = '10px';

//     const nameBg = savedData?.fontWeight ? '#e0e0e0' : '#fff';
//     const valueBg = savedData?.fontSize ? '#e0e0e0' : '#fff';

//     // Encode field name/value for safety
//     const safeName = name.replace(/"/g, '&quot;');
//     const safeValue = value.replace(/"/g, '&quot;');

//     row.innerHTML = `
//         <div style="display:flex; align-items:center; gap:8px;">
//             <input type="text" class="modal-field-name form-control" placeholder="Field Name" 
//                    value="${safeName}" 
//                    style="flex:0.6; min-width:120px; max-width:160px; padding:7px 10px; 
//                    border:1px solid #ccc; border-radius:4px; background:${nameBg};">

//             <input type="text" class="modal-field-value form-control" placeholder="Field Value" 
//                    value="${safeValue}" 
//                    style="flex:1.2; min-width:100px; max-width:150px; padding:7px 10px; 
//                    border:1px solid #ccc; border-radius:4px; background:${valueBg};">

//             <input type="text" class="modal-field-counting form-control" 
//                    value="${counting}" readonly 
//                    style="flex:0.4; min-width:50px; max-width:80px; padding:7px 10px; 
//                    border:1px solid #ccc; border-radius:4px; text-align:center; background:#e0e0e0;">

//             <input type="checkbox" class="qr-keyword-checkbox" title="Include Field Name in QR" 
//                    style="transform:scale(1.2); cursor:pointer;">
//             <input type="checkbox" class="qr-include-checkbox" title="Include Value in QR" 
//                    style="transform:scale(1.2); cursor:pointer;">
//         </div>`;

//     modalFieldsContainer.appendChild(row);

//     // -----------------------------
//     // 🔹 Clamp positions within canvas
//     // -----------------------------
//     const canvasWidth = previewCanvas.getWidth();
//     const canvasHeight = previewCanvas.getHeight();

//     let left = parseFloat(savedData?.left) || 10 + modalFieldsContainer.children.length * 10;
//     let top = parseFloat(savedData?.top) || 10 + modalFieldsContainer.children.length * 10;

//     const tempObj = new fabric.Textbox(value || name, {
//         fontSize: parseInt(savedData?.fontSize) || 30,
//         fontWeight: savedData?.fontWeight || 'bold',
//         scaleX: parseFloat(savedData?.scaleX) || 1,
//         scaleY: parseFloat(savedData?.scaleY) || 1
//     });

//     tempObj.set({ left: 0, top: 0 });
//     tempObj.setCoords();
//     const objWidth = tempObj.getScaledWidth();
//     const objHeight = tempObj.getScaledHeight();

//     left = Math.min(Math.max(0, left), canvasWidth - objWidth);
//     // top = Math.min(Math.max(0, top), canvasHeight - objHeight);
// top = Math.max(0, top);
//     row.dataset.left = left;
//     row.dataset.top = top;
//     row.dataset.scaleX = savedData?.scaleX || 1;
//     row.dataset.scaleY = savedData?.scaleY || 1;
//     row.dataset.fontSize = savedData?.fontSize || 20;
//     row.dataset.fontWeight = savedData?.fontWeight || 'bold';
//     row.dataset.rotation = savedData?.rotation || 0;
 // } // 1. Function bayata ee variable ni pettandi (Global scope)
let spaceAlertShown = false;

function renderModalFieldsToCanvas() {
    if (!previewCanvas) return;

    const fieldRows = modalFieldsContainer.querySelectorAll('.form-group');
    const baseLabelHeight = previewCanvas.getHeight(); // Actual label height (e.g., 150px)

    // 1. Alert Logic
    let estimatedTotalHeight = (fieldRows.length * 22) + 80;
    if (estimatedTotalHeight > baseLabelHeight) {
        if (!spaceAlertShown) {
            alert("Please select the correct dimension! Space is not enough for all fields.");
            spaceAlertShown = true;
            setTimeout(() => { spaceAlertShown = false; }, 3000);
        }
    }

    // 2. Clear previous objects
    previewCanvas.getObjects().forEach(obj => {
        if (!['background', 'logo', 'qrCode', 'barCode', 'barCodePlaceholder'].includes(obj.name)) {
            previewCanvas.remove(obj);
        }
    });

    // 🔹 DYNAMIC CANVAS HEIGHT (Label bayata text kanabadadaniki)
    // Oka vela text ekkuvunte canvas height ni temporary ga penchutunnam
    if (estimatedTotalHeight > baseLabelHeight) {
        previewCanvas.setHeight(estimatedTotalHeight + 50);
    }

 // 3. 🔹 DRAW LABEL BOUNDARY (Blue Dot Box)
    const strokeW = 2; // మరీ లావుగా కాకుండా 2px ఉంటే క్లియర్ గా ఉంటుంది
    const labelBoundary = new fabric.Rect({
        left: 1,  // 0 కి బదులు 1 ఇవ్వండి
        top: 1,   // 0 కి బదులు 1 ఇవ్వండి
        // width మరియు height లో -2 లేదా -3 తగ్గించి చూడండి
        width: previewCanvas.getWidth() - 3, 
        height: baseLabelHeight - 3, 
        fill: 'transparent',
        stroke: '#00BFFF', 
        strokeWidth: strokeW,
        strokeDashArray: [10, 5],
        selectable: false,
        evented: false,
        strokeUniform: true, 
        paintFirst: 'stroke',
        name: 'labelBoundary'
    });

    // పాత బౌండరీ ఏదైనా ఉంటే తీసేయండి
    const oldBoundary = previewCanvas.getObjects().find(o => o.name === 'labelBoundary');
    if (oldBoundary) previewCanvas.remove(oldBoundary);

    previewCanvas.add(labelBoundary);
    labelBoundary.bringToFront(); // అన్నింటికంటే పైన కనిపించడానికి
    let currentTop = 15;
    const verticalGap = 5;

    // 4. TEXT FIELDS LOOP
    fieldRows.forEach((row, idx) => {
        const fieldName = row.querySelector('.modal-field-name')?.value || '';
        const fieldValue = row.querySelector('.modal-field-value')?.value || '';
        if (!fieldValue) return;

        let fontSize = (fieldName.toLowerCase() === 'title') ? 16 : 14;
        let displayText = (fieldName.toLowerCase() === 'title') ? fieldValue : `${fieldName}: ${fieldValue}`;

        const fieldObj = new fabric.Textbox(displayText, {
            left: 15,
            top: currentTop,
            width: 250,
            fontSize: fontSize,
            fontWeight: 'bold',
            fontFamily: 'Verdana',
            selectable: !isLocked,
            name: `field_${fieldName}_${idx}`
        });

        previewCanvas.add(fieldObj);
        fieldObj.setCoords();

        currentTop += (fieldObj.getScaledHeight()) + verticalGap;
    });

   
     previewCanvas.add(barcodeBox);
     barcodeBox.bringToFront();

     previewCanvas.renderAll();
}

function loadModalFieldsFromDB(savedData = {}) {
    if (!modalFieldsContainer) return;

    modalFieldsContainer.innerHTML = '';

    const names = savedData.field_name?.split(',') || [];
    const values = savedData.field_value?.split(',') || [];
    const countings = savedData.counting?.split(',') || [];
    const lefts = savedData.field_left?.split(',') || [];
    const tops = savedData.field_top?.split(',') || [];
    const scaleXs = savedData.field_scaleX?.split(',') || [];
    const scaleYs = savedData.field_scaleY?.split(',') || [];
    const rotations = savedData.field_rotation?.split(',') || [];
    const fontSizes = savedData.field_fontsize?.split(',') || [];
    const fontWeights = savedData.field_fontweight?.split(',') || [];
    const fontFamilies = savedData.field_fontfamily?.split(',') || [];


    
    let autoTop = 15; // Starting gap logic kosam
    const gap = 5;

    for (let i = 0; i < names.length; i++) {
        addModalField(
            names[i],
            values[i],
            countings[i] || '',
            {
                left: lefts[i],
                top: tops[i],
                scaleX: scaleXs[i],
                scaleY: scaleYs[i],
                rotation: rotations[i],
                fontSize: fontSizes[i],
                fontWeight: fontWeights[i],
                fontFamily: fontFamilies[i]
            }
        );
             if (!tops[i]) {
            autoTop += (parseFloat(fontSizes[i]) || 12) + gap;
        }
    }

    // 🔹 Render to canvas after modal is ready
    renderModalFieldsToCanvas();
    renderDefaultProductAndModel();
    addDefaultProductAndModelFields();
}
// ---------------------------
// Add Product Name + Model Name to Canvas by default
// ---------------------------
function renderDefaultProductAndModel() {
    if (!previewCanvas) return;

    // Remove previous product/model if already on canvas
    previewCanvas.getObjects().forEach(obj => {
        if (['productName_0','model'].includes(obj.name)) {
            previewCanvas.remove(obj);
        }
    });

    // Product Name
    const productName = document.getElementById("productName")?.value?.trim() || '';
    if (productName) {
        const productObj = new fabric.Textbox(productName, {
            left: 20,
            top: 20,
            fontSize: 26,
            fontWeight: 'bold',
            fontFamily: 'Verdana',
            name: 'productName_0',
            selectable: true,
            evented: true
        });
        previewCanvas.add(productObj);
        productObj.setCoords();
        labelFieldState['productName_0'] = {
            text: productName,
            left: 20,
            top: 20,
            fontSize: 26,
            fontWeight: 'bold',
            scaleX: 1,
            scaleY: 1,
            rotation: 0
        };
    }

    // Model Name
    const modelVal = document.getElementById("modelInput")?.value?.trim() || '';
    if (modelVal) {
        const modelObj = new fabric.Textbox(`${modelVal}`, {
            left: 20,
            top: 70,
            fontSize: 26,
            fontWeight: 'bold',
            fontFamily: 'Verdana',
            name: 'model',
            selectable: true,
            evented: true
        });
        previewCanvas.add(modelObj);
        modelObj.setCoords();
        labelFieldState['model'] = {
            text: `${modelVal}`,
            left: 20,
            top: 70,
            fontSize: 26,
            fontWeight: 'bold',
            scaleX: 1,
            scaleY: 1,
            rotation: 0
        };
    }

    previewCanvas.renderAll();
}

function addDefaultProductAndModelFields() {
    const productName = document.getElementById("productName")?.value?.trim() || '';
    const modelVal = document.getElementById("modelInput")?.value?.trim() || '';

    if (!modalFieldsContainer) return;

    // 🔹 Collect all existing field names + values (to prevent re-adding)
    const existingFields = Array.from(modalFieldsContainer.querySelectorAll('.modal-field-name'))
        .map(input => input.value.trim().toLowerCase());
    const existingValues = Array.from(modalFieldsContainer.querySelectorAll('.modal-field-value'))
        .map(input => input.value.trim().toLowerCase());

    // 🟢 Add Product Name once
    if (productName && !existingValues.includes(productName.toLowerCase())) {
        addModalField('title', productName);
    }

    // 🟢 Add Model Name once
    if (modelVal && !existingValues.includes(modelVal.toLowerCase())) {
        addModalField('title', modelVal);
    }

    // 🔹 Finally render to canvas
    renderModalFieldsToCanvas();
}

// 🔹 Call this function on page load or after DB fetch
// renderDefaultProductAndModel();




function updateLabelContent(canvasInstance, appendOnly = false) {
    if (!appendOnly) {
        canvasInstance.getObjects().forEach(obj => {
            if (!['background', 'logo', 'qrCode', 'barCode'].includes(obj.name)) {
                canvasInstance.remove(obj);
            }
        });
    }

    let topPosition = 10;

    const addTextBox = (text, objName, fontWeight = 'normal', fontSize = 40) => {
        if (!text) return;
        const textbox = new fabric.Textbox(text, {
            left: 10,
            top: topPosition,
            width: 400,
            fontSize,
            fontWeight,
            fill: '#000',
            fontFamily: 'Verdana',
            selectable: !isLocked,
            evented: !isLocked,
            hasControls: !isLocked,
            name: objName,
            editable: false
        });
        textbox.scaleX = 0.6;
        textbox.scaleY = 0.8;
        canvasInstance.add(textbox);
        textbox.setCoords();
        topPosition += textbox.height * textbox.scaleY + 8;
    };

    const productName = document.getElementById("productName")?.value?.trim() || '';
    addTextBox(productName, 'productName_0', 'bold', 32);

    const modelVal = document.getElementById("modelInput")?.value?.trim() || '';
    if (modelVal) addTextBox(`Model: ${modelVal}`, 'model', 'bold', 30);

    const fieldNames = Array.from(document.querySelectorAll('.modal-field-name')).map(f => f.value.trim());
    const fieldValues = Array.from(document.querySelectorAll('.modal-field-value')).map(f => f.value.trim());

    for (let i = 0; i < fieldNames.length; i++) {
        const fieldName = fieldNames[i];
        const fieldValue = fieldValues[i];
        if (!fieldValue) continue;

        const lowerName = fieldName.toLowerCase();
        const fieldObjName = `field_${fieldName}_${i}`;
        const displayText = lowerName === 'title'
            ? fieldValue
            : `${fieldName}: ${fieldValue}`;

        addTextBox(displayText, fieldObjName, 'bold', 30);
    }

    canvasInstance.renderAll();
}

// 🔥 Page load auto-fetch
window.onload = () => {
    fetchStickerForm(); // Load backend fields into modal + canvas
};

function fetchStickerForm() {
    if (!modalFieldsContainer) return;

    modalFieldsContainer.innerHTML = ''; // Clear modal first

    fetch("<?= site_url('items/getFields') ?>?modelname=" + encodeURIComponent(currentModelName))
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') return;

            const forms = data.fields.filter(f => f.modelname === currentModelName);
            if (!forms.length) return;

            const form = forms[0];

            const savedData = {
                field_name: form.field_name || '',
                field_value: form.field_value || '',
                counting: form.counting || '',
                field_left: form.field_left || '',
                field_top: form.field_top || '',
                field_scaleX: form.field_scaleX || '',
                field_scaleY: form.field_scaleY || '',
                field_rotation: form.field_rotation || '',
                field_fontsize: form.field_fontsize || '',
                field_fontweight: form.field_fontweight || '',
                field_fontfamily: form.field_fontfamily || ''

            };

            // 🔹 Load DB fields into modal + canvas
            loadModalFieldsFromDB(savedData);

            // 🔹 QR Logic
            if (form.qr_node_id) {
                const nodeId = form.qr_node_id;
                const qrSizeMm = form.qr_size_mm || 12;

                const nodeDropdown = document.getElementById("nodeDropdown");
                if (nodeDropdown) nodeDropdown.value = nodeId;

                const nodeInput = document.getElementById("Nodeinput");
                if (nodeInput) nodeInput.value = nodeId;

                const qrSizeInput = document.getElementById("qrSizeInput");
                if (qrSizeInput) qrSizeInput.value = qrSizeMm;

                const model = currentModelName;
                const dom = document.getElementById("domInput")?.value || "";

                let qrContent = `EF/${model}/${dom}/${nodeId}`;

                const fieldRows = document.querySelectorAll("#modalFieldsContainer .form-group");
                let extraData = {};
                fieldRows.forEach((row, idx) => {
                    const keywordCb = row.querySelector(".qr-keyword-checkbox");
                    const valueCb   = row.querySelector(".qr-include-checkbox");
                    const fieldName  = row.querySelector(".modal-field-name")?.value || "";
                    const fieldValue = row.querySelector(".modal-field-value")?.value || "";

                    let fieldContent = "";
                    if (keywordCb?.checked && fieldName) fieldContent += fieldName;
                    if (valueCb?.checked && fieldValue) fieldContent += (fieldContent ? ":" : "") + fieldValue;
                    if (fieldContent) {
                        qrContent += `/${fieldContent}`;
                        extraData[`Field ${idx+1}`] = fieldContent;
                    }
                });

                window.currentQRData = {
                    NodeID: nodeId,
                    QRSizeMM: qrSizeMm,
                    Model: model,
                    DOM: dom,
                    ...extraData
                };

                // 🔹 Pass saved QR position/scale/rotation to regenerateQR
                const qrLeft = parseFloat(form.qr_position_left ?? 50);
                const qrTop = parseFloat(form.qr_position_top ?? 50);
                const qrScaleX = parseFloat(form.qr_position_scaleX ?? 1);
                const qrScaleY = parseFloat(form.qr_position_scaleY ?? 1);
                const qrRotation = parseFloat(form.qr_position_rotation ?? 0);

                regenerateQR(qrContent, {
                    left: qrLeft,
                    top: qrTop,
                    scaleX: qrScaleX,
                    scaleY: qrScaleY,
                    rotation: qrRotation,
                    qrSizeMm: qrSizeMm
                });
            }
        })
        .catch(err => console.error(err));
}

// --- Show QR Data button ---
document.getElementById("showQRDataBtn").addEventListener("click", () => {
    if (!window.currentQRData || Object.keys(window.currentQRData).length === 0) {
        alert("No QR data available!");
        return;
    }

    let html = "<ul style='padding-left:20px;'>";
    for (let key in window.currentQRData) {
        html += `<li><strong>${key}:</strong> ${window.currentQRData[key]}</li>`;
    }
    html += "</ul>";

    document.getElementById("qrDataContent").innerHTML = html;
    document.getElementById("qrDataModal").style.display = "block";
    document.getElementById("modalOverlay").style.display = "block";
});


function refreshModalFields() {
    modalFieldsContainer.innerHTML = ''; // Clear old fields
    fetchStickerForm(); // Reload modal fields + update canvas
}

// 🔹 Load last saved QR position from DB and set on canvas
async function loadLastQRPosition() {
    if (!currentModelName || !previewCanvas) return;

    try {
        const res = await fetch(`<?= site_url('items/getQRPosition?modelname=') ?>${currentModelName}`);
        const data = await res.json();

        if (data.status === 'success') {
            const qrPos = data.qr_position;

            let qrObj = previewCanvas.getObjects().find(o => o.name === 'qrCode');
            if (qrObj && qrPos) {
                qrObj.set({
                    left: qrPos.left,
                    top: qrPos.top,
                    scaleX: qrPos.scaleX,
                    scaleY: qrPos.scaleY,
                    angle: qrPos.rotation
                });
                previewCanvas.renderAll();
            }
        }
    } catch (err) {
        console.error("Error loading QR position:", err);
    }
}

// 🔹 Full save function (existing code intact, QR properly saved)
// async function saveModalFields() {
//     try {
//         if (!previewCanvas) { 
//             alert("Canvas not initialized!"); 
//             return; 
//         }

//         // 🔹 Collect modal field data
//         const fieldNames = Array.from(document.querySelectorAll('.modal-field-name')).map(f => f.value.trim());
//         const fieldValues = Array.from(document.querySelectorAll('.modal-field-value')).map(f => f.value.trim());
//         const countings = Array.from(document.querySelectorAll('.modal-field-counting')).map(f => f.value.trim());

//         // 🔹 Initialize arrays for field properties
//         const fieldLefts = [], fieldTops = [], fieldScaleXs = [], fieldScaleYs = [], fieldRotations = [], fieldFontSizes = [], fieldFontWeights = [];

//         // 🔹 Collect field data from canvas
//         const canvasFields = previewCanvas.getObjects().map(obj => {
//             let leftNum = Number(obj.left) || 0;
//             let topNum = Number(obj.top) || 0;
//             let scaleXNum = Number(obj.scaleX) || 1;
//             let scaleYNum = Number(obj.scaleY) || 1;
//             let angleNum = Number(obj.angle) || 0;
//             const isQR = obj.name === 'qrCode';

//             if (obj.type === 'group') {
//                 const bounds = obj.getBoundingRect();
//                 leftNum = bounds.left || 0;
//                 topNum = bounds.top || 0;
//                 scaleXNum = obj.scaleX || 1;
//                 scaleYNum = obj.scaleY || 1;
//                 angleNum = obj.angle || 0;
//             }

//             if (!isQR) {
//                 fieldLefts.push(leftNum.toFixed(3));
//                 fieldTops.push(topNum.toFixed(3));
//                 fieldScaleXs.push(scaleXNum.toFixed(3));
//                 fieldScaleYs.push(scaleYNum.toFixed(3));
//                 fieldRotations.push(angleNum.toFixed(3));
//                 fieldFontSizes.push(parseInt(obj.fontSize || 12));
//                 fieldFontWeights.push(obj.fontWeight || 'normal');
//                  fieldFontFamilies.push(obj.fontFamily || 'Arial');
//             }

//             return {
//                 id: obj.dbId || null,
//                 field_name: obj.name || '',
//                 field_value: obj.text || '',
//                 counting: labelFieldState[obj.name]?.counting || '',
//                 left: parseFloat(leftNum.toFixed(3)),
//                 top: parseFloat(topNum.toFixed(3)),
//                 scaleX: parseFloat(scaleXNum.toFixed(3)),
//                 scaleY: parseFloat(scaleYNum.toFixed(3)),
//                 rotation: parseFloat(angleNum.toFixed(3))
//             };
//         });

//         // 🔹 Print page dimensions
//         const widthPX = printPageBox ? Math.round(printPageBox.offsetWidth) : null;
//         const heightPX = printPageBox ? Math.round(printPageBox.offsetHeight) : null;
//         const widthMM = widthPX ? (widthPX / 7.992).toFixed(2) : null;
//         const heightMM = heightPX ? (heightPX / 7.992).toFixed(2) : null;

//         // 🔹 Original preview canvas size
//         const origWidthPX = previewCanvas.getWidth();
//         const origHeightPX = previewCanvas.getHeight();
//         const origWidthMM = (origWidthPX / 7.992).toFixed(2);
//         const origHeightMM = (origHeightPX / 7.992).toFixed(2);

//         // 🔹 Prepare payload
//         const payload = {
//             modelname: currentModelName,
//             user_id: "<?= session()->get('user_id') ?>",
//             // field_name: fieldNames.join(','),
//             // field_value: fieldValues.join(','),
//             // counting: countings.join(','),
//             field_name: fieldNames.map(v => encodeURIComponent(v)).join(','),
// field_value: fieldValues.map(v => encodeURIComponent(v)).join(','),
// counting: countings.map(v => encodeURIComponent(v)).join(','),

//             field_left: fieldLefts.join(','),
//             field_top: fieldTops.join(','),
//             field_scaleX: fieldScaleXs.join(','),
//             field_scaleY: fieldScaleYs.join(','),
//             field_rotation: fieldRotations.join(','),
//             field_fontsize: fieldFontSizes.join(','),
//             field_fontweight: fieldFontWeights.join(','),
//             canvas_fields: canvasFields,
//             width_mm: parseFloat(widthMM),
//             height_mm: parseFloat(heightMM),
//             width_px: widthPX,
//             height_px: heightPX,
//             copy_width_mm: parseFloat(copyLabelWidth?.value) || null,
//             copy_height_mm: parseFloat(copyLabelHeight?.value) || null,
//             copy_width_px: copyLabelWidth?.value ? Math.round(parseFloat(copyLabelWidth.value) * 7.992) : null,
//             copy_height_px: copyLabelHeight?.value ? Math.round(parseFloat(copyLabelHeight.value) * 7.992) : null,
//             orig_width_px: origWidthPX,
//             orig_height_px: origHeightPX,
//             orig_width_mm: parseFloat(origWidthMM),
//             orig_height_mm: parseFloat(origHeightMM)
//         };

//         // 🔹 Save QR separately
//         const qrObj = previewCanvas.getObjects().find(o => o.name === 'qrCode');
//         if (qrObj) {
//             let leftNum = Number(qrObj.left) || 0;
//             let topNum = Number(qrObj.top) || 0;
//             let scaleXNum = Number(qrObj.scaleX) || 1;
//             let scaleYNum = Number(qrObj.scaleY) || 1;
//             let angleNum = Number(qrObj.angle) || 0;

//             if (qrObj.type === 'group') {
//                 const bounds = qrObj.getBoundingRect();
//                 leftNum = bounds.left || 0;
//                 topNum = bounds.top || 0;
//                 scaleXNum = qrObj.scaleX || 1;
//                 scaleYNum = qrObj.scaleY || 1;
//                 angleNum = qrObj.angle || 0;
//             }

//             payload.qr_position = {
//                 left: parseFloat(leftNum.toFixed(3)),
//                 top: parseFloat(topNum.toFixed(3)),
//                 scaleX: parseFloat(scaleXNum.toFixed(3)),
//                 scaleY: parseFloat(scaleYNum.toFixed(3)),
//                 rotation: parseFloat(angleNum.toFixed(3))
//             };
//         }

//         // 🔹 Send data to backend
//         const res = await fetch("<?= site_url('items/saveFields') ?>", {
//             method: 'POST',
//             headers: { 
//                 'Content-Type': 'application/json',
//                 'X-Requested-With': 'XMLHttpRequest',
//                 'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
//             },
//             body: JSON.stringify(payload)
//         });

//         const data = await res.json();
//         if (data.status === 'success') {
//             alert("✅ Saved successfully");
//         } else {
//             alert("❌ Save failed: " + (data.message || "Unknown error"));
//         }

//     } catch (err) {
//         console.error("❌ saveModalFields() error:", err);
//         alert("Error saving fields: " + err.message);
//     }
// }

// // 🔹 Open modal & load last QR position
// window.onload = () => { 
//     openModal();
//     loadLastQRPosition();
// };
async function saveModalFields() {
    try {
        if (!previewCanvas) { 
            alert("Canvas not initialized!"); 
            return; 
        }

        // 🔹 Collect modal field data
        const fieldNames = Array.from(document.querySelectorAll('.modal-field-name')).map(f => f.value.trim());
        const fieldValues = Array.from(document.querySelectorAll('.modal-field-value')).map(f => f.value.trim());
        const countings = Array.from(document.querySelectorAll('.modal-field-counting')).map(f => f.value.trim());

        // 🔹 Initialize arrays for field properties
        const fieldLefts = [], fieldTops = [], fieldScaleXs = [], fieldScaleYs = [], fieldRotations = [],
              fieldFontSizes = [], fieldFontWeights = [], fieldFontFamilies = [];

        // 🔹 Collect field data from canvas
        const canvasFields = previewCanvas.getObjects().map(obj => {
            let leftNum = Number(obj.left) || 0;
            let topNum = Number(obj.top) || 0;
            let scaleXNum = Number(obj.scaleX) || 1;
            let scaleYNum = Number(obj.scaleY) || 1;
            let angleNum = Number(obj.angle) || 0;
            const isQR = obj.name === 'qrCode';

            if (obj.type === 'group') {
                const bounds = obj.getBoundingRect();
                leftNum = bounds.left || 0;
                topNum = bounds.top || 0;
                scaleXNum = obj.scaleX || 1;
                scaleYNum = obj.scaleY || 1;
                angleNum = obj.angle || 0;
            }

            if (!isQR) {
                fieldLefts.push(leftNum.toFixed(3));
                fieldTops.push(topNum.toFixed(3));
                fieldScaleXs.push(scaleXNum.toFixed(3));
                fieldScaleYs.push(scaleYNum.toFixed(3));
                fieldRotations.push(angleNum.toFixed(3));
                fieldFontSizes.push(parseInt(obj.fontSize || 12));
                fieldFontWeights.push(obj.fontWeight || 'normal');
                fieldFontFamilies.push(obj.fontFamily || 'Arial');
            }

            return {
                id: obj.dbId || null,
                field_name: obj.name || '',
                field_value: obj.text || '',
                counting: labelFieldState[obj.name]?.counting || '',
                left: parseFloat(leftNum.toFixed(3)),
                top: parseFloat(topNum.toFixed(3)),
                scaleX: parseFloat(scaleXNum.toFixed(3)),
                scaleY: parseFloat(scaleYNum.toFixed(3)),
                rotation: parseFloat(angleNum.toFixed(3))
            };
        });

        // 🔹 Print page dimensions
        const widthPX = printPageBox ? Math.round(printPageBox.offsetWidth) : null;
        const heightPX = printPageBox ? Math.round(printPageBox.offsetHeight) : null;
        const widthMM = widthPX ? (widthPX / 7.992).toFixed(2) : null;
        const heightMM = heightPX ? (heightPX / 7.992).toFixed(2) : null;

        // 🔹 Original preview canvas size
        const origWidthPX = previewCanvas.getWidth();
        const origHeightPX = previewCanvas.getHeight();
        const origWidthMM = (origWidthPX / 7.992).toFixed(2);
        const origHeightMM = (origHeightPX / 7.992).toFixed(2);

        // 🔹 Prepare payload
        const payload = {
            modelname: currentModelName,
            user_id: "<?= session()->get('user_id') ?>",

            // encoded to avoid comma/special character issues
            field_name: fieldNames.map(v => encodeURIComponent(v)).join(','),
            field_value: fieldValues.map(v => encodeURIComponent(v)).join(','),
            counting: countings.map(v => encodeURIComponent(v)).join(','),

            field_left: fieldLefts.join(','),
            field_top: fieldTops.join(','),
            field_scaleX: fieldScaleXs.join(','),
            field_scaleY: fieldScaleYs.join(','),
            field_rotation: fieldRotations.join(','),
            field_fontsize: fieldFontSizes.join(','),
            field_fontweight: fieldFontWeights.join(','),
            field_fontfamily: fieldFontFamilies.map(v => encodeURIComponent(v)).join(','),

            canvas_fields: canvasFields,

            width_mm: parseFloat(widthMM),
            height_mm: parseFloat(heightMM),
            width_px: widthPX,
            height_px: heightPX,

            copy_width_mm: parseFloat(copyLabelWidth?.value) || null,
            copy_height_mm: parseFloat(copyLabelHeight?.value) || null,
            copy_width_px: copyLabelWidth?.value ? Math.round(parseFloat(copyLabelWidth.value) * 7.992) : null,
            copy_height_px: copyLabelHeight?.value ? Math.round(parseFloat(copyLabelHeight.value) * 7.992) : null,

            orig_width_px: origWidthPX,
            orig_height_px: origHeightPX,
            orig_width_mm: parseFloat(origWidthMM),
            orig_height_mm: parseFloat(origHeightMM)
        };

        // 🔹 Save QR separately
        const qrObj = previewCanvas.getObjects().find(o => o.name === 'qrCode');
        if (qrObj) {
            let leftNum = Number(qrObj.left) || 0;
            let topNum = Number(qrObj.top) || 0;
            let scaleXNum = Number(qrObj.scaleX) || 1;
            let scaleYNum = Number(qrObj.scaleY) || 1;
            let angleNum = Number(qrObj.angle) || 0;

            if (qrObj.type === 'group') {
                const bounds = qrObj.getBoundingRect();
                leftNum = bounds.left || 0;
                topNum = bounds.top || 0;
                scaleXNum = qrObj.scaleX || 1;
                scaleYNum = qrObj.scaleY || 1;
                angleNum = qrObj.angle || 0;
            }

            payload.qr_position = {
                left: parseFloat(leftNum.toFixed(3)),
                top: parseFloat(topNum.toFixed(3)),
                scaleX: parseFloat(scaleXNum.toFixed(3)),
                scaleY: parseFloat(scaleYNum.toFixed(3)),
                rotation: parseFloat(angleNum.toFixed(3))
            };
        }

        // 🔹 Send data to backend
        const res = await fetch("<?= site_url('items/saveFields') ?>", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();
        if (data.status === 'success') {
            alert("✅ Saved successfully");
        } else {
            alert("❌ Save failed: " + (data.message || "Unknown error"));
        }

    } catch (err) {
        console.error("❌ saveModalFields() error:", err);
        alert("Error saving fields: " + err.message);
    }
}

// 🔹 Open modal & load last QR position
window.onload = () => { 
    openModal();
    loadLastQRPosition();
};

// Safe DOM getters (avoid null early)
const $ = (id) => document.getElementById(id);


async function fetchNodeIds(modelname) {
    try {
        // Fetch Node IDs from server
        let response = await fetch(`/index.php/items/getNodeId?modelname=${encodeURIComponent(modelname)}`);
        let data = await response.json();
        console.log("Node IDs fetched:", data);

        let dropdown = document.getElementById("nodeDropdown");

        // Agar dropdown already populated hai (ignore first option) → overwrite na karo
        if (dropdown.options.length > 1) return;

        // Clear existing options except the placeholder
        dropdown.innerHTML = '<option value="">--Select Node ID--</option>';

        // Populate dropdown
        if (data.status === "success" && data.node_ids.length > 0) {
            data.node_ids.forEach(id => {
                let opt = document.createElement("option");
                opt.value = id;
                opt.textContent = id;
                dropdown.appendChild(opt);
            });
        } else {
            console.warn("No Node IDs found for this model!");
        }
    } catch (err) {
        console.error("Error fetching Node IDs:", err);
    }
}

// Call fetch only after DOM is ready
document.addEventListener("DOMContentLoaded", function() {
    fetchNodeIds("<?= $modelname ?>");
});


// Optional: update Nodeinput when dropdown changes
document.getElementById("nodeDropdown").addEventListener("change", function() {
    document.getElementById("Nodeinput").value = this.value;
});

// document.getElementById("showQRDataBtn").addEventListener("click", () => {
//     if (!window.currentQRData || Object.keys(window.currentQRData).length === 0) {
//         alert("No QR data available!");
//         return;
//     }

//     let html = "<ul style='padding-left:20px;'>";
//     for (let key in window.currentQRData) {
//         html += `<li><strong>${key}:</strong> ${window.currentQRData[key]}</li>`;
//     }
//     html += "</ul>";

//     document.getElementById("qrDataContent").innerHTML = html;
//     document.getElementById("qrDataModal").style.display = "block";
//     document.getElementById("modalOverlay").style.display = "block";
// });

// document.getElementById("closeQRDataModal").addEventListener("click", () => {
//     document.getElementById("qrDataModal").style.display = "none";
//     document.getElementById("modalOverlay").style.display = "none";
// });

// document.getElementById("modalOverlay").addEventListener("click", () => {
//     document.getElementById("qrDataModal").style.display = "none";
//     document.getElementById("modalOverlay").style.display = "none";
// });
document.getElementById("showQRDataBtn").addEventListener("click", () => {
    if (!window.currentQRData || Object.keys(window.currentQRData).length === 0) {
        alert("No QR data available!");
        return;
    }

    let html = "<ul style='padding-left:20px;'>";
    for (let key in window.currentQRData) {
        let value = window.currentQRData[key];

        // ✅ If key is DOM and value is empty → show today's date
        if (key.toLowerCase() === "dom" && (!value || value.trim() === "")) {
            const today = new Date();
            const day = String(today.getDate()).padStart(2, "0");
            const month = String(today.getMonth() + 1).padStart(2, "0");
            const year = today.getFullYear();
            value = `${day}-${month}-${year}`;
        }

        html += `<li><strong>${key}:</strong> ${value ?? ""}</li>`;
    }
    html += "</ul>";

    document.getElementById("qrDataContent").innerHTML = html;
    document.getElementById("qrDataModal").style.display = "block";
    document.getElementById("modalOverlay").style.display = "block";
});

document.getElementById("closeQRDataModal").addEventListener("click", () => {
    document.getElementById("qrDataModal").style.display = "none";
    document.getElementById("modalOverlay").style.display = "none";
});

document.getElementById("modalOverlay").addEventListener("click", () => {
    document.getElementById("qrDataModal").style.display = "none";
    document.getElementById("modalOverlay").style.display = "none";
});


function toggleBarcodeInputs() {
  const includeBarcode = document.getElementById('includeBarcode');
  const barcodeType = document.getElementById('barcodeType');
  const barcodeTypeLabel = document.querySelector('label[for="barcodeType"]');
  const barcodeValue = document.getElementById('barcodeValue');
  const barcodeValueContainer = barcodeValue.parentElement;
  const barcodeValueLabel = document.querySelector('label[for="barcodeValue"]');

  if (!barcodeType || !barcodeTypeLabel || !barcodeValue || !barcodeValueContainer || !barcodeValueLabel) return;

  if (includeBarcode.value === 'yes') {
    barcodeType.style.display = 'inline-block';
    barcodeTypeLabel.style.display = 'inline-block';
    barcodeValueContainer.style.display = 'flex';
    barcodeValueLabel.style.display = 'inline-block';
  } else {
    barcodeType.style.display = 'none';
    barcodeTypeLabel.style.display = 'none';
    barcodeValueContainer.style.display = 'none';
    barcodeValueLabel.style.display = 'none';
  }
}

document.getElementById('includeBarcode').addEventListener('change', toggleBarcodeInputs);
toggleBarcodeInputs(); // initialize on page load

/* ============ Helpers: QR + Barcode ============ */
function makeQRDataURL(value, size = 100, color = '#111111') {
  const qr = new QRious({ value, size, foreground: color });
  return qr.toDataURL();
}

function addOrReplaceImage(canvasInstance, dataURL, props, nameKey = 'qrCode') {
  const existing = canvasInstance.getObjects().find(o => o.name === nameKey);
  if (existing) canvasInstance.remove(existing);

  fabric.Image.fromURL(dataURL, img => {
    img.set({
      ...props,
      selectable: !isLocked,
      evented: !isLocked,
      hasControls: !isLocked,
      name: nameKey
    });
    canvasInstance.add(img);
    canvasInstance.renderAll();
  });
}

/* ============ Text utils ============ */
function splitTextIntoLines(text, maxLength) {
  const words = (text || '').split(' ');
  const lines = [];
  let current = words[0] || '';
  for (let i = 1; i < words.length; i++) {
    if ((current + ' ' + words[i]).length <= maxLength) current += ' ' + words[i];
    else { lines.push(current); current = words[i]; }
  }
  if (current) lines.push(current);
  return lines;
}



/* ============ Move selected (preview only) ============ */
function moveSelected(direction) {
  if (!previewCanvas) return;
  const obj = previewCanvas.getActiveObject();
  if (!obj) { alert("First Select Object!"); return; }

  const step = 5;
  if (direction === 'up')    obj.top  -= step;
  if (direction === 'down')  obj.top  += step;
  if (direction === 'left')  obj.left -= step;
  if (direction === 'right') obj.left += step;

  obj.setCoords();
  previewCanvas.renderAll();
}

let scaleFactor = 1; // default = no correction

function calibrateScreenMm() {
  const refMm = 100; // reference div 100mm
  const div = document.createElement("div");
  div.style.width = refMm + "mm";
  div.style.position = "absolute";
  div.style.visibility = "hidden";
  document.body.appendChild(div);

  const cssPixels = div.offsetWidth; // browser ke pixels
  document.body.removeChild(div);

  const measuredMm = 50; // ⚠️ isko apne ruler se actual value se badalna
  scaleFactor = refMm / measuredMm;
}
function getQrSizeMm() {
  const qrSizeInput = document.getElementById("qrSizeInput");
  return qrSizeInput ? parseFloat(qrSizeInput.value) || 12 : 12;
}
// px conversion jo calibration respect kare
function mmToPx(mm) {
  const div = document.createElement("div");
  div.style.width = "10mm"; // small ref
  div.style.position = "absolute";
  div.style.visibility = "hidden";
  document.body.appendChild(div);

  const pxPerCssMm = div.offsetWidth / 10;
  document.body.removeChild(div);

  return Math.round(mm * pxPerCssMm * scaleFactor);
}

// Page load pe ek baar calibrate karo
document.addEventListener("DOMContentLoaded", calibrateScreenMm);

function regenerateQR(qrContent, opts = {}) {
    return new Promise((resolve, reject) => {
        if (!previewCanvas || !qrContent) return resolve();

        const qrSizeMm = opts.qrSizeMm || getQrSizeMm();     
        const qrSizePx = mmToPx(qrSizeMm);  

        const maxChars = qrSizeMm * 7;
        if (qrContent.length > maxChars) {
            alert(`⚠️ Data too long (${qrContent.length} chars) for ${qrSizeMm} mm.\nMax allowed: ${maxChars} chars.`);
            return resolve();
        }

        const exQR = previewCanvas.getObjects().find(o => o.name === 'qrCode');
        if (exQR) previewCanvas.remove(exQR);

        const baseQr = new QRious({ value: qrContent, size: qrSizePx * 4, foreground: '#000000' });
        const qrURL = baseQr.toDataURL();
        window.currentQRDataURL = qrURL;
        window.lastQRContent = qrContent;

        fabric.Image.fromURL(qrURL, img => {
            const scale = qrSizePx / img.width;
            img.set({ originX: 'center', originY: 'center', scaleX: scale, scaleY: scale });

            const efFont = Math.max(4, Math.round(qrSizePx * 0.12));
            const efText = new fabric.Text('EF', { fontSize: efFont, fontWeight: 'bold', fill: '#000', originX: 'center', originY: 'center' });
            const efBg = new fabric.Rect({ width: efText.width + 4, height: efText.height + 2, fill: '#fff', originX: 'center', originY: 'center', rx:1, ry:1 });
            const underFont = Math.max(4, Math.round(qrSizePx * 0.10));
            const underText = new fabric.Text('DOWNLOAD', { fontSize: underFont, fill: '#000', fontFamily:'Verdana', fontWeight:'bold', originX:'center', originY:'top', top:(qrSizePx/2)+(underFont*0.6) });

            const qrGroup = new fabric.Group([img, efBg, efText, underText], { 
                originX: 'left',
                originY: 'top',
                selectable:true, 
                evented:true, 
                scaleX: opts.scaleX || 1,
                scaleY: opts.scaleY || 1,
                angle: opts.rotation || 0,
                name:'qrCode'
            });

            // ✅ Use exact left/top from DB or fallback
            qrGroup.left = typeof opts.left === 'number' ? opts.left : 20;
            qrGroup.top  = typeof opts.top === 'number' ? opts.top : 20;

            previewCanvas.add(qrGroup);
            previewCanvas.renderAll();
            resolve();
        }, err => reject(err));
    });
}


window.currentQRData = {}; // Global

function handleNodeOrBarcodeChange() {
    const nodeDropdown = document.getElementById("nodeDropdown");
    const nodeInput    = document.getElementById("Nodeinput");
    const nodeId = nodeDropdown?.value || '';
    if (!nodeId) return;

    if (nodeInput) nodeInput.value = nodeId;

    const model = document.getElementById("modelInput")?.value || "";
    const dom   = document.getElementById("domInput")?.value || "";

    // Start QR content
    let qrContent = `EF/${model}/${dom}/${nodeId}`;

    // Extra fields from modal
    const fieldRows = document.querySelectorAll("#modalFieldsContainer .form-group");
    fieldRows.forEach((row, idx) => {
        const keywordCb = row.querySelector(".qr-keyword-checkbox");
        const valueCb   = row.querySelector(".qr-include-checkbox");
        const fieldName  = row.querySelector(".modal-field-name")?.value || "";
        const fieldValue = row.querySelector(".modal-field-value")?.value || "";

        let fieldContent = "";
        if (keywordCb?.checked && fieldName) fieldContent += fieldName;
        if (valueCb?.checked && fieldValue) fieldContent += (fieldContent ? ":" : "") + fieldValue;
        if (fieldContent) qrContent += `/${fieldContent}`;
    });

    // ✅ Update global QR data
    window.currentQRData = {
        NodeID: nodeId,
        Model: model,
        DOM: dom
    };

    // ✅ Render QR on canvas
    regenerateQR(qrContent);

    // 🔥 Save NodeID + QR Size to backend immediately
    const qrSizeInput = document.getElementById("qrSizeInput");
    const qrSizeMm = qrSizeInput?.value || 12;

    const payload = {
        modelname: currentModelName,
        qr_node_id: nodeId,
        qr_size_mm: qrSizeMm,
        user_id: "<?= session()->get('user_id') ?>"
    };

    fetch("<?= site_url('items/saveFields') ?>", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            console.log("✅ Node ID + QR size updated successfully:", payload);
        } else {
            console.warn("❌ Node ID save failed:", data.message);
        }
    })
    .catch(err => console.error("❌ Node ID save error:", err));
}
document.addEventListener("DOMContentLoaded", function() {
    const qrSizeInput = document.getElementById("qrSizeInput");
    if (!qrSizeInput) return;

    qrSizeInput.addEventListener("change", function() {
        const nodeDropdown = document.getElementById("nodeDropdown");
        const nodeId = nodeDropdown?.value?.trim();
        if (!nodeId) return; // Node select nahi hua toh kuch mat karo

        const model = document.getElementById("modelInput")?.value || "";
        const dom   = document.getElementById("domInput")?.value || "";

        // Regenerate QR content
        let qrContent = `EF/${model}/${dom}/${nodeId}`;

        // Extra fields from modal
        const fieldRows = document.querySelectorAll("#modalFieldsContainer .form-group");
        fieldRows.forEach((row, idx) => {
            const keywordCb = row.querySelector(".qr-keyword-checkbox");
            const valueCb   = row.querySelector(".qr-include-checkbox");
            const fieldName  = row.querySelector(".modal-field-name")?.value || "";
            const fieldValue = row.querySelector(".modal-field-value")?.value || "";

            let fieldContent = "";
            if (keywordCb?.checked && fieldName) fieldContent += fieldName;
            if (valueCb?.checked && fieldValue) fieldContent += (fieldContent ? ":" : "") + fieldValue;
            if (fieldContent) qrContent += `/${fieldContent}`;
        });

        // Update global QR data
        window.currentQRData = {
            NodeID: nodeId,
            Model: model,
            DOM: dom
        };

        // Regenerate QR on canvas
        regenerateQR(qrContent);

        // Save QR size + NodeID to backend
        const qrSizeMm = qrSizeInput.value || 12;
        const payload = {
            modelname: currentModelName,
            qr_node_id: nodeId,
            qr_size_mm: qrSizeMm,
            user_id: "<?= session()->get('user_id') ?>"
        };

        fetch("<?= site_url('items/saveFields') ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                console.log("✅ QR size updated successfully:", payload);
            } else {
                console.warn("❌ QR size save failed:", data.message);
            }
        })
        .catch(err => console.error("❌ QR size save error:", err));
    });
});


function attachSelectionEvents(cv) {
  cv.on('selection:created', e => {
    selectedObject = e.selected?.[0] || null;
    const isText = selectedObject && (selectedObject.type === 'text' || selectedObject.type === 'textbox');
    const toolbar = $("editorToolbar");
    if (toolbar) toolbar.style.display = isText ? 'block' : 'none';
    if (isText) {
      $("fontSizePicker").value  = selectedObject.fontSize || 10;
      $("fontColorPicker").value = selectedObject.fill || '#000000';
      $("fontFamilyPicker").value = selectedObject.fontFamily || 'Verdana';
    }
  });

  cv.on('selection:updated', e => {
    selectedObject = e.selected?.[0] || null;
    const isText = selectedObject && (selectedObject.type === 'text' || selectedObject.type === 'textbox');
    const toolbar = $("editorToolbar");
    if (toolbar) toolbar.style.display = isText ? 'block' : 'none';
    if (isText) {
      $("fontSizePicker").value  = selectedObject.fontSize || 10;
      $("fontColorPicker").value = selectedObject.fill || '#000000';
      $("fontFamilyPicker").value = selectedObject.fontFamily || 'Verdana';
    }
  });

  cv.on('selection:cleared', () => {
    selectedObject = null;
    const toolbar = $("editorToolbar");
    if (toolbar) toolbar.style.display = 'none';
  });
}

/* ============ Text toolbar (preview only) ============ */
$("fontSizePicker").addEventListener('change', function () {
  if (selectedObject && (selectedObject.type === 'text' || selectedObject.type === 'textbox')) {
    selectedObject.set('fontSize', parseInt(this.value, 10));
    selectedObject.setCoords();
    selectedObject.canvas.renderAll();
  }
});

$("fontColorPicker").addEventListener('input', function () {
  if (selectedObject && (selectedObject.type === 'text' || selectedObject.type === 'textbox')) {
    selectedObject.set('fill', this.value);
    selectedObject.setCoords();
    selectedObject.canvas.renderAll();
  }
});

// 🔥 Font Family change
document.getElementById("fontFamilyPicker").addEventListener("change", function () {
  if (selectedObject && (selectedObject.type === "text" || selectedObject.type === "textbox")) {
    selectedObject.set("fontFamily", this.value);
    selectedObject.setCoords();
    selectedObject.canvas.renderAll();
  }
});

function toggleBold() {
  if (selectedObject && (selectedObject.type === 'text' || selectedObject.type === 'textbox')) {
    const newWeight = selectedObject.fontWeight === 'bold' ? 'normal' : 'bold';
    selectedObject.set('fontWeight', newWeight);
    selectedObject.setCoords();
    selectedObject.canvas.renderAll();
  }
}

function toggleItalic() {
  if (selectedObject && (selectedObject.type === 'text' || selectedObject.type === 'textbox')) {
    const newStyle = selectedObject.fontStyle === 'italic' ? 'normal' : 'italic';
    selectedObject.set('fontStyle', newStyle);
    selectedObject.setCoords();
    selectedObject.canvas.renderAll();
  }
}

function toggleUnderline() {
  if (selectedObject && (selectedObject.type === 'text' || selectedObject.type === 'textbox')) {
    selectedObject.set('underline', !selectedObject.underline);
    selectedObject.setCoords();
    selectedObject.canvas.renderAll();
  }
}

function applyLogoToPreview() { 
  if (!window.currentLogoURL || !previewCanvas) return;

  // Remove old logo if exists
  const existing = previewCanvas.getObjects().find(o => o.name === 'logo');
  if (existing) {
    previewCanvas.remove(existing);
  }

  // Add new logo
  fabric.Image.fromURL(window.currentLogoURL, imgObj => {
    const padding = 20;
    const maxW = (previewCanvas.width - 2 * padding) * 0.5;
    const maxH = (previewCanvas.height / 5) * 0.5;
    let scale = 1;

    if (imgObj.width > maxW || imgObj.height > maxH) {
      scale = Math.min(maxW / imgObj.width, maxH / imgObj.height);
    }

    imgObj.set({
      left: (previewCanvas.width - imgObj.width * scale) / 2,
      top: padding,
      scaleX: scale,
      scaleY: scale,
      selectable: true,
      evented: true,
      hasControls: true,
      lockUniScaling: false,
      name: 'logo'
    });

    previewCanvas.add(imgObj);
    previewCanvas.bringToFront(imgObj);
    previewCanvas.setActiveObject(imgObj);
    previewCanvas.renderAll();
  });
}

// Correct selector
document.getElementById("logoUpload").addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (!file) return;
  this.value = '';

  const reader = new FileReader();
  reader.onload = function(event) {
    const img = new Image();
    img.onload = function() {
      const tmp = document.createElement('canvas');
      tmp.width = img.width; tmp.height = img.height;
      const ctx = tmp.getContext('2d');
      ctx.drawImage(img, 0, 0);

      // Monochrome
      const imageData = ctx.getImageData(0, 0, tmp.width, tmp.height);
      const d = imageData.data;
      for (let i = 0; i < d.length; i += 4) {
        const avg = (d[i] + d[i+1] + d[i+2]) / 3;
        const v = avg > 128 ? 255 : 0;
        d[i] = d[i+1] = d[i+2] = v;
      }
      ctx.putImageData(imageData, 0, 0);

      window.currentLogoURL = tmp.toDataURL();
      applyLogoToPreview();
      syncLogoToAllCanvases();
    };
    img.src = event.target.result;
  };
  reader.readAsDataURL(file);
});


function validateBarcode(value, type) {
  const onlyNumbers = /^[0-9]+$/;
  const code39Chars = /^[0-9A-Z\-\.\ \$\/\+\%]+$/;

  switch(type) {
    case 'code128':   return value ? true : "Code128 requires a value (any ASCII).";
    case 'code39':    return code39Chars.test(value) ? true : "Code39 allows only A–Z, 0–9, and - . $ / + % SPACE.";
    case 'ean13':
      if (!onlyNumbers.test(value)) return "EAN13 must be numeric only.";
      if (value.length !== 12) return "EAN13 requires exactly 12 digits (13th is checksum).";
      return true;
    case 'upc':
      if (!onlyNumbers.test(value)) return "UPC-A must be numeric only.";
      if (value.length !== 11) return "UPC-A requires exactly 11 digits (12th is checksum).";
      return true;
    case 'itf':
      if (!onlyNumbers.test(value)) return "ITF requires numeric only.";
      if (value.length % 2 !== 0) return "ITF requires an even number of digits.";
      return true;
    case 'gs1databar':
      if (!onlyNumbers.test(value)) return "GS1 DataBar requires numeric only.";
      if (value.length !== 14) return "GS1 DataBar requires exactly 14 digits (GTIN).";
      return true;
    default:
      return "Please select a barcode type.";
  }
}

function generateBarcode() {
  const value = $("barcodeValue").value.trim();
  const type  = $("barcodeType").value;
  if (!type || !value) return;

  const validation = validateBarcode(value, type);
  if (validation !== true) {
    alert("⚠ " + validation);
    $("barcodeType").value = "";
    return;
  }

  try {
    const hiddenCanvas = document.createElement("canvas");

    if (type === 'gs1databar') {
      BwipJS.toCanvas(hiddenCanvas, {
        bcid: 'databaromni',
        text: value,
        scale: 3,
        height: 10,
        includetext: true,
        barcolor: '999999',        // light grey bars (hex without #)
  backgroundcolor: 'FFFFFF', // white bg
        textxalign: 'center'
      });
    } else {
      
JsBarcode(hiddenCanvas, value, {
  format: type.toUpperCase(),
  displayValue: false,
  lineColor: "#080808ff"  // light grey bars
});

    }

    window.currentBarcodeDataURL = hiddenCanvas.toDataURL("image/png");

    if (previewCanvas) {
      addOrReplaceImage(previewCanvas, window.currentBarcodeDataURL, {
        left: 100, top: 230, scaleX: 0.5, scaleY: 0.3
      }, 'barCode');
      previewCanvas.renderAll();
    }
  } catch (err) {
    alert("⚠ Error generating " + type + " barcode!\n\n" + err.message);
    $("barcodeType").value = "";
  }
}

$("barcodeType").addEventListener('change', generateBarcode);
$("barcodeValue").addEventListener('blur', generateBarcode);


function toggleLockAll() {
  isLocked = !isLocked;
  canvases.slice(1).forEach(cv => {
    cv.getObjects().forEach(o => {
      if (o.name !== 'background') {
        o.selectable = !isLocked;
        o.evented    = !isLocked;
      }
    });
    cv.renderAll();
  });

  const lockBtn = document.querySelector('.lock-btn');
  if (lockBtn) lockBtn.textContent = isLocked ? 'Unlock All' : 'Lock All';
}

document.addEventListener("DOMContentLoaded", function() {
    // 🔹 Jab user nodeDropdown change kare, handleNodeOrBarcodeChange chalega
    $("nodeDropdown").addEventListener("change", handleNodeOrBarcodeChange);
});
function deleteSelected(event) {
    // 1. Double execution ni stop chesthundhi
    if (event) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }

    if (!previewCanvas) return;

    const active = previewCanvas.getActiveObject();

    // 2. Selection check
    if (!active) {
        // Idhi okkasari matrame trigger avvali
        alert("Please select a label first!");
        return; 
    }

    // 3. Object select ayithe, alert lekunda delete chesthundhi
    if (active.name === 'logo') {
        window.currentLogoURL = null;
    }

    if (typeof deletedObjects !== 'undefined') {
        deletedObjects.push(active);
    }

    previewCanvas.remove(active);
    previewCanvas.discardActiveObject();
    previewCanvas.renderAll();
}

function undoDelete() {
  if (previewCanvas && deletedObjects.length > 0) {
    const obj = deletedObjects.pop();
    previewCanvas.add(obj); // re-add to canvas
    previewCanvas.setActiveObject(obj); // select it back
    previewCanvas.renderAll();
  }
}

// --- DELETE PRINT PAGE FUNCTION ---
// Deeni ni mee script lo undoDelete() tharuvatha add cheyandi
function deletePrintPage() {
    if (!confirm("Are you sure you want to delete the print page?")) return;

    const boxToRemove = document.getElementById("printPageBox");
    if (boxToRemove) {
        // Fabric Canvas dispose (Memory cleanup)
        if (window.previewCanvas) {
            // previewCanvas.dispose(); // Optional: If you want to kill the canvas instance
        }

        boxToRemove.remove(); // This removes the white space box
        console.log("Print page deleted.");
    } else {
        alert("No print page found.");
        return;
    }

    // Ticks Reset
    const ticks = ['tick-step1', 'tick-step2', 'tick-step3', 'tick-step4'];
    ticks.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.visibility = 'hidden';
    });

    // Checkbox Reset
    resetDone("chk-create");
    resetDone("chk-preview");
    resetDone("chk-copy");
    resetDone("chk-print");
    
    alert("✅ Reset successful.");
}
function addQRCodeWithCenterEF(nodeId) {
  if (!previewCanvas || !nodeId) return;

  const qrURL = makeQRDataURL(nodeId, 115, '#111111');

  fabric.Image.fromURL(qrURL, qrImg => {
    const maxW = 110;
    const maxH = 110;
    let scale = Math.min(maxW / qrImg.width, maxH / qrImg.height);

    qrImg.set({ scaleX: scale, scaleY: scale });

    // Center EF text
    const efText = new fabric.Text('EF', {
      fontSize: 18,
      fontWeight: 'bold',
      fill: '#ff0000',  // red color, ya white black jo bhi suit kare
      originX: 'center',
      originY: 'center'
    });

    // Group QR + EF
    const qrGroup = new fabric.Group([qrImg, efText], {
      left: 245,  // original QR left
      top: 60,
      selectable: !isLocked,
      evented: !isLocked,
      name: 'qrCode'
    });

    // Remove existing QR if any
    const existing = previewCanvas.getObjects().find(o => o.name === 'qrCode');
    if (existing) previewCanvas.remove(existing);

    previewCanvas.add(qrGroup);
    previewCanvas.renderAll();
  });
}

document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("toggleKeywordsBtn");
    const addBtn = document.getElementById("addKeywordBtn");

    if (!toggleBtn || !addBtn) return; // safety check

    // Toggle Keywords button
    toggleBtn.addEventListener("click", () => {
        keywordsEnabled = !keywordsEnabled;
        toggleBtn.textContent = keywordsEnabled ? "Close Keywords" : "Enable Keywords";
        addBtn.style.display = keywordsEnabled ? "inline-block" : "none";

        document.querySelectorAll(".keyword-field button").forEach(btn => {
            btn.style.display = keywordsEnabled ? "inline-block" : "none";
        });
    });

    // Add Keyword button
    addBtn.addEventListener("click", () => {
        if (keywordCount >= maxKeywords) {
            alert("⚠ Maximum 10 keywords!");
            return;
        }
        addKeywordField();
    });

    // Initially hide all delete buttons
    document.querySelectorAll(".keyword-field button").forEach(btn => {
        btn.style.display = "none";
    });
});


document.addEventListener("DOMContentLoaded", () => {
    const customFormBtn = document.getElementById("customFormBtn");
    const addKeywordBtn = document.getElementById("addKeywordBtn");

    // Initially Add Keyword button hidden
    addKeywordBtn.style.display = "none";

    // Initially hide all delete buttons
    document.querySelectorAll(".keyword-field button").forEach(btn => {
        btn.style.display = "none";
    });

    // Custom Form Button Click -> toggle keywords
    customFormBtn.addEventListener("click", () => {
        keywordsEnabled = !keywordsEnabled;

        // Show/hide Add Keyword button
        addKeywordBtn.style.display = keywordsEnabled ? "inline-block" : "none";

        // Show/hide all delete buttons
        document.querySelectorAll(".keyword-field button").forEach(btn => {
            btn.style.display = keywordsEnabled ? "inline-block" : "none";
        });
    });

    // Add Keyword button click
    addKeywordBtn.addEventListener("click", () => {
        if (keywordCount >= maxKeywords) {
            alert("⚠ Maximum 10 keywords!");
            return;
        }
        addKeywordField();
    });
});

function deleteField(btn) {
       btn.parentElement.remove();
     }

     // ✅ Initially delete buttons hidden rahenge
     document.querySelectorAll(".delete-btn").forEach(btn => {
       btn.style.display = "none";
     });

     // ✅ Custom Form button pe click hote hi show/hide honge
     document.getElementById("customFormBtn").addEventListener("click", function() {
       document.querySelectorAll(".delete-btn").forEach(btn => {
         btn.style.display = (btn.style.display === "none") ? "inline-block" : "none";
       });
     });



function cmToPx(cm) {
  // 96 px per inch and 1 inch = 2.54 cm -> px per cm = 96 / 2.54 ≈ 37.795
  const PX_PER_CM = 192 / 2.54;
  return Math.round(Number(cm) * PX_PER_CM);
}

function ensureBasePropsForCanvas(cv) {
  if (!cv) return;
  cv.getObjects().forEach(obj => {
    if (obj._baseSet) return; // already stored
    // store "original" values only once
    obj._baseScaleX = (obj.scaleX == null ? 1 : obj.scaleX);
    obj._baseScaleY = (obj.scaleY == null ? 1 : obj.scaleY);
    obj._baseLeft   = (obj.left   == null ? 0 : obj.left);
    obj._baseTop    = (obj.top    == null ? 0 : obj.top);
    // for rect background store width/height if present
    if (obj.width != null) obj._baseWidth  = obj.width;
    if (obj.height != null) obj._baseHeight = obj.height;
    obj._baseSet = true;
  });
}

// ---------- Safe nodeDropdown listener (no null errors) ----------
document.addEventListener("DOMContentLoaded", function () {
  const nodeDropdown = document.getElementById("nodeDropdown");
  const nodeInput    = document.getElementById("Nodeinput");
  if (nodeDropdown && nodeInput) {
    nodeDropdown.addEventListener("change", function () {
      nodeInput.value = this.value || '';
    });
  } else {
    // safe fallback: if they aren't present now, maybe created later; try tolerant attach
    console.warn("nodeDropdown or Nodeinput not present at DOMContentLoaded. If they are created dynamically, attach listener after creation.");
  }

  // optional: call resize once on load to sync sizes
  try { resizeAllLabels(); } catch (e) { /* ignore */ }
});

async function printFinalPage() {
    if (!previewCanvas) { 
        alert("⚠️ Please create a Preview Label first!"); 
        return; 
    }

    const nodeDropdown = document.getElementById("nodeDropdown");
    if (!nodeDropdown) {
        alert("⚠️ Node dropdown not found!");
        return;
    }

    const allNodeOptions = Array.from(nodeDropdown.options)
        .map(opt => opt.value)
        .filter(v => v && v.trim() !== "");

    if (allNodeOptions.length === 0) {
        alert("⚠️ No Node IDs available!");
        return;
    }

    // 1. Quantity Selection
    let quantity = parseInt(prompt("Enter number of labels:", "1"));
    if (isNaN(quantity) || quantity <= 0) { 
        alert("Enter valid quantity"); 
        return; 
    }
    if (quantity > allNodeOptions.length) quantity = allNodeOptions.length;

    // 2. Data Processing (Existing Logic)
    const printPage = document.getElementById("printPageBox");
    const modalRows = document.querySelectorAll("#modalFieldsContainer .form-group");
    const fieldsData = [];
    modalRows.forEach((row, idx) => {
        const nameField = row.querySelector(".modal-field-name");
        const valueField = row.querySelector(".modal-field-value");
        const countingField = row.querySelector(".modal-field-counting");
        if (!nameField || !valueField || !countingField) return;

        const rawValue = valueField.value.trim();
        const baseValue = /^[0-9]+(\.[0-9]+)?$/.test(rawValue.replace(/,/g, ''))
            ? parseFloat(rawValue.replace(/,/g, ''))
            : rawValue;

        const counting = parseFloat(countingField.value) || 0;
        fieldsData.push({ nameField, valueField, baseValue, counting, canvasObjName: `field_${idx}` });
    });

    // 3. Label Images Generation
    const images = [];
    for (let i = 0; i < quantity; i++) {
        const currentNodeId = allNodeOptions[i];
        const nodeInput = document.getElementById("Nodeinput");
        if (nodeInput) nodeInput.value = currentNodeId;

        // QR and Fields update logic (Mee existing logic ikkada execute avthundi)
        // ... (Keep your QR and Text update logic here) ...

        previewCanvas.renderAll();
        if (typeof syncCopiesFromOriginal === "function") await syncCopiesFromOriginal(true);

        const canvasImg = await html2canvas(printPage, { scale: 2, backgroundColor: "#fff", useCORS: true });
        images.push(canvasImg.toDataURL("image/png"));
    }

    // --- MODIFICATION START: NO window.print() ---
    // Browser dialog ki badalu maname browser-style Simulator open chestunnam
    openBrowserStyleSimulator(images, quantity, printPage.offsetWidth, printPage.offsetHeight);
}

// 1. Printer configurations mapping (Zebra select chesthe ZPL mode loki velthundhi)
const printerConfigs = {
    "Zebra": { type: 'ZPL', dpi: 203, supportsColor: false },
    "Honeywell": { type: 'ZPL', dpi: 300, supportsColor: false },
    "HP": { type: 'GDI', dpi: 600, supportsColor: true },
    "Canon": { type: 'GDI', dpi: 600, supportsColor: true },
    "Standard": { type: 'PDF', dpi: 300, supportsColor: true }
};
function openBrowserStyleSimulator(labelImages, qty, wPX, hPX) {
    const selectElement = document.getElementById('finalPrinterSelect');
    const myPrinterDrivers = selectElement ? Array.from(selectElement.options).map(opt => opt.text) : ["Standard Printer"];

    const simWindow = window.open("", "_blank", "width=1300,height=950");
    if (!simWindow) {
        alert("Pop-up blocked! Please allow pop-ups for this site.");
        return;
    }

    const htmlContent = `
        <html>
        <head>
            <title>Advanced Printer Simulator</title>
            <style>
                body { margin: 0; display: flex; height: 100vh; font-family: 'Segoe UI', sans-serif; background: #525659; overflow: hidden; }
                .sidebar { width: 330px; background: white; border-right: 1px solid #dadce0; display: flex; flex-direction: column; padding: 20px; box-sizing: border-box; overflow-y: auto; }
                h1 { font-size: 22px; margin: 0 0 15px 0; font-weight: 400; color: #202124; }
                .setting-row { display: flex; flex-direction: column; margin-bottom: 12px; }
                .setting-row label { font-size: 12px; color: #5f6368; margin-bottom: 4px; font-weight: 600; }
                select, input { padding: 8px; font-size: 13px; border: 1px solid #dadce0; border-radius: 4px; background: #fff; }
                #statusBox { background: #e8f0fe; padding: 10px; font-size: 12px; margin-top: 10px; border-radius: 4px; border-left: 4px solid #1a73e8; line-height: 1.4; }
                .footer-btns { margin-top: auto; display: flex; flex-direction: column; gap: 8px; padding-top: 20px; }
                .btn { padding: 10px; border-radius: 4px; font-size: 14px; cursor: pointer; border: 1px solid #dadce0; font-weight: 500; text-align: center; }
                .btn-print { background: #1a73e8; color: white; border: none; }
                .btn-cancel { background: #fff; color: #3c4043; }
                .preview-area { flex: 1; overflow-y: auto; display: flex; flex-direction: column; align-items: center; padding: 40px; scroll-behavior: smooth; }
                .paper-page { background: white; margin-bottom: 30px; box-shadow: 0 0 15px rgba(0,0,0,0.4); border: 1px solid #ddd; }
                .paper-page img { width: 100%; height: 100%; object-fit: contain; }
                .grayscale { filter: grayscale(100%) contrast(1.2); }
            </style>
        </head>
        <body>
            <div class="sidebar">
                <h1>Print Settings</h1>
                <div class="setting-row">
                    <label>Printers</label>
                    <select id="pSelect">
                        ${myPrinterDrivers.map(name => `<option value="${name}">${name}</option>`).join('')}
                    </select>
                </div>
                <div class="setting-row">
                    <label>Copies</label>
                    <input type="number" id="copies" value="${qty || 1}" min="1">
                </div>
                <div class="setting-row">
                    <label>Paper Size</label>
                    <select id="pSize">
                        <option value="4x4">4x6 inches</option>
                        <option value="4x6">3x2 inches</option>
                        <option value="2x1">2x1 inches</option>
                        <option value="A4">A4 (210 x 297 mm)</option>
                        <option value="Letter">Letter(85x11 in)</option>
                        <option value="Custom">Custom Size</option>
                    </select>
                </div>
                <div class="setting-row">
                    <label>Print Quality</label>
                    <select id="pQuality"><option>Normal</option><option>High Quality</option><option>Photo Quality</option></select>
                </div>
                <div class="setting-row">
                    <label>Print Mode</label>
                    <select id="pMode"><option value="color">Color</option><option value="bw">Black & White</option><option value="zpl">Gray Scale</option></select>
                </div>
                <div id="statusBox"><b>Status:</b> Ready to simulate</div>
                <div class="footer-btns">
                    <button class="btn btn-print" onclick="doSimulatePrint()">Print </button>
                    <button class="btn btn-cancel" onclick="window.close()">Close</button>
                </div>
            </div>
            <div class="preview-area" id="previewArea">
                </div>

            <script>
                const pSelect = document.getElementById('pSelect');
                const pSize = document.getElementById('pSize');
                const pMode = document.getElementById('pMode');
                const copiesInput = document.getElementById('copies');
                const status = document.getElementById('statusBox');
                const previewArea = document.getElementById('previewArea');
                
                // Original image list from parent
                const labelImages = ${JSON.stringify(labelImages)};

                function renderPreview() {
                    const sizeVal = pSize.value;
                    const modeVal = pMode.value;
                    const copies = parseInt(copiesInput.value) || 1;
                    const selectedPrinter = pSelect.value.toLowerCase();

                    // Dimensions
                    let w = ${wPX}, h = ${hPX};
                    if (sizeVal === "4x4") { w = 400; h = 600; }
                    else if (sizeVal === "4x6") { w = 300; h = 200; }
                    else if (sizeVal === "2x1") { w = 200; h = 100; }
                    else if (sizeVal === "A4") { w = 794; h = 1123; }
                    else if (sizeVal === "Letter") { w = 816; h = 1056; }

                    // Thermal Auto Detect
                    let isThermal = selectedPrinter.includes('zebra') || selectedPrinter.includes('thermal') || modeVal !== 'color';

                    let html = "";
                    // Repeat images based on copies
                    for(let i=0; i < copies; i++) {
                        labelImages.forEach(imgSrc => {
                            html += \`<div class="paper-page" style="width:\${w}px; height:\${h}px;">
                                        <img src="\${imgSrc}" class="\${isThermal ? 'grayscale' : ''}">
                                     </div>\`;
                        });
                    }
                    previewArea.innerHTML = html;
                    
                    status.innerHTML = "<b>Status:</b> Displaying " + (labelImages.length * copies) + " labels for " + pSelect.value;
                }

                // Update preview whenever any setting changes
                pSelect.onchange = renderPreview;
                pSize.onchange = renderPreview;
                pMode.onchange = renderPreview;
                copiesInput.oninput = renderPreview;

                function doSimulatePrint() {
                    // This function just refreshes the simulation without opening browser print
                    renderPreview();
                    alert(" Updated! " + copiesInput.value + " copies generated in the preview area.");
                }

                // Initial Load
                window.onload = renderPreview;
            <\/script>
        </body>
        </html>
    `;

    simWindow.document.open();
    simWindow.document.write(htmlContent);
    simWindow.document.close();
}
// 🔹 Async sync function — ensures all objects (text + QR + groups) are cloned before snapshot
async function syncCopiesFromOriginal() {
    const clonePromises = canvases.map(copyCanvas => {
        if (!copyCanvas || copyCanvas === previewCanvas) return Promise.resolve();

        return new Promise(resolveCanvas => {
            if (!copyCanvas.contextContainer) return resolveCanvas();

            try {
                // Preserve background + border
                const preserved = copyCanvas.getObjects().filter(o => o.name === 'background' || o.name === 'border');
                copyCanvas.getObjects().forEach(obj => {
                    if (!preserved.includes(obj)) copyCanvas.remove(obj);
                });

                const scaleX = copyCanvas.getWidth() / previewCanvas.getWidth();
                const scaleY = copyCanvas.getHeight() / previewCanvas.getHeight();

                const objPromises = previewCanvas.getObjects().map(obj => {
                    if (obj.name === 'background' || obj.name === 'border') return Promise.resolve();

                    return new Promise(resolveObj => {
                        obj.clone(async cloned => {
                            if (!cloned) return resolveObj();

                            if (cloned.type === "text" || cloned.type === "textbox") {
                              cloned.set({
                                 left: obj.left * scaleX,
                                 top: obj.top * scaleY,
                                 fontSize: (obj.fontSize || 20) * ((scaleX + scaleY)/2),
                                 text: obj.text,
                                 name: obj.name            // 🔥 THIS LINE FIXES PRINT
                                  });

                                cloned.setCoords();
                                copyCanvas.add(cloned);
                                copyCanvas.renderAll();
                                resolveObj();
                            } 
                            else if (cloned.type === "image") {
                                const imgEl = new Image();
                                imgEl.crossOrigin = "anonymous";
                                imgEl.onload = function() {
                                    cloned.setElement(imgEl);
                                    cloned.set({
                                        left: obj.left * scaleX,
                                        top: obj.top * scaleY,
                                        scaleX: (obj.scaleX || 1) * scaleX,
                                        scaleY: (obj.scaleY || 1) * scaleY
                                    });
                                    cloned.setCoords();
                                    copyCanvas.add(cloned);
                                    copyCanvas.renderAll();
                                    resolveObj();
                                };
                                imgEl.src = obj._element.src;
                            } 
                            else if (cloned.type === "group") {
                                cloned.set({
                                    left: obj.left * scaleX,
                                    top: obj.top * scaleY,
                                    scaleX: (obj.scaleX || 1) * scaleX,
                                    scaleY: (obj.scaleY || 1) * scaleY
                                });
                                cloned.forEachObject(o => {
                                    o.set({
                                        left: o.left * scaleX,
                                        top: o.top * scaleY,
                                        scaleX: (o.scaleX || 1) * scaleX,
                                        scaleY: (o.scaleY || 1) * scaleY
                                    });
                                    o.setCoords();
                                });
                                cloned.setCoords();
                                copyCanvas.add(cloned);
                                copyCanvas.renderAll();
                                resolveObj();
                            } 
                            else {
                                cloned.set({
                                    left: obj.left * scaleX,
                                    top: obj.top * scaleY,
                                    scaleX: (obj.scaleX || 1) * scaleX,
                                    scaleY: (obj.scaleY || 1) * scaleY
                                });
                                cloned.setCoords();
                                copyCanvas.add(cloned);
                                copyCanvas.renderAll();
                                resolveObj();
                            }
                        });
                    });
                });

                Promise.all(objPromises).then(() => resolveCanvas());

            } catch (err) {
                console.warn("⚠️ Sync failed for one copy:", err);
                resolveCanvas();
            }
        });
    });

    await Promise.all(clonePromises);
    console.log("✅ All copies fully synced (text + QR + groups)");
}
document.addEventListener("DOMContentLoaded", async function () {
  console.log("🧠 JS Started!");

  const printerSelect = document.getElementById("printerSelect");
  const statusBox = document.getElementById("printerStatus");
  const dpiInfoBox = document.getElementById("dpiInfo");
  const getInfoBtn = document.getElementById("getInfoBtn");

  statusBox.textContent = "🔄 Loading printers...";

  try {
    const response = await fetch("http://localhost/items/printer_list");
    console.log("🌐 Response:", response);
    const data = await response.json();
    console.log("📦 Data:", data);

    if (data.success) {
      statusBox.textContent = `✅ Found ${data.printers.length} printer(s)`;
      printerSelect.innerHTML = '<option value="">Select Printer</option>';
      data.printers.forEach(p => {
        const opt = document.createElement("option");
        opt.value = p.trim();
        opt.textContent = p.trim();
        printerSelect.appendChild(opt);
      });
    } else {
      statusBox.textContent = "⚠️ No printers found";
    }
  } catch (err) {
    console.error("🚨 Fetch error:", err);
    statusBox.textContent = "❌ Error fetching printers";
  }

// document.addEventListener("DOMContentLoaded", async function () {
//   const printerSelect = document.getElementById("printerSelect");
//   const statusBox = document.getElementById("printerStatus");
//   const dpiInfoBox = document.getElementById("dpiInfo");
//   const getInfoBtn = document.getElementById("getInfoBtn");

//   console.log("🧠 DOM Loaded:", printerSelect);

//   statusBox.textContent = "🔄 Loading printers...";

//   try {
//     const response = await fetch("http://localhost/items/printer_list");
//     console.log("🌐 Raw response:", response);

//     if (!response.ok) {
//       throw new Error(`HTTP ${response.status} - ${response.statusText}`);
//     }

//     const data = await response.json();
//     console.log("📦 JSON Data:", data);

//     if (data.success && Array.isArray(data.printers) && data.printers.length > 0) {
//       statusBox.textContent = `✅ Found ${data.printers.length} printer(s)`;

//       printerSelect.innerHTML = '<option value="">Select Printer</option>';
//       data.printers.forEach((printer, i) => {
//         const opt = document.createElement("option");
//         opt.value = printer.trim();
//         opt.textContent = printer.trim();
//         printerSelect.appendChild(opt);
//         console.log(`🔹 Added printer [${i + 1}]: ${printer.trim()}`);
//       });

//       console.log("✅ Final dropdown options:", printerSelect.innerHTML);
//     } else {
//       statusBox.textContent = "⚠️ No printers found.";
//       console.warn("⚠️ Empty printer list:", data);
//     }

//   } catch (err) {
//     console.error("🚨 Fetch error:", err);
//     statusBox.textContent = "❌ Error fetching printers (check console)";
//   }

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
// document.addEventListener("DOMContentLoaded", async function () {
//   const printerSelect = document.getElementById("printerSelect");
//   const statusBox = document.getElementById("printerStatus");
//   const dpiInfoBox = document.getElementById("dpiInfo");
//   const getInfoBtn = document.getElementById("getInfoBtn");

//   console.log("🧠 DOM Loaded:", printerSelect);

//   statusBox.textContent = "🔄 Loading printers...";

//   try {
//     // ✅ Use full correct URL (important)
//     const response = await fetch("http://localhost/items/printer_list");
//     console.log("🌐 Raw response:", response);

//     // ✅ Check if response ok
//     if (!response.ok) {
//       throw new Error(`HTTP ${response.status} - ${response.statusText}`);
//     }

//     // ✅ Get JSON safely
//     const data = await response.json();
//     console.log("📦 JSON Data:", data);

//     if (data.success && Array.isArray(data.printers) && data.printers.length > 0) {
//       statusBox.textContent = `✅ Found ${data.printers.length} printer(s)`;

//       // Clear and populate dropdown
//       printerSelect.innerHTML = '<option value="">Select Printer</option>';
//       data.printers.forEach((printer, i) => {
//         const opt = document.createElement("option");
//         opt.value = printer.trim();
//         opt.textContent = printer.trim();
//         printerSelect.appendChild(opt);
//         console.log(`🔹 Added printer [${i + 1}]: ${printer.trim()}`);
//       });

//       console.log("✅ Final dropdown options:", printerSelect.innerHTML);
//     } else {
//       statusBox.textContent = "⚠️ No printers found.";
//       console.warn("⚠️ Empty printer list:", data);
//     }

//   } catch (err) {
//     console.error("🚨 Fetch error:", err);
//     statusBox.textContent = "❌ Error fetching printers (check console)";
//   }

//   // ✅ Get DPI Info button click
//   getInfoBtn.addEventListener("click", async function () {
//     const printerName = printerSelect.value;
//     if (!printerName) {
//       alert("Please select a printer first!");
//       return;
//     }

//     console.log("🔍 Fetching info for:", printerName);
//     dpiInfoBox.textContent = "⏳ Fetching printer info...";

//     try {
//       const res = await fetch("http://localhost/items/printer_info", {
//         method: "POST",
//         headers: { "Content-Type": "application/json" },
//         body: JSON.stringify({ printerName }),
//       });

//       if (!res.ok) throw new Error("HTTP error " + res.status);

//       const data = await res.json();
//       console.log("🖨 Printer Info Response:", data);

//       if (data.success && data.info) {
//         const info = data.info;
//         dpiInfoBox.textContent =
//           `🖨 Printer: ${info.printer}\n📏 DPI: ${info.dpiX} x ${info.dpiY}\n📄 Paper Size: ${info.paperSize}`;
//       } else {
//         dpiInfoBox.textContent = "⚠️ No info found for this printer.";
//       }
//     } catch (err) {
//       console.error("🚨 Info fetch error:", err);
//       dpiInfoBox.textContent = "❌ Error fetching printer info.";
//     }
//   });
// });



// --- UPDATED PROGRESS TICK (GREEN TICK) ---
function updateProgress(stepId) {
    const statusEl = document.getElementById('tick-step' + stepId); // tick-step ID ki match cheyandi
    if (statusEl) {
        statusEl.style.visibility = 'visible';
        statusEl.innerHTML = "✅";
        statusEl.style.color = "green";
    }
}

// --- STEP 1: CREATE PRINT PAGE ---
confirmPrintPage.addEventListener('click', async () => {
    const widthMM = parseFloat(printPageWidthInput.value);
    const heightMM = parseFloat(printPageHeightInput.value);

    // ... (Your validation code) ...

    if (printPageBox) printPageBox.remove();

    // Create the White Box (The Print Page)
    printPageBox = document.createElement('div');
    printPageBox.id = "printPageBox";
    
    // Apply your styles (White background, border, etc.)
    Object.assign(printPageBox.style, {
        width: (widthMM * 7.992) + 'px',
        height: (heightMM * 7.992) + 'px',
        background: '#fff',
        border: '2px solid #000',
        position: 'absolute',
        top: '80px',
        left: '80px',
        zIndex: '5'
    });
    
    labelPreviewBox.appendChild(printPageBox);

    // ✅ FIXED: Show the tick mark using the correct ID
    const tick = document.getElementById('tick-step1');
    if (tick) {
        tick.style.visibility = 'visible'; 
    }

    printPageModal.style.display = 'none';
});



function markDone(id) {
  const box = document.getElementById(id);
  if (!box) return;

  box.classList.add("active");
  box.textContent = "✔";   // ✔ INSIDE the box
}

function resetDone(id) {
  const box = document.getElementById(id);
  if (!box) return;

  box.classList.remove("active");
  box.textContent = "";    // empty box
}




function calculateLabelLayout(paperSize, labelSize, margins) {
    const paper = getPaperDimensions(paperSize);
    const usableWidth = paper.width - margins.left - margins.right;
    const usableHeight = paper.height - margins.top - margins.bottom;
    
    const cols = Math.floor(usableWidth / labelSize.width);
    const rows = Math.floor(usableHeight / labelSize.height);
    
    return {
        cols: cols,
        rows: rows,
        total: cols * rows,
        spacing: {
            horizontal: (usableWidth - (cols * labelSize.width)) / (cols - 1) || 0,
            vertical: (usableHeight - (rows * labelSize.height)) / (rows - 1) || 0
        }
    };
}
async function saveAndUpdatePreview() {
    console.log("🚀 Final Spacing Fix: Tightening everything to fit all values...");
    
    try {
        if (!previewCanvas) {
            alert("⚠️ Preview Canvas not found!");
            return;
        }

        const inputs = document.querySelectorAll('.app-container input[type="text"]');
        let canvasObjects = previewCanvas.getObjects().filter(obj => 
            (obj.type === 'text' || obj.type === 'i-text' || obj.type === 'textbox') && obj.name !== 'logo'
        );

        let fieldsToSave = [];
        let textObjectIndex = 0;
 
        const startTop = 10;        
        const rowHeight = 14;       

        for (let i = 0; i < inputs.length; i += 2) {
            const labelName = inputs[i] ? inputs[i].value.trim() : "";
            const labelValue = inputs[i+1] ? inputs[i+1].value.trim() : "";
            
            if (labelName || labelValue) {
                fieldsToSave.push({ name: labelName, value: labelValue });

                // 🔥 ఇక్కడ మార్పు చేశాను: 
                // Title అయితేనే డైరెక్ట్ వాల్యూ వస్తుంది. 
                // మిగిలిన అన్నింటికీ (Status, Capacity, etc.) "Name : Value" ఫార్మాట్ వస్తుంది.
                let finalString = "";
                if (labelName.toLowerCase() === 'title') {
                    finalString = labelValue;
                } else if (labelName !== "" && labelValue !== "") {
                    finalString = `${labelName} : ${labelValue}`;
                } else {
                    finalString = labelName || labelValue; // ఒకవేళ ఏదైనా ఒకటి ఖాళీగా ఉంటే ఉన్నది చూపిస్తుంది
                }

                if (canvasObjects[textObjectIndex]) {
                    canvasObjects[textObjectIndex].set({ 
                        text: finalString,
                        fontWeight: 'bold',
                        fontSize: 14,            
                        lineHeight: 0.4,         
                        width: 600,
                        fontFamily: 'Verdana',
                        top: startTop + (textObjectIndex * rowHeight), 
                        splitByGrapheme: false,
                        objectCaching: false
                    });
                    canvasObjects[textObjectIndex].setCoords();
                } 
                else {
                    const newText = new fabric.Textbox(finalString, {
                        left: 10,
                        top: startTop + (textObjectIndex * rowHeight), 
                        fontSize: 14,
                        fontWeight: 'bold',
                        lineHeight: 0.4,
                        width: 600,
                        fontFamily: 'Verdana',
                        splitByGrapheme: false
                    });
                    previewCanvas.add(newText);
                }
                textObjectIndex++;
            }
        }

        while (textObjectIndex < canvasObjects.length) {
            previewCanvas.remove(canvasObjects[textObjectIndex]);
            textObjectIndex++;
        }

        previewCanvas.renderAll();
        
        if (typeof syncCopiesFromOriginal === "function") {
            await syncCopiesFromOriginal();
        }

        alert("✅ Preview Updated with " + fieldsToSave.length + " values!");

    } catch (error) {
        console.error("🚨 Update Error:", error);
    }
}
</script>


   
</body>
</html>
<!-- <STOP>
  <STOP> -->
