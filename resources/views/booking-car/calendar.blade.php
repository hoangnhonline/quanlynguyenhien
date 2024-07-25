@extends('layout')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Lịch trình xe
            </h1>
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
                            <form class="form-inline" role="form" method="GET" action="{{ route('booking-car.calendar') }}"
                                  id="searchForm">
                                <div class="form-group">
                                    <select class="form-control select2" name="driver_id" id="driver_id"
                                            style="width: 140px">
                                        <option value="">--Tài xế--</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}"
                                                {{ ($filters['driver_id'] ?? null) == $driver->id ? 'selected' : '' }}>
                                                {{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <select class="form-control select2" name="status" id="status">
                                        <option value="">--Trạng thái--</option>
                                        @foreach (Helper::getConstant('booking_car_status') as $value => $label)
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
                events: function (info, successCallback, failureCallback) {
                    const query = get_query()
                    $.ajax({
                        url: '/booking-car/calendar',
                        data: {...info, ...query},
                        success: function (data) {
                            const color = {
                                1: '#b5c3e0',
                                2: '#00a65a',
                                3: 'red',
                            }
                            const events = data.map((o, index) => {
                                const isDontienfree = !!o?.booking
                                return {
                                    ...o,
                                    title: isDontienfree ? '(free) ' + o?.booking?.name : o?.name,
                                    start: moment(o.use_date_time || o.use_date).toISOString(),
                                    // end: moment(o.use_date_time || o.use_date).toISOString(),
                                    color: color[o.status],
                                    textColor: 'black'
                                }
                            });
                            successCallback(events)
                        },
                        error: function (res) {
                            alert(res.responseJSON.message)
                        }
                    });
                },
                eventClick: function (info) {
                    const event = info.event;
                    const data = event?._def.extendedProps
                    window.open("tel:" + data.phone)
                },
                eventDidMount: function (info) {
                    const event = info.event;
                    const data = event?._def.extendedProps

                    let content = "";
                        content += `<strong>Mã BK:</strong> ${data?.booking?.id || event.id}`
                        content += `<br/><strong>Khách hàng:</strong> ${event.title}`
                        content += `<br/><strong>SDT:</strong> ${data.phone}`
                        content += `<br/><strong>Ghi chú:</strong> <span style="color:red">${data?.notes || ''}</span>`
                        content += `<br/><strong>Thời gian:</strong> ${moment(event.start).format('DD/MM/YYYY HH:mm')}`
                        content += `<br/><strong>Nơi đón:</strong> ${data?.location?.name || ""}`
                        content += `<br/><strong>Nơi trả:</strong> ${data?.location2?.name || ""}`

                    $(info.el).popover({
                        html: true,
                        placement: 'top',
                        trigger: 'hover',
                        title: `<strong>${event.title}</strong>`,
                        content: content,
                        container: 'body'
                    });
                }
            });
            calendar.render();
        });

    </script>
@stop
