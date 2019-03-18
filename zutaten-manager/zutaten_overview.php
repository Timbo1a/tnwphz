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
                rowId : 'PK_Zutat',
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
                            return "<span style=\"display:none;\">"+data+"</span><input class=\"rowInputMember\" id=\"rowInputID_Bezeichnung\" size=\"50\" type=\"text\" value=\"" + data + "\" />";
                        },
                        "targets": 1
                    },
                    {
                        "render": function (data, type, row) {
                            //Alle Warengruppen iterieren und Vorauswahl treffen
                            var buf = '<select id=\"rowInputID_FK_Warengruppe\" class="rowInputMember">';
                            var searchField = "";
                            $(warengruppe).each(function () {
                                buf += '<option value="' + this.PK_Warengruppe + '" ' + (data == this.PK_Warengruppe ? 'selected' : '') + ' >' + this.Bezeichnung + '</option>';
                                if(data == this.PK_Warengruppe) searchField = this.Bezeichnung;
                            });
                            buf += '</select>';
                            
                            //Für die Suche:
                            buf = '<span style="display:none;">'+searchField+'</span>' + buf;
                            return buf;
                        },
                        "targets": 2,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Energie_KJ\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Energie_KJ\">kj</label>";
                        },
                        "targets": 3,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Fett\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Fett\"></label>";
                        },
                        "targets": 4,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Fett_gesaettigt\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Fett_gesaettigt\"></label>";
                        },
                        "targets": 5,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Kohlenhydrate\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Kohlenhydrate\"></label>";
                        },
                        "targets": 6,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Kohlenhydrate_Zucker\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Kohlenhydrate_Zucker\"></label>";
                        },
                        "targets": 7,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Eiweiss\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Eiweiss\"></label>";
                        },
                        "targets": 8,
                        "width": "1%"
                    },
                    {
                        "render": function (data, type, row) {
                            if (data == null) data = "";
                            return "<input id=\"rowInputID_Salz\" class=\"rowInputMember\" size=\"4\" type=\"text\" value=\"" + data + "\" /><label class=\"inRowLabel\" for=\"rowInputID_Salz\"></label>";
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
                            var buff = "<div class=\"delete\">";
                            if (data == null || data == "0") {
                                buff += "<a class=\"deleteIngredient\" data-pkzutat=\"" + row.PK_Zutat + "\" href=#>Löschen</a>";
                            } else {
                                buff += data + "x verwendet";
                            }
                            buff += "</div>";
                            buff += "<div style=\"display:none;\" class=\"actualize\"><a href=\"javascript:void(0);\" onClick=\"updateRow('"+encodeURI(JSON.stringify(row))+"');\">Aktualisieren</a></div>";
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
                        var thisRow = $(this).parent().parent();
                        //ID zu Namen ändern, da ID eindeutig sein sollte.
                        table.row(row).data().Bezeichnung = $(thisRow).find('#rowInputID_Bezeichnung').val();
                        table.row(row).data().FK_Warengruppe = $(thisRow).find('#rowInputID_FK_Warengruppe').val();
                        table.row(row).data().Energie_KJ = $(thisRow).find('#rowInputID_Energie_KJ').val();
                        table.row(row).data().Fett = $(thisRow).find('#rowInputID_Fett').val()
                        table.row(row).data().Fett_gesaettigt = $(thisRow).find('#rowInputID_Fett_gesaettigt').val();
                        table.row(row).data().Kohlenhydrate = $(thisRow).find('#rowInputID_Kohlenhydrate').val();
                        table.row(row).data().Kohlenhydrate_Zucker = $(thisRow).find('#rowInputID_Kohlenhydrate_Zucker').val();
                        table.row(row).data().Eiweiss = $(thisRow).find('#rowInputID_Eiweiss').val();
                        table.row(row).data().Salz = $(thisRow).find('#rowInputID_Salz').val();
                        table.row(row).data().Einheit = $(thisRow).find('#rowInputID_Einheit option:selected').val();
                        table.row(row).data().immer_zuhause = $(thisRow).find('#rowInputID_immer_zuhause').is(':checked');
                        table.row(row).data().hasChanged = true;

                        $(thisRow).find('.actualize').show();
                        $(thisRow).find('.delete').hide();
                        $(thisRow).css('background-color', '#ed9955');
                        //console.log(table.row(row).data());
                        //AJAX Delete
                        /*$.post(endpoint, { action: "zmAJAX", function: "updateIngredient", data: rowData })
                            .done(function (data) {
                                table.ajax.reload(null, false);
                            })
                            .fail(function (data) {
                                alert("Fehler beim Löschen");
                                table.ajax.reload(null, false);
                            });*/
                    });

                    //Klick Event für Löschen dynamisch binden
                    var element2 = $(row).find('.deleteIngredient');
                    $(element2).unbind("click");
                    element2.on("click", function () {
                        //AJAX Delete
                        $.post(endpoint, { action: "zmAJAX", function: "deleteIngredient", id: rowData.PK_Zutat })
                            .done(function (data) {
                                table.ajax.reload(null, false);
                                writeMessage('Der Datensatz ' + JSON.stringify(data) + " wurde gelöscht.");
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
                        table.ajax.reload(null, false);
                        writeMessage("Zutat " + bez + " erfolgreich mit der ID " + data.data + "  hinzugefügt.")
                    })
                    .fail(function (data) {
                        alert("Fehler beim Hinzufügen");
                    });
            });

            //TODO: Hier wird für jede Zeile ein eigener AJAX-Call gemacht, das ist suboptimal, funktioniert aber erstmal...
            $('#updateDT').click(function(){
                console.log(table.rows().data());
                $(table.rows().data()).each(function(){
                    if(typeof(this.hasChanged) == "boolean"){
                        if(this.hasChanged === true) updateRow(encodeURI(JSON.stringify(this)));
                    }
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
            container.html(container.html() + "<br/>"+msg);
            container.show(10);
            //setTimeout(function () { $('#messageContainer').hide(1000) }, 5000);
        }

        function updateRow(rowData){
            var row = JSON.parse(decodeURI(rowData));
            var actualRowData = table.row('#'+row.PK_Zutat).data();
            $.post(endpoint, { action: "zmAJAX", function: "updateIngredient", data: actualRowData })
                .done(function (data) {
                    table.ajax.reload(null, false);
                    writeMessage("Erfolgreich aktualisiert: " +actualRowData.Bezeichnung);
                })
                .fail(function (data) {
                    alert("Fehler beim Aktualisieren");
                    writeMessage(data.responseText);
                    table.ajax.reload(null, false);
            });
        }
            
        </script>
       
       <div id="Zutaten">


    <div id=""><input type="text" name="Bezeichnung" id="zmIngredientName" value="" /> 
        <select id="zmIngredientFKWG"></select> <button onClick="javascript:void(0);" id="zmAddIngredient">Hinzufügen</button>
    </div>


<form name="zutaten">
    <div class="table-responsive" style="width:90%">
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
        
        <button type="button" onClick="javascript:void(0);" id="updateDT" class="btn btn-warning">Alle Aktualisieren</button>
        <hr />
        <div style="width:100%;border:1px black solid;display:none;padding:5px" id="messageContainer"></div>
    </div>
</form>
</div>