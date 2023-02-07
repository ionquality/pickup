<?php


?>

@extends('layouts/layoutMaster')

@section('title', 'Pickup List')

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
      getPickupList()
    };

    function getPickupList() {
      $.ajax({
        url: '/pickup-list',
        data: {
          _token: "{{csrf_token()}}"
        },
        success: function (data) {
          $('#main-info').hide().html(data).fadeIn();
          $('#datatable').DataTable();
        }
      });
    }
    function createAgreementTemplateForm() {
      $('#utility-modal').modal('show');
      $("#loading_message_modal").fadeIn();
      $("#modal-info").hide();
      $.ajax({
        url: "/agreement-template-create/",
        data: {
          _token: "{{csrf_token()}}"
        },
        success: function (data) {
          $("#loading_message_modal").fadeOut('slow', function () {
            $("#modal-info").html(data).fadeIn();
            loadTiny();
          })
        }
      });
    }



    function createAgreementTemplate() {
      var form = $('#createAgreementTemplate')[0];
      var formData = new FormData(form);
      $("#modal-info").hide();
      $("#loading_message_modal").fadeIn();
      $.ajax({
        type: "POST",
        url: '/agreement-template-create',
        processData: false,
        contentType: false,
        data: formData,
        success: function (data) {
          $('#utility-modal').modal('hide');
          toastr.success(data.msg);
          getAgreementTemplateList()
        },
        error: function (xhr) {// Error...
          $.each(xhr.responseJSON.errors, function (key, value) {
            toastr.error(value);
          });
        }
      });
    }
    function addSalesRepToConsultant() {
      var form = $('#addSalesRepToConsultant')[0];
      var formData = new FormData(form);
      $("#modal-info").hide();
      $("#loading_message_modal").fadeIn();
      $.ajax({
        type: "POST",
        url: 'consultant-sales-rep-create',
        processData: false,
        contentType: false,
        data: formData,
        success: function (data) {
          toastr.success(data.msg);
          $('#utility-modal').modal('hide');
        },
        error: function (xhr) {// Error...
          $.each(xhr.responseJSON.errors, function (key, value) {
            toastr.error(value);
          });
        }
      });
    }

    function deleteAgreementTemplate(agreement_template_id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You will not be able to recover this data",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel please!",
        buttonsStyling: true
      }).then(function (e) {
          if (e.value === true) {
            $.ajax({
              type: 'DELETE',
              url: "/agreement-template/" + agreement_template_id,
              data: {
                _token: "{{csrf_token()}}",

              },
              dataType: "Json",
              success: function (data) {
                Swal.fire("Deleted!", "Your data has been deleted.", "success");
                getAgreementTemplateList();
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
  </script>
@endsection

