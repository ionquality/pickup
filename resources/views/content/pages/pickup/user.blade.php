<?php


?>

@extends('layouts/layoutMaster')

@section('title', $title)

@section('vendor-style')


@endsection

@section('page-style')
  {{-- Page Css files --}}

@endsection

@section('content')

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-content">
          <div class="card-body">
            <div id="main-info"></div><!--END MAIN INFO -->
          </div><!--END CARD BODY -->
        </div><!--END CARD CONTENET -->
      </div><!--END CARD -->
    </div><!--END COL -->
  </div><!--END ROW -->

  <!-- users edit ends -->
@endsection

@section('vendor-script')
  {{-- Vendor js files --}}
  @include('panels.datatable')

@endsection

@section('page-script')



  <script>
    window.onload = function () {
      getAvatarForm()
    };
    var user_id = {{$user_id}};

    function getAvatarForm() {
      $.ajax({
        url: '/user-avatar/' + user_id,
        data: {
          _token: "{{csrf_token()}}"
        },
        success: function (data) {
          $('#main-info').hide().html(data).fadeIn();

        }
      });
    }

    function updateUser() {
      var form = $('#updateUser')[0];
      var formData = new FormData(form);

      $.ajax({
        type: "POST",
        url: '/user/' + user_id,
        processData: false,
        contentType: false,
        data: formData,
        success: function (data) {
          $('#utility-modal').modal('hide');
          toastr.success(data.msg);
          getAvatarForm()
        },
        error: function (xhr) {// Error...
          $.each(xhr.responseJSON.errors, function (key, value) {
            toastr.error(value);
          });
        }
      });
    }


  </script>
@endsection

