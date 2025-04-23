<div class="modal fade" role="dialog" id="modal_type_image_avatar">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form_modal_image_avatar" action="{!! route('users.update_avatar') !!}" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Users::users.update_image')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <input type="file" class="form-control-file" name="avatar" id="avatarFile" aria-describedby="fileHelp">
                        <img id="uploaded_image_preview" src="#" style="display: none;" alt="Avatar" />
                        <small id="fileHelp" class="form-text text-muted">Please upload a valid image file. Size of image should not be more than 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn forlearn-btn add">
                        <i class="far fa-save"></i>@lang('modal.confirm_button')
                    </button>
                    <button type="button" class="btn forlearn-btn cancel" data-dismiss="modal">
                        <i class="far fa-window-close"></i>@lang('modal.cancel_button')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@section('scripts')
@parent
<script>
    function readURL(input) {

        if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $('#uploaded_image_preview').attr('src', e.target.result);
            $('#uploaded_image_preview').fadeIn(500);
        };

        reader.readAsDataURL(input.files[0]);
        }
    }

    $("#avatarFile").change(function() {
        var a=(this.files[0].size);

        if(a > 1000000) {
            alert('A imagem é demasiado grande.');
        }else{
            readURL(this);
        }
    });
</script>
@endsection
