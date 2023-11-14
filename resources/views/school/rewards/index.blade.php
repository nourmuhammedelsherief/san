@extends('school.lteLayout.master')

@section('title')
    @lang('messages.rewards')
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('lte/plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{ URL::asset('lte/sweetalert.css') }}">
    <!-- Theme style -->
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>@lang('messages.rewards')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{url('/school/home')}}">
                                @lang('messages.control_panel')
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{route('rewards.index')}}"></a>
                            @lang('messages.rewards')
                        </li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    @include('flash::message')

    <section class="content">
        <div class="row">
            <div class="col-12">
                <h3>
                    <a href="{{route('rewards.create')}}" class="btn btn-info">
                        <i class="fa fa-plus"></i>
                        @lang('messages.add_new')
                    </a>
                </h3>
                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="group-checkable" data-set="#sample_1 .checkboxes" />
                                        <span></span>
                                    </label>
                                </th>
                                <th></th>
                                <th> @lang('messages.name') </th>
                                <th> @lang('messages.points') </th>
                                <th> @lang('messages.operations') </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i=0 ?>
                            @foreach($rewards as $reward)
                                <tr class="odd gradeX">
                                    <td>
                                        <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                            <input type="checkbox" class="checkboxes" value="1" />
                                            <span></span>
                                        </label>
                                    </td>
                                    <td><?php echo ++$i ?></td>
                                    <td>{{$reward->name}}</td>
                                    <td>{{$reward->points}}</td>
{{--                                    <td>--}}
{{--                                        <button type="button" class="btn btn-success" data-toggle="modal"--}}
{{--                                                data-target="#modal-success-{{$reward->id}}">--}}
{{--                                            <i class="fa fa-eye"></i>--}}
{{--                                        </button>--}}
{{--                                        <div class="modal fade" id="modal-success-{{$reward->id}}">--}}
{{--                                            <div class="modal-dialog">--}}
{{--                                                <div class="modal-content bg-success">--}}
{{--                                                    <div class="modal-header">--}}
{{--                                                        <h4 class="modal-title">@lang('messages.photo')</h4>--}}
{{--                                                        <button type="button" class="close" data-dismiss="modal"--}}
{{--                                                                aria-label="Close">--}}
{{--                                                            <span aria-hidden="true">&times;</span></button>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="modal-body">--}}
{{--                                                        <p>--}}
{{--                                                            <img--}}
{{--                                                                src="{{asset('/uploads/rewards/' . $reward->photo)}}"--}}
{{--                                                                width="400" height="400">--}}
{{--                                                        </p>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="modal-footer justify-content-between">--}}
{{--                                                        <button type="button" class="btn btn-outline-light"--}}
{{--                                                                data-dismiss="modal">@lang('messages.close')</button>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <!-- /.modal-content -->--}}
{{--                                            </div>--}}
{{--                                            <!-- /.modal-dialog -->--}}
{{--                                        </div>--}}
{{--                                    </td>--}}
                                    <td>

                                        <a class="btn btn-info" href="{{route('rewards.edit' , $reward->id)}}">
                                            <i class="fa fa-user-edit"></i> @lang('messages.edit')
                                        </a>

                                        <a class="delete_data btn btn-danger" data="{{ $reward->id }}" data_name="{{$reward->name}}" >
                                            <i class="fa fa-key"></i> @lang('messages.delete')
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>

@endsection

@section('scripts')

    <script src="{{asset('lte/dist/js/adminlte.min.js')}}"></script>
    <script src="{{asset('lte/plugins/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('lte/plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
    <script src="{{ URL::asset('lte/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('lte/ui-sweetalert.min.js') }}"></script>
    <script>
        $(function () {
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
            });
        });
    </script>
    <script>
        $( document ).ready(function () {
            $('body').on('click', '.delete_data', function() {
                var id = $(this).attr('data');
                var swal_text = '{{trans('messages.delete')}} ' + $(this).attr('data_name');
                var swal_title = "{{trans('messages.deleteSure')}}";

                swal({
                    title: swal_title,
                    text: swal_text,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-warning",
                    confirmButtonText: "{{trans('messages.sure')}}",
                    cancelButtonText: "{{trans('messages.close')}}"
                }, function() {

                    window.location.href = "{{ url('/') }}" + "/school/rewards/delete/" + id;

                });

            });
        });
    </script>
@endsection
