@extends('pages.ormawa.index')
@section('content')
    <style>
        .tracking-calendar .fc-daygrid-event {
            border: 0;
            border-radius: 9999px;
            margin-top: 4px;
            padding: 0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.12);
            cursor: pointer;
        }

        .tracking-calendar .fc-event-main {
            padding: 0;
        }

        .tracking-badge {
            display: inline-flex;
            width: 100%;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
            color: #0f172a;
            background: #e2e8f0;
            border: 1px solid transparent;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .tracking-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.14);
        }

        .tracking-dot {
            width: 7px;
            height: 7px;
            border-radius: 9999px;
            flex-shrink: 0;
            background: currentColor;
        }

        .tracking-badge-text {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        .tracking-badge-blue {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .tracking-badge-green {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }

        .tracking-badge-red {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .tracking-badge-gray {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #334155;
        }

        .tracking-calendar .fc-daygrid-day.fc-day-today {
            background: #f8fafc;
        }
    </style>

    <div class="max-w-screen-xl p-4 mx-auto">
        <div class="mb-6 text-2xl font-bold text-gray-800">
            Tracking
        </div>
        <div id="calendar" class="tracking-calendar p-6 bg-white rounded-xl shadow-sm border border-gray-100"></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                dayMaxEvents: 2,
                eventDisplay: 'block',
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch('{{ route('tracking.json') }}')
                        .then(response => response.json())
                        .then(data => successCallback(data))
                        .catch(error => {
                            failureCallback(error);
                        });
                },
                eventContent: function(arg) {
                    var colorClass = 'tracking-badge-gray';

                    if (arg.event.backgroundColor === '#0000ff') {
                        colorClass = 'tracking-badge-blue';
                    } else if (arg.event.backgroundColor === '#008000') {
                        colorClass = 'tracking-badge-green';
                    } else if (arg.event.backgroundColor === '#FF0000') {
                        colorClass = 'tracking-badge-red';
                    }

                    return {
                        html: '<span class="tracking-badge ' + colorClass + '">' +
                            '<span class="tracking-dot"></span>' +
                            '<span class="tracking-badge-text" title="' + arg.event.title + '">' + arg
                            .event.title + '</span>' +
                            '</span>'
                    };
                },
                eventClick: function(info) {
                    Swal.fire({
                        title: info.event.title,
                        html: `<b>Unit Kerja:</b> ${info.event.extendedProps.description} <br>
                    <b>Status Permohonan:</b> ${info.event.extendedProps.status} <br>
                    <b>Status Pengembalian:</b> ${info.event.extendedProps.status_pengembalian}`,
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                }
            });
            calendar.render();
        });
    </script>
@endsection
