window.addEventListener('DOMContentLoaded', () => {
    const d = document
  	const form = d.querySelector('#wc-shipping-sim-form');
    const results = d.querySelector('#wc-shipping-sim-results');
    const input = form.querySelector('#wc-shipping-sim .input-text');
    const button = form.querySelector('#wc-shipping-sim .button.submit');
    const nonce = d.querySelector('#wc-shipping-sim #nonce');
    const I = (val) => val;
    const hooks = window.wc_shipping_simulator = {
        filterFormData: I,
        filterXHR: I,
        filterResults: I,
        filterProduct: I,
        filterQuantity: I,
        beforeSubmit: (xhr) => {
            results.innerHTML = '';
            input.disabled = true;
            button.disabled = true;
            button.classList.add('loading')
        },
        afterSubmit: (xhr) => {
            input.disabled = false;
            button.disabled = false;
            button.classList.remove('loading')
        },
        submitHandler: (evt) => {
            evt.preventDefault();
    
            const qty = getQuantity();
            const product = getProduct();
    
            let formData = `action=${form.dataset.ajaxAction}&nonce=${nonce.value}&postcode=${input.value}&product=${product.id}&quantity=${qty >= 1 ? qty : 1}`;
            
            formData = hooks.filterFormData(formData);
            
            let xhr = new XMLHttpRequest();
            xhr.open('GET', encodeURI(form.action + '?' + formData), true);
            xhr.onreadystatechange = () => {
                if (XMLHttpRequest.DONE !== xhr.readyState) return;
                hooks.afterSubmit(xhr)
                
                const res = JSON.parse(xhr.responseText);
                if (res.success) {
                    // success
                    results.innerHTML = hooks.filterResults(res.results_html ? res.results_html : '')
                } else {
                    // error
                    alert(res.error)
                }
                console.log(xhr)
            };
    
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
        }
    };

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
