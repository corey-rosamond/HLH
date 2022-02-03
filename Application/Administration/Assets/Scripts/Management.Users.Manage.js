/** Make the page object for this page with an init */
var SystemPage = {
  userDatatable: null,
  /** initialize the page javascript object */
  init :function(){
    /** Initalize the datatable */
    this.userDatatable = new Framework.Datatable( { datatable: $('#user-datatable') } );
    /** user information button */
    $(".user-information").on( 'click', function( event ){
      System.hasPermission( 'user-system', 'edit-information', 'SystemPage.callback', 'showUserInformation', event );
    });
    /** user log button */
    $('.user-log').on( 'click', function( event ){
      System.hasPermission( 'user-system', 'view-log', 'SystemPage.callback', 'showUserLog', event );
    });
    /** user permissions button */
    $('.user-permissions').on( 'click', function( event ){
      System.hasPermission( 'user-system', 'edit-permissions', 'SystemPage.callback', 'showUserPermissions', event );
    });
    /** setup the confirmation box and disable / enable actions */
    $('.disable-user').confirmation( { onConfirm: function( event ){
      SystemPage.changeUserStatus( event );
    }});
    /** clear the windows out after we are done with them. This way we dont have to worry about stale data */
    $('body').on('hide.bs.modal', function( event ){
      $('#'+event.target.id).removeAttr("style");
      $('#'+event.target.id).html("");
    });
  },
  /** run the orignaly requested function if applicable */
  callback: function( data, method, event ){
    /** check if we failed if we did tell them */
    if( !data.result ){
      $.notific8( data.data, { theme: 'ruby', life: 3000 } );
      return false;
    }
    /** find the correct function and run it */
    switch( method ){
      case 'showUserPermissions': SystemPage.showUserPermissions( event );  break;
      case 'showUserInformation': SystemPage.showUserInformation( event );  break;
      case 'showUserLog':         SystemPage.showUserLog( event );          break;
    }
  },
  /** show the user permissions window */
  showUserPermissions: function( event ){
    /** get the user permissions modal */
    var $userPermissions      = $('#user-permissions');
    /** get the user key for the user in question */
    var $key                  = SystemPage.getUserKey( event );
    /** all possible permissions will be stored here */
    var $possiblePermissions  = null;
    /** permission list container **/
    var $permissionList       = null;
    /** this users permissions */
    var $currentPermissions   = null;
    /** load the content into the modal */
    $userPermissions.load($(event.target).attr('data-url'), function(result){
      /** get the possible permissions */
      $.post( "/Request/ManagementUsersManage", { 'action':'get-possible-permissions', 'key': $key }, function( pPermData ) {
        /** if we have an error use notificat8 to tell the user there was a problem and stop */
        if(!pPermData.result){ $.notific8(pPermData.data, { theme: 'ruby', life: 3000 }); return false; }
        /** Nothing went wrong save the possible permissions */
        $possiblePermissions = pPermData.data;
        /** get the users permissions */
        $.post( "/Request/ManagementUsersManage", { 'action':'get-user-permissions', 'key': $key }, function( cPerm ) {
          /** if we have an error use notificat8 to tell the user there was a problem and stop */
          if(!cPerm.result){ $.notific8(cPerm.data, { theme: 'ruby', life: 3000 }); return false; }
          /** Nothing went wrong save the possible permissions */
          $currentPermissions = cPerm.data;
          /** set the user key */
          $('#user-permissions-form #key').val($key);
          /** get the permission list container **/
          $permissionList = $('#permission-list');
          /** this is where we store the groups while we build them **/
          var $group      = null;
          var fullWidth   = $(document).width();
          var width       = ( Math.floor( fullWidth / 350 ) - 1 )*350;
          var left        = Math.floor( ( fullWidth - width ) / 2 )+250;
          /** build the permission boxes into the container */
          $.each( $possiblePermissions, function( key, value ){
            $group = '<div class="section">';
              $group += '<div class="section-header">';
                $group += '<label class="control-label">'+value['simple-name']+'</label>';
                $group += '<div class="placement">';
                  $group += '<input type="checkbox" data-permission="'+key+'" class="make-switch" ' +
                    (( value['default'] || $currentPermissions[key] !== void 0 )?'checked ':'') +
                    'data-on-color="success" data-off-color="danger" data-size="small" />';
                $group += '</div>';
              $group += '</div>';
              $group += '<div class="section-content">';
                $.each( value['permissions'], function( subkey, subvalue ){
                  $group += '<div class="permission">';
                    $group += '<label>'+subvalue['simple-name']+'</label>';
                    $group += '<div class="switch">';
                      $group += '<input type="checkbox" class="make-switch" data-permission="'+subkey+'" ' +
                        (( value['default'] || ( $currentPermissions[key] !== void 0 && $currentPermissions[key][subkey] !== void 0 ) )?'checked ':'') +
                        'data-on-color="success" data-off-color="danger" data-size="small" />';
                    $group += '</div>';
                  $group += '</div>';
                });
              $group += '</div>';
            $group += '</div>';
            /** add this group to the container */
            $permissionList.append( $($group) );
            /** null the group out to avoid stale data */
            $group = null;
          });
          /** STARRRTTT YOURRR SWITCHESSS */
          $('.make-switch').bootstrapSwitch();
          /** listen for click events on the handle off this way we can
              select and deselect the main section after the user */
          $('.bootstrap-switch-handle-off').on('click',function( event ){
            SystemPage.fixPermissionSection( $(event.target).closest('.section') );
          });
          /** listen for click events on the handle on this way we can
              select and deselect the main section after the user */
          $('.bootstrap-switch-handle-on').on('click',function( event ){
            SystemPage.fixPermissionSection( $(event.target).closest('.section') );
          });
          /** find the save button and bind the save event */
          $userPermissions.find('.save-model').on('click', SystemPage.savePermissions );
          /** Bring the modal into view */
          $userPermissions.modal('show').css({'width':( width )+'px','top':'20%','left':left+'px'});
        },'json');
      },'json');
    });
  },
  /** save the new permissions */
  savePermissions: function(){
    /** get an arry of the new permissions for this user */
    var userPermissions = {};
    var $userPermissionsForm = $('#user-permissions-form');
    $.each( $userPermissionsForm.find('.section'), function( key, value ){
      var groupInput = $(value).find('.section-header').find('.make-switch');
      if( $(groupInput).bootstrapSwitch('state') ){
        var group = $(groupInput).attr('data-permission');
        userPermissions[group] = {};
        var sectionContent = $(value).find('.section-content');
        $.each( $(sectionContent).find('.make-switch'), function( subkey, subvalue ){
          if( $(subvalue).bootstrapSwitch('state') ){ userPermissions[group][$(subvalue).attr('data-permission')] = true; }
        });
      }
    });
    /** now that we have the array get the post data */
    var postData = {
      'action':       $('#action').val(),
      'key':          $('#key').val(),
      'permissions':  JSON.stringify(userPermissions)
    }
    /** submit the data to the hanlder */
    $.post( "/Request/ManagementUsersManage", postData, function(json) {
      /** if we have an error use notificat8 to tell the user there was a problem and stop */
      if(!json.result){ $.notific8(json.data, { theme: 'ruby', life: 3000 }); return false; }
      $('#user-permissions').modal('hide');
    },'json');
  },
  /** fix the switches in one section of the permissions form */
  fixPermissionSection: function( section ){
    /** @var int the count of switchs in the on position */
    var on  = 0;
    /** @var int the count of switches in the off position */
    var off = 0;
    /** @var section header */
    var sectionHeader = $(section).find('.section-header');
    /** @var group input */
    var groupInput = $(sectionHeader).find('.make-switch');
    /** now loop through all the input boxes and count the on and off switches */
    $.each( $(section).find('.make-switch'), function( key, value ){
      if( $(value).bootstrapSwitch('state') ){ on++; } else { off++; }
    });
    /** check if we have 1 or more members in a group **/
    if( on >= 1 ){
      /** at least one switch is in the on position */
      if( on == 1 ){
        if( groupInput.bootstrapSwitch('state') ){
          /** the only switch marked as on is the group switch turn it off and exit the function */
          $(groupInput).bootstrapSwitch('toggleState');
          return true;
        } else {
          /** one of the sub switches is marked as on mark the gropu as on and exit the function */
          $(groupInput).bootstrapSwitch('toggleState');
          return true;
        }
      } else {
        /** more than one switch is marked as on make sure the group switch is on */
        if( !groupInput.bootstrapSwitch('state') ){
          /** somehow it was not on. Turn it on and exit the function */
          $(groupInput).bootstrapSwitch('toggleState');
          return true;
        } else { return true; }
      }
    }
  },
  /** Show user information popup */
  showUserInformation: function( event ){
    var $userInformation = $('#user-information');
    /** Get the key for the user selected */
    var key = SystemPage.getUserKey( event );
    /** Get all of the users information */
    $.post( "/Request/ManagementUsersManage", { 'action':'get-user-information', 'key': key }, function(json) {
      /** if we have an error use notificat8 to tell the user there was a problem and stop */
      if(!json.result){
        $.notific8(json.data, { theme: 'ruby', life: 3000 });
        return false;
      }
      /** if all went well lets get the form ready and render it */
      $userInformation.load($(event.target).attr('data-url'), function(result){
        $userInformation.modal('show').css({'width':'600px','top':'5%'});
        SystemPage.setupUserInformationForm( json.data );
        $userInformation.find('.save-model').on('click',SystemPage.saveUserInformation);
      });
    },'json');
  },
  saveUserInformation: function( event ){
    /** Get all the post data we are going to need */
    var postData = {
      'action':       $('#action').val(),
      'key':          $('#key').val(),
      'username':     $('#username').data().editable.value,
      'first-name':   $('#first-name').data().editable.value,
      'last-name':    $('#last-name').data().editable.value,
      'phone-number': $('#phone-number').data().editable.value,
      'email-address':$('#email-address').data().editable.value
    }
    /** post the data to the request handler */
    $.post( "/Request/ManagementUsersManage",
      postData,
      function(json) {
        /** if we have an error use notificat8 to tell the user there was a problem and stop */
        if(!json.result){
          $.notific8(json.data, { theme: 'ruby', life: 3000 });
          return false;
        }
        SystemPage.updateUserListRow( json.data );
        $('#user-information').modal('hide');
    },'json');
  },
  /** update a row in the user list with new data */
  updateUserListRow: function( data ){
    /**  Get the datatable */
    var datatable = $('#user-datatable').DataTable();
    /** Find the dex */
    var dex = SystemPage.findUserRow( datatable, data['key'] );
    var old = datatable.row( dex ).data();
    datatable.row( dex ).data( [ data['key'], data['username'], data['first-name']+' '+data['last-name'], data['email-address'], data['phone-number'], old[5], old[6] ] ).draw() ;
  },
  /** find a dex for a row in the data table with the user key */
  findUserRow( datatable, userKey ){
    // Loop through the talbe rows looking for our row
    jQuery.each( datatable.rows().data(), function( dex, row ){
      // The first row is our int so that will be the quickest find
      if( row[0] == userKey ){
        // We found it return the dex and stop looping
        return dex;
      }
    });
  },
  /** Setup the user information form */
  setupUserInformationForm: function( data ){
    $.fn.editable.defaults.mode = 'inline';
    $.fn.editable.defaults.inputclass = 'form-control';
    $.fn.editable.defaults.url = '/post';
    $('#key').val( data['key'] );
    $('#username').editable({
      value: data['username'],
      disabled: true,
      locked: true
    });
    $('#first-name').editable({
      value: data['first-name'],
      validate: function(value) { if ($.trim(value) == '') return 'This field is required'; }
    });
    $('#last-name').editable({
      value: data['last-name'],
      validate: function(value) { if ($.trim(value) == '') return 'This field is required'; }
    });
    $('#phone-number').editable({
      value: data['phone-number'],
      validate: function(value) { if ($.trim(value) == '') return 'This field is required'; }
    });
    $('#email-address').editable({
      value: data['email-address'],
      validate: function(value) { if ($.trim(value) == '') return 'This field is required'; }
    });
  },
  /** disable user */
  changeUserStatus: function( event ){
    /** Setup our styles and colors */
    var attributes    = {
      "Activate": {   color:'green-sharp',      icon:'fa fa-thumbs-o-down' },
      "Disable": {    color:'red-thunderbird',  icon:'fa fa-thumbs-o-up' }
    };
    /** get the target */
    var target = event.target;

    if(target.nodeName == 'I'){
      /** if the target is an i get the parent */
      target = target.parentNode;
    }
    /** Get the base elements */
    var buttonGroup     = $(event.target).closest('.btn-group');
    var key             = $(buttonGroup).attr('data-user-key');
    var lastButton      = $(buttonGroup).find('button:last');
    var icon            = $(lastButton).find('i');
    var action          = $(lastButton).attr('data-action');
    /** set active and not active */
    if( action == 'Activate' ){
      var next      = 'Activate';
      var previous  = 'Disable';
    } else {
      var next      = 'Disable';
      var previous  = 'Activate';
    }
    $.post( "/Request/ManagementUsersManage", { 'action':'change-user-status', 'status': next, 'key': key }, function(json) {
      /** if we have an error use notificat8 to tell the user there was a problem and stop */
      if(!json.result){
        $.notific8(json.data, { theme: 'ruby', life: 3000 });
        return false;
      }
      /** Switch the color classes */
      $(buttonGroup).find('button').each(function(){
        $(this).removeClass( attributes[previous].color );
        $(this).addClass( attributes[next].color );
      });
      /** swap the icon content */
      $(icon).removeClass( attributes[previous].icon );
      $(icon).addClass( attributes[next].icon );
      /** Change the text */
      $(lastButton).html( $(lastButton).html().replace(next, previous));
      /** set the attributes */
      $(lastButton).attr({ 'data-action':previous });
      $(lastButton).attr({ 'data-original-title':previous+' User!' });
    },'json');
  },


  /** Show the user log */
  showUserLog: function( event ){
    /** get the user key that we need the log for */
    var key = SystemPage.getUserKey( event );
    /** post request to get the user list for this user */
    $.post( "/Request/ManagementUsersManage", { 'action':'show-recent-log', 'key': key }, function(json) {
      /** if we have an error use notificat8 to tell the user there was a problem and stop */
      if(!json.result){
        $.notific8(json.data, { theme: 'ruby', life: 3000 });
        return false;
      }
      /** no errors  lets setup the modal */
      var $userLog          = $('#user-log');
      $userLog.load($(event.target).attr('data-url'), function(result){
        /** If it doesent have the role attr then we need to register it */
        if( $('#user-log-datatable').attr('role') === void 0 ){ System.registerDatatable('user-log-datatable');
        } else { $('#user-log-datatable').DataTable().clear(); }
        var $datatable        = $('#user-log-datatable').DataTable();
        /** poulate the datatable */
        for( var index = 0; index < json.data.length; index++ ){
          $datatable.row.add( [ json.data[index]['key'], json.data[index]['date-stamp'], json.data[index]['message'] ]);
        }
        $datatable.draw();
        $userLog.modal('show').css({'left':'20%' ,'top':'5%','width':'60%','margin':'0px'});
        /** draw the datatable */
        $($userLog).find('.dt-buttons').css({'position':'absolute','top':'-18px','right':'50px'});
      });
    },'json');
  },
  /** Get the user key from the parent element */
  getUserKey: function( event ){
    return $(event.target.parentNode).attr('data-user-key');
  }
}
