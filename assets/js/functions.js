import TomSelect from 'tom-select';

const bootstrap = require('bootstrap/dist/js/bootstrap.bundle.min.js');
const selectElement = (element) => document.querySelector(element);

window.addEventListener('DOMContentLoaded', () => {
    selectElement('.hamburger').addEventListener('click', () => {
        selectElement('.hamburger').classList.toggle('active');
    });
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    if (toastElList) {
        const toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl, {delay: 5000});
        });
        toastList.forEach(toast => toast.show());
    }

    initialiseTomSelect();
});


function initialiseTomSelect() {
    if (!document.getElementById('tom-select')) {
        return;
    }

    new TomSelect('#tom-select', {
        plugins: {
            remove_button: {
                title: '',
            },
        },
        maxItems: 5,
        render: {
            option: (data) => {
                return '<div class="text-start">' + '<span class="badge rounded-pill bg-dark text-start">' + data.text + '</span>' + '</div> + <hr>';
            },
            item: (data) => {
                return '<div>' + '<span class="badge rounded-pill bg-dark">' + data.text + '</span>' + '</div>';
            }
        }
    });

    // document.getElementById('tom-select-ts-dropdown').addEventListener('click', () => {
    //     const input = document.getElementById('tom-select-ts-control');
    //     if (document.getElementsByClassName('item').length === 4) {
    //         input.style.caretColor = 'transparent';
    //         input.style.height = '.25rem';
    //         input.blur();
    //         let removeButtons = document.getElementsByClassName('remove');
    //         for (let item of removeButtons) {
    //             item.addEventListener('click', () => {
    //                 input.style.caretColor = 'initial';
    //                 input.style.height = 'initial';
    //             })
    //         }
    //     }
    // });
}
