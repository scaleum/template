(function (window, $) {
    "use strict";

    var uuid = 0;
    var wnd = $(window);

    $.fn.extend({
        uniqueSpinId: function () {
            return this.each(function () {
                if (!this.id) {
                    this.id = "spinbox-" + (++uuid);
                }
            });
        },
        getScreenSize: function () {
            var x, y, xScroll, yScroll, pageWidth, pageHeight, windowWidth, windowHeight;

            if (window.innerHeight && window.scrollMaxY) {
                xScroll = document.body.scrollWidth;
                yScroll = window.innerHeight + window.scrollMaxY;
                x = window.scrollLeft;
                y = window.scrollTop;
            }
            else if (document.body.scrollHeight > document.body.offsetHeight) { // all but Explorer Mac
                xScroll = document.body.scrollWidth;
                yScroll = document.body.scrollHeight;
                x = document.body.scrollLeft;
                y = document.body.scrollTop;
            }
            else if (document.documentElement && document.documentElement.scrollHeight > document.documentElement.offsetHeight) { // Explorer 6 strict mode
                xScroll = document.documentElement.scrollWidth;
                yScroll = document.documentElement.scrollHeight;
                x = document.documentElement.scrollLeft;
                y = document.documentElement.scrollTop;
            }
            else { // Explorer Mac...would also work in Mozilla and Safari
                xScroll = document.body.offsetWidth;
                yScroll = document.body.offsetHeight;
                x = document.body.scrollLeft;
                y = document.body.scrollTop;
            }

            if (self.innerHeight) { // all except Explorer
                windowWidth = self.innerWidth;
                windowHeight = self.innerHeight;
            }
            else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
                windowWidth = document.documentElement.clientWidth;
                windowHeight = document.documentElement.clientHeight;
            }
            else if (document.body) { // other Explorers
                windowWidth = document.body.clientWidth;
                windowHeight = document.body.clientHeight;
            }

            // for small pages with total height less then height of the viewport
            if (yScroll < windowHeight) {
                pageHeight = windowHeight;
            }
            else {
                pageHeight = yScroll;
            }

            // for small pages with total width less then width of the viewport
            if (xScroll < windowWidth) {
                pageWidth = windowWidth;
            }
            else {
                pageWidth = xScroll;
            }

            return {width: windowWidth, height: windowHeight, x: x, y: y, page_width: pageWidth, page_height: pageHeight};
        },

        getViewport: function (el) {
            var size = {
                x: 0,//wnd.scrollLeft(),
                y: 0//wnd.scrollTop()
            };

            el = typeof el !== 'undefined' && el !== null ? el : false;

            if (el) {
                size.width = Math.max(el[0].clientWidth, el[0].offsetWidth, el[0].innerWidth || 0, $(el).width());
                size.height = Math.max(el[0].clientHeight, el[0].offsetHeight, el[0].innerHeight || 0, $(el).height());
            }
            else {
                size = this.getScreenSize();
            }

            return size;
        }
    });

    $.extend({
        spinbox: {
            current: false,
            createModal: function (id) {
                var el;

                el = $('<div/>');
                el.uniqueSpinId();
                el.addClass('spinbox-modal');
                el.attr('data-spinbox', id);
                el.css({position: 'absolute', top: 0, left: 0});
                el.insertBefore($('#' + id));
            },
            createBox: function (tag, className) {
                var el, el_comply;
                el_comply = '<' + tag + '/>';

                el = $(el_comply).uniqueSpinId().addClass(className).html(el_comply);

                return el;
            },
            posBox: function (el) {
                var s_el, s_spin;
                el = typeof el !== 'undefined' ? el : false;

                if (el == false) return;

                s_el = el.getViewport(($(el).parent().is('body') ? null : $(el).parent()));
                s_spin = el.getViewport($(el).find('div:first-child'));
                el.css({
                    position: 'absolute',
                    top: ((s_el.height / 2) + s_el.y) - (s_spin.height / 2),
                    left: ((s_el.width / 2) + s_el.x) - (s_spin.width / 2)
                });
            },

            show: function () {
                var el;
                this.close();

                el = this.createBox('div', 'spinbox-loading').appendTo('body');
                this.posBox(el);
                return this.current = el.attr('id');
            },
            showModal: function () {
                var el;
                this.close();

                el = this.createBox('div', 'spinbox-loading').appendTo('body');
                this.posBox(el);
                this.createModal(el.attr('id'));

                return this.current = el.attr('id');
            },
            showTo: function (target) {
                var el;
                target = typeof target !== 'undefined' && target !== null ? target : 'body';
                el = this.createBox('div', 'spinbox-loading-internal').appendTo(target);

                this.posBox(el);
                return this.current = el.attr('id');
            },
            close: function (id) {
                id = typeof id !== 'undefined' && id !== null ? id : this.current;

                $('.spinbox-modal[data-spinbox="' + id + '"]').remove();
                $('#' + id).remove();
            }
        }
    });
})(window, jQuery);