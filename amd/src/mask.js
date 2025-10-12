import Imask from 'mod_contractactivity/imask';

const init = () => {
    $(document).ready(() => {
        const postalCode = document.querySelector('#id_postal_code');
        const cpf = document.querySelector('#id_cpf');
        const personDocument = document.querySelector('#id_document');
        Imask(postalCode, {
            mask: '00000-000'
        });
        Imask(cpf, {
            mask: '000.000.000-00'
        });
        Imask(personDocument, {
            mask: [
                {
                    mask: '00.000.000-0'
                },
                {
                    mask: '00000000000'
                },
            ]
        });
    });
};

export default { init };
