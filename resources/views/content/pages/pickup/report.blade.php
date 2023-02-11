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
        <button class="btn btn-primary" type="button">Automatic Pickups</button>
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

  </script>
@endsection

