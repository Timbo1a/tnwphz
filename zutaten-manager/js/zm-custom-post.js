  function ingredientTableAddRow(){
    var actualSelection = $('#zmAddIngredientSelection');
    var id = $(actualSelection).val();
    var text = $(actualSelection).data('text');
    var nRows = $('#ZMIngredientTable').find('tr').length-1; //Amount of rows in table
  
    var rowBuffer = '';
    rowBuffer += '<tr>';
        rowBuffer += '<td><input name="zmMenge['+nRows+']" size="5" type="text" value="1.0" /></td>';  
        rowBuffer += '<td>'+getHiddenSelectBoxForAddFunction(nRows)+'</td>';
        rowBuffer += '<td><input name="zmZutat['+nRows+']" type="hidden" value="'+id+'"  /><b>'+text+'</b></td>';
        rowBuffer += '<td><input name="zmZusatz['+nRows+']" type="text" value="" /></td>';    
        rowBuffer += '<td><input name="zmGruppe['+nRows+']" type="text" value="" /></td>';    
        rowBuffer += '<td><a href="javascript:void(0);" onClick="$(this).parent().parent().remove();" id="zmDeleteRowLink">Löschen</a></td>';
    rowBuffer += '</tr>';
  
    $('#ZMIngredientTable').append(rowBuffer);
  }
  
  function getHiddenSelectBoxForAddFunction(nRows){
    return atob($('#zmHiddenSelectBoxForAddFunction').val()).replace("%nRows%", nRows);
  }
  
  //Style each selectbox option 
  function formatIngredientOption (ingredient) {
  var markup = "<div style=\"\"><b>"+ingredient.text+"</b><br/>";
  markup += ingredient.Energie_KJ +" kJ, Fett: " +Math.round(ingredient.Fett,2)+"g, ... " +"</div>";
  return markup;
  }
  
  //trigger on selection
  function formatIngredientSelection (ingrdient) {
  return '<input type="hidden" data-text="'+ingrdient.text+'" id="zmAddIngredientSelection" value="'+ingrdient.id+'" />' + ingrdient.text;
  }
  
  //Add entry to subrecipe group
  function addSubRecipeGroupItem(name){
    $('#zmSelectboxMulti').append('<option value="'+name+'">'+name+'</option>');
  }
  
  
  var zmIngredientsDD;
  var data;
  $(document).ready(function () {
  
  //Event Listeners
    $('#zmAddIngredientToRecipe').click(function(){
        ingredientTableAddRow();
        $( "#ZMIngredientTable" ).sortable();
        $( "#ZMIngredientTable" ).disableSelection();
    })
  
        zmIngredientsDD =  $('.js-example-basic-single').select2({
        ajax: {
            url: ajaxurl,
            type: "POST",
            delay: 150,
            data: function(params){
                params.action = "zmAJAX";
                params.function = "zmSimpleAjaxLoadAllIngredients";
                return params;
            },
            processResults: function(data){
              //remap data to id/title format
              data.data = $.map(data.data, function (obj) {
                  obj.id = obj.id || obj.pk || obj.PK_Zutat; 
                  obj.text = obj.text || obj.Bezeichnung; 
                
                  return obj;
              });
             
              return{
                  results: data.data
              };
          },
            transport: function(params, success, failure){ 
                var $request = $.ajax(params);
                $request.then(success);
                $request.fail(failure);
                return $request;
            }
        },
        theme: "classic",
        width : "20%",
        placeholder: "Nach Zutat suchen",
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatIngredientOption,
        templateSelection: formatIngredientSelection
  
  });
  
  
  /*$('#zmSelectboxMulti').select2({
        width: "20%"
  });*/
  
  
  
  // //END Document.ready
  });
  