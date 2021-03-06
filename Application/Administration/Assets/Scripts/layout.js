/**
Core script to handle the entire theme and core functions
**/
var Layout = function () {

    var layoutImgPath = 'layouts/layout/img/';

    var layoutCssPath = 'layouts/layout/css/';

    var resBreakpointMd = App.getResponsiveBreakpoint('md');

    //* BEGIN:CORE HANDLERS *//
    // this function handles responsive layout on screen size resize or mobile device rotate.

    // Set proper height for sidebar and content. The content and sidebar height must be synced always.
    var handleSidebarAndContentHeight = function () {
        var content = $('.page-content');
        var sidebar = $('.page-sidebar');
        var body = $('body');
        var height;

        if (body.hasClass("page-footer-fixed") === true && body.hasClass("page-sidebar-fixed") === false) {
            var available_height = App.getViewPort().height - $('.page-footer').outerHeight() - $('.page-header').outerHeight();
            if (content.height() < available_height) {
                content.attr('style', 'min-height:' + available_height + 'px');
            }
        } else {
            if (body.hasClass('page-sidebar-fixed')) {
                height = _calculateFixedSidebarViewportHeight();
                if (body.hasClass('page-footer-fixed') === false) {
                    height = height - $('.page-footer').outerHeight();
                }
            } else {
                var headerHeight = $('.page-header').outerHeight();
                var footerHeight = $('.page-footer').outerHeight();

                if (App.getViewPort().width < resBreakpointMd) {
                    height = App.getViewPort().height - headerHeight - footerHeight;
                } else {
                    height = sidebar.height() + 20;
                }

                if ((height + headerHeight + footerHeight) <= App.getViewPort().height) {
                    height = App.getViewPort().height - headerHeight - footerHeight;
                }
            }
            content.attr('style', 'min-height:' + height + 'px');
        }
    };

    // Handle sidebar menu links
    var handleSidebarMenuActiveLink = function(mode, el) {
        var url = location.hash.toLowerCase();    

        var menu = $('.page-sidebar-menu');

        if (mode === 'click' || mode === 'set') {
            el = $(el);
        } else if (mode === 'match') {
            menu.find("li > a").each(function() {
                var path = $(this).attr("href").toLowerCase();       
                // url match condition         
                if (path.length > 1 && url.substr(1, path.length - 1) == path.substr(1)) {
                    el = $(this);
                    return; 
                }
            });
        }

        if (!el || el.size() == 0) {
            return;
        }

        if (el.attr('href').toLowerCase() === 'javascript:;' || el.attr('href').toLowerCase() === '#') {
            return;
        }        

        var slideSpeed = parseInt(menu.data("slide-speed"));
        var keepExpand = menu.data("keep-expanded");

        // disable active states
        menu.find('li.active').removeClass('active');
        menu.find('li > a > .selected').remove();

        if (menu.hasClass('page-sidebar-menu-hover-submenu') === false) {
            menu.find('li.open').each(function(){
                if ($(this).children('.sub-menu').size() === 0) {
                    $(this).removeClass('open');
                    $(this).find('> a > .arrow.open').removeClass('open');
                }                             
            }); 
        } else {
             menu.find('li.open').removeClass('open');
        }

        el.parents('li').each(function () {
            $(this).addClass('active');
            $(this).find('> a > span.arrow').addClass('open');

            if ($(this).parent('ul.page-sidebar-menu').size() === 1) {
                $(this).find('> a').append('<span class="selected"></span>');
            }
            
            if ($(this).children('ul.sub-menu').size() === 1) {
                $(this).addClass('open');
            }
        });

        if (mode === 'click') {
            if (App.getViewPort().width < resBreakpointMd && $('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                $('.page-header .responsive-toggler').click();
            }
        }
    };

    // Handle sidebar menu
    var handleSidebarMenu = function () {
        // handle sidebar link click
        $('.page-sidebar-menu').on('click', 'li > a.nav-toggle, li > a > span.nav-toggle', function (e) {
            var that = $(this).closest('.nav-item').children('.nav-link');

            if (App.getViewPort().width >= resBreakpointMd && !$('.page-sidebar-menu').attr("data-initialized") && $('body').hasClass('page-sidebar-closed') &&  that.parent('li').parent('.page-sidebar-menu').size() === 1) {
                return;
            }

            var hasSubMenu = that.next().hasClass('sub-menu');

            if (App.getViewPort().width >= resBreakpointMd && that.parents('.page-sidebar-menu-hover-submenu').size() === 1) { // exit of hover sidebar menu
                return;
            }

            if (hasSubMenu === false) {
                if (App.getViewPort().width < resBreakpointMd && $('.page-sidebar').hasClass("in")) { // close the menu on mobile view while laoding a page 
                    $('.page-header .responsive-toggler').click();
                }
                return;
            }

            if (that.next().hasClass('sub-menu always-open')) {
                return;
            }

            var parent =that.parent().parent();
            var the = that;
            var menu = $('.page-sidebar-menu');
            var sub = that.next();

            var autoScroll = menu.data("auto-scroll");
            var slideSpeed = parseInt(menu.data("slide-speed"));
            var keepExpand = menu.data("keep-expanded");
            
            if (!keepExpand) {
                parent.children('li.open').children('a').children('.arrow').removeClass('open');
                parent.children('li.open').children('.sub-menu:not(.always-open)').slideUp(slideSpeed);
                parent.children('li.open').removeClass('open');
            }

            var slideOffeset = -200;

      if(sub.is(":visible")){
        $('.arrow', the).removeClass("open");
        the.parent().removeClass("open");
        sub.slideUp(slideSpeed, function(){
          if(autoScroll === true && $('body').hasClass('page-sidebar-closed') === false){
            if($('body').hasClass('page-sidebar-fixed')){
              menu.slimScroll({
                'scrollTo': (the.position()).top
              });
            } else {
              App.scrollTo(the, slideOffeset);
            }
          }
          handleSidebarAndContentHeight();
        });
      } else if(hasSubMenu){
        $('.arrow', the).addClass("open");
        the.parent().addClass("open");
        sub.slideDown(slideSpeed, function(){
          if(autoScroll === true && $('body').hasClass('page-sidebar-closed') === false){
            if($('body').hasClass('page-sidebar-fixed')){
              menu.slimScroll({ 'scrollTo': (the.position()).top });
            } else {
              App.scrollTo(the, slideOffeset);
            }
          }
          handleSidebarAndContentHeight();
        });
      }
      e.preventDefault();
    });
    if(App.isAngularJsApp()){
      $(".page-sidebar-menu li > a").on("click", function(e){
        if(App.getViewPort().width <resBreakpointMd && $(this).next().hasClass('sub-menu')===false){
          $('.page-header .responsive-toggler').click();
        }
      });
    }
    $('.page-sidebar').on('click', ' li > a.ajaxify', function(e){
      e.preventDefault();
      App.scrollTop();
      var url = $(this).attr("href");
      var menuContainer = $('.page-sidebar ul');
      var pageContent = $('.page-content');
      var pageContentBody = $('.page-content .page-content-body');
      menuContainer.children('li.active').removeClass('active');
      menuContainer.children('arrow.open').removeClass('open');
      $( this ).parents('li').each(function(){
        $( this ).addClass('active');
        $( this ).children('a > span.arrow').addClass('open');
      });
      $(this).parents('li').addClass('active');

      if( App.getViewPort().width < resBreakpointMd && $('.page-sidebar').hasClass("in") ){
        $('.page-header .responsive-toggler').click();
      }

      App.startPageLoading();
      var the = $(this);
      $.ajax({
        type: "GET",
        cache: false,
        url: url,
        dataType: "html",
        success: function (res) {
          if(the.parents('li.open').size() === 0){
            $('.page-sidebar-menu > li.open > a').click();
          }
          App.stopPageLoading();
          pageContentBody.html( res );
          Layout.fixContentHeight();
          App.initAjax();
        },
        error: function(xhr, ajaxOptions, thrownError){
          App.stopPageLoading();
          pageContentBody.html( '<h4>Could not load the requested content.</h4>' );
        }
      });
    });
    $( '.page-content' ).on( 'click', '.ajaxify', function(e){
      e.preventDefault();
      App.scrollTop();
      var url = $( this ).attr( "href" );
      var pageContent = $( '.page-content' );
      var pageContentBody = $( '.page-content .page-content-body' );
      App.startPageLoading();
      if( App.getViewPort().width < resBreakpointMd && $( '.page-sidebar' ).hasClass( "in" )){
        $('.page-header .responsive-toggler').click();
      }
      $.ajax({
          type: "GET",
          cache: false,
          url: url,
          dataType: "html",
          success: function( res ){
            App.stopPageLoading();
            pageContentBody.html( res );
            Layout.fixContentHeight();
            App.initAjax();
          },
          error: function(xhr, ajaxOptions, thrownError) {
            pageContentBody.html('<h4>Could not load the requested content.</h4>');
            App.stopPageLoading();
          }
        });
      });
      $( document ).on( 'click', '.page-header-fixed-mobile .page-header .responsive-toggler', function(){
        App.scrollTop(); 
      });            
      handleFixedSidebarHoverEffect();
    };
    var _calculateFixedSidebarViewportHeight = function () {
      var sidebarHeight = App.getViewPort().height - $('.page-header').outerHeight(true);
      if($('body').hasClass("page-footer-fixed")){
        sidebarHeight = sidebarHeight - $('.page-footer').outerHeight();
      }
      return sidebarHeight;
    };
    var handleFixedSidebar = function(){
      var menu = $('.page-sidebar-menu');
      App.destroySlimScroll( menu );
      if($('.page-sidebar-fixed').size() === 0){
        handleSidebarAndContentHeight();
        return;
      }
      if(App.getViewPort().width>=resBreakpointMd){
        menu.attr( "data-height", _calculateFixedSidebarViewportHeight() );
        App.initSlimScroll( menu );
        handleSidebarAndContentHeight();
      }
    };
    var handleFixedSidebarHoverEffect = function(){
      var body = $('body');
      if( body.hasClass( 'page-sidebar-fixed' )){
        $('.page-sidebar').on('mouseenter', function(){
          if( body.hasClass( 'page-sidebar-closed' ) ){
            $( this ).find('.page-sidebar-menu').removeClass('page-sidebar-menu-closed');
          }
        }).on( 'mouseleave', function(){
          if( body.hasClass( 'page-sidebar-closed' ) ){
            $( this ).find( '.page-sidebar-menu' ).addClass( 'page-sidebar-menu-closed' );
          }
        });
      } 
    };
    var handleSidebarToggler = function(){
      var body = $('body');
      if( $.cookie && $.cookie( 'sidebar_closed' ) === '1' && App.getViewPort().width >= resBreakpointMd ){
        $( 'body' ).addClass( 'page-sidebar-closed' );
        $( '.page-sidebar-menu' ).addClass( 'page-sidebar-menu-closed' );
      }
      $( 'body' ).on( 'click', '.sidebar-toggler', function( e ){
        var sidebar = $( '.page-sidebar' );
        var sidebarMenu = $( '.page-sidebar-menu' );
        if( body.hasClass( "page-sidebar-closed" ) ){
          body.removeClass( "page-sidebar-closed" );
          sidebarMenu.removeClass( "page-sidebar-menu-closed" );
          if( $.cookie ){ 
            $.cookie( 'sidebar_closed', '0' ); 
          }
        } else {
          body.addClass( "page-sidebar-closed" );
          sidebarMenu.addClass( "page-sidebar-menu-closed" );
          if( body.hasClass( "page-sidebar-fixed" ) ){
            sidebarMenu.trigger( "mouseleave" ); 
          }
          if( $.cookie ){ 
            $.cookie( 'sidebar_closed', '1' );  
          }
        }
        ( window ).trigger( 'resize' );
      });
    };
    var handleHorizontalMenu = function() {
      $( '.page-header' ).on( 'click', '.hor-menu a[data-toggle="tab"]', function(e){
        e.preventDefault();
        var nav = $( ".hor-menu .nav" );
        var active_link = nav.find( 'li.current' );
        $( 'li.active', active_link ).removeClass("active");
        $( '.selected', active_link ).remove();
        var new_link = $( this ).parents( 'li' ).last();
        new_link.addClass( "current" );
        new_link.find( "a:first" ).append( '<span class="selected"></span>' );
      });
      $( '[data-hover="megamenu-dropdown"]' ).not( '.hover-initialized' ).each(function(){   
        $( this ).dropdownHover(); 
        $( this ).addClass( 'hover-initialized' ); 
      });
      $( document ).on( 'click', '.mega-menu-dropdown .dropdown-menu', 
        function(e){ e.stopPropagation(); 
      });
    };
    var handleTabs = function(){
      $( 'body' ).on( 'shown.bs.tab', 'a[data-toggle="tab"]', function (){
        handleSidebarAndContentHeight();
      });
    };

    // Handles the go to top button at the footer
    var handleGoTop = function () {
      var offset = 300;
      var duration = 500;
      if( navigator.userAgent.match( /iPhone|iPad|iPod/i ) ){
        $( window ).bind( "touchend touchcancel touchleave", function( e ){
          if( $( this ).scrollTop() > offset ){ $( '.scroll-to-top' ).fadeIn( duration );
          } else {  $( '.scroll-to-top' ).fadeOut( duration ); }
        });
      } else {
        $( window ).scroll( function(){
          if( $( this ).scrollTop() > offset ){ $( '.scroll-to-top' ).fadeIn( duration );
          } else { $( '.scroll-to-top' ).fadeOut( duration ); }
        });
      }
      $( '.scroll-to-top' ).click( function( e ){
        e.preventDefault();
        $( 'html, body' ).animate( { scrollTop: 0 }, duration );
        return false;
      });
    };
    // Hanlde 100% height elements(block, portlet, etc)
    var handle100HeightContent = function () {

        $('.full-height-content').each(function(){
            var target = $(this);
            var height;

            height = App.getViewPort().height -
                $('.page-header').outerHeight(true) -
                $('.page-footer').outerHeight(true) -
                $('.page-title').outerHeight(true) -
                $('.page-bar').outerHeight(true);

            if (target.hasClass('portlet')) {
                var portletBody = target.find('.portlet-body');

                App.destroySlimScroll(portletBody.find('.full-height-content-body')); // destroy slimscroll 
                
                height = height -
                    target.find('.portlet-title').outerHeight(true) -
                    parseInt(target.find('.portlet-body').css('padding-top')) -
                    parseInt(target.find('.portlet-body').css('padding-bottom')) - 5;

                if (App.getViewPort().width >= resBreakpointMd && target.hasClass("full-height-content-scrollable")) {
                    height = height - 35;
                    portletBody.find('.full-height-content-body').css('height', height);
                    App.initSlimScroll(portletBody.find('.full-height-content-body'));
                } else {
                    portletBody.css('min-height', height);
                }
            } else {
               App.destroySlimScroll(target.find('.full-height-content-body')); // destroy slimscroll 

                if (App.getViewPort().width >= resBreakpointMd && target.hasClass("full-height-content-scrollable")) {
                    height = height - 35;
                    target.find('.full-height-content-body').css('height', height);
                    App.initSlimScroll(target.find('.full-height-content-body'));
                } else {
                    target.css('min-height', height);
                }
            }
        });        
    };
    //* END:CORE HANDLERS *//

    return {
        // Main init methods to initialize the layout
        //IMPORTANT!!!: Do not modify the core handlers call order.

        initHeader: function() {
            handleHorizontalMenu(); // handles horizontal menu    
        },

        setSidebarMenuActiveLink: function(mode, el) {
            handleSidebarMenuActiveLink(mode, el);
        },

        initSidebar: function() {
            //layout handlers
            handleFixedSidebar(); // handles fixed sidebar menu
            handleSidebarMenu(); // handles main menu
            handleSidebarToggler(); // handles sidebar hide/show

            if (App.isAngularJsApp()) {      
                handleSidebarMenuActiveLink('match'); // init sidebar active links 
            }

            App.addResizeHandler(handleFixedSidebar); // reinitialize fixed sidebar on window resize
        },

        initContent: function() {
            handle100HeightContent(); // handles 100% height elements(block, portlet, etc)
            handleTabs(); // handle bootstrah tabs

            App.addResizeHandler(handleSidebarAndContentHeight); // recalculate sidebar & content height on window resize
            App.addResizeHandler(handle100HeightContent); // reinitialize content height on window resize 
        },

        initFooter: function() {
            handleGoTop(); //handles scroll to top functionality in the footer
        },

        init: function () {            
            this.initHeader();
            this.initSidebar();
            this.initContent();
            this.initFooter();
        },

        //public function to fix the sidebar and content height accordingly
        fixContentHeight: function () {
            handleSidebarAndContentHeight();
        },

        initFixedSidebarHoverEffect: function() {
            handleFixedSidebarHoverEffect();
        },

        initFixedSidebar: function() {
            handleFixedSidebar();
        },

        getLayoutImgPath: function () {
            return App.getAssetsPath() + layoutImgPath;
        },

        getLayoutCssPath: function () {
            return App.getAssetsPath() + layoutCssPath;
        }
    };

}();

if (App.isAngularJsApp() === false) {
    jQuery(document).ready(function() {    
       Layout.init(); // init metronic core componets
    });
}