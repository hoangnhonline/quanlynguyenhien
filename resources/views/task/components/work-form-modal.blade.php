<div class="modal-header">
    <h4 class="modal-title">{{ $title }}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <i class="icon-close"></i>
    </button>
</div>

<div class="modal-body">
    {!! $component !!}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-info" id="btnSaveInfo">Lưu lại</button>
</div>
