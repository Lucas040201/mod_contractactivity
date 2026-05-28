import Imask from 'mod_contractactivity/imask';

const init = () => {
    $(document).ready(() => {
        const postalCode = document.querySelector('#id_postal_code');
        const cpf = document.querySelector('#id_cpf');
        Imask(postalCode, {
            mask: '00000-000'
        });
        Imask(cpf, {
            mask: '000.000.000-00'
        });
    });
};

export default { init };
