window.UserController = {
  initialize: function (){
    UserController._eInitialize();
  },

  /** EVENTS */
  _eInitialize: function() {
    $('#log-out').on('click', function(){
      $.post("/Command/User",{"action":"logout"},function(){
        window.location = "/Main/Dashboard/Dashboard";
      },"json");
    });
    $('#my-profile').click(function(){
      window.location = "/Main/User/MyProfile";
    });
  }
}
/** Tell simplicity that you want to run when the page loads */
Simplicity.onLoad(UserController.initialize);