/**handles:admin-bar**/
/*! This file is auto-generated */
!function(d,f,m){function p(e){var t;27===e.which&&(t=A(e.target,".menupop"))&&(t.querySelector(".menupop > .ab-item").focus(),w(t,"hover"))}function h(e){var t;13===e.which&&(A(e.target,".ab-sub-wrapper")||(t=A(e.target,".menupop"))&&(e.preventDefault(),E(t,"hover")?w(t,"hover"):L(t,"hover")))}function v(e){var t;13===e.which&&(t=e.target.getAttribute("href"),-1<m.userAgent.toLowerCase().indexOf("applewebkit")&&t&&"#"===t.charAt(0)&&setTimeout(function(){var e=d.getElementById(t.replace("#",""));e&&(e.setAttribute("tabIndex","0"),e.focus())},100))}function g(e,t){var n;A(t.target,".ab-sub-wrapper")||(t.preventDefault(),(n=A(t.target,".menupop"))&&(E(n,"hover")?w(n,"hover"):(S(e),L(n,"hover"))))}function b(e){var t,n=e.target.parentNode;if(n&&(t=n.querySelector(".shortlink-input")),t)return e.preventDefault&&e.preventDefault(),e.returnValue=!1,L(n,"selected"),t.focus(),t.select(),!(t.onblur=function(){w(n,"selected")})}function y(){if("sessionStorage"in f)try{for(var e in sessionStorage)-1<e.indexOf("wp-autosave-")&&sessionStorage.removeItem(e)}catch(e){}}function E(e,t){return!!e&&(e.classList&&e.classList.contains?e.classList.contains(t):!!e.className&&-1<e.className.split(" ").indexOf(t))}function L(e,t){e&&(e.classList&&e.classList.add?e.classList.add(t):E(e,t)||(e.className&&(e.className+=" "),e.className+=t))}function w(e,t){var n,r;if(e&&E(e,t))if(e.classList&&e.classList.remove)e.classList.remove(t);else{for(n=" "+t+" ",r=" "+e.className+" ";-1<r.indexOf(n);)r=r.replace(n,"");e.className=r.replace(/^[\s]+|[\s]+$/g,"")}}function S(e){if(e&&e.length)for(var t=0;t<e.length;t++)w(e[t],"hover")}function k(e){if(!e.target||"wpadminbar"===e.target.id||"wp-admin-bar-top-secondary"===e.target.id)try{f.scrollTo({top:-32,left:0,behavior:"smooth"})}catch(e){f.scrollTo(0,-32)}}function A(e,t){for(f.Element.prototype.matches||(f.Element.prototype.matches=f.Element.prototype.matchesSelector||f.Element.prototype.mozMatchesSelector||f.Element.prototype.msMatchesSelector||f.Element.prototype.oMatchesSelector||f.Element.prototype.webkitMatchesSelector||function(e){for(var t=(this.document||this.ownerDocument).querySelectorAll(e),n=t.length;0<=--n&&t.item(n)!==this;);return-1<n});e&&e!==d;e=e.parentNode)if(e.matches(t))return e;return null}d.addEventListener("DOMContentLoaded",function(){var n,e,t,r,o,a,s,i,c,l,u=d.getElementById("wpadminbar");if(u&&"querySelectorAll"in u){n=u.querySelectorAll("li.menupop"),e=u.querySelectorAll(".ab-item"),t=d.getElementById("wp-admin-bar-logout"),r=d.getElementById("adminbarsearch"),o=d.getElementById("wp-admin-bar-get-shortlink"),a=u.querySelector(".screen-reader-shortcut"),s=/Mobile\/.+Safari/.test(m.userAgent)?"touchstart":"click",i=/Android (1.0|1.1|1.5|1.6|2.0|2.1)|Nokia|Opera Mini|w(eb)?OSBrowser|webOS|UCWEB|Windows Phone OS 7|XBLWP7|ZuneWP7|MSIE 7/,w(u,"nojs"),"ontouchstart"in f&&(d.body.addEventListener(s,function(e){A(e.target,"li.menupop")||S(n)}),u.addEventListener("touchstart",function e(){for(var t=0;t<n.length;t++)n[t].addEventListener("click",g.bind(null,n));u.removeEventListener("touchstart",e)})),u.addEventListener("click",k);for(l=0;l<n.length;l++)f.hoverintent(n[l],L.bind(null,n[l],"hover"),w.bind(null,n[l],"hover")).options({timeout:180}),n[l].addEventListener("keydown",h);for(l=0;l<e.length;l++)e[l].addEventListener("keydown",p);r&&((c=d.getElementById("adminbar-search")).addEventListener("focus",function(){L(r,"adminbar-focused")}),c.addEventListener("blur",function(){w(r,"adminbar-focused")})),a&&a.addEventListener("keydown",v),o&&o.addEventListener("click",b),f.location.hash&&f.scrollBy(0,-32),m.userAgent&&i.test(m.userAgent)&&!E(d.body,"no-font-face")&&L(d.body,"no-font-face"),t&&t.addEventListener("click",y)}})}(document,window,navigator);