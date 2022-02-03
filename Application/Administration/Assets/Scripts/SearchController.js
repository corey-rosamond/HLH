window.SearchController = {
  _needle: null,
  _results: null,
  _searchId: "#search-form",
  _search: null,
  _searchOpen: false,
  initialize: function (){
    SearchController._search = $(SearchController._searchId);
    SearchController._eInitialize();
  },
  _processSearch: function(){
    console.log('search');
  },
  /** SEARCH BOX DOM EVENTS */
  _eOpenSearch: function(){
    this._searchOpen = true;
    $(this._search).addClass("open");
    $(this._search).find('.form-control').focus();
    $(this._search).on('keypress', function(event){
      if(event.which == 13){
        event.preventDefault();
        SearchController._processSearch();
        return false;
      }
    });
    $(this._search).find('.form-control').on('blur', function(){
      SearchController._eCloseSearch();
    });
  },
  _eCloseSearch: function(){
    this._searchOpen = false;
    $(this._search).removeClass("open");
    $(this._search).find('.form-control').unbind("blur");
    $(this._search).unbind("keypress");
  },
  /** EVENTS */
  _eInitialize: function(){
    $(this._search).on('click', function(){
      if(SearchController._searchOpen){ SearchController._eCloseSearch();
      } else { SearchController._eOpenSearch(); }
    });
  }
}
/** Tell simplicity that you want to run when the page loads */
Simplicity.onLoad(SearchController.initialize);