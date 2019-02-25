<script type="text/javascript">
        var table;
        var endpoint = ajaxurl;
        //Wird per Ajax erstellt (Vor document.ready)
        var warengruppe;

        $.post(endpoint, { action: "zmAJAX", function: "loadProductGroups" })
            .done(function (data) {
                //warengruppe = JSON.parse(data);
                warengruppe = data.data;
                //Warengruppen dem Hinzufügen-Dialog hinzufügen
                addWGToAddDialog();
            })
            .fail(function (data) {
                alert("Fehler beim Laden der Warengruppen");
            });

        $(document).ready(function () {
            //var data = table.$('input, select').serialize(); //Form submit
            table = $('#ztOverview').DataTable({
                "ajax": {
                    "url": endpoint,
                    "type": "POST",
                    "data": {
                        "action": "zmAJAX",
                        "function": "zmAllIngredients"
                    },
                    "responsive": true
                },
                "columns": [
                    { "data": "PK_Zutat" },
                    { "data": "Bezeichnung" },
                    { "data": "FK_Warengruppe" },
                    { "data": "Energie_KJ" },
                    { "data": "Fett" },
                    { "data": "Fett_gesaettigt" },
                    { "data": "Kohlenhydrate" },
                    { "data": "Kohlenhydrate_Zucker" },
                    { "data": "Eiweiss" },
                    { "data": "Salz" },
                    { "data": "Einheit" },
                    { "data": "immer_zuhause" },
                    { "data": "referenzen" }
                ],
                "columnDefs": [
                    {
                        "targets": 0,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            return "<input class=\"rowInputMember\" id=\"rowInputID_Bezeichnung\" size=\"50\" type=\"text\" value=\"" + data + "\" />";
                        },
                        "targets": 1
                    },
                    {
                        "render": function (data, type, row) {
                            //Alle Warengruppen iterieren und Vorauswahl treffen
                            var buf = '<select id=\"rowInputID_FK_Warengruppe\" class="rowInputMember">';
                            $(warengruppe).each(function () {
                                buf += '<option value="' + this.PK_Warengruppe + '" ' + (data == this.PK_Warengruppe ? 'selected' : '') + ' >' + this.Bezeichnung + '</option>';
                            });
                            buf += '</select>';
                            return buf;
                        },
                        "targets": 2,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Energie_KJ class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Energie_KJ\">kj</label>";
                        },
                        "targets": 3,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Fett\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Fett\">g</label>";
                        },
                        "targets": 4,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Fett_gesaettigt\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Fett_gesaettigt\">g</label>";
                        },
                        "targets": 5,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Kohlenhydrate\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Kohlenhydrate\">g</label>";
                        },
                        "targets": 6,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Kohlenhydrate_Zucker\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Kohlenhydrate_Zucker\">g</label>";
                        },
                        "targets": 7,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Eiweiss\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Eiweiss\">g</label>";
                        },
                        "targets": 8,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Salz\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Salz\">g</label>";
                        },
                        "targets": 9,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<select id=\"rowInputID_Einheit\" class=\"rowInputMember\"><option " + (data == null ? "selected" : "") + " value=\"null\">---</option><option " + (data == "100g" ? "selected" : "") + " value=\"100g\">auf 100g</option><option " + (data == "100ml" ? "selected" : "") + " value=\"100ml\">auf 100ml</option></select>";
                        },
                        "targets": 10,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            return "<input id=\"rowInputID_immer_zuhause\" class=\"rowInputMember\" type=\"checkbox\" value=\"" + data + "\" " + (data == 1 ? "checked" : "") + " />";
                        },
                        "targets": 11,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            var buff = "";
                            if (data == null || data == "0") {
                                buff = "<a class=\"deleteIngredient\" data-pkzutat=\"" + row.PK_Zutat + "\" href=#>Löschen</a>";
                            } else {
                                buff = data + "x verwendet";
                            }
                            return buff;
                        },
                        "targets": 12,
                        "width": "1%"
                    }
                ],

                //EventListener binden.
                "rowCallback": function (row, rowData) {
                    var element = $(row).find('.rowInputMember');
                    $(element).unbind("change");

                    element.on("change", function () {
                        //Sync data source with table manually
                        table.row(row).data().Bezeichnung = $('#rowInputID_Bezeichnung').val();
                        table.row(row).data().FK_Warengruppe = $('#rowInputID_FK_Warengruppe').val();
                        table.row(row).data().Energie_KJ = $('#rowInputID_Energie_KJ').val();
                        table.row(row).data().Fett = $('#rowInputID_Fett').val();
                        table.row(row).data().Fett_gesaettigt = $('#rowInputID_Fett_gesaettigt').val();
                        table.row(row).data().Kohlenhydrate = $('#rowInputID_Kohlenhydrate').val();
                        table.row(row).data().Kohlenhydrate_Zucker = $('#rowInputID_Kohlenhydrate_Zucker').val();
                        table.row(row).data().Eiweiss = $('#rowInputID_Eiweiss').val();
                        table.row(row).data().Salz = $('#rowInputID_Salz').val();
                        table.row(row).data().Einheit = $('#rowInputID_Einheit').val();
                        table.row(row).data().immer_zuhause = $('#rowInputID_immer_zuhause').val();

                        //AJAX Delete
                        $.post(endpoint, { action: "zmAJAX", function: "updateIngredient", data: rowData })
                            .done(function (data) {
                                table.ajax.reload();
                            })
                            .fail(function (data) {
                                alert("Fehler beim Löschen");
                                table.ajax.reload();
                            });
                    });

                    //Klick Event für Löschen dynamisch binden
                    var element2 = $(row).find('.deleteIngredient');
                    $(element2).unbind("click");
                    element2.on("click", function () {
                        //AJAX Delete
                        $.post(endpoint, { action: "zmAJAX", function: "deleteIngredient", id: rowData.PK_Zutat })
                            .done(function (data) {
                                table.ajax.reload();
                            })
                            .fail(function (data) {
                                writeMessage('Fehler beim Löschen des Datensatzes: ' + data, "error");
                            });
                    })
                }
            });

            //Event Listener Zutat hinzufügen
            $('#zmAddIngredient').click(function () {
                var bez = $('#zmIngredientName').val();
                var fkwg = $('#zmIngredientFKWG').val();
                $.post(endpoint, { action: "zmAJAX", function: "addIngredient", "bezeichnung": bez, "FK_Warengruppe": fkwg })
                    .done(function (data) {
                        table.ajax.reload();
                    })
                    .fail(function (data) {
                        alert("Fehler beim Hinzufügen");
                    });
            });
        });

        //Lädt die Warengruppen aus dem Objekt "Warengruppen" und populiert Dropdown
        function addWGToAddDialog() {
            var buff = "";
            $(warengruppe).each(function () {
                buff += '<option value="' + this.PK_Warengruppe + '" >' + this.Bezeichnung + '</option>';
            });
            $('#zmIngredientFKWG').html(buff);
        }

        function writeMessage(msg, state) {
            var container = $('#messageContainer')
            
            //if (state === "error") {
            //    container.css("background-color", "red");
            //} 
            container.html(msg);
            container.show(10);
            setTimeout(function () { $('#messageContainer').hide(1000) }, 5000);
        }
            
        </script>
       
       <div id="Zutaten">


    <div id=""><input type="text" name="Bezeichnung" id="zmIngredientName" value="" /> <select id="zmIngredientFKWG"></select> <button onClick="javascript:void(0);" id="zmAddIngredient">Hinzufügen</button></div>


<form name="zutaten">
    <div class="table-responsive" style="width:80%">
        <table id="ztOverview" class="display compact" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bezeichnung</th>
                    <th>Warengruppe</th>
                    <th>Energie</th>
                    <th>Fett</th>
                    <th>-gesättigt</th>
                    <th>Kohlenhydrate</th>
                    <th>-gesättigt</th>
                    <th>Eiweiß</th>
                    <th>Salz</th>
                    <th>Angabe</th>
                    <th>IZ</th>
                    <th></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Bezeichnung</th>
                    <th>Warengruppe</th>
                    <th>Energiegehalt</th>
                    <th>Fettanteil_</th>
                    <th>Fett-gesättigt</th>
                    <th>Kohlenhydrate</th>
                    <th>davon-gesättigt</th>
                    <th>Eiweißgehalt</th>
                    <th>Salzgehalt</th>
                    <th>Angabe</th>
                    <th>IZ</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <div style="width:100%;height:30px;border:1px black solid;display:none" id="messageContainer"></div>
    </div>
</form>
</div>