jQuery(document).ready(function($) {
    var popupButton = $('.popup-button');
    var popupOverlay = $('#popup-overlay');
    var popupClose = $('.popup-close');
    var selectedDealer = ''; // Store selected dealership location

    // Show the dealership selection popup when the main button is clicked
    popupButton.on('click', function(e) {
        e.preventDefault();
        popupOverlay.fadeIn();
    });

    // Hide the popup when the close button is clicked
    popupClose.on('click', function() {
        popupOverlay.fadeOut();
    });

    // Grab dealership info when the contact form is opened
    $(document).on('click', '.contact-form-btn', function(e) {
        e.preventDefault();
        selectedDealer = $(this).closest('.dealership').find('h3').text();
        var productName = $('h1.product_title').text();

        var contactFormHtml = `
            <div id="contact-form-popup" class="popup-overlay">
                <div class="popup-form">
                    <span class="popup-close contact-popup-close">&times;</span>
                    <form id="deal-contact-form">
                        <label for="dealer-location" class="formlabel">Selected Dealer Location:</label>
                        <input type="text" id="dealer-location" name="dealer-location" value="${selectedDealer}" readonly><br><br>
                        <label for="product-name" class="formlabel">Product Name:</label>
                        <input type="text" id="product-name" name="product-name" value="${productName}" readonly><br><br>
                        <div style="display: flex; gap: 10px;">
                            <div style="flex: 1;">
                                <label for="first-name" class="formlabel">First Name:</label>
                                <input type="text" id="first-name" name="first-name" required>
                            </div>
                            <div style="flex: 1;">
                                <label for="last-name" class="formlabel">Last Name:</label>
                                <input type="text" id="last-name" name="last-name" required>
                            </div>
                        </div><br>
                        <label for="phone"  class="formlabel">Phone:</label>
                        <input type="text" id="phone" name="phone" required><br><br>
                        <label for="email"  class="formlabel">Email:</label>
                        <input type="email" id="email" name="email" required><br><br>
                        <label for="address" class="formlabel">Address:</label>
                        <input type="text" id="address" name="address" required><br><br>
                        <label for="message" class="formlabel">Message:</label><br>
                        <textarea id="message" name="message" rows="4" cols="50" required></textarea><br><br>
						<div  class="deal-submit">
                        <button type="submit" class="submit-contact-form">Submit</button>
						</div>
                    </form>
                </div>
            </div>
        `;

        $('body').append(contactFormHtml);
        $('#contact-form-popup').fadeIn();

        $(document).on('click', '.contact-popup-close', function() {
            $('#contact-form-popup').fadeOut(function() {
                $(this).remove();
            });
        });

        $(window).on('click', function(e) {
            if ($(e.target).is('#contact-form-popup')) {
                $('#contact-form-popup').fadeOut(function() {
                    $(this).remove();
                });
            }
        });

        $(document).on('submit', '#contact-form', function(e) {
            e.preventDefault();
            var formData = {
                action: 'send_contact_form',
                security: contactFormAjax.security,
                first_name: $('#first-name').val(),
                last_name: $('#last-name').val(),
                phone: $('#phone').val(),
                email: $('#email').val(),
                address: $('#address').val(),
                message: $('#message').val(),
                dealer_location: $('#dealer-location').val(),
                product_name: $('#product-name').val(),
            };

            $.post(contactFormAjax.ajax_url, formData, function(response) {
                if (response.success) {
                    alert('Your message has been sent successfully!');
                    $('#contact-form-popup').fadeOut(function() {
                        $(this).remove();
                    });
                } else {
                    alert('Failed to send message. Please try again.');
                }
            });
        });
    });

    // Handle "CALL NOW" button
    $(document).on('click', '.call-now-btn', function(e) {
        e.preventDefault();
        var phoneNumber = $(this).closest('.dealership').find('p').text().match(/(\d{3}.\d{3}.\d{4})/)[0];
        
        if (/Mobi|Android/i.test(navigator.userAgent)) {
            window.location.href = 'tel:' + phoneNumber;
        } else {
            alert('Please Call ' + phoneNumber + ' (not supported on desktop)');
        }
    });
});
