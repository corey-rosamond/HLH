window.Simplicity = {
  _olQueue: [],
  initialize: function(){
    this._olInitialize();
    this._cbInitialize();
    this._dtInitialize();


    if(this._page!==void 0){
      this._page.initialize();
    }
  },

  onLoad: function(callback){
    this._olQueue.push(callback);
  },
  /** Handle the onLoad callbacks */
  _olInitialize: function(){
    for(var index=0;index<this._olQueue.length; index++){
      this._olQueue[index].call();
    }
  },
  /** CALLBACKS */
  _cbInitialize: function(){
    $('.simplicity-callback').each(function(){
      var callbackOn = $(this).attr('data-simplicity-callback-type');
      $(this).on(callbackOn,Simplicity._cbProcess);
    });
  },
  _cbProcess: function(event){
    var target = event.target;
    var actionParts = $(target).attr('data-simplicity-callback-action').split(":");
    var actionType = actionParts[0];
    var actionSelector = actionParts[1];
    switch(actionType){
      case 'popup':
        var object = document.getElementById(actionSelector);
        $(object).modal('show').css({'width':'40%','top':'20%','left':'40%'});
        break;
    }
  },
  /** DATATABLES */
  _dtCollection: [],
  _dtTools: ['print','pdf','excel','csv'],
  _dtButtonClass: 'btn btn-outline dark',
  _dtSaveState: false,
  _dtResponsive: true,
  _dtPageLength: 10,
  _dtMenuLength: [[5,10,15,20,-1],[5,10,15,20,"All"]],
  _dtInitialize: function(){
    $('.framework-datatable').each(function(){
      Simplicity._dtConstruct(this);
    });
  },
  _dtConstruct: function(table){
    this._dtCollection[table.id] = $(table).dataTable({
      stateSave: Simplicity._dtSaveState,
      "language":{
        "aria":{
          "sortAscending": ": activate to sort column ascending",
          "sortDescending": ": activate to sort column descending"
        },
        "emptyTable": "No data available in table",
        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
        "infoEmpty": "No entries found",
        "infoFiltered": "(filtered1 from _MAX_ total entries)",
        "lengthMenu": "_MENU_ &nbsp;&nbsp;entries",
        "search": "Search:&nbsp;&nbsp;",
        "zeroRecords": "No matching records found"
      },
      processing: true,
      serverSide: true,
      ajax:{
        url:"/Datatable/"+table.id,
        type:"POST"
      },
      pagingType: "full_numbers",
      buttons: Simplicity._dtSetTools(table),
      responsive: Simplicity._dtResponsive,
      columns: Simplicity._dtSetColumns(table),
      lengthMenu: Simplicity._dtGetMenuLength(table),
      pageLength: Simplicity._dtGetPageLength(table),
      filters: Simplicity._dtGetFilters(table),

      "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
      drawCallback: function(settings){

      }
    });
    //grid.getTableWrapper().on('click', '.table-group-action-submit', function(e){
    //this._dtCollection[table.id] = dataTable.DataTable();
  },
  _dtSetTools: function(table){
    if($(table).attr('data-datatable-tools')===void 0){
      return false;
    }
    var index = 0;
    var toolIndex = 0;
    var toolStr = $(table).attr('data-datatable-tools');
    var tools = toolStr.replace(/ /g,'').split(',');
    var toolsArray = [];
    do{
      if(this._dtTools.indexOf(tools[index])> -1){
        toolsArray[toolIndex] ={
          extend: tools[index],
          class: this._dtButtonClass
        };
        toolIndex++;
      }
      index++;
    } while(index<=tools.length);
    return toolsArray;
  },
  _dtSetColumns: function(table){
    var index = 0;
    var columns = [];
    $(table).find('th').each(function(){
      columns[index] = {};
      columns[index].orderable = (($(this).attr('data-datatable-order'))?true:false);
      index++;
    });
    return columns;
  },
  _dtGetMenuLength: function(table){
    if($(table).attr('data-datatable-menu-length')===void 0){
      return this._dtMenuLength;
    }
    var value = $(table).attr('data-datatable-menu-length');
    return JSON.parse(value);
  },
  _dtGetPageLength: function(table){
    if($(table).attr('data-datatable-page-length')===void 0){
      return this._dtPageLength;
    }
    return $(table).attr('data-datatable-page-length');
  },
  _dtGetFilters: function(table){
    var filterId = "#"+table.id+"-filters";
    if($(filterId).length){
      $(filterId).find('td').each(function(){
        if($(this).html().length!=0){
          console.log('active filter');
        }
      });
    }
  }
};
/** Call the Simplicity initialize when the document is ready */
$(document).ready(function(){
  window.Simplicity.initialize();
});
