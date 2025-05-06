<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'util/connection.php';


// Set dummy user ID for testing if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = '1';
}
$user_id = $_SESSION['user_id'];

// Fetch event types (if needed for display)
$eventTypes = [];
$sql = "SELECT id, type_name FROM event_type"; 
$stmt = $conn->prepare($sql);
$stmt->execute();
$eventTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Calendar</title>
    <link rel="stylesheet" href="js/fullcalendar/lib/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="wrapper">
            <!-- Calendar view -->
            <div id="calendar"></div>
        </div>
    </div>

    <!-- No "Add Event" button shown to applicant -->

    <!-- View-only Event Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Details</h5>
                <a href="#" class="close-button" id="close-modal">&times;</a>
            </div>
            <div class="modal-body">
                <form id="view-event-form">
                    <input type="hidden" id="event-id" name="event-id">

                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" readonly>
                    </div>

                    <div class="type-status-row">
                        <div class="form-group">
                            <label for="start-date">Start Date:</label>
                            <input type="date" id="start-date" name="start-date" readonly>
                        </div>
                        <div class="form-group">
                            <label for="start-time">Start Time:</label>
                            <input type="time" id="start-time" name="start-time" readonly>
                        </div>
                    </div>

                    <div class="type-status-row">
                        <div class="form-group">
                            <label for="end-date">End Date:</label>
                            <input type="date" id="end-date" name="end-date" readonly>
                        </div>
                        <div class="form-group">
                            <label for="end-time">End Time:</label>
                            <input type="time" id="end-time" name="end-time" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" readonly></textarea>
                    </div>

                    <div class="form-group">
                        <label for="type">Type:</label>
                        <select id="type" name="type" disabled>
                            <option value="">-- Select Type --</option>
                            <?php foreach($eventTypes as $type): ?>
                                <option value="<?= htmlspecialchars($type['id']) ?>">
                                    <?= htmlspecialchars($type['type_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div style="margin-left: auto;">
                    <button type="button" class="button button-secondary" id="close-modal-btn">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/fullcalendar/lib/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const modal = document.getElementById('eventModal');
            const closeBtn = document.getElementById('close-modal');
            const closeFooterBtn = document.getElementById('close-modal-btn');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                displayEventTime: false,
                headerToolbar: {
                    left: 'prev today',
                    center: 'title',
                    right: 'dayGridMonth,listMonth next',
                },
                eventDisplay: 'block',
                events: {
                    url: 'util/read_event.php',
                    method: 'POST',
                    extraParams: {
                        user_id: '<?= $user_id ?>'
                    },
                    failure: function() {
                        alert('There was an error while fetching events!');
                    }
                },
                eventClick: function(info) {
                    const event = info.event;

                    // Populate modal fields
                    document.getElementById('title').value = event.title || '';
                    document.getElementById('description').value = event.extendedProps.description || '';
                    document.getElementById('start-date').value = event.startStr.split('T')[0] || '';
                    document.getElementById('start-time').value = event.startStr.split('T')[1]?.substring(0,5) || '';
                    document.getElementById('end-date').value = event.endStr?.split('T')[0] || '';
                    document.getElementById('end-time').value = event.endStr?.split('T')[1]?.substring(0,5) || '';
                    document.getElementById('type').value = event.extendedProps.type || '';

                    modal.style.display = 'block';
                }
            });

            calendar.render();

            closeBtn.addEventListener('click', () => modal.style.display = 'none');
            closeFooterBtn.addEventListener('click', () => modal.style.display = 'none');
        });
    </script>
</body>
</html>