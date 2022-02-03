(function( window, undefined ) {
  /** @var readyEvents indexed array of events to fire on ready */
  var readyEvents;
  /** @var framework Main reference to the framework */
  var framework       = null;
  /** @var correct document reference */
  var document        = window.document;
  /** @var location correct location reference */
  var location        = window.location;
  /** @var Scriptaculous backup framework reference */
  var Scriptaculous   = window.Scriptaculous;
  /** @var $S Main framework reference */
  var $S              = window.$S;
  /**
   * Main entry point for the framework
   * @method Scriptaculous
   * @param  {object} selector Object trying to use the framework
   * @param  {object} context  Contect the object was called from
   * @return {object}          The framework object
   * @access
   */
  Scriptaculous = function( selector, context ){
    return new Scriptaculous.proto.initialize( selector, context, framework );
  };
  /** Regular Expressions */
  var core_pnum       = /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source;
  var core_rnotwhite  = /\S+/g;
  var rtrim           = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
  var rsingleTag      = /^<(\w+)\s*\/?>(?:<\/\1>|)$/;
  var rvalidchars     = /^[\],:{}\s]*$/;
  var rvalidbraces    = /(?:^|:|,)(?:;s*\[)+/g;
  var rvalidescape    = /\\(?:["\\\/bfnrt]|u[\da-fA-F]{4})/g;
  var rvalidtokens    = /"[^"\\\r\n]*"|true|false|null|-?(?:\d+\.|)\d+(?:[eE][+-]?\d+|)/g;
  var rmsPrefix       = /^-ms-/;
  var rdashAlpha      = /-([\da-z])/gi;
  var rquickExpr      = /^(?:(<[\w\W]+>)[^>]*|#([\w-]*))$/;
  /**
   * Scriptaculous Prototype
   * @type {Scriptaculous.prototype}
   */
  Scriptaculous.proto = Scriptaculous.prototype = {
    selector:     "",
    length:       0,
    constructor:  Scriptaculous,
    /**
     * This is where every framework object starts this makes
     * sure we create everything correctly
     * @method initialize
     * @param  {object|null} selector   The selector $S( selector )
     * @param  {object|null} context    The context for the selector
     * @param  {object|null} framework  A reference to the root framework object
     * @return {Scriptaculous}
     */
    initialize: function( selector, context, framework ){
      /** Handle all versions of not having a selector */
      if( !selector ){ return this; }
      if( Scriptaculous.isType( 'String', selector ) ){
        /** Get the matches **/
        if( Scriptaculous.html.isStringTag( selector ) ){ match = [ null, selector, null ];
  			} else { match = rquickExpr.exec( selector ); }
        /** Check for html or lack of context */
        if ( match && (match[1] || !context) ) {
          if( match[1] ){
            context = (( context instanceof Scriptaculous )? context[0] : context );
            Scriptaculous.merge( this, Scriptaculous.parseHTML( match[1], (( context && context.nodeType ) ? ( context.ownerDocument || context ) : document ), true ) );
            if( rsingleTag.test( match[1] ) && Scriptaculous.isType( 'PlainObject', context ) ){
              for ( match in context ) {
                if ( Scriptaculous.isType( 'Function', this[ match ] ) ){
                  this[ match ]( context[ match ] );
                } else { this.attr( match, context[ match ] ); }
              }
            }
            return this;
          } else {
            var element = document.getElementById( match[2] );
            if( element && element.parentNode ){
              if( element.id !== match[2] ){ return framework.find( selector ); }
              this.length = 1;
              this[0]     = element;
            }
            this.context  = document;
            this.selector = selector;
            return this;
          }
        } else if ( !context || context.Scriptaculous ){ return ( context || framework ).find( selector );
        } else { return this.constructor( context ).find( selector ); }
      } else if ( selector.nodeType ){
        this.context = this[0] = selector;
        this.length = 1;
        return this;
      } else if ( Scriptaculous.isType( 'Function', selector ) ) {
        return framework.ready( selector );
      }
      if ( Scriptaculous.notType( 'Undefined', elector.selector ) ) {
  			this.selector = selector.selector;
  			this.context = selector.context;
  		}
      return Scriptaculous.makeArray( selector, this );
    },
    /** this.size */
    size: function(){
  		return this.length;
  	},
    /** this.toArray */
    toArray: function() {
  		return Scriptaculous.slice.call( this );
  	},
    /** this.get */
    get: function( number ) {
  		return (( number == null ) ? this.toArray() : (( number < 0 ) ? this[ this.length + number ] : this[ number ] ) );
  	},
    /** this.pushStack */
    pushStack: function( elements ) {
  		var ret         = Scriptaculous.merge( this.constructor(), elements );
  		ret.prevObject  = this;
  		ret.context     = this.context;
  		return ret;
  	},
    /** this.slice */
    slice: function() {
  		return this.pushStack( Scriptaculous.slice.apply( this, arguments ) );
  	},
    /** this.first */
    first: function() {
  		return this.eq( 0 );
  	},
    /** this.last */
    last: function() {
  		return this.eq( -1 );
  	},
    /** this.eq */
    eq: function( index ) {
  	   var len = this.length, j = + index + ( index < 0 ? len : 0 );
  		 return this.pushStack( j >= 0 && j < len ? [ this[j] ] : []);
  	},
    /** this.map */
    map: function( callback ){
		  return this.pushStack( Scriptaculous.map( this, function( element, index ){ return callback.call( element, index, element ); } ) );
	  },
    /** this.end */
    end: function(){
      return this.prevObject || this.constructor(null);
    },
    /** this.isPlainObject */
  };

  Scriptaculous.ready.event = function( object ){
    if( !readyEvents ) {
      readyEvents = jQuery.Deferred();
  }


  Scriptaculous: function( wait ) {

		// Abort if there are pending holds or we're already ready
		if ( wait === true ? --jQuery.readyWait : jQuery.isReady ) {
			return;
		}

		// Make sure body exists, at least, in case IE gets a little overzealous (ticket #5443).
		if ( !document.body ) {
			return setTimeout( jQuery.ready );
		}

		// Remember that the DOM is ready
		jQuery.isReady = true;

		// If a normal DOM Ready event fired, decrement, and wait if need be
		if ( wait !== true && --jQuery.readyWait > 0 ) {
			return;
		}

		// If there are functions bound, to execute
		readyList.resolveWith( document, [ jQuery ] );

		// Trigger any bound ready events
		if ( jQuery.fn.trigger ) {
			jQuery( document ).trigger("ready").off("ready");
		}
	},
  },


  /**
   * Merrge two objects
   * @method merge
   * @param  {object} first   Second object
   * @param  {object} second  First object
   * @return {object}         Result of the two items being merged
   */
  Scriptaculous.merge = function( first, second ) {
    /** Setup the most likely start and end */
		var end     = second.length;
    var start   = first.length;
    var index   = 0;
    /** if our end is a number do this */
    if( Scriptaculous.isType( "Number", end ) ){
			for( ; index < end; index++ ){
				first[ start++ ] = second[ index ];
			}
      /** Set length to start and return it */
      return ( first.length = start );
		}
    /** End was not a number use this loop */
		while ( Scriptaculous.notType( 'Undefined', second[ index ] ) ) {
			first[ start++ ] = second[ index++ ];
		}
    /** Set length to start and return it */
		return ( first.length = start );
	},
  /** Add proto to the initialize so we get extend on initialize */
  Scriptaculous.proto.initialize.prototype = Scriptaculous.proto;

  Scriptaculous.extend  = function(){
    var src         = null;
    var copyAsArray = null;
    var copy        = null;
    var name        = null;
    var options     = null;
    var clone       = null;
    var target      = arguments[0] || {};
    var i           = 1;
    var length      = arguments.length;
    var deep        = false;
    if( typeof target === "boolean" ){  deep = target;  target = arguments[1] || {};  i = 2; }
    if( typeof target !== "object" && typeof target !== "function" ){ target = {}; }
    if( length === i ){ target = this;  --i; }
    for( ; i < length; i++ ){
      if( ( options = arguments[i] ) != null ){
        for( name in options ){
          src  = target[ name ];
  			  copy = options[ name ];
  			  if( target === copy ){ continue; }
    			if( deep && copy && ( Scriptaculous.isPlainObject( copy ) || ( copyIsArray = Scriptaculous.isWindow( copy ) ))){
            if( copyIsArray ){
  			      copyIsArray = false;
      				clone = (( src && typeof src === 'array' ) ? src : [] );
      			} else { clone = (( src && Scriptaculous.isPlainObject( src ) ) ? src : {} ); }
            target[ name ] = Scriptaculous.extend( deep, clone, copy );
    			} else if( copy !== undefined ){ target[ name ] = copy; }
  			}
  		}
  	}
    return target;
  };



  Scriptaculous.isPlainObject = function( object ){
    if( !object || typeof object !== "object" || object.nodeType || Scriptaculous.isWindow( object ) ) { return false; }
    try{
      if(
        object.constructor &&
        !Scriptaculous.hasOwn.call( object, "constructor" ) &&
        !Scriptaculous.hasOwn.call( object.constructor.prototype, "isPrototypeOf")
      ){
      return false;
      }
    } catch ( e ){ return false; }
    var key;
    for( key in object ){}
    return key === undefined || Scriptaculous.hasOwn.call( object, key );
  };


  Scriptaculous.isWindow = function( object ) {
    return object != null && object == object.window;
  };

  /** Adding references to core javascript methods */
  Scriptaculous.concat    = Array.prototype.concat;
  Scriptaculous.push      = Array.prototype.push;
  Scriptaculous.slice     = Array.prototype.slice;
  Scriptaculous.indexOf   = Array.prototype.indexOf;
  Scriptaculous.toString  = Object.prototype.toString;
  Scriptaculous.hasOwn    = Object.prototype.hasOwnProperty;
  Scriptaculous.trim      = String.prototype.trim;
  Scriptaculous.sort      = [].sort;
  Scriptaculous.splice    = [].splice;


  Scriptaculous.data = function( options ){
    var store = [];

  };



  Scriptaculous.event = function( options ){


  };

  /**
   * The html object houses all of the html
   * related functions
   * @method html
   */
  Scriptaculous.html = {
    /**
     * Check if there is a tag the string
     * @method isStringTag
     * @param  {String} target String to test
     * @return {boolean}
     * @access
     */
    isStringTag: function( target ){
      (( target === void 0 || target.constructor !== String  ) ? false : (( target.charAt(0) === '<' && target.charAt( target.length - 1 ) === '>'  ) ? true : false ));
    },
    /**
     * Parse an html string
     * @method parse
     * @param  {string}       data        String html we want to parse
     * @param  {object|null}  context     The context to build it in
     * @param  {boolean|null} keepScripts Are we keeping scripts
     * @return {[type]}
     */
    parse: function( data, context, keepScripts ){
      /** If data is false or not a string return null */
  		if( !data || Scriptaculous.notType( "String", data ) ){ return null; }
  		if( Scriptaculous.isType( "boolean", context )){
        /** Context is a boolean so move it to keepScripts and set it to false */
  			keepScripts = context, context = false;
  		}
      /** If context is false set it to document */
  		context = context || document;
      /** Get the tags */
  		var parsed = rsingleTag.exec( data ), scripts = !keepScripts && [];
  		/** Make a single tag and return it */
  		if ( parsed ){ return [ context.createElement( parsed[1] ) ]; }
      console.log( 'Implement Scriptaculous.HTML.parse()');
    }
  };

  /** Make the parseHTML refer to the html objects parse method */
  Scriptaculous.parseHTML = Scriptaculous.html.parse;

  /** I hate this I will rewrite it when I have time */
  Scriptaculous.each = function( element, callback, arguments ){
    var value   = null;
    var index   = 0;
    var length  = element.length;
    var isArray = Scriptaculous.isLike( 'Array', element );
    if( arguments  ){
			if ( isArray ) {
				for ( ; index < length; index ++ ) {
					value = callback.apply( elements[ index ], arguments );
					if ( value === false ) { break; }
				}
			} else {
				for ( index in element ) {
					value = callback.apply( element[ index ], arguments );
					if( value === false ){ break; }
				}
			}
		} else {
			if ( isArray ) {
				for ( ; index < length; index++ ) {
					value = callback.call( element[ index ], index, element[ index ] );
					if ( value === false ) { break; }
				}
			} else {
				for( index in element ){
					value = callback.call( element[ index ], index, element[ index ] );
					if( value === false ){ break; }
				}
			}
		}
		return element;
  };

  /**
   * Check if this element is like another
   * @method isLike
   * @param  {string} type
   * @param  {object} element
   * @return {boolean}
   * @access
   */
  Scriptaculous.isLike = function( type, element ){
    var target = target || this.select;
    switch( type ){
      case 'Array':
        var length = element.length;
        var type   = Scriptaculous.typeOf( element );
        if( Scriptaculous.isType( 'Window', element ) ){ return false; }
        if( element.nodeType === 1 && length ){ return true; }
        return ( type === "array" || type !== "function" ) && ( length === 0 || Scriptaculous.isType( 'Number' , length ) ) && ( length > 0 && ( length - 1 ) in element );
      break;
    }
  };

  /**
   * Use the type object to determine if something is
   * a specific type
   * @method isType
   * @param  {string} type
   * @param  {object} target
   * @return {boolean}
   */
  Scriptaculous.isType = function( type, target ){
    var target = target || this.select;
    return Scriptaculous.type.is( type, target );
  };

  /**
   * Use the type object to determine if something is
   * not a specifc type
   * @method notType
   * @param  {string} type   [description]
   * @param  {object} target [description]
   * @return {boolean}
   */
  Scriptaculous.notType = function( type, target ){
    var target = target || this.select;
    return framework.type.not( type, ( target || this.select ) );
  };

  /**
   * The type object houses all the type collection as well
   * as tests for each of the types
   * @method type
   */
  Scriptaculous.type = {
    /**
     * Test if this is of the given type
     * @method is
     * @param  {string} type    The type to test against
     * @param  {object} target  Object to test against
     * @return {boolean}
     */
    is: function( type, target ){
      return this.test[ type ]( target );
    },

    /**
     * Test that this is not of the given type
     * @method not
     * @param  {string} type   Type to test against
     * @param  {object} target Object to test
     * @return {boolean}
     */
    not: function( type, target ){
      return !this.test[ type ]( target );
    },

    /**
     * Return the result of typeof
     * @method type
     * @param  {object} target  Object to test
     * @return {string}         type of the object
     */
    type: function( target ){
      return typeof target;
    },

    /** @var tests Collection of object specific tests */
    test: {
      /** Use the objects constructor to test the literal */
      'String': function( target ){
        return (( target === void 0 ) ? false : (( target.constructor === String ) ? true: false ));
      },
      /** Use the objects constructor to test the literal */
      'Number': function( target ){
        return (( target === void 0 ) ? false : (( target.constructor === Number ) ? true: false ));
      },
      /** Use the objects constructor to test the literal */
      'Boolean': function( target ){
        return (( target === void 0 ) ? false : (( target.constructor === Boolean ) ? true: false ));
      },
      /** Use the objects constructor to test the literal */
      'Array': function( target ){
        return (( target === void 0 ) ? false : (( target.constructor == Array ) ? true: false ));
      },
      /** Use the objects constructor to test the literal */
      'Object': function( target ){
        return (( target === void 0 ) ? false : (( target.constructor == Object ) ? true: false ));
      },
      /** Compare it to the null literal */
      'Null': function( target ){
        return (( target === void 0 ) ? false : (( target === null ) ? true: false ));
      },
      /** Compare it to void 0 which is the undefined literal */
      'Undefined': function( target ){
        return (( target === void 0 ) ? true: false );
      },
      /** Use typeof to compare it to the string function */
      'Function': function( target ){
        return (( target === void 0 ) ? false : (( typeof target === "function" ) ? true: false ));
      },
      'PlainObject': function( target ){
        return (( Scriptaculous.isPlainObject( target )) ? true: false );
      },
      'Window': function( target ){
        return (( Scriptaculous.isWindow( target )) ? true: false );
      },
      'EmptyObject': function( target ){
        var name;
        for( name in target ){ return false; }
        return true;
      }
    }
  }
  /** Make the typeof function use the type funciton in the type object */
  Scriptaculous.typeOf = Scriptaculous.type.type;
  Scriptaculous.callback = function( options ){
    options = typeof options === "string" ?
		( optionsCache[ options ] || createOptions( options ) ) :
		jQuery.extend( {}, options );
  };







  /** this.each **/
  /*Scriptaculous.each({
  	appendTo:      "append",
  	prependTo:     "prepend",
  	insertBefore:  "before",
  	insertAfter:   "after",
  	replaceAll:    "replaceWith"
  }, function( name, original ){
    Scriptaculous.proto[ name ] = function( selector ) {
  		var elems  = null;
  		var i      = 0;
  		var ret    = [];
  		var insert = Scriptaculous( selector );
  		var last   = insert.length - 1;
  	  for ( ; i <= last; i++ ) {
        elems = i === last ? this : this.clone( true );
  		  Scriptaculous( insert[i] )[ original ]( elems );
  		  core_push.apply( ret, elems.get() );
  	  }
  	  return this.pushStack( ret );
    };
  });*/
  /** this.each */
  /*Scriptaculous.each({
    parent: function( element ){
  		var parent = element.parentNode;
  		return parent && parent.nodeType !== 11 ? parent : null;
  	},
    parents:      function( element ){              return Scriptaculous.dir( element, "parentNode" ); },
    parentsUntil: function( element, i, until ){    return Scriptaculous.dir( element, "parentNode", until ); },
    next:         function( element ){              return sibling( element, "nextSibling" );  },
    prev:         function( element ){              return sibling( element, "previousSibling" ); },
    nextAll:      function( element ){              return Scriptaculous.dir( element, "nextSibling" ); },
  	prevAll:      function( element ){              return Scriptaculous.dir( element, "previousSibling" ); },
  	nextUntil:    function( element, i, until ){    return Scriptaculous.dir( element, "nextSibling", until ); },
  	prevUntil:    function( element, i, until ){    return Scriptaculous.dir( element, "previousSibling", until ); },
  	siblings:     function( element ){              return Scriptaculous.sibling( ( element.parentNode || {} ).firstChild, element ); },
  	children:     function( element ){              return Scriptaculous.sibling( element.firstChild ); },
    contents:     function( element ){
  		return Scriptaculous.nodeName( element, "iframe" ) ? element.contentDocument || element.contentWindow.document : Scriptaculous.merge( [], element.childNodes )
    }, function( name, proto ){
      Scriptaculous.proto[ name ] = function( until, selector ) {
        var ret = Scriptaculous.map( this, proto, until );
  		  if( !runtil.test( name ) ){ selector = until; }
  		  if( selector && Scriptaculous.is( "String", selector) ){ ret = Scriptaculous.filter( selector, ret ); }
  		  ret = this.length > 1 && !guaranteedUnique[ name ] ? Scriptaculous.unique( ret ) : ret;
  	    if ( this.length > 1 && rparentsprev.test( name ) ){  ret = ret.reverse(); }
  		  return this.pushStack( ret );
      };
  }
});*/





  Scriptaculous.event =
  Scriptaculous.proto.event = {
    events:     [],
    isReady:    false,
    readyWait:  1,
    holdReady:  function( hold ){ if( hold ){ Scriptaculous.readyWait++; } else { Scriptaculous.ready( true ); } },
    ready:      function( wait ){
      if( (( wait === true )? --Scriptaculous.readyWait : Scriptaculous.isReady) ){ return; }
      if( !document.body ){   return setTimeout( Scriptaculous.ready ); }
      Scriptaculous.isReady = true;
      readyEvents.resolveWith( document, [ Scriptaculous ] );
      if( Scriptaculous.event.trigger ){ Scriptaculous( document ).trigger("ready").off("ready"); }
    },
    add: function( element, type, handler, data, selector ) {


    },

    remove: function( elem, types, handler, selector, mappedTypes ) {


    },

    trigger: function( event, data, elem, onlyHandlers ) {

    },

    dispatch: function( event ) {


    },

    handler: function( event, handlers ) {

    },

    hooks: {

      mouse: {
        props: "button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),

      },
      click: {

      },
      focus: {

      },
      blur: {

      },
      beforeunload: {

      },
      load: {

      }
    }
  };

  framework = Scriptaculous(document);
  /** Expose the framework */
  window.Scriptaculous = window.$S = Scriptaculous;

})( window );

$S(document).ready( function(){
  //var loggedin = $S('#logged-in')
});
