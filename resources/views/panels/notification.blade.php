<link rel="stylesheet" href="{{asset('assets/vendor/libs/toastr/toastr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<script src="{{asset('assets/vendor/libs/toastr/toastr.js')}}"></script>

@if (Session::has('message'))
  <script>toastr.success('{!! Session::get('message') !!}')</script>


@elseif (Session::has('error'))
  <script>toastr.error('{!! Session::get('error') !!}')</script>
@elseif (Session::has('info'))
  <script>toastr.info('{!! Session::get('info') !!}')</script>
@elseif (Session::has('warning'))
  <script>toastr.warning('{!! Session::get('warning') !!}')</script>

@endif
@if ($errors->any())

  @foreach ($errors->all() as $error)
    <script>toastr.error('{{ $error }}')</script>

  @endforeach

@endif
