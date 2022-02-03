/**
 * Every page that has javascript functionality this one is for the Management/MediaBuy/EmailLinks Page
 *
 * SysteePage Object
 * @type {Object}
 */
var SystemPage = {
  /**
   * This is an array of the datatables indexed by the promoter key
   * @type {Array}
   */
  datatables: [],

  /**
   * This is the array of promoters on this page currently
   * @type {Array}
   */
  promoters: [],

  /**
   * This function sets up the page
   * @method init
   * @return {boolean} Return true on success
   */
  init :function(){
    /** Set the default values for the editable plugin */
    $.fn.editable.defaults.mode       = 'inline';
    SystemPage.mainPermission         = 'media-buy';
    /** Setup the datatables */
    this.setupDatatables();
    /** Add the events to the page */
    this.setEvents();
    /** When a model is closed we need to remove its content to avoid posting all form data */
    $('body').on('hide.bs.modal', function( event ){
      $('#'+event.target.id).removeAttr("style");
      $('#'+event.target.id).html("");
    });
  },

  /**
   * Initalize all the datatable objects and add them to the datatables array
   * @method setupDatatables
   * @return {void}
   */
  setupDatatables: function(){
    /** Find all the promoter portlets and loop through them */
    $.each( $('.email-link-portlet'), function(){
      /** Add this promoter name to the promoters list */
      SystemPage.promoters[ $( this).attr('data-promoter-name') ] = this;
      /** Initalize this portlet and add it to the collection */
      SystemPage.datatables[ $(this).attr('data-promoter-key') ] = new Framework.Datatable( { datatable: $(this).find( '.DataTbl' ) } );
    });
  },

  /**
   * Set the events for this page
   * @method setEvents
   * @return {true}
   */
  setEvents: function(){
    /** Remove the events so we dont double bind */
    $('.add-promoter').unbind( 'click' );
    $('.add-link').unbind( 'click' );
    $('config').unbind( 'click' );
    $('.edit-link').unbind( 'click' );
    /** Add the events */
    $('.add-promoter').on(  'click', function( event ){  System.hasPermission( SystemPage.mainPermission, 'add-promoter',   'SystemPage.callback', 'makeAddPromoterForm',   event ); });
    $('.add-link').on(      'click', function( event ){  System.hasPermission( SystemPage.mainPermission, 'add-email-link', 'SystemPage.callback', 'makeAddLinkForm',       event ); });
    $('.config').on(        'click', function( event ){  System.hasPermission( SystemPage.mainPermission, 'edit-promoter',  'SystemPage.callback', 'makeEditPromoterForm',  event ); });
    $('.edit-link').on(     'click', function( event ){  System.hasPermission( SystemPage.mainPermission, 'edit-email-link','SystemPage.callback', 'makeEditLinkForm',      event ); });
    /** All done return to end the function faster */
    return true;
  },

  /**
   * Callback method for the check permissions system method
   * @method callback
   * @param  {object} data   Any data that was passed back from sent request
   * @param  {string} method The orignal method name that was called
   * @param  {[type]} event  The event that started the chain
   * @return {boolean}       True if we passed
   */
  callback: function( data, method, event ){
    /** check if we failed */
    if( !data.result ){
      /** Something went wrong tell the user and return false to end the method */
      $.notific8( data.data, { theme: 'ruby', life: 3000 } );
      return false;
    }
    /** find the correct function and run it */
    switch( method ){
      /** Make the add promoter form */
      case 'makeAddPromoterForm':   this.makeAddPromoterForm( event );  break;
      /** Make the add link form */
      case 'makeAddLinkForm':       this.makeAddLinkForm( event );      break;
      /** Make the edit promoter form */
      case 'makeEditPromoterForm':  this.makeEditPromoterForm( event ); break;
      /** Make the edit link form */
      case 'makeEditLinkForm':      this.makeEditLinkForm( event );     break;
    }
    /** We are done there were no problems return true */
    return true;
  },

  /**
   * make add link form
   * @method function
   */
  makeAddLinkForm: function(){
    /** Start loading the add promoter model */
    $('#add-link').load( $('#add-link').attr('data-url'),function(e){
      /** Wait 250ms for DOM to register the model contents */
      setTimeout( function(e){
        $('#name').editable({
          onblur:       'submit',
          placeholder:  'Set the link name',
          emptytext :   'Set the link name',
          validate: function(value) {
            if($.trim(value) == '')       return 'Link name is required!';
            if($.trim(value).length < 5 ) return 'Link names must be 5 characters or more!';
          }
        });
        /** Promoter Select2 */
        $('#promoter').editable({
          type: 'select2',
          pk: 1,
          onblur: 'submit',
          emptytext: 'Select a promoter!',
          select2: {
            placeholder:  'Select a promoter!',
            allowClear:   true,
            width:        '300px',
            minimumInputLength: 1,
            ajaxOptions:  { type: 'post', dataType: 'json' },
            id:           function( item ){ return item.id; },
            text:         function( item ){ return item.text; },
            ajax: {
              dataType:     'json',
              type:         'post',
              cache:        false,
              quietMillis:  250,
              url:'/Request/ManagementMediaBuyEmailLinks',
              data: function(term, page){
                return { action: 'search-promoters', query: term || '' }
              }, results: function(data, page){
                if( !data.result && data.data ){ return []; }
                return { results: $.map( data.data, function( item ){ return { id: item.id, text: item.text } }) };
              }, processResults: function (data, page) {
                if( !data.result && data.data ){ return []; }
                return { results: $.map( data.data, function( item ){ return { id: item.id, text: item.text } } ) };
              }
            },
            formatResult:     function (item){  return item.text; },
            formatSelection:  function (item) { return item.text; },
            initSelection:    function( element, callback ){
              var id = $(element).val();
              if (id !== "") {
                $.post('/Request/ManagementMediaBuyEmailLinks',
                  { action: 'get-promoter-name', key: id }, function( data ){
                    if( !data.result ){
                      $.notific8( data.data, { theme: 'ruby', life: 3000 } );
                      return false;
                    }
                },'json').done(function( data ) {
                  if( !data.result ){
                    $.notific8( data.data, { theme: 'ruby', life: 3000 } );
                    return false;
                  }
                  callback( { 'id': data.data.id, 'text': data.data.text });
                },'json');
              }
            }
          }
        });
        /** Funnel Select2 */
        $('#funnel').editable({
          type: 'select2',
          pk: 1,
          onblur: 'submit',
          emptytext: 'Select a funnel!',
          select2: {
            placeholder:  'Select a funnel!',
            allowClear:   true,
            width:        '300px',
            minimumInputLength: 1,
            ajaxOptions:  { type: 'post', dataType: 'json' },
            id:           function( item ){ return item.id; },
            text:         function( item ){ return item.text; },
            ajax: {
              dataType:     'json',
              type:         'post',
              cache:        false,
              quietMillis:  250,
              url:'/Request/ManagementMediaBuyEmailLinks',
              data: function(term, page){
                return { action: 'search-funnels', query: term || '' }
              }, results: function(data, page){
                if( !data.result && data.data ){ return []; }
                return { results: $.map( data.data, function( item ){ return { id: item.id, text: item.text } }) };
              }, processResults: function (data, page) {
                if( !data.result && data.data ){ return []; }
                return { results: $.map( data.data, function( item ){ return { id: item.id, text: item.text } } ) };
              }
            },
            formatResult:     function (item){  return item.text; },
            formatSelection:  function (item) { return item.text; },
            initSelection:    function( element, callback ){
              var id = $(element).val();
              if (id !== "") {
                $.post('/Request/ManagementMediaBuyEmailLinks',
                  { action: 'get-funnel-name', key: id }, function( data ){
                    if( !data.result ){
                      $.notific8( data.data, { theme: 'ruby', life: 3000 } );
                      return false;
                    }
                },'json').done(function( data ) {
                  if( !data.result ){
                    $.notific8( data.data, { theme: 'ruby', life: 3000 } );
                    return false;
                  }
                  callback( { 'id': data.data.id, 'text': data.data.text });
                },'json');
              }
            }
          }
        });
        /** Link Cost Text */
        $('#cost').editable({
          onblur:       'submit',
          placeholder:  '0.00',
          emptytext :   'Set the link cost',
          validate: function(value) {
            var regexp = new RegExp("^\d{0,2}(\.\d{0,2}){0,1}$");
            if($.trim(value) == '') return 'Link cost is required!';
          }
        });
        /** Link Return Text **/
        $('#return').editable({
          pk:           1,
          onblur:       'submit',
          placeholder:  '0.00',
          emptytext :   'Set the link expected return',
          validate: function(value) {
            if($.trim(value) == '')       return 'Link expected return is required!';
          }
        });
        /** Start Date datetimepicker **/
        $('#edit-start-date').editable({
          pk:               1,
          format:           'dd-mm-yyyy hh:ii:ss',
          viewformat:       'dd MM yyyy - hh:ii P',
          mode:             'popup',
          placement:        'right',
          placeholder:      'Set the start date',
          emptytext :       'Set the start date',
          datetimepicker: {
            format:           'dd-mm-yyyy hh:ii:ss',
            weekStart:        1,
            showMeridian:     true,
          }
        });
        /** End Date datetimepicker **/
        $('#edit-end-date').editable({
          pk:               1,
          format:           'dd-mm-yyyy hh:ii:ss',
          viewformat:       'dd MM yyyy - hh:ii P',
          mode:             'popup',
          placement:        'right',
          placeholder:      'Set the end date',
          emptytext :       'Set the end date',
          datetimepicker: {
            format:           'dd-mm-yyyy hh:ii:ss',
            weekStart:        1,
            showMeridian:     true,
          }
        });
        /** Add the event to the save button */
        $('.save-model').on('click',SystemPage.saveAddLink );
      },250)
    /** Done adding to the model set the styles and show it */
    }).modal('show').css({'width':'40%','top':'20%','left':'40%'});
  },

  /**
   * Setup the add promoter form and editables
   * @method makeAddPromoterForm
   * @return {void}
   */
  makeAddPromoterForm: function(){
    /** Start loading the add promoter model */
    $('#add-promoter').load( $('#add-promoter').attr('data-url'),function(e){
      /** Wait 250ms for DOM to register the model contents */
      setTimeout( function(e){
        /** Set the name field to editable */
        $('#name').editable({ onblur: 'submit', placeholder: 'Promoter Name', validate: function(value) { if ($.trim(value) == '') return 'This field is required'; } });
        /** Setup the select2 editable */
        $('#owning-user').editable({
          onblur: 'submit',
          select2: {
            placeholder: 'Select Owning User', width: 400,
            ajax: {
              /** Setup the ajax paramaters */
              url: '/Request/ManagementMediaBuyEmailLinks', dataType: 'json', type: 'post', quietMillis:300,
              /** Setup the data attribute that we will be submiting */
              data: function(term, page){ return { action: 'search-users', query: term || 'HolylandHealth' } },
              /** Sort the results into the correct format and add them to the select2 object */
              results: function(data, page){
                if( !data.result && data.data ){ return []; }
                return { results: $.map( data.data, function( user ){ return { id: user.id, text: user.text } }) };
              }
            }
          }
        });
        /** Add the event to the save button */
        $('.save-model').on('click',SystemPage.saveAddPromoter );
      },250);
    /** Done adding to the model set the styles and show it */
    }).modal('show').css({'width':'40%','top':'20%','left':'40%'});
  },

  /**
   * Build the edit form and setup the editables
   * @method makeEditLinkForm
   * @param  {[type]} event Open the editLink form
   */
  makeEditLinkForm: function( event ){
    /** Check if we have the Icon */
    if( event.target.nodeName == 'SPAN'){
      /** We have the icon get the parent */
      event.target = event.target.parentNode;
    }
    /** This is the link key */
    var key =  $( event.target ).attr('data-link-key');
    /** Get the link data */
    $.post( "/Request/ManagementMediaBuyEmailLinks", { action: 'get-link-data', 'key': key }, function( data ){
      /** Check for errors */
      if( !data.result ){
        /** We have an error tell the  user and return to exit the method */
        $.notific8( data.data, { theme: 'ruby', life: 3000 } );
        return false;
      }
    }, 'json').error( function( data ){
      $.notific8( 'error', { theme: 'ruby', life: 3000 } );
      return false;
    }).done( function( data ){
      /** Check for errors */
      if( !data.result ){
        /** We have an error tell the  user and return to exit the method */
        $.notific8( data.data, { theme: 'ruby', life: 3000 } );
        return false;
      }
      /** Start building out the form */
      var linkData  = data.data;
      /** Start loading the add promoter model */
      $('#edit-link').load( $('#edit-link').attr('data-url'),function(e){
        /** Wait 250ms for DOM to register the model contents */
        setTimeout( function(e){
          /** Make the header for the model more granular */
          $('#link-name').html( linkData['promoter-name']+': '+linkData['link-name'] );
          /** Setup the link name field */
          $('#name').editable({
            value:        linkData['link-name'],
            onblur:       'submit',
            placeholder:  'Set the link name',
            emptytext :   'Set the link name',
            validate: function(value) {
              if($.trim(value) == '')       return 'Link name is required!';
              if($.trim(value).length < 5 ) return 'Link names must be 5 characters or more!';
            }
          });
          /** Promoter Select2 */
          $('#promoter').text(linkData['promoter-name']);
          $('#promoter').attr({'data-value':linkData['promoter-key']});
          $('#promoter').editable({
            type: 'select2',
            pk: 1,
            onblur: 'submit',
            emptytext: 'Not Set',
            select2: {
              placeholder:  'Select a promoter!',
              allowClear:   true,
              width:        '300px',
              minimumInputLength: 1,
              ajaxOptions:  { type: 'post', dataType: 'json' },
              id:           function( item ){ return item.id; },
              text:         function( item ){ return item.text; },
              ajax: {
                dataType:     'json',
                type:         'post',
                cache:        false,
                quietMillis:  250,
                url:'/Request/ManagementMediaBuyEmailLinks',
                data: function(term, page){
                  return { action: 'search-promoters', query: term || '' }
                }, results: function(data, page){
                  if( !data.result && data.data ){ return []; }
                  return { results: $.map( data.data, function( item ){ return { id: item.id, text: item.text } }) };
                }, processResults: function (data, page) {
                  if( !data.result && data.data ){ return []; }
                  return { results: $.map( data.data, function( item ){ return { id: item.id, text: item.text } } ) };
                }
              },
              formatResult:     function (item){  return item.text; },
              formatSelection:  function (item) { return item.text; },
              initSelection:    function( element, callback ){
                var id = $(element).val();
                if (id !== "") {
                  $.post('/Request/ManagementMediaBuyEmailLinks',
                    { action: 'get-promoter-name', key: id }, function( data ){
                      if( !data.result ){
                        $.notific8( data.data, { theme: 'ruby', life: 3000 } );
                        return false;
                      }
                  },'json').done(function( data ) {
                    if( !data.result ){
                      $.notific8( data.data, { theme: 'ruby', life: 3000 } );
                      return false;
                    }
                    callback( { 'id': data.data.id, 'text': data.data.text });
                  },'json');
                }
              }
            }
          });
          /** Funnel Select2 */
          $('#funnel').text(linkData['funnel-name']);
          $('#funnel').attr({'data-value':linkData['funnel-key']});
          $('#funnel').editable({
            type: 'select2',
            pk: 1,
            onblur: 'submit',
            emptytext: 'Not Set',
            select2: {
              placeholder:  'Select a funnel!',
              allowClear:   true,
              width:        '300px',
              minimumInputLength: 1,
              ajaxOptions:  { type: 'post', dataType: 'json' },
              id:           function( item ){ return item.id; },
              text:         function( item ){ return item.text; },
              ajax: {
                dataType:     'json',
                type:         'post',
                cache:        false,
                quietMillis:  250,
                url:'/Request/ManagementMediaBuyEmailLinks',
                data: function(term, page){
                  return { action: 'search-funnels', query: term || '' }
                }, results: function(data, page){
                  if( !data.result && data.data ){ return []; }
                  return { results: $.map( data.data, function( item ){ return { id: item.id, text: item.text } }) };
                }, processResults: function (data, page) {
                  if( !data.result && data.data ){ return []; }
                  return { results: $.map( data.data, function( item ){ return { id: item.id, text: item.text } } ) };
                }
              },
              formatResult:     function (item){  return item.text; },
              formatSelection:  function (item) { return item.text; },
              initSelection:    function( element, callback ){
                var id = $(element).val();
                if (id !== "") {
                  $.post('/Request/ManagementMediaBuyEmailLinks',
                    { action: 'get-funnel-name', key: id }, function( data ){
                      if( !data.result ){
                        $.notific8( data.data, { theme: 'ruby', life: 3000 } );
                        return false;
                      }
                  },'json').done(function( data ) {
                    if( !data.result ){
                      $.notific8( data.data, { theme: 'ruby', life: 3000 } );
                      return false;
                    }
                    callback( { 'id': data.data.id, 'text': data.data.text });
                  },'json');
                }
              }
            }
          });
          /** Link Cost Text */
          $('#cost').editable({
            value:        linkData['link-cost'],
            onblur:       'submit',
            placeholder:  'Set the link cost',
            emptytext :   'Set the link cost',
            validate: function(value) {
              var regexp = new RegExp("^\d{0,2}(\.\d{0,2}){0,1}$");
              if($.trim(value) == '') return 'Link cost is required!';
            }
          });
          /** Link Return Text **/
          $('#return').editable({
            pk:           1,
            value:        linkData['link-return'],
            onblur:       'submit',
            placeholder:  'Set the link expected return',
            emptytext :   'Set the link expected return',
            validate: function(value) {
              if($.trim(value) == '')       return 'Link expected return is required!';
            }
          });
          /** Start Date datetimepicker **/
          $('#edit-start-date').editable({
            pk:               1,
            format:           'dd-mm-yyyy hh:ii:ss',
            value:            linkData['start-date'],
            viewformat:       'dd MM yyyy - hh:ii P',
            mode:             'popup',
            placement:        'right',
            placeholder:      'Set the start date',
            emptytext :       'Set the start date',
            datetimepicker: {
              format:           'dd-mm-yyyy hh:ii:ss',
              weekStart:        1,
              showMeridian:     true,
            }
          });
          /** End Date datetimepicker **/
          $('#edit-end-date').editable({
            pk:               1,
            format:           'dd-mm-yyyy hh:ii:ss',
            value:            linkData['end-date'],
            viewformat:       'dd MM yyyy - hh:ii P',
            mode:             'popup',
            placement:        'right',
            placeholder:      'Set the end date',
            emptytext :       'Set the end date',
            datetimepicker: {
              format:           'dd-mm-yyyy hh:ii:ss',
              weekStart:        1,
              showMeridian:     true,
            }
          });
          /** Add the event to the save button */
          $('.save-model').on('click',SystemPage.saveEditLink );
          /** Set the link key **/
          $('#key').val( linkData['link-key'] );
        },200);
      /** Done adding to the model set the styles and show it */
      }).modal('show').css({'width':'40%','top':'20%','left':'40%'});
    });
  },

  /**
   * Make the edit promoter form
   * @method function
   * @param  {event}    event 
   */
  makeEditPromoterForm: function( event ){

  },

  /**
   * Save the new link
   * @method saveAddLink
   * @param  {[type]} event the save event
   */
  saveAddLink: function( event ){
    /** Build our post data */
    var link = {
      action:   $('#action').val(),
      key:      $('#key').val(),
      name:     $('#name').data().editable.value,
      promoter: $('#promoter').data().editable.value,
      funnel:   $('#funnel').data().editable.value,
      cost:     $('#cost').data().editable.value,
      return:   $('#return').data().editable.value,
      start:    moment( $('#edit-start-date').data().editable.value ).format('YYYY-MM-DD HH:mm:ss'),
      end:      moment( $('#edit-end-date').data().editable.value ).format('YYYY-MM-DD HH:mm:ss')
    };
    /** Update the email link */
    $.post( "/Request/ManagementMediaBuyEmailLinks", link, function( data ){
        /** Check if there was an error */
        if( !data.result ){
          /** We have an error notify the user */
          $.notific8( data.data, { theme: 'ruby', life: 3000 } );
          return false;
        }
        /** All done update the row and return */
        $('#add-link').modal('hide');
        return SystemPage.addLinkRow( data.data );
    }, 'json');
  },

  /**
   * Save the new link data
   * @method saveEditLink
   * @param  {object} event The save event
   */
  saveEditLink: function( event ){
    /** Build our post data */
    var link = {
      action:   $('#action').val(),
      key:      $('#key').val(),
      name:     $('#name').data().editable.value,
      promoter: $('#promoter').data().editable.value,
      funnel:   $('#funnel').data().editable.value,
      cost:     $('#cost').data().editable.value,
      return:   $('#return').data().editable.value,
      start:    moment( $('#edit-start-date').data().editable.value ).format('YYYY-MM-DD HH:mm:ss'),
      end:      moment( $('#edit-end-date').data().editable.value ).format('YYYY-MM-DD HH:mm:ss')
    };
    /** Update the email link */
    $.post( "/Request/ManagementMediaBuyEmailLinks", link, function( data ){
        /** Check if there was an error */
        if( !data.result ){
          /** We have an error notify the user */
          $.notific8( data.data, { theme: 'ruby', life: 3000 } );
          return false;
        }
        /** All done update the row and return */
        $('#edit-link').modal('hide');
        return SystemPage.updateLinkRow( data.data );
    }, 'json');
  },

  /**
   * Save the new promoter and add it to the page
   * @method saveAddPromoter
   * @return {void}
   */
  saveAddPromoter: function(){
    /** Make sure the owning user is set */
    if( $('#owning-user').data().editable.value == null ){
      $.notific8('You must specifiy an owner for this promoter!', { theme: 'ruby', life: 3000 });
      return false;
    }
    /** Make sure the name of this promoter is set */
    if( $('#name').data().editable.value == null ){
      $.notific8('You must specifiy name for this promoter!', { theme: 'ruby', life: 3000 });
      return false;
    }
    /** Build the post data object */
    var postData = {
      'action':     $('#action').val(),
      'owner-key':  $('#owning-user').data().editable.value,
      'name':       $('#name').data().editable.value
    };
    /** Post the new promoter data to the database */
    $.post( "/Request/ManagementMediaBuyEmailLinks", postData, function( data ){
        /** Check if there was an error */
        if( !data.result ){
          /** We have an error notify the user */
          $.notific8( data.data, { theme: 'ruby', life: 3000 } );
          return false;
        }
        /** Now take the returned data and add this promoter to the screen */
        $('#add-link').modal('hide');
        SystemPage.makePromoterPortlet( data.data['name'], data.data['key'] );
    }, 'json');
  },

  /**
   *  Update the row in the promoters data table that contains this link
   *  This way theres no need to refresh the page
   *  @method   updateLinkRow
   *  @param    {object}  Link data object
   */
  updateLinkRow: function( link ){
    var row = SystemPage.datatables[ link['promoter-key'] ].searchColumn( 0, link['link-key'] );
    SystemPage.datatables[ link['promoter-key'] ].updateRow(
      row,
      [
        link['link-key'],
        link['link-name'],
        link['start-date'],
        link['end-date'],
        '$'+link['link-cost'],
        '$'+link['link-return'],
        '<a href="javascript:;" class="btn btn-sm default blue edit-link" data-link-key="'+link['link-key']+'"><span class="glyphicon glyphicon-cog">&nbsp;</span>Edit </a>'
      ]
    );
    SystemPage.setEvents();
    return true;
  },

  /**
   * Add a new link row to the datatable
   * @method addLinkRow
   * @param  {object} link The data for the link
   */
  addLinkRow: function( link ){
    var row = SystemPage.datatables[ link['promoter-key'] ].searchColumn( 0, link['link-key'] );
    console.log( row );
    SystemPage.datatables[ link['promoter-key'] ].addRow(
      [
        link['link-key'],
        link['link-name'],
        link['start-date'],
        link['end-date'],
        '$'+link['link-cost'],
        '$'+link['link-return'],
        '<a href="javascript:;" class="btn btn-sm default blue edit-link" data-link-key="'+link['link-key']+'"><span class="glyphicon glyphicon-cog">&nbsp;</span>Edit </a>'
      ]
    );
    SystemPage.setEvents();
    return true;
  },

  /**
   * Update the promoter
   * @method updatePromoter
   * @return {object} The promoter resource
   */
  updatePromoter:       function(){

  },

  /**
   * Make a promoter portlet with the datatable add it to the page
   * @method makePromoterPortlet
   * @param  {string} name  The name of the promoter we are making a portlet for
   * @param  {number} key   The key of the promoter we are making a portlet for
   * @return {boolean}      Did we succeed
   */
  makePromoterPortlet: function( name, key ){
    /** Make all the html needed for a promoter portlet and add it to the html variable */
    var html =
      '<div data-promoter-name="' + name + '" data-promoter-key="' + key + '" class="portlet box blue-sharp email-link-portlet">' +
        '<div class="portlet-title">' +
          '<div class="caption">' +
              '<i class="fa fa-users">&nbsp;</i>' +
              '<span class="caption-subject">' + name + '</span>' +
          '</div>'+
          '<div class="tools">' +
            '<a class="config" href="javascript:;" data-original-title="" title="">&nbsp;</a>&nbsp;&nbsp;' +
            '<a class="fullscreen" href="javascript:;" data-original-title="" title="">&nbsp;</a>&nbsp;&nbsp;' +
            '<a class="collapse" href="javascript:;" data-original-title="" title=""> </a>&nbsp;&nbsp;' +
          '</div>' +
          '<div class="actions">' +
            '<a href="javascript:;" class="btn blue-hoki border-white add-link btn-sm"><i class="fa fa-plus"></i> Add Link </a>' +
          '</div>' +
        '</div>' +
        '<div class="portlet-body">' +
          '<table class="table table-striped table-bordered table-hover dt-responsive DataTbl" id="promoter-links-' + key + '" data-datatable-tools="" width="100%">' +
            '<thead>' +
              '<tr>' +
                '<th data-datatable-order="1" style="text-align:center;width:50px;">Key</th>' +
                '<th data-datatable-order="1" style="text-align:center;width:50px;">Name</th>' +
                '<th data-datatable-order="1" style="text-align:center;width:50px;">Start Date</th>' +
                '<th data-datatable-order="1" style="text-align:center;width:50px;">End Date</th>' +
                '<th data-datatable-order="1" style="text-align:center;width:50px;">Cost</th>' +
                '<th data-datatable-order="1" style="text-align:center;width:50px;">Expected Return</th>' +
                '<th data-datatable-order="1" style="text-align:center;width:50px;">Actions</th>' +
              '</tr>' +
          '</table>' +
        '</div>' +
      '</div>';
      /** Add this promoter to the list */
      this.promoters[name]    = null;
      /** Get the preceedingPromoter if there is one */
      var preceedingPromoter  = this.getPreceedingPromoter( name );
      /** Check if we have a preceedingPromoter */
      if( preceedingPromoter ){ var promoterPortlet = $( html ).insertAfter( preceedingPromoter );
      } else { var promoterPortlet = $( html ).prependTo( $('#email-link-promoters').find( '.portlet-body' )[0] ); }
      /** Set the reference to this promoter portlet in the promoters array */
      this.promoters[name] = promoterPortlet;
      /** Initalize the datatable and add it to the datatables array */
      this.datatables[ $(promoterPortlet).attr('data-promoter-key') ] = new Framework.Datatable( { datatable: $(promoterPortlet).find( '.DataTbl' ) } );
      /** Reset all the events now that we have added a new portlet and datatable */
      this.setEvents();
      /** We are all done return true */
      return true;
  },

  /**
   * Get the promoter before this one in alphabetical order or false
   * @method getPreceedingPromoter
   * @param  { string }           promoter  Promoter name for the promoter before this one in alphabetical order
   * @return { string | false }             The name of the promoter before this one or false if none available
   */
  getPreceedingPromoter: function( promoter ){
    /** Make a reference to the promoters array */
    var promoters = this.promoters;
    /** Sort the array to keep it in alphabetical order */
    promoters.sort();
    /** Make a null previous promoters variable */
    var previousPromoter  = null;
    /** Loop through the promoters array */
    for( var currentPromoter in promoters ){
      /** Check if this is the promoter we are looking for */
      if( promoter > currentPromoter ){
        /** We found the correct promoter now return the previous one */
        return previousPromoter;
      }
      /** We dident find a match so set this to the previousPromoter and move on */
      previousPromoter = promoters[ currentPromoter ];
    }
    /** If we get here that means it is the first in the list return false */
    return false
  },
};
