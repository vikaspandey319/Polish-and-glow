$(function () {
  $('.navbar-nav a').on('click', function () {
    const menu = bootstrap.Collapse.getInstance(document.getElementById('mainNav'));
    if (menu) menu.hide();
  });
  $('#bookingForm').on('submit', function (event) {
    event.preventDefault();
    const $button = $(this).find('button[type="submit"]');
    const payload = { name: $.trim($('#name').val()), phone: $.trim($('#phone').val()), interest: $('#interest').val(), preferred_date: $('#date').val(), message: $.trim($('#message').val()), website: $('#website').val() };
    $('#formStatus').removeClass('error').text('');
    if (!payload.name || !/^\+?[0-9\s-]{8,15}$/.test(payload.phone) || !payload.interest) {
      $('#formStatus').text('Please add your name, a valid phone number and an interest.').addClass('error');
      return;
    }
    $button.prop('disabled', true).text('Saving enquiry…');
    $.ajax({ url: 'api/submit-enquiry.php', method: 'POST', data: payload, dataType: 'json' })
      .done(function (response) {
        if (!response.success || !response.whatsapp_url) { $('#formStatus').text(response.message || 'Unable to save your enquiry.').addClass('error'); return; }
        $('#formStatus').text('Enquiry saved. Opening WhatsApp…');
        window.location.assign(response.whatsapp_url);
      })
      .fail(function (xhr) {
        const message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'We could not save your enquiry. Please try again.';
        $('#formStatus').text(message).addClass('error');
      })
      .always(function () { $button.prop('disabled', false).text('Save & open WhatsApp ↗'); });
  });
});
