define("ekyna-api/api",["routing","es6-promise","jquery"],function(a,b,c){"use strict";function d(b){return new e(function(d,e){var f=window.localStorage;if(f){var g=f.getItem(b);if(g&&(g=JSON.parse(g),g.expires_at>Math.floor((new Date).getTime()/1e3)))return void d(g.token)}var h=c.ajax({url:a.generate(b),method:"GET"});h.done(function(a){f&&f.setItem(b,JSON.stringify(a)),d(a.token)})})}b.polyfill();var e=b.Promise,f={};return f.init=function(a){d(a).then(function(a){c(document).ajaxSend(function(b,c){c.setRequestHeader("X-Auth-Token",a)})})},f});