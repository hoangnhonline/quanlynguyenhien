@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Lịch công việc
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="{{ route('task.index') }}">Quản lý công việc</a></li>
                <li class="active">Lịch công việc</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Bộ lọc</h3>
                        </div>
                        <div class="panel-body">
                            <form class="form-inline" role="form" method="GET" action="{{ route('task.calendar') }}"
                                  id="searchForm">
                                {{--                                <div class="form-group">--}}
                                {{--                                    <input type="text" class="form-control daterange" autocomplete="off"--}}
                                {{--                                           name="range_date" value="{{ $filters['range_date'] ?? '' }}"/>--}}
                                {{--                                </div>--}}
                                <div class="form-group">
                                    <select class="form-control select2" name="staff_id" id="staff_id">
                                        <option value="">--Nhân viên--</option>
                                        @foreach ($userList as $item)
                                            <option value="{{ $item->id }}"
                                                {{ ($filters['staff_id'] ?? null) == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" style="width: 140px">
                                    <select class="form-control select2" name="department_id" id="department_id">
                                        <option value="">--Bộ phận--</option>
                                        @foreach ($departmentList as $item)
                                            <option value="{{ $item->id }}"
                                                {{ ($filters['department_id'] ?? null) == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control select2" name="plan_id" id="plan_id"
                                            style="width: 140px">
                                        <option value="">--Kế hoạch--</option>
                                        @foreach ($planList as $plan)
                                            <option value="{{ $plan->id }}"
                                                {{ ($filters['plan_id'] ?? null) == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control select2" name="status" id="status">
                                        <option value="">--Trạng thái--</option>
                                        @foreach (Helper::getConstant('task_status') as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ ($filters['status'] ?? null) == $value ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-info btn-sm" style="margin-top: -5px;">Lọc</button>
                            </form>
                        </div>
                    </div>
                    <div class="box">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div id='calendar'></div>
                        </div>
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
        </section>
        <!-- /.content -->
    </div>
@stop

@section('js')
    <script src='{{ asset('admin/plugins/fullcalendar-6.1.9/dist/index.global.min.js') }}'></script>
    <script src='{{ asset('admin/plugins/fullcalendar-6.1.9/packages/core/locales/vi.global.min.js') }}'></script>
    <script>
        function get_query() {
            var url = document.location.href;
            var qs = url.substring(url.indexOf('?') + 1).split('&');
            for (var i = 0, result = {}; i < qs.length; i++) {
                qs[i] = qs[i].split('=');
                result[qs[i][0]] = decodeURIComponent(qs[i][1]);
            }
            return result;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'vi',
                firstDay: "1",
                initialView: 'dayGridMonth',
                eventTimeFormat: { // like '14:30:00'
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                headerToolbar: {
                    left: "prev,next today",
                    center: "title",
                    right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
                },
                buttonText: {
                    today: 'Hôm nay',
                    month: 'Tháng',
                    week: 'Tuần',
                    day: 'Ngày',
                    list: 'Danh sách'
                },
                eventClick: function (info) {
                    window.open("/task?task_id=" + info.event.id, '_blank');
                },
                events: function (info, successCallback, failureCallback) {
                    const query = get_query()
                    $.ajax({
                        url: '/task/calendar/ajax-list',
                        data: {...info, ...query},
                        success: function (data) {
                            const color = {
                                1: '#b5c3e0',
                                2: '#FFC107',
                                3: '#00a65a',
                            }
                            const events = data.map((o) => ({
                                ...o,
                                title: o.name,
                                ...(o?.from_date ? {start: moment(o.from_date).toISOString()} : {}),
                                ...(o?.to_date ? {end: moment(o.to_date).toISOString()} : {}),
                                color: color[o.status],
                                textColor: 'black'
                            }));
                            successCallback(events)
                        },
                        error: function (res) {
                            alert(res.responseJSON.message)
                        }
                    });
                },
                eventDidMount: function (info) {
                    const event = info.event;
                    let content = "";
                    if (event.start) {
                        content += `Ngày bắt đầu: ${moment(event.start).format('DD/MM/YYYY HH:mm')}`
                    }
                    if (event.end) {
                        content += `<br/>Ngày kết thúc: ${moment(event.end).format('DD/MM/YYYY HH:mm')}`
                    }
                    if (event?._def?.extendedProps?.completed_date) {
                        content += `<br/>Ngày hoành thành: ${moment(event?._def?.extendedProps?.completed_date).format('DD/MM/YYYY HH:mm')}`
                    }
                    $(info.el).popover({
                        html: true,
                        placement: 'top',
                        trigger: 'hover',
                        title: `<strong>${event._def.extendedProps.staff.name}</strong> - ${event.title}`,
                        content: content,
                        container: 'body'
                    });
                    $(info.el).find('.fc-event-title').prepend(`<strong>${event._def.extendedProps.staff.name}</strong> - `);
                    $(info.el).find('.fc-list-event-title a').prepend(`<strong>${event._def.extendedProps.staff.name}</strong> - `);
                }
            });
            calendar.render();
        });

    </script>
@stop
