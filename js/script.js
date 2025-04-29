document.addEventListener('DOMContentLoaded', function() {
    var calendarElement = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarElement, {
        initialView: 'dayGridMonth',
        height: 500,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
        },
        editable: true,
        selectable: true,
        eventDisplay: 'block',
        timeZone: 'Asia/Manila',
        events: 'util/read_event.php',
        
        eventDrop: function(info) {
            updateEvent(info.event);
        },
        eventResize: function(info) {
            updateEvent(info.event);
        },
        select: function(info) {
            openModal(true); // Set true for 'Add Event'
            document.getElementById('event-id').value = ''; // Clear event ID for new event
            document.getElementById('start').value =info.startStr.slice(0, 16); 
            document.getElementById('end').value = info.endStr ? info.endStr.slice(0, 16) : info.startStr.slice(0, 16);
        },
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            openModal(false); // Set false for 'Edit Event'
            document.getElementById('event-id').value = info.event.id;
            document.getElementById('title').value = info.event.title;
            document.getElementById('start').value = info.event.start.toISOString().slice(0, 16);
            document.getElementById('end').value = info.event.end ? info.event.end.toISOString().slice(0, 16) : '';
            document.getElementById('description').value = info.event.extendedProps.description || '';
            document.getElementById('type').value = info.event.extendedProps.type || '';
            document.getElementById('status').value = info.event.extendedProps.status || '';
            document.getElementById('delete-button').style.display = 'inline-block'; // Show delete button
        }
    });

    calendar.render();

    const addEventButton = document.getElementById('add-event-button');
    const eventModal = document.getElementById('eventModal');
    const closeModalButton = document.getElementById('close-modal');
    const cancelAddEventButton = document.getElementById('cancel-add-event');
    const addEventForm = document.getElementById('add-event-form');
    const modalTitle = document.getElementById('eventModalLabel'); // Modal title element
    const deleteEventButton = document.getElementById('delete-button'); // Delete button

    function openModal(isNewEvent) {
        eventModal.style.display = 'block';
        addEventForm.reset();
        deleteEventButton.style.display = 'none'; // Hide delete button when adding a new event
        if (isNewEvent) {
            modalTitle.textContent = 'Add New Event'; // Set to "Add New Event" for adding
        } else {
            modalTitle.textContent = 'Edit Event'; // Set to "Edit Event" for editing
        }
    }

    addEventButton.addEventListener('click', () => {
        openModal(true); // New event
        document.getElementById('event-id').value = ''; // Clear event ID
    });

    closeModalButton.addEventListener('click', () => {
        eventModal.style.display = 'none';
    });

    cancelAddEventButton.addEventListener('click', () => {
        eventModal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target == eventModal) {
            eventModal.style.display = 'none';
        }
    });

    addEventForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const eventId = document.getElementById('event-id').value;
        const title = document.getElementById('title').value;
        const start_datetime = document.getElementById('start').value;
        const end_datetime = document.getElementById('end').value;
        const description = document.getElementById('description').value;
        const type = document.getElementById('type').value; 
        const status = document.getElementById('status').value;
        const userId = 'current_user_id'; // Replace this with actual user ID (use database, session, etc.)
        
        let url = eventId ? 'util/update_event.php' : 'util/create_event.php';
        let bodyData = eventId ? 
            JSON.stringify({
                event_id: eventId,
                title: title,
                start_datetime: start_datetime,
                end_datetime: end_datetime,
                description: description,
                type: type,
                status: status,
            }) : 
            new URLSearchParams({
                title: title,
                start_datetime: start_datetime,
                end_datetime: end_datetime,
                description: description,
                type: type,
                status: status,
            });
    
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': eventId ? 'application/json' : 'application/x-www-form-urlencoded',
            },
            body: bodyData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Proceed only if the event is created, not updated
                if (!eventId) {  // Check if the event is new (i.e., eventId is not present)
                    const eventTrackingData = {
                        event_id: data.event_id, // The event_id from the response
                        user_id: userId,
                        office: type, // The type or office value from the form
                        status: status,
                    };
                    console.log(data);
    
                    fetch('util/add_tracking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(eventTrackingData),
                    })
                    .then(response => response.json())
                    .then(trackData => {
                        if (trackData.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Event Successfully Created and Tracked',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                eventModal.style.display = 'none';
                                calendar.refetchEvents();
                                addEventForm.reset();
                            });
                        } else {
                            // Handle the failure of tracking separately
                            console.log(trackData);
                            Swal.fire({
                                icon: 'error',
                                title: 'Tracking Error',
                                text: trackData.message || 'An error occurred while tracking the event.',
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Tracking Error',
                            text: 'An error occurred while tracking the event.',
                        });
                    });
                } else {
                    // Event update successful, no tracking required
                    Swal.fire({
                        icon: 'success',
                        title: 'Event Successfully Updated',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        eventModal.style.display = 'none';
                        calendar.refetchEvents();
                        addEventForm.reset();
                    });
                }
            } else {
                // Handle the event creation/update failure
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'An error occurred while updating the event.',
                });
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'An error occurred while communicating with the server.',
            });
        });
    });
    

    deleteEventButton.addEventListener('click', () => {
        const eventId = document.getElementById('event-id').value;
        if (!eventId) return;

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to delete this event?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('util/delete_event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        event_id: eventId
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Event Deleted',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            eventModal.style.display = 'none';
                            calendar.refetchEvents();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the event.');
                });
            }
        });
    });

});

function updateEvent(event) {
    fetch('util/update_event.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            event_id: event.id,
            title: event.title,
            start_datetime: event.start.toISOString(),
            end_datetime: event.end ? event.end.toISOString() : event.start.toISOString(),
            description: event.extendedProps.description || '',
            type: event.extendedProps.type || '', 
            status: event.extendedProps.status || ''
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            console.log("Event successfully updated!");
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the event.');
    });
}
