window.addEventListener('DOMContentLoaded', () => {
    const d = document
  	const form = d.querySelector('#wc-shipping-sim-form');
    const input = form.querySelector('.input-postcode');
    const button = form.querySelector('.button.submit');
    const nonce = form.querySelector('#nonce');
    const results = d.querySelector('#wc-shipping-sim-results');
    const I = (val) => val;
    const hooks = {
        filterFormData: I,
        filterXHR: I,
        filterResults: I,
        filterProduct: I,
        filterQuantity: I,

        beforeSubmit: (xhr) => {
            hooks.resultsHandler('');
            input.disabled = true;
            button.classList.add('loading')
        },
        afterSubmit: (xhr) => {
            input.disabled = false;
            button.classList.remove('loading')
        },
        resultsHandler: (html) => {
            results.innerHTML = html;
        },
        errorHandler: (message, data) => {
            alert(message ? message : 'Error');
            console.error('wc-shipping-simulator request error:', data);
        },
        submitHandler: (evt) => {
            evt.preventDefault();
            if (config.requesting) return;

            config.requesting = true;

            const product = getProduct();
            if ( 0 === product.id ) {
                config.requesting = false;
                return;
            }

            const variation = product.variation_id ? '&variation=' + product.variation_id : '';
            const qty = getQuantity();

            const formData = hooks.filterFormData(`action=${form.dataset.ajaxAction}&nonce=${nonce.value}&postcode=${input.value}&product=${product.id}&quantity=${qty >= 1 ? qty : 1}${variation}`);

            let xhr = new XMLHttpRequest();

            xhr.open('GET', encodeURI(form.action + '?' + formData), true);

            xhr.timeout = 300000; // 5 minutes

            xhr.onload = () => {
                config.requesting = false;
                hooks.afterSubmit(xhr)

                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        const results = hooks.filterResults(res.results_html ? res.results_html : '')
                        hooks.resultsHandler(results);
                    } else {
                        hooks.errorHandler(res.error, res)
                    }
                } catch (e) {
                    hooks.errorHandler('Unexpected error', e)
                }
            };

            xhr.ontimeout = () => {
                hooks.errorHandler('Timeout error', 'timeout')
            }

            xhr = hooks.filterXHR(xhr)

            hooks.beforeSubmit(xhr)

            xhr.send();
        },
        inputMaskHandler: () => {
            if (input.dataset.mask) {
                // apply input mask
                input.addEventListener('input', (evt) => {
                    const mask = input.dataset.mask;
                    input.value = applyMask(input.value || '', mask);
                    input.maxLength = mask ? mask.length : 20;
                })

                // usage: applyMask('01012000', 'XX-XX-XXXX') // returns "01-01-2000"
                function applyMask (text, mask, symbol = 'X') {
                    if (!mask) return text;
                    let result = '';
                    // remove all non allphanumerics
                    const _text = (text + '').replace(/[^a-z0-9]/gi, '');
                    for (let i = 0, j = 0, len = mask.length; i < len; i++) {
                        if (!_text[j]) break;
                        if (symbol === mask[i]) {
                            result += _text[j]
                            j++
                        } else {
                            result += mask[i] || '';
                            j = j > 0 ? j-- : 0;
                        }
                    }
                    return result;
                  }
            }
        },
    };
    const config = window.wc_shipping_simulator = {
        requesting: false,
        hooks,
    }

    const event = new Event('wc_shipping_simulator:init');
    d.dispatchEvent(event);

    form.addEventListener('submit', hooks.submitHandler);

    hooks.inputMaskHandler && hooks.inputMaskHandler();

    function getProduct () {
        const product = {
            type: form.dataset.productType,
            id: form.dataset.productId
        };
        if ( 'variable' === product.type ) {
            const variation_id_input = d.querySelector('.variations_form .variation_id');
            product.variation_id = variation_id_input ? variation_id_input.value : null;
            if (!product.variation_id) {
                const error = wc_add_to_cart_variation_params ? wc_add_to_cart_variation_params.i18n_make_a_selection_text : '';
                hooks.errorHandler(error, 'no_variation_selected')
                product.id = 0; // abort the submit
            }
        }
        return hooks.filterProduct(product)
    }

    function getQuantity () {
        let value = form.dataset.quantity;
        let input = d.querySelector('[name="quantity"]');
        if (input) {
            value = input.value;
        }
        return hooks.filterQuantity(value|0)
    }
})
