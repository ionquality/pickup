<?php


?>

@extends('layouts/layoutMaster')

@section('title', 'Reports')

@section('vendor-style')


@endsection

@section('page-style')
  {{-- Page Css files --}}

@endsection

@section('content')

  <div class="row">
    <div class="col-md-3">
      <div class="d-grid gap-2">
        <button class="btn btn-primary" type="button" onclick="getReport('All')">Pickup Report</button>
        <button class="btn btn-primary" type="button" onclick="getReport('Deleted')">Deleted Report</button>
        <button class="btn btn-primary" type="button" onclick="getReport('Automatic')">Automatic Pickups</button>
      </div>
    </div>
    <div class="col-md-9">
      <div class="card">
        <div class="card-content">
          <div class="card-body">
            <div id="main-info"></div><!--END MAIN INFO -->
            <div id="loading-info" style="display: none">
              <div class="d-flex justify-content-center">
                <div class="spinner-border spinner-border-lg text-warning" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
            </div>
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
      getReport('All')
    };
    $.fn.dataTable.moment('MM/DD/YYYY h:mm a');

    function getReport(type) {
      $('#loading-info').fadeIn();
      $('#main-info').fadeOut();
      $.ajax({
        url: '/pickup-report',
        data: {
          _token: "{{csrf_token()}}",
          type
        },
        success: function (data) {
          $("#loading-info").fadeOut('slow', function () {
            $('#main-info').html(data).fadeIn();
            $('#datatable').DataTable( {
              "aaSorting": []
            } );
          })
        }
      });
    }

    function filterReport() {
      $('#loading-info').fadeIn();
      $('#main-info').fadeOut();
      $.ajax({
        type:"GET",
        url:"/pickup-report",
        data:$("#filterReport").serialize(),
        success: function (data) {
          $("#loading-info").fadeOut('slow', function () {
            $('#main-info').html(data).fadeIn();
            $('#datatable').DataTable( {
              "aaSorting": []
            } );
          })
        },
        error: function (xhr) {// Error...
          $.each(xhr.responseJSON.errors, function (key, value) {
            toastr.error(value);
          });
        }
      });
    }
    function addAutomaticPickupForm(customer) {
      $('#utility-modal').modal('show');
      $("#loading_message_modal").fadeIn();
      $("#modal-info").hide();
      $.ajax({
        url: "/automatic-pickup-create/",
        data: {
          _token: "{{csrf_token()}}",
          cu_name: customer
        },
        success: function (data) {
          $("#loading_message_modal").fadeOut('slow', function () {
            $("#modal-info").html(data).fadeIn();
            $('#customer-select').on('change', function(e) {
              var customer = $(this).val();
              addAutomaticPickupForm(customer)

            });
          })
        }
      });
    }

    function createAutomaticPickup() {
      var form = $('#createAutomaticPickup')[0];
      var formData = new FormData(form);
      $("#modal-info").hide();
      $("#loading_message_modal").fadeIn();
      $.ajax({
        type: "POST",
        url: '/automatic-pickup-create',
        processData: false,
        contentType: false,
        data: formData,
        success: function (data) {
          $('#utility-modal').modal('hide');
          toastr.success(data.msg);
          getReport('Automatic')
        },
        error: function (xhr) {// Error...
          $.each(xhr.responseJSON.errors, function (key, value) {
            toastr.error(value);
          });
        }
      });
    }

    function deleteAutomaticPickup(pickup_id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You will not be able to recover this data",
        type: "warning",
        showCancelButton: false,
        confirmButtonText: "Yes, delete it!",
      }).then(function (e) {
          if (e.value === true) {
            $.ajax({
              type: 'DELETE',
              url: "/automatic-pickup-delete/" + pickup_id,
              data: {
                _token: "{{csrf_token()}}",

              },
              dataType: "Json",
              success: function (data) {
                Swal.fire("Deleted!", "Your data has been deleted.", "success");
                getReport('Automatic')
              },
              error: function (data) {
                Swal.fire("NOT Deleted!", "Something blew up.", "error");
              }
            });
          } else {
            e.dismiss;
          }

        },
        function (dismiss) {
          if (dismiss === "cancel") {
            Swal.fire(
              "Cancelled",
              "Canceled Note",
              "error"
            )
          }
        })
    }

    function addAutomaticPickups() {
      Swal.fire({
        title: "Add Automatic Pickups to pickup list?",
        text: "You cannot undo this",
        type: "success",
        showCancelButton: false,
        confirmButtonText: "Yes!",
      }).then(function (e) {
          if (e.value === true) {
            $.ajax({
              type: 'POST',
              url: "/automatic-pickup-deploy/",
              data: {
                _token: "{{csrf_token()}}",

              },
              dataType: "Json",
              success: function (data) {
                Swal.fire("Success!", "Pickups have been added.", "success");
                getReport('Automatic')
              },
              error: function (data) {
                Swal.fire("NOT Completed!", "Something blew up.", "error");
              }
            });
          } else {
            e.dismiss;
          }

        },
        function (dismiss) {
          if (dismiss === "cancel") {
            Swal.fire(
              "Cancelled",
              "Canceled Note",
              "error"
            )
          }
        })
    }
  </script>
@endsection

