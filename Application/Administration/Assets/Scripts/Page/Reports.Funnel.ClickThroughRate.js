ClickThroughRate = {
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
    ClickThroughRate._saveButton = $(ClickThroughRate._saveButtonId);
    ClickThroughRate._funnelSelect = $(ClickThroughRate._funnelSelectId);
    ClickThroughRate._emailLinkSelect = $(ClickThroughRate._emailLinkId);
    ClickThroughRate._eventInitialize();
  },
  /** DOM EVENTS */
  _populateLinkSelect: function(linkData){
    $(this._emailLinkSelect).html("");
    for(var key in linkData){
      $(this._emailLinkSelect).append("<option value='"+linkData[key].key+"'>"+linkData[key].name+" ("+linkData[key].key+")</option>")
    }
  },
  _generateReport: function(){
    var object = document.getElementById('Reports.Funnel.ClickThroughRate.Configuration');
    $(object).modal('hide');
    var DataTable = Simplicity._dtCollection['ClickThroughRate'].dataTable();
    DataTable.dataTableSettings[0].ajax.data['funnel-key'] = ClickThroughRate._activeFunnel;
    DataTable.dataTableSettings[0].ajax.data['link-key'] = ClickThroughRate._activeLink;
    DataTable._fnAjaxUpdate();
  },
  /** SETTERS */
  _updateSelectedLink: function(){
    ClickThroughRate._activeLink = $(ClickThroughRate._emailLinkSelect).val();
  },
  /** GETTERS */
  _getPromoterSelect: function(event){
    var target = event.target;
    ClickThroughRate._activeFunnel = $(target).val();
    if(ClickThroughRate._mediaBuyLinks[ClickThroughRate._activeFunnel]===void 0) {
      $.post("/Command/MediaBuyEmail",{action: "funnel-links","funnelKey": ClickThroughRate._activeFunnel},
        function (data){
        ClickThroughRate._mediaBuyLinks[ClickThroughRate._activeFunnel] = data.message;
        ClickThroughRate._populateLinkSelect(ClickThroughRate._mediaBuyLinks[ClickThroughRate._activeFunnel]);
      }, "json");
    } else {
      ClickThroughRate._populateLinkSelect(ClickThroughRate._mediaBuyLinks[ClickThroughRate._activeFunnel]);
    }
  },
  /** EVENTS */
  _eventInitialize: function(){
    $(this._funnelSelect).on("change",ClickThroughRate._getPromoterSelect);
    $(this._emailLinkSelect).on("change",ClickThroughRate._updateSelectedLink);
    $(this._saveButton).on("click",ClickThroughRate._generateReport);
  }
}
Simplicity.onLoad(ClickThroughRate.initialize);