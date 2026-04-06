@extends('pages.ormawa.index')
@section('content')
    <div class="max-w-screen-xl p-4 mx-auto">
        <div class="mb-6 text-2xl font-bold text-gray-800">
            Tracking
        </div>
        <div id="calendar" class="p-6 bg-white rounded-xl shadow-sm border border-gray-100"></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch('{{ route('tracking.json') }}')
                        .then(response => response.json())
                        .then(data => {
                            console.log("Data Kalender Diterima:", data); // Debugging
                            successCallback(data);
                        })
                        .catch(error => {
                            console.error("Error loading events:", error);
                            failureCallback(error);
                        });
                },
                eventClick: function(info) {
                    console.log("Event Diklik:", info.event);
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
