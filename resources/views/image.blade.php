@if($image)
<img src="{{ Storage::url($image) }}" height="75" width="75" alt="" />
@else
<img src="{{ Storage::url('images/dummy_user_image.png') }}" alt="" height="75" width="75">
@endif
