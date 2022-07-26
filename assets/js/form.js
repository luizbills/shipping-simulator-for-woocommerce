window.addEventListener('DOMContentLoaded', () => {
    const params = window.wc_shipping_simulator_params || {};
    const d = document;
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
        filterPostcodeMaxLength: () =>
            input.dataset.mask ? input.dataset.mask.length : 20,

        beforeSubmit: (xhr) => {
            config.hooks.resultsHandler('');
            input.disabled = true;
            button.classList.add('loading');
        },
        afterSubmit: (xhr) => {
            input.disabled = false;
            button.classList.remove('loading');
        },
        resultsHandler: (html) => {
            results.innerHTML = html;
        },
        errorHandler: (message, data) => {
            alert(message || config.errors.unexpected);
            console.error('wc-shipping-simulator request error:', data);
        },
        submitHandler: (evt) => {
            evt.preventDefault();
            if (config.requesting) return;

            config.requesting = true;

            const product = getProduct();
            if (0 === product.id) {
                config.requesting = false;
                return;
            }

            const variation = product.variation_id
                ? '&variation=' + product.variation_id
                : '';
            const qty = getQuantity();

            const formData = config.hooks.filterFormData(
                `action=${form.dataset.ajaxAction}&nonce=${
                    nonce.value
                }&postcode=${input.value}&product=${product.id}&quantity=${
                    qty >= 1 ? qty : 1
                }${variation}`
            );

            let xhr = new XMLHttpRequest();

            xhr.open('GET', encodeURI(form.action + '?' + formData), true);

            xhr.timeout = +config.timeout || 0;

            xhr.onload = () => {
                config.requesting = false;
                config.hooks.afterSubmit(xhr);

                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.success) {
                        const results = config.hooks.filterResults(
                            res.results_html ? res.results_html : ''
                        );
                        config.hooks.resultsHandler(results);
                    } else {
                        config.hooks.errorHandler(res.error, res);
                    }
                } catch (e) {
                    config.hooks.errorHandler(null, e);
                }
            };

            xhr.ontimeout = () => {
                config.requesting = false;
                config.hooks.afterSubmit(xhr);
                config.hooks.errorHandler(config.errors.timeout, 'timeout');
            };

            xhr = config.hooks.filterXHR(xhr);

            config.hooks.beforeSubmit(xhr);

            xhr.send();
        },
        inputMaskHandler: () => {
            if (input.dataset.mask) {
                // apply input mask
                input.addEventListener('input', (evt) => {
                    const mask = input.dataset.mask;
                    input.value = applyMask(input.value || '', mask);
                    input.maxLength = config.hooks.filterPostcodeMaxLength();
                });

                d.dispatchEvent(new Event('input'));

                // usage: applyMask('01012000', 'XX-XX-XXXX') // returns "01-01-2000"
                function applyMask(text, mask, symbol = 'X') {
                    if (!mask) return text;
                    let result = '';
                    // remove all non allphanumerics
                    const _text = (text + '').replace(/[^a-z0-9]/gi, '');
                    for (let i = 0, j = 0, len = mask.length; i < len; i++) {
                        if (!_text[j]) break;
                        if (symbol === mask[i]) {
                            result += _text[j];
                            j++;
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
    const config = {
        requesting: false,
        ...params,
        hooks,
        // errors: {
        //     timeout: 'Timeout error',
        //     unexpected: 'Unexpected error',
        // },
    };

    // Use this global object to manipulate the simulator
    window.wc_shipping_simulator = config;

    // Event to update the global object
    d.dispatchEvent(new Event('wc_shipping_simulator:init'));

    config.hooks.inputMaskHandler && config.hooks.inputMaskHandler();

    form.addEventListener('submit', config.hooks.submitHandler);

    if (config.auto_submit) {
        input.addEventListener('input', (evt) => {
            if (
                input.value &&
                input.value.length === config.hooks.filterPostcodeMaxLength()
            ) {
                button.click();
                input.blur();
            }
        });
    }

    function getProduct() {
        const product = {
            type: form.dataset.productType,
            id: form.dataset.productId,
        };
        if ('variable' === product.type) {
            const variation_id_input = d.querySelector(
                '.variations_form .variation_id'
            );
            product.variation_id = variation_id_input
                ? variation_id_input.value
                : 0;
            if (config.requires_variation && !product.variation_id) {
                const error = wc_add_to_cart_variation_params
                    ? wc_add_to_cart_variation_params.i18n_make_a_selection_text
                    : '';
                config.hooks.errorHandler(error, 'no_variation_selected');
                product.id = 0; // abort the submit
            }
        }
        return config.hooks.filterProduct(product);
    }

    function getQuantity() {
        let value = form.dataset.quantity;
        let input = d.querySelector('[name="quantity"]');
        if (input) {
            value = input.value;
        }
        return config.hooks.filterQuantity(value | 0);
    }
});
