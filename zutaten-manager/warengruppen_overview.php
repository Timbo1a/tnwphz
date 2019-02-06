<script type="text/javascript">
var tableWG;
var endpoint = ajaxurl;
$(document).ready(function () {
	
	 tableWG = $('#wgOverview').DataTable({
	      "ajax": {
              "url": endpoint,
              "type": "POST",
              "data": {
                  "action": "zmAJAX",
                  "function": "loadProductGroups"
              },
              "responsive": true
          },
          "columns": [
              { "data": "PK_Warengruppe" },
              { "data": "Bezeichnung" }
          ]
          

	 });
	
});

</script>
        
<form name="warengruppen">
    <div class="table-responsive" style="width:80%">
        <table id="wgOverview" class="display compact" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bezeichnung</th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Bezeichnung</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <div style="width:100%;height:30px;border:1px black solid;display:none" id="messageContainer"></div>
    </div>
</form>