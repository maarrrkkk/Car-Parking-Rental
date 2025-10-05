
<sectio>
    <div class="row">

        
    </div>
</section>




<script>
    function viewBooking(bookingId) {
        // Mock data population - in real app, this would fetch from server
        const bookingData = {
            'BK001': {
                customerName: 'John Doe',
                customerEmail: 'john@example.com',
                location: 'Downtown Business District',
                space: 'A-15',
                date: '2024-01-09',
                startTime: '09:00',
                endTime: '17:00',
                amount: '25.00',
                paymentStatus: 'Paid'
            }
        };

        const booking = bookingData[bookingId];
        if (booking) {
            document.getElementById('modalCustomerName').textContent = booking.customerName;
            document.getElementById('modalCustomerEmail').textContent = booking.customerEmail;
            document.getElementById('modalBookingId').textContent = bookingId;
            document.getElementById('modalLocation').textContent = booking.location;
            document.getElementById('modalSpace').textContent = booking.space;
            document.getElementById('modalDate').textContent = booking.date;
            document.getElementById('modalStartTime').textContent = booking.startTime;
            document.getElementById('modalEndTime').textContent = booking.endTime;
            document.getElementById('modalAmount').textContent = booking.amount;
            document.getElementById('modalPaymentStatus').textContent = booking.paymentStatus;
        }
    }

    function cancelBooking(bookingId) {
        if (confirm('Are you sure you want to cancel this booking?')) {
            alert('Booking ' + bookingId + ' has been cancelled.');
            // In real app, send AJAX request to cancel booking
        }
    }
</script>