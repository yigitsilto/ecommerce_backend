<script type="text/html" id="tax-rate-template">
    <tr class="tax-rate">
        <td class="text-center">
            <span class="drag-icon">
                <i class="fa">&#xf142;</i>
                <i class="fa">&#xf142;</i>
            </span>
        </td>

        <td class="tax-name">
            <input type="hidden" name="rates[<%- rateId %>][id]" value="<%- rate.id %>">

            <input type="text" name="rates[<%- rateId %>][name]" value="<%- rate.name %>" class="form-control" id="rates.<%- rateId %>.name">
        </td>



        <td class="rate">
            <input type="number" name="rates[<%- rateId %>][rate]" value="<%- rate.rate %>" step="0.01" min="0" class="form-control" id="rates.<%- rateId %>.rate">
        </td>

{{--        <td class="text-center">--}}
{{--            <button type="button" class="btn btn-default delete-row" data-toggle="tooltip" title="{{ trans('tax::taxes.form.delete_rate') }}">--}}
{{--                <i class="fa fa-trash"></i>--}}
{{--            </button>--}}
{{--        </td>--}}
    </tr>
</script>
