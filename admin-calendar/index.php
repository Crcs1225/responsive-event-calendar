<?php
include 'util/connection.php'; // connection

$_SESSION['user_id'] = '12345'; //SESSION variable for applicant ID 
                                                // default to 1 for testing
// Check if the user is logged in

// Fetch event types
$eventTypes = [];
$sql = "SELECT id, type_name FROM event_type"; 

$stmt = $conn->prepare($sql);
$stmt->execute();
// Fetch all at once
$eventTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="js/fullcalendar/lib/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="wrapper">
            <!-- calendar view -->
            <div id="calendar"></div>
        </div>
    </div>

    <a href="#" id="add-event-button" class="floating-button">+</a>

    <div id="eventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Add New Event</h5>
                <a href="#" class="close-button" id="close-modal">&times;</a>
            </div>
            <div class="modal-body">
            <form id="add-event-form">
                <input type="hidden" id="event-id" name="event-id">

                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="type-status-row">
                    <div class="form-group">
                        <label for="start-date">Start Date:</label>
                        <input type="date" id="start-date" name="start-date" required>
                    </div>
                    <div class="form-group">
                        <label for="start-time">Start Time:</label>
                        <input type="time" id="start-time" name="start-time" required>
                    </div>
                </div>

                <div class="type-status-row">
                    <div class="form-group">
                        <label for="end-date">End Date:</label>
                        <input type="date" id="end-date" name="end-date">
                    </div>
                    <div class="form-group">
                        <label for="end-time">End Time:</label>
                        <input type="time" id="end-time" name="end-time">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"></textarea>
                </div>

                <div class="form-group">
                    <label for="type">Type:</label>
                    <select id="type" name="type" required>
                        <option value="">-- Select Type --</option>
                        <?php foreach($eventTypes as $type): ?>
                            <option value="<?php echo htmlspecialchars($type['id']); ?>">
                                <?php echo htmlspecialchars($type['type_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            <div id="rsp-extension" class="rsp-extension mt-3" style="display: none;">
                <div class="form-group">
                    <label for="search-applicant">Search Applicant:</label>
                    <input type="text" id="search-applicant" class="form-control" placeholder="Search by name...">
                </div>

                <div class="form-group">
                    <label for="filter-by">Filter By:</label>
                    <select id="filter-by" class="form-control">
                        <option value="all">All</option>
                        <option value="name">Name</option>
                        <option value="department">Department</option>
                        <option value="position">Position</option>
                    </select>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered" id="applicant-table">
                        <thead class="thead-dark">
                            <tr>
                                <th>Select</th>
                                <th>Name</th>
                                <th>Office</th>
                                <th>Position</th>
                            </tr>
                        </thead>
                        <tbody id="applicant-table-body">
                            <!-- Fetched applicants will appear here -->
                        </tbody>
                    </table>
                </div>
            </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="button button-danger" id="delete-button">Delete</button> 
                <div style="margin-left: auto;">
                    <button type="button" class="button button-secondary" id="cancel-add-event">Close</button>
                    <button type="submit" class="button button-primary" form="add-event-form">Save Event</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script src="js/fullcalendar/lib/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/extended_modal.js"></script>
</body>
</html>
