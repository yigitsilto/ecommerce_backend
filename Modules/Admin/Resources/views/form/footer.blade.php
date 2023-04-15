<div class="form-group">
    <div class="{{ ($buttonOffset ?? true) ? 'col-md-offset-2' : '' }} col-md-10">
        <button type="submit" class="btn btn-primary" id="save-button-form" data-loading>
            {{ trans('admin::admin.buttons.save') }}
        </button>
    </div>
</div>
