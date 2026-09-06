$(function () {
  $('.navbar-nav a').on('click', function () {
    const menu = bootstrap.Collapse.getInstance(document.getElementById('mainNav'));
    if (menu) menu.hide();
  });

  $('#bookingForm').on('submit', function (event) {
    event.preventDefault();
    const name = $.trim($('#name').val());
    const phone = $.trim($('#phone').val());
    const interest = $('#interest').val();
    const date = $('#date').val() || 'Not specified';
    const message = $.trim($('#message').val()) || '—';
    const whatsapp = String($(this).data('whatsapp'));
    if (!name || !/^\+?[0-9\s-]{8,15}$/.test(phone) || !interest) {
      $('#formStatus').text('Please add your name, a valid phone number and an interest.').addClass('error');
      return;
    }
    const text = `Hello Polish & Glow! ✨\n\nName: ${name}\nPhone: ${phone}\nInterested in: ${interest}\nPreferred date: ${date}\nMessage: ${message}`;
    if (!/^\d{10,15}$/.test(whatsapp)) {
      $('#formStatus').text('WhatsApp is not configured yet. Update it in config.php.').addClass('error');
      return;
    }
    window.open(`https://wa.me/${whatsapp}?text=${encodeURIComponent(text)}`, '_blank', 'noopener,noreferrer');
    $('#formStatus').text('Your WhatsApp enquiry is ready to send.').removeClass('error');
  });
});
