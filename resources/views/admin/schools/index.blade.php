@extends('admin.lteLayout.master')

@section('title')
    @lang('messages.schools')
    @if($status == 'active')
        (@lang('messages.active'))
    @elseif($status == 'not_active')
        (@lang('messages.not_active'))
    @elseif($status == 'finished')
        (@lang('messages.finished'))
    @endif
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
                    <h1>
                        @lang('messages.schools')
                        @if($status == 'active')
                            (@lang('messages.active'))
                        @elseif($status == 'not_active')
                            (@lang('messages.not_active'))
                        @elseif($status == 'finished')
                            (@lang('messages.finished'))
                        @endif
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{url('/admin/home')}}">
                                @lang('messages.control_panel')
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{route('adminSchools.index' , $status)}}"></a>
                            @lang('messages.schools')
                            @if($status == 'active')
                                (@lang('messages.active'))
                            @elseif($status == 'not_active')
                                (@lang('messages.not_active'))
                            @elseif($status == 'finished')
                                (@lang('messages.finished'))
                            @endif
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
                {{--                <h3>--}}
                {{--                    <a href="{{route('schools.create')}}" class="btn btn-info">--}}
                {{--                        <i class="fa fa-plus"></i>--}}
                {{--                        @lang('messages.add_new')--}}
                {{--                    </a>--}}
                {{--                </h3>--}}
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
                                <th> @lang('messages.name') </th>
                                <th> @lang('messages.email') </th>
                                <th> @lang('messages.city') </th>
                                <th> @lang('messages.identity_code') </th>
                                <th> @lang('messages.payment_type') </th>
                                <th> @lang('messages.end_subscription') </th>
                                <th> @lang('messages.teachers') </th>
                                <th> @lang('messages.students') </th>
                                <th> @lang('messages.operations') </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0 ?>
                            @foreach($schools as $school)
                                <tr class="odd gradeX">
                                    <td>
                                        <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                            <input type="checkbox" class="checkboxes" value="1"/>
                                            <span></span>
                                        </label>
                                    </td>
                                    <td><?php echo ++$i ?></td>
                                    <td>
                                        {{$school->name}}
                                    </td>
                                    <td><a href="mailTo:{{$school->email}}">{{$school->email}}</a></td>
                                    <td> {{app()->getLocale() == 'ar' ? $school->city->name_ar : $school->city->name_en}} </td>
                                    <td> {{$school->identity_code}} </td>
                                    <td>
                                        @if($school->subscription->payment_type == 'bank')
                                            @lang('messages.bank_transfers')
                                        @elseif($school->subscription->payment_type == 'online')
                                            @lang('messages.online_payment')
                                        @endif
                                    </td>
                                    <td>
                                        @if($school->subscription->end_at)
                                            {{$school->subscription->end_at->format('Y-m-d')}}
                                        @else
                                            @lang('messages.noSubscription')
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('schoolTeachers' , $school->id)}}" class="btn btn-primary">
                                            {{\App\Models\Teacher\Teacher::with('teacher_classrooms')
                                                ->whereHas('teacher_classrooms', function ($q) use($school){
                                                    $q->with('classroom');
                                                    $q->whereHas('classroom', function ($c) use($school){
                                                        $c->whereSchoolId($school->id);
                                                    });
                                                })->count()
                                                }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{route('schoolStudents' , $school->id)}}" class="btn btn-info">
                                            {{\App\Models\Student::with('classroom')
                                                ->whereHas('classroom' , function ($q) use($school){
                                                    $q->whereSchoolId($school->id);
                                                })->count()}}
                                        </a>
                                    </td>
                                    <td>

                                        {{--                                        <a class="btn btn-info" href="{{route('schools.edit' , $school->id)}}">--}}
                                        {{--                                            <i class="fa fa-user-edit"></i> @lang('messages.edit')--}}
                                        {{--                                        </a>--}}

                                        <a class="delete_data btn btn-danger" data="{{ $school->id }}"
                                           data_name="{{$school->name}}">
                                            <i class="fa fa-key"></i> @lang('messages.delete')
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{$schools->links()}}
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

                    window.location.href = "{{ url('/') }}" + "/admin/schools/delete/" + id;

                });

            });
        });
    </script>
@endsection
