// EXECUTAR MÁSCARAS
function mascara(o, funcao) {
    setTimeout(function () {
    o.value = funcao(o.value);
    }, 1);
    }

function executaMascara(){
    objeto.value=funcao(objeto.value)
}

//MASCARAS
//TELEFONE

function mascara(campo, funcao) {
    setTimeout(() => {
        campo.value = funcao(campo.value);
    }, 1);
}
//rg/cpf

function nome(v){
    return v.replace(/\d/g,"")
}

function telefone1(v) {
    v = v.replace(/\D/g, "");
    v = v.replace(/^(\d\d)(\d)/g, "($1) $2");
    v = v.replace(/(\d{5})(\d)/, "$1-$2");
    return v;
    }