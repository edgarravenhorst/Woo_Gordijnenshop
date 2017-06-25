require('./bootstrap');
//var Flickity = require('flickity');

const Menu = require('advanced-menus');

jQuery( document ).ready(function() {

  // var mainslider = new Flickity( 'section.mainslider',{
  //   autoPlay: false,
  // });

  var mainmenu = new Menu({
    options:{
      parent_clickable: true,
      disable_scroll: true
    },
    selectors:{
      toggle_button: false,
      open_button: ".toggle-mainmenu-button .open-button",
      close_button: ".toggle-mainmenu-button .close-button"
    },
    events:{
      swipeLeft: function(menu, e){
        menu.close();
      },
      // swipeRight: function(menu, e){
      //   menu.open();
      // }
    }
  });
});
