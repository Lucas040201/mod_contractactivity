import $ from 'jquery';

const init = async () => {
    $(document).ready(() => {
        const $cep = $('#id_postal_code');
        const $address = $('#id_address');
        const $neighbourhood = $('#id_neighbourhood');
        const $city = $('#id_city');

        if (!$cep.length) return;

        // Cria popup de loading (overlay)
        const $overlay = $(`
            <div id="viacep-overlay">
                <div class="viacep-spinner">
                    <div class="viacep-loader"></div>
                    <p>Consultando CEP...</p>
                </div>
            </div>
        `).css({
            position: 'fixed',
            top: 0,
            left: 0,
            width: '100%',
            height: '100%',
            background: 'rgba(0,0,0,0.4)',
            display: 'none',
            justifyContent: 'center',
            alignItems: 'center',
            zIndex: 9999,
        });

        // CSS interno do spinner e texto
        const spinnerCSS = `
            #viacep-overlay .viacep-spinner {
                text-align: center;
                color: white;
                font-size: 16px;
                font-family: sans-serif;
            }
            #viacep-overlay .viacep-loader {
                border: 4px solid #f3f3f3;
                border-top: 4px solid #0073e6;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                animation: spin 1s linear infinite;
                margin: 0 auto 10px;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;

        // Injeta o estilo dinamicamente
        $('head').append(`<style>${spinnerCSS}</style>`);

        // Adiciona overlay ao body
        $('body').append($overlay);

        $cep.on('blur', async function() {
            const cep = $(this).val().replace(/\D/g, '');
            if (cep.length !== 8) return;

            // Mostra overlay
            $overlay.fadeIn(200);
            $overlay.css({
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center'
            })

            try {
                const response = await $.getJSON(`https://viacep.com.br/ws/${cep}/json/`);
                if (response.erro) {
                    alert('CEP não encontrado!');
                    return;
                }

                $address.val(response.logradouro || '');
                $neighbourhood.val(response.bairro || '');
                $city.val(response.localidade || '');
            } catch (error) {
                console.error('Erro ao consultar o CEP:', error);
                alert('Erro ao consultar o CEP.');
            } finally {
                // Esconde overlay
                $overlay.fadeOut(200);
            }
        });
    });
};

export default { init };
