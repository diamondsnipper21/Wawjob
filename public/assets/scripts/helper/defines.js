/**
 * defines.js
 *
 * Global utility functinos used both in Admin & Frontend
 */

function getObjectSize(obj) {
  if (typeof obj == "undefined") {
    return 0;
  }

  if (typeof Object.keys == "function") {
    var keys = Object.keys(obj);
    if (!keys) {
      return 0;
    }

    return keys.length;
  }

  var nCount = 0;
  for(i in obj) {
    if (obj.hasOwnProperty(i)) {
      nCount++;
    }
  }

  return nCount;
}

function getQuery(url) {
  var qs = url.substring(url.indexOf('?') + 1).split('&');
  for(var i = 0, result = {}; i < qs.length; i++){
      qs[i] = qs[i].split('=');
      result[qs[i][0]] = decodeURIComponent(qs[i][1]);
  }
  
  return result;
};

function nl2br(str) {
  return str.replace(/\n/g, '<br>');
}

function br2nl(str) {
  return str.replace(/<br\s?\/?>/g, "\n");
}
/*
$.fn.loading = function() {
  return this.each(function() {  
    $(this).load(function() {
      console.log("loaded");
      $(this).unwrap();
    });
    if ( !$(this).parent().hasClass("i-loading") ) {
      $(this).wrap('<div class="i-loading" />');
    }
  });

};*/

