document.addEventListener('DOMContentLoaded', function () {
    let elems = document.querySelectorAll('select');
    let instances = M.FormSelect.init(elems, {});

    elems = document.querySelectorAll('.datepicker');
    instances = M.Datepicker.init(elems, {
        format: 'yyyy-mm-dd',
        autoClose: true
    });
});
