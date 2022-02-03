var SystemPage = {
  init :function(){
    $.fn.editable.defaults.mode       = 'inline';
    SystemPage.mainPermission         = 'media-buy';

    //this.setupDatatables();
    this.setEvents();
  },
  setEvents: function(){
     $('.config').on(  'click', SystemPage.makeSelectLinkForm );
  },
  makeSelectLinkForm: function(){
    $('#select-link').modal('show').css({'width':'40%','top':'20%','left':'40%'});


        $('#link').editable({
          type: 'select2',
          pk: 1,
          onblur: 'submit',
          emptytext: 'Select a Link!',
          select2: {
            placeholder:  'Select a Link!',
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
              url: '/Request/ReportsLinkSystemLinkBreakdown',
              data: function(term, page){
                return { action: 'search-links', query: term || '' }
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
                $.post('/Request/ReportsLinkSystemLinkBreakdown',
                  { action: 'get-link-name', key: id }, function( data ){
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
        $('.save-model').on('click',SystemPage.saveSelectLinkForm );
  },
  saveSelectLinkForm: function(){
    window.location = "/Reports/LinkSystem/LinkBreakdown?key="+$('#link').data().editable.value;
  }
}
