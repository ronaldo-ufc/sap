/* global BASE_URL */
$(document).ready(function () {
    $('.dinheiro').mask('#.##0,00', {reverse: true});
    carremaMskCPFCNPJ();
});

$(document).on("click", "#btnAdicionar", function () {
    var info = $(this).attr('data-id');
    var str = info.split('|');
    var titulo = str[0];
    var id = str[1];
    $("#titulo").html(titulo);
    $("#titulo").val(id);
    if(titulo == 'Localização'){
        document.getElementById('sigla').removeAttribute('hidden');
        document.getElementById('sigla_item').setAttribute('type','text');
    }else{
        document.getElementById('sigla').setAttribute('hidden','');
        document.getElementById('sigla_item').setAttribute('type','hidden');
    }
});

$(document).on("click", "#btnEntrada", function () {
    var codigo = $(this).attr('data-id');
    var str = codigo.split('|');
    var titulo = str[0];
    var id = str[1];
    $("#titulo").html(titulo);
    $("#item").val(id);
    $("#setor_id").val(str[2]);
});

function aumenta(obj){
    obj.height=obj.height*1000;
    obj.width=obj.width*1000;
}
 
function diminui(obj){
	obj.height=obj.height/2;
	obj.width=obj.width/2;
}

$(document).on("click", "#btnAdicionar", function () {
    var info = $(this).attr('data-id');
    var str = info.split('|');
    var titulo = str[0];
    var id = str[1];
    $("#titulo").html(titulo);
    $("#titulo").val(id);
});

function btnExcluir(url,titulo,mensagem){
    $('#btn_modal_excluir').attr({
       'href': url
    });
    $('#modalLabelExcluir').empty();
    $('#mensagemModal').empty();
    $('#modalLabelExcluir').append(titulo);
    $('#mensagemModal').append(mensagem);
}

function btnCancelar(url,titulo,mensagem){
    $('#btn_modal_cancelar').attr({
       'href': url
    });
    $('#modalLabelCancelar').empty();
    $('#mensagemModalCancel').empty();
    $('#modalLabelCancelar').append(titulo);
    $('#mensagemModalCancel').append(mensagem);
}


function btnReabrir(url){
    $('#btn_modal_reabrir').attr({
       'href': url
    });
}

function btnRemover(patrimonio){
    $('#input_hidden').attr({
       'value': patrimonio
    });
}

function patrimonioRemover(){
    
    var pat = document.getElementById('input_hidden').value;
    document.getElementById(pat).remove();
//    console.log(pat);
}

$("#busca_item").keyup(function () {
    var nomeProduto = $("#busca_item").val();
    if (nomeProduto.length <= 3) return;
    $.ajax({

        url: BASE_URL+"/materiais/seach/"+nomeProduto,
        dataType: 'html',
        data: {produto: nomeProduto},
        type: "POST",

        beforeSend: function () {
            $('#carregando').show();
         
        },
        success: function (data) {
            $('#carregando').hide();
            $("#resBusca").html(data);

        },
        error: function (data) {
             $('#carregando').html(data);
            
        }



    });
});

function choiceProduto(id){
    $("#resBusca").html('');
    $.ajax({

        url: BASE_URL+"/materiais/seach/itens/"+id,
        dataType: 'html',
        data: {produto: id},
        type: "POST",

        beforeSend: function () {
            $('#carregando').show();
         
        },
        success: function (data) {
            $('#carregando').hide();
            $("#resChoice").html(data);

        },
        error: function (data) {
             $('#carregando').html(data);
            
        }



    });
    
}

function editarProduto(id){ 
    var URL = BASE_URL+'/materiais/produto/editar/'+id;
    $(window.document.location).attr('href', URL);
}

function carremaMskCPFCNPJ(){
    var cpfMascara = function (val) {
       return val.replace(/\D/g, '').length > 11 ? '00.000.000/0000-00' : '000.000.000-009';
    },
    cpfOptions = {
       onKeyPress: function(val, e, field, options) {
          field.mask(cpfMascara.apply({}, arguments), options);
       }
    };
    $('.mascara-cpfcnpj').mask(cpfMascara, cpfOptions);
}


