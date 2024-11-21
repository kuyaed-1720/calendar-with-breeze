<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Event Calendar</title>
    
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.css" rel="stylesheet">

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Optional: Set the size of the calendar */
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <h1 class="text-2xl font-semibold text-center my-4">Event Calendar</h1>
    <div id="calendar"></div>

    <!-- Modal for Adding Event -->
    <div id="event-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-xl font-semibold mb-4">Add Event</h2>
            <form id="event-form">
                <label for="title" class="block text-sm">Event Title:</label>
                <input type="text" id="title" name="title" class="w-full border px-4 py-2 mb-4" required><br><br>

                <label for="start" class="block text-sm">Start Date:</label>
                <input type="datetime-local" id="start" name="start" class="w-full border px-4 py-2 mb-4" required><br><br>

                <label for="end" class="block text-sm">End Date:</label>
                <input type="datetime-local" id="end" name="end" class="w-full border px-4 py-2 mb-4" required><br><br>

                <label for="description" class="block text-sm">Description:</label>
                <textarea id="description" name="description" class="w-full border px-4 py-2 mb-4"></textarea><br><br>

                <div class="flex justify-end space-x-4">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Add Event</button>
                    <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.9.0/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Pass the events from Blade to JavaScript
            const events = @json($events);

            // Initialize the FullCalendar
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: events.map(event => ({
                    title: event.title,
                    start: event.start_date,
                    end: event.end_date,
                    description: event.description
                })),
                dateClick: function(info) {
                    // Open modal to add an event when a date is clicked
                    openModal(info.dateStr);
                }
            });
            calendar.render();

            // Handle form submission to add a new event
            document.getElementById('event-form').addEventListener('submit', function (e) {
                e.preventDefault();
                const title = document.getElementById('title').value;
                const start = document.getElementById('start').value;
                const end = document.getElementById('end').value;
                const description = document.getElementById('description').value;

                // Send the new event data to the server
                addEventToServer({ title, start, end, description });
            });
        });

        // Open the modal when a date is clicked
        function openModal(dateStr) {
            document.getElementById('event-modal').classList.remove('hidden');
            document.getElementById('start').value = dateStr;  // Set the start date to the clicked date
            document.getElementById('end').value = dateStr;    // Set the end date to the same date (can be adjusted by the user)
        }

        // Close the modal
        function closeModal() {
            document.getElementById('event-modal').classList.add('hidden');
        }

        // Function to send the event data to the server
        function addEventToServer(eventData) {
            fetch('/events', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(eventData)
            })
            .then(response => response.json())
            .then(data => {
                // If event is added successfully, reload the page
                if (data.success) {
                    location.reload(); // Reload the page to show the new event
                } else {
                    alert('Error adding event');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</body>
</html>
