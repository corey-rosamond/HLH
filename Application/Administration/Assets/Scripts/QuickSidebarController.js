window.QuickSidebarController = {
  _qbToggleOpenId: "#QuickSidebar-Toggle-Open",
  _qbToggleCloseId: "#QuickSidebar-Toggle-Close",
  _qbWrapperId: "#QuickSidebar-Wrapper",
  _qbToggle: null,
  _qbWrapper: null,
  _qbOpen: false,
  initialize: function (){
    QuickSidebarController._qbToggleOpen = $(QuickSidebarController._qbToggleOpenId);
    QuickSidebarController._qbToggleClose = $(QuickSidebarController._qbToggleCloseId);
    QuickSidebarController._qbWrapper = $(QuickSidebarController._qbWrapperId);
    QuickSidebarController._eInitialize();
  },
  /** QuickSidebar DOM Events */
  _eOpenQuickbar: function(){
    this._qbOpen = true;
    $('body').addClass('page-quick-sidebar-open');
  },
  _eCloseQuickbar: function(){
    this._qbOpen = false;
    $('body').removeClass('page-quick-sidebar-open');
  },
  /** EVENTS */
  _eInitialize: function(){
    $(this._qbToggleOpen).on('click', function(){
     QuickSidebarController._eOpenQuickbar();
    });
    $(this._qbToggleClose).on('click', function(){
      QuickSidebarController._eCloseQuickbar();
    });
  }
}
/** Tell simplicity that you want to run when the page loads */
Simplicity.onLoad(QuickSidebarController.initialize);