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
    };

    const event = new Event('wc_shipping_simulator:init');
    d.dispatchEvent(event);
    
    form.addEventListener('submit', (evt) => {
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
            if (200 === xhr.status) {
                // success
                results.innerHTML = hooks.filterResults(res.results_html ? res.results_html : '')
            } else {
                // error
            }
            console.log(xhr)
        };

        xhr = hooks.filterXHR(xhr)

        hooks.beforeSubmit(xhr)

        button.classList.add('loading')
        xhr.send();
    });

    if (input.dataset.mask) {
        // apply input mask
        input.addEventListener('input', (evt) => {
            input.value = applyMask(input.value || '', input.dataset.mask);
            input.maxLength = input.dataset.mask.length;
        })
        // helper: applyMask (text: string, mask: string, symbol: string = 'X'): string
        // usage: applyMask('01012000', 'XX-XX-XXXX') // returns "01-01-2000"
        function applyMask(e,t,n="X"){let l="";const o=(e+"").replace(/[^a-z0-9]/gi,"");for(let e=0,r=0,c=t.length;e<c&&o[r];e++)n===t[e]?(l+=o[r],r++):(l+=t[e]||"",r=r>0?r--:0);return l}
    }

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
