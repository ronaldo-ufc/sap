
function menuSelect() {
    
    var e = document.getElementById("privilegio");
    var itemSelecionado = e.options[e.selectedIndex].value;

    var url = BASE_URL+'/services/menu/'+itemSelecionado;
    
    $.getJSON(url, function (data)  {
            
      var option_menu = '<option value="">Selecione um menu... </option>';
     
      $.each(data, function (i, val) {
          option_menu +=  '<option value="' + val[0] + '">' + val[1]+ '</option>';
      });    
      $('#menu').html(option_menu);
    });
                              
}

function subMenuSelect() {
    var e = document.getElementById("privilegio");
    var privilegio = e.options[e.selectedIndex].value;
    
    var f = document.getElementById("menu");
    var menu = f.options[f.selectedIndex].value;
    
    var url = BASE_URL+'/services/submenu/'+privilegio+'/'+menu;
    
    $.getJSON(url, function (data)  {
            
      var option_menu;
     
      $.each(data, function (i, val) {
        var c = "";
        if (val[2] === 'S'){
            c = "checked";
        }
          option_menu +=  '<tr class="text-center"><td><input '+c+' name="chk[]" value="'+ val[0] +'" type="checkbox"> </td> <td>'+val[1]+'</td></tr>';
      });
      
      
      $('#submenu').html(option_menu);
    });
                              
}


function modeloSelect() {
    
    var e = document.getElementById("marca");
    var itemSelecionado = e.options[e.selectedIndex].value;

    var url = BASE_URL+'/services/modelos/'+itemSelecionado;

    $.getJSON(url, function (data)  {

      var option_menu = '<option value="">Selecione um modelo... </option>';

      $.each(data, function (i, val) {
          option_menu +=  '<option value="' + val[0] + '">' + val[1]+ '</option>';
      });    
      $('#modelo').html(option_menu);
    });
                              
}

function notaSelect() {
    var e = $('#doc_select').val();
    var url = BASE_URL+'/services/notas/'+$('#doc_select').val().replace(/[^0-9]/g,'');

    $.getJSON(url, function (data)  {
      var option_menu, nome;
      $.each(data, function (i, val) {
          nome = val[2];
          option_menu +=  '<option value="' + val[0] + '">' + val[1]+ '</option>';
      });    
      $('#nota_select').html(option_menu);
      $('#razaosocial_select').val(nome);
    });                        
}

function buscaRazaoSocial() {
    var e = $('#doc_select').val();
    var url = BASE_URL+'/services/fornecedor/'+$('#doc_select').val().replace(/[^0-9]/g,'');

    $.getJSON(url, function (data)  {
      $('#razaosocial_select').val(data);
    });                        
}