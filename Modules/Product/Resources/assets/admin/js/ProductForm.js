export default class {
    constructor() {
        this.managerStock();

        window.admin.removeSubmitButtonOffsetOn([
            '#images', '#downloads', '#attributes', '#options', '#reviews',
        ]);

        $('#product-create-form, #product-edit-form').on('submit', this.submit);
    }

    managerStock() {
        $('#manage_stock').on('change', (e) => {
            if (e.currentTarget.value === '1') {
                $('#qty-field').removeClass('hide');
            } else {
                $('#qty-field').addClass('hide');
            }
        });
    }

    submit(e) {
        e.preventDefault();

        DataTable.removeLengthFields();

        e.currentTarget.submit();
    }
}
