document.addEventListener('DOMContentLoaded', function() {
    var calendarElement = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarElement, {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
        },
        editable: true,
        selectable: true,
        eventDisplay: 'block',
        displayEventTime: false,
        timeZone: 'Asia/Manila',
        events: 'util/read_event.php',
        
        eventDrop: function(info) {
            updateEvent(info.event);
        },
        eventResize: function(info) {
            updateEvent(info.event);
        },
        select: function(info) {
            populateEventForm({
                isNew: true,
                startStr: info.startStr,
                endStr: info.endStr
            });
        },
        
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            populateEventForm({
                isNew: false,
                event: info.event
            });
        }

        
    });

    function populateEventForm({ isNew, event = null, startStr = '', endStr = '' }) {
        const formElements = {
            id: document.getElementById('event-id'),
            title: document.getElementById('title'),
            startDate: document.getElementById('start-date'),
            startTime: document.getElementById('start-time'),
            endDate: document.getElementById('end-date'),
            endTime: document.getElementById('end-time'),
            description: document.getElementById('description'),
            type: document.getElementById('type'),
            deleteBtn: document.getElementById('delete-button')
        };
    
        if (isNew) {
            openModal(true); // Add Event
            formElements.id.value = '';
            const startDateTime = new Date(startStr);
            const endDateTime = endStr ? new Date(endStr) : startDateTime;
    
            formElements.startDate.value = startDateTime.toISOString().slice(0, 10);
            formElements.startTime.value = startDateTime.toISOString().slice(11, 16);
            formElements.endDate.value = endDateTime.toISOString().slice(0, 10);
            formElements.endTime.value = endDateTime.toISOString().slice(11, 16);
            formElements.title.value = '';
            formElements.description.value = '';
            formElements.type.value = '';
            formElements.type.disabled = false; 
            formElements.deleteBtn.style.display = 'none';
        } else {
            openModal(false); // Edit Event
            const startDateTime = new Date(event.start);
            const endDateTime = event.end ? new Date(event.end) : null;
    
            formElements.id.value = event.id;
            formElements.title.value = event.title;
            formElements.startDate.value = startDateTime.toISOString().slice(0, 10);
            formElements.startTime.value = startDateTime.toISOString().slice(11, 16);
            formElements.endDate.value = endDateTime ? endDateTime.toISOString().slice(0, 10) : '';
            formElements.endTime.value = endDateTime ? endDateTime.toISOString().slice(11, 16) : '';
            formElements.description.value = event.extendedProps.description || '';
            formElements.type.value = event.extendedProps.type || '';
            formElements.type.disabled = true;
            formElements.deleteBtn.style.display = 'inline-block';
        }
    }
    
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
    
        const startDate = document.getElementById('start-date').value;
        const startTime = document.getElementById('start-time').value;
        const endDate = document.getElementById('end-date').value;
        const endTime = document.getElementById('end-time').value;
    
        const description = document.getElementById('description').value;
        const type = document.getElementById('type').value;
    
        const userId = "<?php echo $_SESSION['user_id']; ?>";// Replace with actual user ID logic
    
        const url = eventId ? 'util/update_event.php' : 'util/create_event.php';
    
        const bodyData = eventId ?
            JSON.stringify({
                event_id: eventId,
                title,
                start_date: startDate,
                start_time: startTime,
                end_date: endDate,
                end_time: endTime,
                description,
                type
            }) :
            new URLSearchParams({
                title,
                start_date: startDate,
                start_time: startTime,
                end_date: endDate,
                end_time: endTime,
                description,
                type
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
                if (!eventId) {
                    const eventTrackingData = {
                        event_id: data.event_id,
                        user_id: userId,
                    };
    
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
                            const typeSelect = document.getElementById("type");
                            const eventTrackingId = trackData.event_tracking_id;
                            const type = typeSelect.options[typeSelect.selectedIndex].text;
                    
                            // Get applicants: all if not RSP Interview, selected if RSP
                            let applicantIds = [];
                    
                            if (type === "RSP Interview") {
                                applicantIds = Array.from(window.selectedApplicants);
                            } else {
                                applicantIds = (window.allApplicants || []).map(app => app.id);
                            }
                    
                            // Track each applicant
                            const trackPromises = applicantIds.map(uniqueId => {
                                const applicantTrackData = {
                                    event_tracking_id: eventTrackingId,
                                    unique_id: uniqueId
                                };
                    
                                return fetch('util/calendar_tracking.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify(applicantTrackData),
                                })
                                .then(res => res.text()) // 👈 get raw response first
                                .then(text => {
                                    //console.log("Raw tracking response:", text); // see what PHP really returns
                                    try {
                                        return JSON.parse(text);
                                    } catch (e) {
                                        throw new Error("Invalid JSON: " + text);
                                    }
                                });
                            });
                    
                            // Wait for all calendar tracking to complete
                            Promise.all(trackPromises)
                                .then(() => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Event Created and Tracked',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        eventModal.style.display = 'none';
                                        calendar.refetchEvents();
                                        addEventForm.reset();
                                    });
                                })
                                .catch(err => {
                                    console.error("Error during calendar tracking:", err);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Tracking Error',
                                        text: 'Some applicants may not have been tracked properly.',
                                    });
                                });
                    
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Tracking Error',
                                text: trackData.message || 'Error tracking the event.',
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Tracking Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Tracking Error',
                            text: 'An error occurred while tracking the event.',
                        });
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Event Updated',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        eventModal.style.display = 'none';
                        calendar.refetchEvents();
                        addEventForm.reset();
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to update/create event.',
                });
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Server communication failed.',
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
    // Get the separated start date and time, and end date and time
    const startDate = event.start.toISOString().slice(0, 10); // YYYY-MM-DD
    const startTime = event.start.toISOString().slice(11, 16); // HH:mm
    const endDate = event.end ? event.end.toISOString().slice(0, 10) : startDate; // Default to start date if no end date
    const endTime = event.end ? event.end.toISOString().slice(11, 16) : startTime; // Default to start time if no end time

    fetch('util/update_event.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            event_id: event.id,
            title: event.title,
            start_date: startDate,
            start_time: startTime,
            end_date: endDate,
            end_time: endTime,
            description: event.extendedProps.description || '',
            type: event.extendedProps.type || ''
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
