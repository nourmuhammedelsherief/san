@extends('admin.lteLayout.master')

@section('title')
    @lang('messages.bank_transfers')
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
                    <h1>@lang('messages.teacher_bank_transfers')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{url('/admin/home')}}">
                                @lang('messages.control_panel')
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{route('teacher_transfers')}}"></a>
                            @lang('messages.bank_transfers')
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
                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>
                                    <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="group-checkable"
                                               data-set="#sample_1 .checkboxes"/>
                                        <span></span>
                                    </label>
                                </th>
                                <th></th>
                                <th> @lang('messages.teacher') </th>
                                <th> @lang('messages.seller_code') </th>
                                <th> @lang('messages.invitation_code') </th>
                                <th> @lang('messages.discount') </th>
                                <th> @lang('messages.price') </th>
                                <th> @lang('messages.transfer_photo') </th>
                                <th> @lang('messages.paid_at') </th>
                                <th> @lang('messages.operations') </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0 ?>
                            @foreach($transfers as $transfer)
                                <tr class="odd gradeX">
                                    <td>
                                        <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                            <input type="checkbox" class="checkboxes" value="1"/>
                                            <span></span>
                                        </label>
                                    </td>
                                    <td><?php echo ++$i ?></td>
                                    <td>
                                        {{$transfer->teacher->name}}
                                    </td>
                                    <td> {{$transfer->seller_code_id == null ? '' : $transfer->seller_code->code}} </td>
                                    <td> {{$transfer->invitation_code_id == null ? '' : $transfer->invitation_code->invitation_code}} </td>
                                    <td> {{$transfer->discount}} @lang('messages.SR')</td>
                                    <td>
                                        {{$transfer->paid_amount}} @lang('messages.SR')
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-success" data-toggle="modal"
                                                data-target="#modal-success-{{$transfer->id}}">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <div class="modal fade" id="modal-success-{{$transfer->id}}">
                                            <div class="modal-dialog">
                                                <div class="modal-content bg-success">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">@lang('messages.transfer_photo')</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>
                                                            <img
                                                                src="{{asset('/uploads/teacher_transfers/' . $transfer->transfer_photo)}}"
                                                                width="400" height="400">
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer justify-content-between">
                                                        <button type="button" class="btn btn-outline-light"
                                                                data-dismiss="modal">@lang('messages.close')</button>
                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                    </td>
                                    <td> {{$transfer->created_at->format('Y-m-d')}} </td>
                                    <td>

                                        <a class="btn btn-info"
                                           href="{{route('teacher_transfers.submit' , [$transfer->id , 'done'])}}">
                                            <i class="fa fa-user-edit"></i> @lang('messages.confirm')
                                        </a>

                                        <a class="btn btn-danger"
                                           href="{{route('teacher_transfers.submit' , [$transfer->id , 'remove'])}}">
                                            <i class="fa fa-key"></i> @lang('messages.cancel')
                                        </a>

                                        {{--                                        <a class="delete_data btn btn-danger" data="{{ $transfer->id }}" data_name="{{app()->getLocale() == 'ar' ? $transfer->name_ar : $transfer->name_en}}" >--}}
                                        {{--                                            <i class="fa fa-key"></i> @lang('messages.delete')--}}
                                        {{--                                        </a>--}}
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
        $(document).ready(function () {
            $('body').on('click', '.delete_data', function () {
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
                }, function () {

                    window.location.href = "{{ url('/') }}" + "/admin/bank_transfers/delete/" + id;

                });

            });
        });
    </script>
@endsection
