$(document).ready(function() {
  // hide the optional parameters field
  $('.loading').hide();
  $('.alert').hide();

  // close the alert bar
  $('.close').click(function() {
    $('.alert').slideUp();
  });

  // clear the form and page
  $('#clear').click(function(e) {
    $('.alert').slideUp();
    e.preventDefault();
    // reset the parameter field if it was marked as error
    $('#input-param').removeClass('is-invalid');
    // reset the form and update the doc modal
    $(this).closest('form').get(0).reset();
    doPOST($('#query').val());
    if (typeof grecaptcha.reset === "function") { grecaptcha.reset(); }
  });

  // reset the view to the default one
  $('#backhome').click(function() {
    if (typeof grecaptcha.reset === "function") { grecaptcha.reset(); }
  });

  // initialize the help modal
  doPOST($('#query').val());

  // update help when a command is selected
  $('#query').on('change', function(e) {
    e.preventDefault();
    doPOST($('#query').val());
  });

  // if the field has been completed, turn it back to normal
  $('#input-param').change(function() {
    $('#input-param').removeClass('is-invalid');
  });

  // send an ajax request that will get the info on the router
  $('form').on('submit', function(e) {
    e.preventDefault();
    $('#output').html('');
    $.ajax({
      type: 'post',
      url: 'run.php',
      data: $('form').serialize(),
      beforeSend: function() {
        // show loading bar
        $('#command_properties').attr('disabled', '');
        $('.alert').hide();
        $('.loading').show();
      },
      complete: function() {
        // hide loading bar
        $('#command_properties').removeAttr('disabled');
        $('.loading').hide();
        if (typeof grecaptcha.reset === "function") { grecaptcha.reset(); }
      }
    }).done(function(response) {
      if (!response || (response.length === 0)) {
        // no parameter given
        $('#error-text').text('No parameter given.');
        $('#input-param').focus().addClass('is-invalid');
        $('.alert').slideDown();
      } else {
        var response = $.parseJSON(response);
        if (response.error) {
          $('#error-text').text(response.error);
          $('.alert').slideDown();
        } else {
          $('#output').html(response.result);
        }
      }
    }).fail(function(xhr) {
      $('#error-text').text(xhr.responseText);
      $('.alert').slideDown();
    });
  });

  function doPOST(query) {
    if (!!query) {
      $.ajax({
        type: 'post',
        url: 'run.php',
        data: { doc: query, dontlook: '' }
      }).done(function(response) {
        var response = $.parseJSON(response);
        $('#command-reminder').text(response.command);
        $('#description-help').html(response.description);
        $('#parameter-help').html(response.parameter);
      }).fail(function(xhr) {
        $('#description-help').text('Cannot load documentation...');
      });
    }
  }

});
