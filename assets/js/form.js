window.addEventListener('DOMContentLoaded', () => {
    const d = document
  	const form = d.querySelector('#wc-shipping-sim-form');
    const results = d.querySelector('#wc-shipping-sim-results');
    const input = form.querySelector('#wc-shipping-sim .input-text');
    const button = form.querySelector('#wc-shipping-sim .button.submit');
    const nonce = d.querySelector('#wc-shipping-sim #nonce');
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
            alert(message);
            console.error('wc-shipping-simulator request error:', data);
        },
        submitHandler: (evt) => {
            evt.preventDefault();
            if (config.requesting) return;
            config.requesting = true;

            const qty = getQuantity();
            const product = getProduct();
            const formData = hooks.filterFormData(`action=${form.dataset.ajaxAction}&nonce=${nonce.value}&postcode=${input.value}&product=${product.id}&quantity=${qty >= 1 ? qty : 1}`);

            if (config.cache && config.cache[formData]) {
                hooks.resultsHandler(config.cache[formData])
                config.requesting = false;
                return;
            }

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
                        config.cache[formData] = results;
                    } else {
                        hooks.errorHandler(res.error, res)
                    }
                } catch (e) {
                    hooks.errorHandler('Unexpected error', e)
                }
            };

            xhr.ontimeout = () => {
                hooks.errorHandler('Timeout error', null)
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
        cache: {},
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
            // TODO
        }
        return hooks.filterProduct(product)
    }

    function getQuantity () {
        let value = form.dataset.quantity ? form.dataset.quantity|0 : 1;
        return hooks.filterQuantity(value)
    }
})
