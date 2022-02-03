var EcommerceOrders = function () {

    var initPickers = function(){
      $('.date-picker').datepicker({
        rtl: App.isRTL(), autoclose: true
      });
    }

    var handleOrders = function(){
      var grid = new Datatable();
      grid.init({
        src: $("#datatable_orders"),
        onSuccess: function( grid ){  console.log('success'); },
        onError: function( grid ){    console.log('error'); },
        loadingMessage: 'Loading...',
        dataTable: {
          "columns": [
            { "orderable":true },
            { "orderable":true },
            { "orderable":true },
            { "orderable":true },
            { "orderable":true },
            { "orderable":true },
            { "orderable":false }
          ],
          "order":      [ [ 0, 'desc' ] ],
          "lengthMenu": [ [10, 20, 50, 75], [10, 20, 50, 75] ],
          "pageLength": 50,
          "ajax": { "url":  "/Request/OrdersDatatable", "type": "POST" }
        }
      });
    grid.getTableWrapper().on('click', '.table-group-action-submit', function(e){
      e.preventDefault();
      var action = $( ".table-group-action-input", grid.getTableWrapper());
      if( action.val() != "" && grid.getSelectedRowsCount() > 0 ){
        grid.setAjaxParam( "customActionType", "group_action" );
        grid.setAjaxParam( "customActionName", action.val() );
        grid.setAjaxParam( "id", grid.getSelectedRows() );
        grid.getDataTable().ajax.reload();
        grid.clearAjaxParams();
      } else if( action.val() == "" ){ alert({ type: 'danger', icon: 'warning', message: 'Please select an action', container: grid.getTableWrapper(), place: 'prepend' });
      } else if( grid.getSelectedRowsCount() === 0 ){ alert({ type: 'danger', icon: 'warning', message: 'No record selected', container: grid.getTableWrapper(), place: 'prepend' }); }
    });
  }
  /** Initalize the datatable */
  return { init: function(){
    initPickers();
    handleOrders();
  }};
}();
/** setup when the document is ready */
jQuery(document).ready(function(){ EcommerceOrders.init(); });
