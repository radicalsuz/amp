                var listMenu = new FSMenu('listMenu', true, 'display', 'block', 'none');
                page.winW=function()
                 { with (this) return Math.max(minW, MS?win.document[db].clientWidth:win.innerWidth) };
                page.winH=function()
                 { with (this) return Math.max(minH, MS?win.document[db].clientHeight:win.innerHeight) };
                page.scrollX=function()
                 { with (this) return MS?win.document[db].scrollLeft:win.pageXOffset };
                page.scrollY=function()
                 { with (this) return MS?win.document[db].scrollTop:win.pageYOffset };

                function repositionMenus(mN) { with (this)
                {
                 var menu = this.menus[mN].lyr;

                 // Showing before measuring corrects MSIE bug.
                 menu.sty.display = 'block';
                 // Reset to and/or store original margins.
                 if (!menu._fsm_origML) menu._fsm_origML = menu.ref.currentStyle ?
                  menu.ref.currentStyle.marginLeft : (menu.sty.marginLeft || 'auto');
                 if (!menu._fsm_origMT) menu._fsm_origMT = menu.ref.currentStyle ?
                  menu.ref.currentStyle.marginTop : (menu.sty.marginTop || 'auto');
                 menu.sty.marginLeft = menu._fsm_origML;
                 menu.sty.marginTop = menu._fsm_origMT;

                 // Calculate absolute position within document.
                 var menuX = 0, menuY = 0,
                  menuW = menu.ref.offsetWidth, menuH = menu.ref.offsetHeight,
                  vpL = page.scrollX(), vpR = vpL + page.winW() - 16,
                  vpT = page.scrollY(), vpB = vpT + page.winH() - 16;
                 var tmp = menu.ref;
                 while (tmp)
                 {
                  menuX += tmp.offsetLeft;
                  menuY += tmp.offsetTop;
                  tmp = tmp.offsetParent;
                 }

                 // Compare position to viewport, reposition accordingly.
                 var mgL = 0, mgT = 0;
                 if (menuX + menuW > vpR) mgL = vpR - menuX - menuW;
                 if (menuX + mgL < vpL) mgL = vpL - menuX;
                 if (menuY + menuH > vpB) mgT = vpB - menuY - menuH;
                 if (menuY + mgT < vpT) mgT = vpT - menuY;

                 if (mgL) menu.sty.marginLeft = mgL + 'px';
                 if (mgT) menu.sty.marginTop = mgT + 'px';
                }};

                // Set this to process menu show events for a given object.
                addEvent(listMenu, 'show', repositionMenus, true);

                // Hide all menus when the document is clicked
                addEvent(document, 'click',  function() {
                 listMenu.hideAll();
                });


                // Here's a second method. This only works in IE 5.5+ on Windows, but it doesn't make
                // select boxes appear and disappear (menus cleanly cover them).

                FSMenu.prototype.ieSelBoxFixShow = function(mN) { with (this)
                {
                 var m = menus[mN];
                 if (!isIE || !window.createPopup) return;
                 if (navigator.userAgent.match(/MSIE ([\d\.]+)/) && parseFloat(RegExp.$1) > 6.5)
                  return;
                 // Create a new transparent IFRAME if needed, and insert under the menu.
                 if (!m.ifr)
                 {
                  m.ifr = document.createElement('iframe');
                  m.ifr.src = 'about:blank';
                  with (m.ifr.style)
                  {
                   position = 'absolute';
                   border = 'none';
                   filter = 'progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)';
                  }
                  m.lyr.ref.parentNode.insertBefore(m.ifr, m.lyr.ref);
                 }
                 // Position and show it on each call.
                 with (m.ifr.style)
                 {
                  left = m.lyr.ref.offsetLeft + 'px';
                  top = m.lyr.ref.offsetTop + 'px';
                  width = m.lyr.ref.offsetWidth + 'px';
                  height = m.lyr.ref.offsetHeight + 'px';
                  visibility = 'visible';
                 }
                }};
                FSMenu.prototype.ieSelBoxFixHide = function(mN) { with (this)
                {
                 if (!isIE || !window.createPopup) return;
                 var m = menus[mN];
                 if (m.ifr) m.ifr.style.visibility = 'hidden';
                }};

                addEvent(listMenu, 'show', function(mN) { this.ieSelBoxFixShow(mN) }, 1);
                addEvent(listMenu, 'hide', function(mN) { this.ieSelBoxFixHide(mN) }, 1);

                var arrow = null;
                if (document.createElement && document.documentElement)
                {
                     arrow = document.createElement('span');
                     arrow.appendChild(document.createTextNode('>'));
                     arrow.className = 'subind';
                }
