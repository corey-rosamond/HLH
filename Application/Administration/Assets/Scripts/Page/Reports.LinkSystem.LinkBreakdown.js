LinkBreakdown = {
  _saveButtonId: "#save-options",
  _saveButton: null,
  _funnelSelectId: "#funnelKey",
  _emailLinkId: "#emailLink",
  _funnelSelect: null,
  _emailLinkSelect: null,
  _mediaBuyLinks: [],
  _activeFunnel: null,
  _activeLink: null,
  initialize: function(){
    LinkBreakdown._saveButton = $(LinkBreakdown._saveButtonId);
    LinkBreakdown._funnelSelect = $(LinkBreakdown._funnelSelectId);
    LinkBreakdown._emailLinkSelect = $(LinkBreakdown._emailLinkId);
    LinkBreakdown._eventInitialize();
  },
  /** DOM EVENTS */
  _populateLinkSelect: function(linkData){
    $(this._emailLinkSelect).html("");
    for(var key in linkData){
      $(this._emailLinkSelect).append("<option value='"+linkData[key].key+"'>"+linkData[key].name+" ("+linkData[key].key+")</option>")
    }
  },
  _generateReport: function(){
    var object = document.getElementById('Reports.LinkSystem.LinkBreakdown.Configuration');
    $(object).modal('hide');
    var DataTable = Simplicity._dtCollection['LinkBreakdown'].dataTable();
    DataTable.dataTableSettings[0].ajax.data['funnel-key'] = LinkBreakdown._activeFunnel;
    DataTable.dataTableSettings[0].ajax.data['link-key'] = LinkBreakdown._activeLink;
    DataTable._fnAjaxUpdate();
  },
  /** SETTERS */
  _updateSelectedLink: function(){
    LinkBreakdown._activeLink = $(LinkBreakdown._emailLinkSelect).val();
  },
  /** GETTERS */
  _getPromoterSelect: function(event){
    var target = event.target;
    LinkBreakdown._activeFunnel = $(target).val();
    if(LinkBreakdown._mediaBuyLinks[LinkBreakdown._activeFunnel]===void 0) {
      $.post("/Command/MediaBuyEmail",{action: "funnel-links","funnelKey": LinkBreakdown._activeFunnel},
        function (data){
          LinkBreakdown._mediaBuyLinks[LinkBreakdown._activeFunnel] = data.message;
          LinkBreakdown._populateLinkSelect(LinkBreakdown._mediaBuyLinks[LinkBreakdown._activeFunnel]);
        }, "json");
    } else {
      LinkBreakdown._populateLinkSelect(LinkBreakdown._mediaBuyLinks[LinkBreakdown._activeFunnel]);
    }
  },
  /** EVENTS */
  _eventInitialize: function(){
    $(this._funnelSelect).on("change",LinkBreakdown._getPromoterSelect);
    $(this._emailLinkSelect).on("change",LinkBreakdown._updateSelectedLink);
    $(this._saveButton).on("click",LinkBreakdown._generateReport);
  }
}
Simplicity.onLoad(LinkBreakdown.initialize);