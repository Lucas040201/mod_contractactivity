const init = async () => {
    $('form').on('submit', function(e) {
        const diploma = $('[name="diploma"]').val();
        if (!diploma) {
            alert('Envie frente e verso do diploma antes de submeter o formulario.');
            e.preventDefault();
        }
        const document = $('[name="rg_cnh"]').val();
        if (!document) {
            alert('Envie frente e verso do documento antes de submeter o formulario.');
            e.preventDefault();
        }
        const adress_proof = $('[name="address_proof"]').val();
        if (!adress_proof) {
            alert('Envie frente e verso do comprovante de endereço antes de submeter o formulario.');
            e.preventDefault();
        }
    });
}

export default {
    init
}
