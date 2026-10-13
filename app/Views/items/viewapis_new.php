<div id="page-content" class="page-wrapper clearfix scrollable-page">
  <div class="card">
    <div class="page-title clearfix">
      <h1><?php echo 'APIS'; ?></h1>
    </div>
    <div class="table-responsive">
      <table id="item-table" class="display" cellspacing="0" width="100%"></table>
    </div>
  </div>
</div>

<!-- Full-Screen Modal -->
<div id="rowModal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
    <h3>Row Details</h3>
    <table id="rowDetailsTable" border="1" width="100%"></table>
  </div>
</div>

<style>
.scrollable-page {
  max-height: 500px;
  overflow-y: auto;
}

/* Modal overlay */
#rowModal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.7);
  display: none;
  z-index: 9999;
}

/* Full screen modal content */
.modal-content {
  position: relative;
  background: #fff;
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 20px;
  overflow-y: auto;
  border-radius: 0;
}

.close-btn {
  position: absolute;
  top: 15px;
  right: 20px;
  cursor: pointer;
  font-size: 24px;
  font-weight: bold;
}

#rowDetailsTable td {
  padding: 6px;
  border: 1px solid #ccc;
}
#rowDetailsTable tr:nth-child(odd) {
  background: #f9f9f9;
}
</style>

<script>
function buildDetailsTable(headers, row){
  let html = "";
  for (let i=0; i<headers.length; i++){
    let param = headers[i] || ("Column "+(i+1));
    let value = row[i] || "";

    if(param && value){
      if(param === "User Id" && value.toLowerCase().includes("edit")){
        html += `<tr style="background:#ffecec; color:black; font-weight:bold;">
                  <td><b>${param}</b></td>
                  <td>${value}</td>
                </tr>`;
      }
      else if(param === "User Id" && value.toLowerCase().includes("delete")){
        html += `<tr style="background:#fff0f0; color:black; font-weight:bold;">
                  <td><b>${param}</b></td>
                  <td>${value}</td>
                </tr>`;
      }
      else {
        html += `<tr><td><b>${param}</b></td><td>${value}</td></tr>`;
      }
    }
  }
  return html;
}

$(document).ready(function(){
  var $tbl = $('#item-table');

  $tbl.appTable({
    source: '<?php echo_uri("items/list_data2/".$itemid) ?>',
    order: [[0, 'desc']],
    columns: [
      {title: "Brand Name", visible:false},
      {title: "Brand logo", visible:false},
      {title: "Product Name", visible:false},
      {title: "Device Image", visible:false},
      {title: "Model Name", visible:false},
      {title: "Product Description", visible:false},
      {title: "Number of pages", visible:false},
      {title: "Date", visible:false},
      {title: "itemid", visible:false},
      {title: "filename", visible:false},
      {title: "apidate", visible:false},
      // {title: "Parameter Name"},
      {
  title: "Parameter Name",
  class: "text-left",
  render: function(data, type, row){
    return '<b>' + data + '</b>';
  }
},

      {
        title: "Action",
        class: "text-center",
        orderable: false,
        print_details: false,
        render: function(){
          return '<button class="btn btn-sm btn-info view-btn">View</button>';
        }
      },
      {title: "Command Dataflow", visible:false},
      {title: "Command framesize", visible:false},
      {title: "Command no of rows", visible:false},
      {title: "Command frametrigger", visible:false},
      {title: "Command multipleui", visible:false},
      {title: "Command triggerui", visible:false},
      {title: "Response Dataflow", visible:false},
      {title: "Response Framesize", visible:false},
      {title: "Response no of rows", visible:false},
      {title: "Response Frametrigger", visible:false},
      {title: "Response multipleui", visible:false},
      {title: "Response triggerui", visible:false},
      {title: "Error Dataflow", visible:false},
      {title: "Error Framesize", visible:false},
      {title: "Error no of rows", visible:false},
      {title: "Error Frametrigger", visible:false},
      {title: "Error multipleui", visible:false},
      {title: "Error triggerui", visible:false},
      {title: "Command startBytePosition", visible:false},
      {title: "Command endBytePosition", visible:false},
      {title: "Command fieldDescription", visible:false},
      {title: "Command numberOfBytes", visible:false},
      {title: "Command bitSize", visible:false},
      {title: "Command dataType", visible:false},
      {title: "Command valueDefinition", visible:false},
      {title: "Command displayValueMin", visible:false},
      {title: "Command displayValueMax", visible:false},
      {title: "Command valueInHexMin", visible:false},
      {title: "Command valueInHexMax", visible:false},
      {title: "Command uiType", visible:false},
      {title: "Command uiId", visible:false},
      {title: "Command uiPageNo", visible:false},
      {title: "Command asperUI", visible:false},
      {title: "Command Serial Number asperUI", visible:false},
      {title: "Command asperuivalue", visible:false},
      {title: "Command uiDescriptionInOut", visible:false},
      {title: "Command sliderStepValue", visible:false},
      {title: "Command defaultvalue", visible:false},
      {title: "Command developerNotes", visible:false},
      {title: "Response startBytePosition", visible:false},
      {title: "Response endBytePosition", visible:false},
      {title: "Response fieldDescription", visible:false},
      {title: "Response numberOfBytes", visible:false},
      {title: "Response bitSize", visible:false},
      {title: "Response dataType", visible:false},
      {title: "Response valueDefinition", visible:false},
      {title: "Response displayValueMin", visible:false},
      {title: "Response displayValueMax", visible:false},
      {title: "Response valueInHexMin", visible:false},
      {title: "Response valueInHexMax", visible:false},
      {title: "Response uiType", visible:false},
      {title: "Response uiId", visible:false},
      {title: "Response uiPageNo", visible:false},
      {title: "Response asperUI", visible:false},
      {title: "Response Serial Number asperUI", visible:false},
      {title: "Response asperuivalue", visible:false},
      {title: "Response uiDescriptionInOut", visible:false},
      {title: "Response sliderStepValue", visible:false},
      {title: "Response defaultvalue", visible:false},
      {title: "Response developerNotes", visible:false},
      {title: "Error startBytePosition", visible:false},
      {title: "Error endBytePosition", visible:false},
      {title: "Error fieldDescription", visible:false},
      {title: "Error numberOfBytes", visible:false},
      {title: "Error bitSize", visible:false},
      {title: "Error dataType", visible:false},
      {title: "Error valueDefinition", visible:false},
      {title: "Error displayValueMin", visible:false},
      {title: "Error displayValueMax", visible:false},
      {title: "Error valueInHexMin", visible:false},
      {title: "Error valueInHexMax", visible:false},
      {title: "Error uiType", visible:false},
      {title: "Error uiId", visible:false},
      {title: "Error uiPageNo", visible:false},
      {title: "Error asperUI", visible:false},
      {title: "Error Serial Number asperUI", visible:false},
      {title: "Error asperuivalue", visible:false},
      {title: "Error uiDescriptionInOut", visible:false},
      {title: "Error sliderStepValue", visible:false},
      {title: "Error defaultvalue", visible:false},
      {title: "Error developerNotes", visible:false},
      // {title: "Update"},
      {
  title: "Update",
  class: "text-center",
  orderable: false,
  render: function(data, type, row){
    // yaha data me "Edit ×" aayega
    return '<button class="btn btn-sm btn-info">'+ data +'</button>';
  }
},

      {title: "Options", visible:false},
    ]
  });

var dt = $tbl.DataTable();

// View button
$('#item-table tbody').on('click', '.view-btn', function(){
  var row = dt.row($(this).closest('tr')).data();
  var headers = dt.columns().header().to$().map(function(){return $(this).text();}).get();
  $('#rowDetailsTable').html(buildDetailsTable(headers, row));
  $('#rowModal').fadeIn(200);
});

// Close view modal
$(document).on('click', '.close-btn', function(){
  $('#rowModal').fadeOut(200);
});


});

</script>
