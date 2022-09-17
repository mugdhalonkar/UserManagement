<!DOCTYPE html>
<html>
<head>
  <title>User Management</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link  href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
  <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
</head>
<body>
<div class="container mt-4">

  <div class="col-md-12 mt-1 mb-2"><button type="button" id="addNewuser" class="btn btn-success">Add</button></div>
  <div class="card">
    <div class="card-header text-center font-weight-bold">
      <h2>User Management</h2>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="datatable-ajax-crud">
           <thead>
              <tr>
                 <th>Id</th>
                 <th>Image</th>
                 <th>Name</th>
                 <th>Email</th>
                 <th>Experience</th>
                 <th>Action</th>
              </tr>
           </thead>
        </table>
    </div>
  </div>

    <div class="modal fade" id="ajax-user-model" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="ajaxuserModel"></h4>
          </div>
          <div class="modal-body">
            <form action="javascript:void(0)" id="addEdituserForm" name="addEdituserForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="id" id="id">
              <div class="form-group">
                <label class="col-sm-12 control-label">Upload Image</label>
                <div class="col-sm-6 pull-left">
                  <input type="file" class="form-control" id="image" name="image" required="">
                </div>
                <div class="col-sm-6 pull-right">
                  <img id="preview-image" src="{{ Storage::url('images/dummy_user_image.png') }}"
                        alt="preview image" style="max-height: 100px;">
                </div>
              </div>
              <div class="form-group">
                <label for="name" class="col-sm-12 control-label">Full Name</label>
                <div class="col-sm-12">
                  <input type="text" class="form-control" id="name" name="name" placeholder="Enter full name" maxlength="50" required="">
                </div>
              </div>
              <div class="form-group">
                <label for="name" class="col-sm-12 control-label">Email</label>
                <div class="col-sm-12">
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" maxlength="50" required="">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-12 control-label">Date of joining</label>
                <div class="col-sm-12">
                  <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" placeholder="" required="">
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-12 control-label">Date of leaving</label>
                <div class="col-sm-12">
                  <input type="date" class="form-control" id="date_of_leaving" name="date_of_leaving" placeholder="">
                </div>
              </div>

              <div class="form-group col-sm-12">
                <input class="form-check-input" style="margin-left: 1px" type="checkbox" value="1" id="checkbox">
                <label class="form-check-label" style="margin-left: 20px" for="checkbox">
                Still working
                </label>
              </div>

              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary" id="btn-save" value="addNewuser">Save changes
                </button>
              </div>
            </form>
          </div>
          <div class="modal-footer">

          </div>
        </div>
      </div>
    </div>

<script type="text/javascript">

 $(document).ready( function () {
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#image').change(function(){

    let reader = new FileReader();
    reader.onload = (e) => {
      $('#preview-image').attr('src', e.target.result);
    }
    reader.readAsDataURL(this.files[0]);

   });
    $('#datatable-ajax-crud').DataTable({
           processing: true,
           serverSide: true,
           ajax: "{{ url('list-users') }}",
           columns: [
                    {data: 'id', name: 'id', 'visible': false},
                    { data: 'image', name: 'image' , orderable: false},
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'experience', name: 'experience' },
                    {data: 'action', name: 'action', orderable: false},
                 ],
          order: [[0, 'desc']]
    });
    $('#addNewuser').click(function () {
       $('#addEdituserForm').trigger("reset");
       $('#ajaxuserModel').html("Add user");
       $('#ajax-user-model').modal('show');
       $("#image").attr("required", "true");
       $('#id').val('');
       $('#preview-image').attr('src',base_url + '/images/dummy_user_image.png');
    });

    $('body').on('click', '.edit', function () {
        var id = $(this).data('id');
        var base_url = '<?php echo Storage::url(''); ?>';
        var checkbox = document.getElementById('checkbox').checked;
        $.ajax({
            type:"POST",
            url: "{{ url('edit-user') }}",
            data: { id: id },
            dataType: 'json',
            success: function(res){
              $('#ajaxuserModel').html("Edit user");
              $('#ajax-user-model').modal('show');
              $('#id').val(res.id);
              $('#image').removeAttr('required');
              let text =res.image;
              let result = text.replace("public/","");
              $('#preview-image').attr('src',base_url + result);
              $('#name').val(res.name);
              $('#email').val(res.email);
              $('#date_of_joining').val(res.date_of_joining);
              if(checkbox == true){
                $('#date_of_leaving').val();
              }else{
              $('#date_of_leaving').val(res.date_of_leaving);
              }
              if(res.date_of_leaving == null){
                $("#checkbox").prop("checked", true);
              }
           }
        });
    });
    $('body').on('click', '.delete', function () {
       if (confirm("Delete Record?") == true) {
        var id = $(this).data('id');

        $.ajax({
            type:"POST",
            url: "{{ url('delete-user') }}",
            data: { id: id },
            dataType: 'json',
            success: function(res){
              var oTable = $('#list-users').dataTable();
              oTable.fnDraw(true);
            //   $('#datatable-ajax-crud').DataTable().ajax.reload();
            //   window.location.reload();
           }
        });
       }
    });
   $('#addEdituserForm').submit(function(e) {
     e.preventDefault();

     var formData = new FormData(this);
     $.ajax({
        type:'POST',
        url: "{{ url('add-update-user')}}",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {
          $("#ajax-user-model").modal('hide');
          var oTable = $('#list-users').dataTable();
          oTable.fnDraw(false);
          $("#btn-save").html('Submit');
          $("#btn-save"). attr("disabled", false);
          window.location.reload();
        },
        error: function(data){
           console.log(data);
         }
       });
   });

   $("#checkbox").change(function(event) {
    var checkbox = document.getElementById('checkbox').checked;
    if (checkbox==true) {
        $("#checkbox").prop("checked", true);
        $('#date_of_leaving').val('');
    }else{
        $("#checkbox").prop("checked", false);
    }
});
});
</script>
</div>
</body>
</html>