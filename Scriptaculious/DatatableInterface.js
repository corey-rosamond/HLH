/** Create the framework namespace if it doesent exist */
var Framework = Framework || {};
/**
 * The DatatableInterface object is used to simplify
 * working with datatables
 *
 * @type Datatable The datatable interface
 */
Framework.Datatable = function( paramaters ){
  /**
   * The id of the datatable if possible
   * @type {int}
   */
  this.id           = null;
  /**
   * A reference to the datatable object you have to have this
   * @type {object}
   */
  this.datatable    = null;
  /**
   * An array of columns in this datatable
   * @type {Array}
   */
  this.columns      = [];
  /**
   * The tools you are wanting supported by this datatable
   * @type {Array}
   */
  this.tools        = [];
  /**
   * loadLocation if this is not false the data table needs to be loaded with ajax
   * @type {Boolean|string}   This will be the url ajax should go to if not false
   */
  this.loadLocation = false;
  /**
   * The ajax method to use when getting the data
   * @type {string}
   */
  this.loadMethod   = 'post';
  /**
   * The data members to use when submitting an ajax query to get the datatable contents
   * @type {Object}
   */
  this.loadData     = {};
  /**
   * Do we want this datatable to save state
   * @type {Boolean}
   */
  this.saveState    = false;
  /**
   * Do you want the datatable to be responsive
   * @type {Boolean}
   */
  this.responsive   = true;
  /**
   * The number of records to display per page
   * @type {number}
   */
  this.pageLength   = 10;
  /**
   * The page lenght dropdown options
   * @type {Array}
   */
  this.menuLength   = [ [5, 10, 15, 20, -1], [5, 10, 15, 20, "All"] ];
  /**
   * The button class you would like to use when creating the tool buttons
   * @type {string}
   */
  this.buttonClass  = 'btn blue border-blue-hoki bold uppercase';
  /**
   * Setup the datatable and support for it
   * @method initialize
   * @param  {object} paramatersaters An object with all of the paramaters needed to make this object
   * @return {Framework.Datatable}    This object
   */
  this.initalize = function( paramaters ){
    /** If we cant get the datatable object we are done fail */
    if( !this.setDatatable( paramaters ) ){ return false; }
    /** If we have a load location use it */
    if( paramaters.loadLocation !== void 0 ){
      this.loadLocation = paramaters.loadLocation;
    }
    /** If loadMethod was passed override the default */
    if( paramaters.loadMethod !== void 0 ){
      this.loadMethod = paramaters.loadMethod;
    }
    /** If loadData was passed override the default */
    if( paramaters.loadData !== void 0 ){
      this.loadData = paramaters.loadData;
    }
    /** If saveState was passed override the default */
    if( paramaters.saveState !== void 0 ){
      this.saveState = paramaters.saveState;
    }
    /** If responsive was passed override the default */
    if( paramaters.responsive !== void 0 ){
      this.responsive = paramaters.responsive;
    }
    /** If pageLength was passed override the default */
    if( paramaters.pageLength !== void 0 ){
      this.pageLength = paramaters.pageLength;
    }
    /** If menuLength was passed override the default */
    if( paramaters.menuLength !== void 0 ){
      this.menuLength = paramaters.menuLength;
    }
    /** If buttonClass was passed override the default */
    if( paramaters.buttonClass !== void 0 ){
      this.buttonClass = paramaters.buttonClass;
    }
    /** We have a datatable lets setup the columns */
    this.setColumns();
    /** Set the tools we want to include for this datatable */
    this.setTools();
    /** Make the datatable */
    this.datatable = this.datatable.dataTable({
      stateSave:        this.saveState,
      "language": {
        "aria":         { "sortAscending": ": activate to sort column ascending", "sortDescending": ": activate to sort column descending" },
        "emptyTable":   "No data available in table",
        "info":         "Showing _START_ to _END_ of _TOTAL_ entries",
        "infoEmpty":    "No entries found",
        "infoFiltered": "(filtered1 from _MAX_ total entries)",
        "lengthMenu":   "_MENU_ &nbsp;&nbsp;entries",
        "search":       "Search:&nbsp;&nbsp;",
        "zeroRecords":  "No matching records found"
      },
      buttons:          this.tools,
      responsive:       this.responsive,
      columns:          this.columns,
      lengthMenu:       this.menuLength,
      pageLength:       this.pageLength,
      "dom":            "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
      drawCallback:     function( settings ){
        /** I want to add a variable callback here */
      }
    });
    /** now that we loaded the datatable update our variable */
    this.datatable = this.datatable.DataTable();
    /** if we have a load location use it */
    if( this.loadLocation ){ this.loadDatatable(); }
    /** All done */
    return this;
  }

  /**
   * Go and get the data for this datatable
   * @method loadDatatable
   * @return boolean Were we able to load the data
   */
  this.loadDatatable = function(){
    /** Use the ajax method to go get the datatable rows */
    $.ajax({
      method:   this.loadMethod,
      url:      this.loadLocation,
      data:     this.loadData,
      dataType: 'json',
    /** We have the data now we should load it into the datatable */
    }).done( function( data ){
      this.addBatch( data );
      return true;
    /** We failed to load the data tell them */
    }).fail( function( data ){
      console.log( "Failed to load row data! " );
      return false;
    });
    /** If we are here we failed and the connection hung fail */
    return false;
  }

  /**
   * Set the datatable variable so that we can use it later
   * @method setDatatables
   * @param  object   param The paramaters passed into the datatable initialize function
   * @return boolean        Were we able to set the datatable variable
   */
  this.setDatatable = function( param ){
    /** We dont have an id or an object we are done fail */
    if( param.id === void 0 && param.datatable === void 0 ){
      return false;
    }
    /** If we have a buttonClass use it */
    if( param.buttonClass !== void 0 ){
      this.buttonClass = param.buttonClass;
    }
    /** We have an id or an object use one of them to get the object */
    if( param.id !== void 0 ){
      /** We hvae an id use it to get the object and finish the function */
      this.datatable = $('#'+param.id);
      return true;
    }
    /** Check if we have the the datatable */
    if( param.datatable !== void 0 ){
      /** We have the object set it and finish the function */
      this.datatable = param.datatable;
      return true;
    }
    /** If we still dont have the object we fail */
    return false;
  }

  /**
   * Set the column data up so we can use it later
   * @method setColumns
   * @return boolean Were we able to set the columns
   */
  this.setColumns = function(){
    /** Keep track of where we are */
    var index     = 0;
    /** Make a reference to our datatable object so we can use it */
    var datatable = this;
    /** Loop over the th elements and pull the column definitions out */
    $( this.datatable ).find('th').each( function(){
      /** For each column we want to add an object to the columns array */
      datatable.columns[ index ] = {};
      /** If there is a data attribute saying that it is orderable then mark it */
      datatable.columns[ index ].orderable = ( ($( this ).attr('data-datatable-order') )? true : false );
      /** Move forward */
      index++;
    });
    /** All done end the function */
    return true;
  }

  /**
   * Set the tools for this datatable if we have any
   * @method setTools
   * @return boolean    Did we succeed
   */
  this.setTools = function(){
    /** These are the currently supported tools */
    var possibleTools = ['print','pdf','excel','csv'];
    /** If we don't have a data attribute with a list off tools in it we are done return false */
    if( $(this).attr( 'data-datatable-tools') === void 0){ return false; }
    /** Keep track of where we are */
    var loopIndex, toolIndex = 0;
    /** Get the string that contains the tools we want to load */
    var toolsString = $( table ).attr( 'data-datatable-tools' );
    /** Remove all spaces and bad characters then split the string on the comma */
    var tools       = toolStr.replace(/ /g,'').split(',');
    /** Loop through the tools backwards to gain a bit of speed */
    do{
      /** Check if this tool exists in our possibleTools array */
      if( possibleTools.indexOf( tools[ index ] ) > -1 ){
        /** we have a tool add it to our tools variable */
        this.tools[ toolIndex ] = { extend: tools[index], class: this.buttonClass };
        /** Move forward so we don't overwrite this tool if we get another */
        toolIndex++;
      }
      /** Move forward */
      index++;
    /** While we still have tools keep looping */
    } while( index <= tools.length );
    /** All done */
    return true;
  }

  /**
   * Search through every cell and every row to find a string
   * @method search
   * @param  {search} term  The string we are looking for
   * @return {false|row}    The row our search term is in or false
   */
  this.search = function( term ){
    /** Make a reference to the datatable so we can use it inside the loop */
    var datatable = this.datatable;
    /** Set the row to false unless we find the term */
    var row       = false;
    /** Loop over the rows in the datatable */
    datatable.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
      /** Put the data into a variable for faster access */
      var data    = this.data();
      /** Set the index to the last data position */
      var index   = ( data.length - 1 );
      /** Start looping backwards its  much faster */
      do{
        /** If the cell content equals the search term set row to this.index and return */
        if( data[index] === term ){ return row = this.index(); }
        /** Decrement the counter */
        index--;
      /** Keep looping till return is called or index reaches 0 */
      } while( index );
    });
    /** We are done with the search return */
    return row;
  }

  this.searchColumn = function( index, term ){
    /** Make a reference to the datatable so we can use it inside the loop */
    var datatable = this.datatable;
    /** Set the row to false unless we find the term */
    var row       = false;
    /** Loop over the rows in the datatable */
    datatable.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
      /** Check if the defined cell has the correct value */
      if( this.data()[index] == term ){ return row = this.index(); }
    });
    /** We are done with the search return */
    return row;
  }

  /**
   * Remove a row from the datatable and draw the changes unless otherwise requested
   * @method removeRow
   * @param  int      index       The index of the row we want to remove
   * @param  boolean  draw        Do we want to draw the changes to the browser
   * @return boolean              Did we succeed
   */
  this.removeRow = function( index, draw ){
    /** Check if draw is set thank you chrome */
    draw = draw || true;
    /** Remove the row and draw the changes if requested */
    return this.getRow( index ).remove().draw( draw );
  }

  /**
   * Update all values in a row
   * @method updateRow
   * @param  int      index   The row index
   * @param  array    content The new Content
   * @param  boolean  draw    Are we going to draw the changes to the browser
   * @return boolean          Did we succeed
   */
  this.updateRow = function( index, content, draw ){
    /** Check if draw is set thank you chrome */
    draw = draw || true;
    /** Update all the cell values in this row */
    return this.getRow( index ).data( content ).draw( draw );
  }

  /**
   * Add a row to the datatable optionaly draw the changes
   * @method addRow
   * @param  array      content The row content
   * @param  boolean    draw    Do we want to draw the changes
   * @return boolean            Did we succeed
   */
  this.addRow = function( content, draw ){
    /** Check if draw is set thank you chrome */
    draw = draw || true;
    /** Add the row to the datatable and draw the changes if requested */
    return this.datatable.row.add( content ).draw( draw );
  }

  /**
   * Batch add row method
   * @method function
   * @param  array    Data An array of rows with data to add to the datatable
   * @return boolean  Did we add the rows to the datatable
   */
  this.addBatch = function( data ){
    /** Get the length so we don't have to test it after each loop */
    var length  = data.length;
    /** index is always our current position */
    var index   = 0;
    /** Now start looping over the data eleents */
    do{
      /** Add the row to the table but dont redraw */
      this.addRow( data[ index ], false );
      /** Move forward */
      index++;
    } while( index <= length );
    /** We have added all the new rows now draw the changes */
    this.datatable.draw( true );
    /** All done */
    return true;
  }

  /**
   * updateColumn value
   * @method function
   * @param  int      index   Row index
   * @param  content  column  Columnn index
   * @param  string   content New Content
   * @param  boolean  draw    Are we going to draw the changes to the browser
   * @return boolean          Did we succeed
   */
  this.updateColumn = function( index, column, content, draw ){
    /** Check if draw is set thank you chrome */
    draw = draw || true;
    /** Get the row and colum and replace the content */
    return this.getRow( index ).cell( column ).data( content ).draw( draw );
  }

  /**
   * Get the row at the requested index
   * @method getRow
   * @param  int    index The index of the row we want to return
   * @return array        The row at the requested position
   */
  this.getRow = function( index ){
    /** Return the requested row */
    return this.datatable.row( index );
  }

  /**
   * Get a column from a specific row
   * @method getColumn
   * @param  int  index   Index of the row in question
   * @param  int  column  Index of the column in the row
   * @return string       Content that was in the requested row and column
   */
  this.getColumn = function( index, column ){
    /** Give them just the row the requested */
    return this.getRow( index )[ column ];
  }

  /**
   * Call the initialize function with the paramatersaters passed to this object and return its output
   * @method initialize
   * @param  {object}  paramatersaters paramatersaters passed to the object constructor
   * @return {Framework.Datatable} The Framework.Datatable object
   * @access
   */
  return this.initalize( paramaters );
}
