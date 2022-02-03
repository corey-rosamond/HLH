Login = {
  initialize: function (){
    Login._eventInitialize();
  },
  /** EVENTS */
  _eventInitialize: function(){
    $('.login-form').validate({
      errorElement: 'span',
      errorClass: 'help-block',
      focusInvalid: false,
      rules:{username:{required: true },password:{required: true},remember:{required: false}},
      messages:{username:{required: "Username is required."},password:{required:"Password is required."}},
      invalidHandler: function(event, validator) {
        $('.alert-danger', $('.login-form')).show();
      },
      highlight: function(element){ $(element).closest('.form-group').addClass('has-error');  },
      errorPlacement: function(error, element){ error.insertAfter(element.closest('.input-icon')); },
      success: function(label) {
        label.closest('.form-group').removeClass('has-error');
        label.remove();
      },
      submitHandler: function(form){
        var jqxhr = $.post( "/Command/User",{
          action: "login",
          username: $("input[name=username]").val(),
          password: $("input[name=password]").val(),
          remember: ((($('input.checkbox_check').is(':checked')))?true:false)
        }, function(data) {
          if(!data.result){
            $('.alert-danger', $('.login-form')).show();
            $( ".alert-danger" ).find( "span" ).html(data.message);
          } else { window.location = "/Main/Dashboard/Dashboard"; }
        }, "json");
        return false;
      }
    });
    $('.login-form input').keypress(function(e){
      if (e.which == 13) {
        if ($('.login-form').validate().form()){ $('.login-form').submit(); }
        return false;
      }
    });
    $('.login-form input').keypress( function(e){
      if (e.which == 13) {
        if ($('.login-form').validate().form()) { $('.login-form').submit(); }
        return false;
      }
    });
  }
}
Simplicity.onLoad(Login.initialize);
