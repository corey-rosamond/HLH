Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};
/** START FUNNEL SUPPORT OBJECT */
var Framework = {
  Init: function( InstructionObject ){
    if($(InstructionObject).attr("InterruptClose") !== void 0){
      Framework.InterruptClose = (($(InstructionObject).attr("InterruptClose")==1)?true:false);
      if(Framework.InterruptClose){
        Framework.InterruptCloseHide        = $(InstructionObject).attr("InterruptCloseHide");
        Framework.InterruptCloseShow        = $(InstructionObject).attr("InterruptCloseShow");
        Framework.InterruptCloseDisplayText = $(InstructionObject).attr("InterruptCloseDisplayText");
      }
    } else { Framework.InterruptClose = false; }
    if($(InstructionObject).attr("DelayVisibility") !== void 0){
      Framework.DelayVisibility = (($(InstructionObject).attr("DelayVisibility")==1)?true:false);
      if(Framework.DelayVisibility){
        Framework.DelayVisibilityTarget = $(InstructionObject).attr("DelayVisibilityTarget");
        Framework.DelayVisibilityAmount = $(InstructionObject).attr("DelayVisibilityAmount");
      }
    } else { Framework.DelayVisibility = false; }
    if($(InstructionObject).attr("SiteURL") !== void 0){
      Framework.SiteURL = $(InstructionObject).attr("SiteURL");
    } else { Framework.SiteURL = false; }
    if($(InstructionObject).attr("CountdownTimer") !== void 0){
      Framework.CountdownTimer = (($(InstructionObject).attr("CountdownTimer")==1)?true:false);
      if(Framework.CountdownTimer){
        Framework.CountdownTimerContainer = $(InstructionObject).attr("CountdownTimerTarget");
        Framework.CountdownTimerDuration  = $(InstructionObject).attr("CountdownTimerDuration");
        Framework.CountdownTimerMinimum   = $(InstructionObject).attr("CountdownTimerMinimum");
        Framework.CountdownTimerPreText   = $(InstructionObject).attr("CountdownTimerPreText");
        Framework.CountdownTimerPostText  = $(InstructionObject).attr("CountdownTimerPostText");
      }
    } else { Framework.CountdownTimer = false; }
    if($(InstructionObject).attr("StockCounter") !== void 0){
      Framework.StockCounter = (($(InstructionObject).attr("StockCounter")==1)?true:false);
      if(Framework.StockCounter){
        Framework.StockCounterContainer             = $(InstructionObject).attr("StockCounterContainer");
        Framework.StockCounterMinimum               = $(InstructionObject).attr("StockCounterMinimum");
        Framework.StockCounterStartAmount           = $(InstructionObject).attr("StockCounterStartAmount");
        Framework.StockCounterDecrementRangeStart   = $(InstructionObject).attr("StockCounterDecrementRangeStart");
        Framework.StockCounterDecrementRangeEnd     = $(InstructionObject).attr("StockCounterDecrementRangeEnd");
        Framework.StockCounterRunTime               = $(InstructionObject).attr("StockCounterRunTime");
        Framework.StockCounterPreText               = $(InstructionObject).attr("StockCounterPreText");
        Framework.StockCounterPostText              = $(InstructionObject).attr("StockCounterPostText");
      }
    } else { Framework.StockCounter = false; }
    if(Framework.StockCounter){
      $('.'+Framework.StockCounterContainer+' .prefix').html(Framework.StockCounterPreText);
      $('.'+Framework.StockCounterContainer+' .count').html(parseInt(Framework.StockCounterStartAmount).format());
      $('.'+Framework.StockCounterContainer+' .postfix').html(Framework.StockCounterPostText);
      var min = parseInt(Framework.StockCounterDecrementRangeStart);
      var max = parseInt(Framework.StockCounterDecrementRangeEnd);
      var rand = Math.floor(Math.random() * (max - min + 1)) + min;
      var timeInto = parseInt(Framework.StockCounterStartAmount)/rand;
      var interval = (parseInt(Framework.StockCounterRunTime)/timeInto)*1000;
      Framework.StockCounterMin = min;
      Framework.StockCounterMax = max;
      Framework.StockCounterInterval = interval;
      setTimeout(function(){
        Framework.StockCount();
      }, Framework.StockCounterInterval);
    }
    if(Framework.DelayVisibility){
      $('#'+Framework.DelayVisibilityTarget).hide();
      setTimeout(function(){
        $('#'+Framework.DelayVisibilityTarget).show();
      },(Framework.DelayVisibilityAmount*1000));
    }
    if(Framework.InterruptClose){
      $('#'+Framework.InterruptCloseShow).hide();
      $(window).bind('beforeunload', Framework.Interupt);
      $(document).on('click','*',function(event){
        $(window).unbind('beforeunload');
        setTimeout(function(){
          $(window).bind('beforeunload',Framework.Interupt)
        },500);
      });
    }
  },
  Interupt: function( event ){
    $('.framework-funnel-video-player').each(function(){
      $(this).get(0).pause();
    });
    $('#'+Framework.InterruptCloseHide).hide();
    $('#'+Framework.InterruptCloseShow).show();
    return Framework.InterruptCloseDisplayText;
  },
  StockCount: function( event ){
    var currentCount = $('.'+Framework.StockCounterContainer+' .count').html();
    currentCount = currentCount.replace(",","");
    currentCount = parseInt(currentCount);
    var rand = Math.floor(Math.random() * (parseInt(Framework.StockCounterMax) - parseInt(Framework.StockCounterMin) + 1)) +parseInt(Framework.StockCounterMin);
    var newCount = (parseInt(currentCount)-parseInt(rand));
    if(newCount<Framework.StockCounterMinimum){
      newCount = parseInt(Framework.StockCounterMinimum);
      $('.'+Framework.StockCounterContainer+' .count').html(parseInt(newCount).format());
      return true;
    }
    $('.'+Framework.StockCounterContainer+' .count').html(parseInt(newCount).format());
    setTimeout(function(){ Framework.StockCount(); }, Framework.StockCounterInterval);
  }
};
/** END FUNNEL SUPPORT OBJECT */
$(document).ready(function(){
  /** PICKUP OUR INSTRUCTIONS */
  if($("#framework-instruction-object").length){
    Framework.Init($("#framework-instruction-object"));
  }
  /** UPSELL PAGE **/
  if($('#funnel-upsell-form').length){
    $('.funnel-submit-button').on('click', function(event){
      $('#funnel-upsell-form').submit();
    });
  };
  /** DOWNSELL PAGE **/
  if($('#funnel-downsell-form').length){
    $('.funnel-submit-button').on('click', function(event){
      $('#funnel-downsell-form').submit();
    });
  };
  if($('#vsl-buy-button').length){
    video = $('#vsl-player').get(0);
    $(video).on('click',function(event){
      video.play();
    });
    $('.funnel-submit-button').on('click', function(event){
      $(window).unbind('beforeunload');
      window.location = $('#vsl-buy-button').attr('href');
    });
  };
  /** CHECKOUT PAGE */
  if($('#checkout-box').length){
    errors = new Array();
    billingAddressErrors  = new Array();
    shippingAddressErrors = new Array();
    validateShipping  = $('#billing-is-shipping').is(':checked');
    billingValidated  = false;
    shippingValidated = false;
    function cleanInput(string){
      var tokens = {" " : "","(" : "",")" : "","[" : "","]" : "","-" : "","/" : "","\\": "","=": "",">": "","<": "",":": "",";": "","{": "","}": "",",": "","\"": "","'": ""};
      for(var token in tokens ) {
        while (string.indexOf(token) != -1) {
          string = string.replace(token, tokens[token]);
        }
      }
      return string;
    }
    function setErrorContent( id, message ){
      $('#'+id).closest('.funnel-input-row').find('.funnel-input-error').html(message);
    }
    function validateType(type, value){
      value = cleanInput(value);
      switch(type){
        case 1: var filter = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/; break;
        case 2: var filter = /\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/; break;
      }
      var valid = ((filter.test(value))?true:false);
      if(!valid){ return false; } else { return true; }

    }
    function addError(message, id){
      var name = id;
      name = name.replace("-"," ");
      name = name.replace("-"," ");
      name = name.charAt(0).toUpperCase() + name.slice(1);
      errors.push("<span class='label'>"+name+"</span>: "+message);
    }
    // ON SUBMIT VALIDATE
    $('#checkout-form').submit(function(){
      errors = new Array();
      billingAddressErrors  = new Array();
      shippingAddressErrors = new Array();
      validateShipping  = $('#billing-is-shipping').is(':checked');
      billingValidated  = false;
      shippingValidated = false;
      $.each(ValidationData, function(id, validation){ // LOOP THROUGH THE VALIDATION INFORMATION
        var error = false; // SET ERROR TO FALSE
        if(id.indexOf('shipping') > -1 && validateShipping){
          // Do nothing
        } else {
          // IS REQUIED VALIDATION
          if(validation['required']){
            if($('#'+id).val() == ''){
              error = validation['required-error'];
              addError(validation['required-error'], id);
            }
          }
          // LENGTH VALIDATION
          if(!error){
            if(validation['minimum-length']!=false||validation['minimum-length']!=void 0,validation['minimum-length']!=0){ // IS THERE  A MIN LEN SET
              if($('#'+id).val().length < validation['minimum-length']){ // DID THEY REACH THE MIN LEN
                error = validation['minimum-length-error']; // NOPE ADD AN ERROR
                error = error.replace('%s', validation['minimum-length']); // REPLACE %S WITH THE LENGTH THEY DIDENT MEET
                addError(error, id);
              }
            }
            if(!error){
              if(validation['validate-as']!== void 0 ){
                if(!validateType(validation['validate-as'],$('#'+id).val())){
                  error = validation['invalid-error'];
                  addError(validation['invalid-error'],id);
                }
              }
            }
          }
          // Set the error content
          if(error==false){ setErrorContent(id, "");
          } else { setErrorContent(id, '<span><i class="fa fa-exclamation-triangle"></i></span> <strong>Error: </strong>'+error); }
        }
      });
      if( errors.length>=1 ){
        var title = ((errors.length==1)?'Error':'Errors');
        $('#funnel-checkout-errors .title .text .count').html(errors.length);
        $('#funnel-checkout-errors .title .text').html("<span class='count'>"+errors.length+"</span> "+title);
        var content = "";
        content += "<ul>";
        $.each(errors, function(key, message){ content += "<li>"+message+"</li>"; });
        content += "</ul>";
        $('#funnel-checkout-errors .content').html(content);
        $('#funnel-checkout-errors').show();
      } else {
        $('#funnel-checkout-errors').hide();
        return true;
      }
      return false;
    });
    // CLOSE THE ERROR POPUP
    $('#funnel-checkout-errors .close').on('click',function(event){
      $('#funnel-checkout-errors').hide();
    });
    // OPEN HELPER POPUP
    $('.checkout-help').on('click',function(event){
      $('.funnel-input-helper').hide();
      $(event.target).closest('.funnel-input-row').find('.funnel-input-helper').show();
    });
    // CLOSE HELPER POPUP BOX
    $('.funnel-input-helper .header .close').on('click',function(event){
      $(event.target).closest('.funnel-input-row').find('.funnel-input-helper').hide();
    });
    // SHOW THE PRODUCT DESCRIPTION POPUP
    $('.funnel-product-image').on('click',function(event){
      $('.product-description-popup').hide();
      var targetID = event.target.id;
      if(event.target.nodeName=="IMG"){
        targetID = event.target.parentNode.id;
      }
      targetID = "#"+targetID.replace('image','product');
      $(targetID).show();
    });
    // CLOSE FUNCTION FOR PRODUCT POPUP
    $('.product-description-popup .header .close').on('click',function(event){
      $(event.target).closest('.product-description-popup').hide();
    });
    // SHOW HIDE SHIPPING ADDRESS
    $('#billing-is-shipping').on('click',function(event){
      if($('#billing-is-shipping').is(":checked")){ $('#shipping-address').hide();
      } else { $('#shipping-address').show(); }
    });
  }
  /** CONTACT US FORM */
  if($("#funnel-contact-us-form").length){
    var errors = false;
    $('#funnel-contact-us-form').submit(function() {
      if($('#name').val()==""){
        errors = true;
        $('#contactus-name-error').show();
        $('#contactus-name-error').html("A name is required!");
      }
      if($('#email').val()==""){
        errors = true;
        $('#contactus-email-error').show();
        $('#contactus-email-error').html("An email address is required!");
      }
      if($('#message').val()==""){
        errors = true;
        $('#contactus-message-error').show();
        $('#contactus-message-error').html("A message is required!");
      }
      if(errors) {
        return false;
      }
      $.post('/Event/AjaxPost',
        {
          action: 'ContactUsSend',
          name: $('#name').val(),
          email: $('#email').val(),
          subject: $('#subject').val(),
          message: $('#message').val(),
          customer: 0
        }, function (data) {
          if (data.result) {
            $("#funnel-content").html("<h1>" + data.title + "</h1><p>" + data.content + "</p>");
          } else {
            // We should add some form of error message here
          }
        },'json');
      return false;
    });
  }
});
