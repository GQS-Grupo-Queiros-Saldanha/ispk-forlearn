<input title="{{ $title ?? '' }}" type="checkbox" name="items[]" class="form-check-input" value="{{ $id }}" @if($checked) checked @endif @if($disabled ?? false) disabled @endif>
