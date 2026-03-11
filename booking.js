// Booking form JavaScript

const bookingForm = document.getElementById('bookingForm');
const guestSelect = document.getElementById('guests');
const dateInput = document.getElementById('date');
const timeSelect = document.getElementById('time');

// Update available time slots when date or guests change
function updateAvailableSlots() {
    const guests = guestSelect.value;
    const date = dateInput.value;

    if (!guests || !date) {
        timeSelect.innerHTML = '<option value="">Select time</option>';
        return;
    }

    // Fetch available time slots via AJAX
    fetch('../php/check_availability.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'date=' + date + '&guests=' + guests
    })
    .then(response => response.json())
    .then(data => {
        const currentTime = timeSelect.value;
        timeSelect.innerHTML = '<option value="">Select time</option>';

        if (data.available) {
            data.slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot;
                const timeObj = new Date('2000-01-01 ' + slot);
                option.textContent = timeObj.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'});
                timeSelect.appendChild(option);
            });

            // Restore previous selection if still available
            if (currentTime && timeSelect.querySelector(`option[value="${currentTime}"]`)) {
                timeSelect.value = currentTime;
            }
        } else {
            const option = document.createElement('option');
            option.textContent = data.message || 'No available slots';
            option.disabled = true;
            timeSelect.appendChild(option);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        timeSelect.innerHTML = '<option value="">Error loading slots</option>';
    });
}

if (guestSelect) {
    guestSelect.addEventListener('change', updateAvailableSlots);
}

if (dateInput) {
    dateInput.addEventListener('change', updateAvailableSlots);
}

// Form submission validation
if (bookingForm) {
    bookingForm.addEventListener('submit', function(e) {
        const guests = guestSelect.value;
        const date = dateInput.value;
        const time = timeSelect.value;

        if (!guests || !date || !time) {
            e.preventDefault();
            alert('Please fill in all required fields!');
            return false;
        }

        // Verify date is in future
        const selectedDate = new Date(date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
            e.preventDefault();
            alert('Please select a future date!');
            return false;
        }

        return true;
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    if (dateInput) {
        dateInput.min = tomorrow.toISOString().split('T')[0];
    }

    // Set maximum date to 90 days from now
    const maxDate = new Date();
    maxDate.setDate(maxDate.getDate() + 90);
    if (dateInput) {
        dateInput.max = maxDate.toISOString().split('T')[0];
    }
});
