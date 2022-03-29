const bootstrap = require('bootstrap/dist/js/bootstrap.bundle.min.js');
const selectElement = (element) => document.querySelector(element);

window.addEventListener('load', () => {
    selectElement('.hamburger').addEventListener('click', () => {
        selectElement('.hamburger').classList.toggle('active');
    });
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    if (toastElList) {
        const toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl, { delay: 5000 });
        });
        toastList.forEach(toast => toast.show());
    }
});
