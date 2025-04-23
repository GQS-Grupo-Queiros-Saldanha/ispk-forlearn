@role('admin|superadmin')
<div class="blk-background-image" style="background-image: url('{{ asset('img/background_staff.jpg') }}');"></div>
@elserole('student')
<div class="blk-background-image" style="background-image: url('{{ asset('img/background_students.jpg') }}');"></div>
@elserole('teacher')
<div class="blk-background-image" style="background-image: url('{{ asset('img/background_teachers.jpg') }}');"></div>
@else
<div class="blk-background-image" style="background-image: url('{{ asset('img/background_students.jpg') }}');"></div>
@endrole
