window.et={
    author: 'Etng',
    version: '1.0',
    update: '2011/12/12',
    config: {
        log: false
    },
    ns: function(str, func)
    {
        var f = str.split('.'), m = window;
        while (f.length > 0) {
            var node = f.shift();
            if (!m[node]) {
                m[node] = {};
            }
            m = m[node];
        }
        func();
    }
}

Object.extend = function(tObj, sObj) {
    for (var o in sObj) {
        tObj[o] = sObj[o];
    }
    return tObj;
};
Object.extend(String.prototype, {
    trim: function() {
        return this.replace(/^\s+|\s+$/, '');
    }
});
Object.extend(Array.prototype, {
    _each: function(iterator, collect) {
        var r = [];
        try {
            for (var i = 0, il = this.length; i < il; i++) {
                var v = iterator(this[i], i);
                if (collect && typeof(v) != 'undefined')
                    r.push(v);
            }
        }
        catch (e) {}
        return r;
    },
    collect: function(iterator) {
        return this._each(iterator, true);
    },
    each: function(iterator) {
        this._each(iterator, false);
        return this;
    },
    include: function(value) {
        return this.index(value) != -1;
    },
    index: function(value) {
        for (var i = 0, il = this.length; i < il; i++) {
            if (this[i] == value)
                return i;
        }
        return - 1;
    },
    unique: function() {
        for (var i = 0; i < this.length; i++) {
            var it = this[i];
            for (var j = this.length - 1; j > i; j--) {
                if (this[j] == it)
                    this.splice(j, 1);
            }
        }
        return this;
    },
    del: function(obj) {
        var index = this.index(obj);
        if (index >= 0 && index < this.length) {
            this.splice(index, 1);
        }
        return this;
    }
});
var Class = {
    create: function() {
        var c = function() {
            this.initialize.apply(this, arguments);
        }
        for (var i = 0, il = arguments.length, it; i < il; i++) {
            it = arguments[i];
            if (it == null)
                continue;
            Object.extend(c.prototype, it);
        }
        return c;
    }
};
function customEvent(obj) {
    obj._e_ = {};
    obj.on = function(type, func) {
        if (!obj._e_[type]) {
            obj._e_[type] = [];
        }
        this._e_[type].push(func);
    }
    obj.fire = function(type, args) {
        var events = obj._e_[type], arg = [];
        if (!events) {
            return;
        }
        for (var i = 1, l = arguments.length; i < l; i++) {
            arg.push(arguments[i]);
        }
        for (var i = 0; i < l; i++) {
            var lt = events[i];
            if (lt)
                lt.apply(null, arg);
        }
    }
    obj.un = function(type, func) {
        var events = obj._e_[type], l = events.length;
        for (var i = 0; i < l; i++) {
            if (events[i] == func) {
                obj.event[type] = events.splice(i, 1);
            }
        }
    }
};
Object.extend(Function.prototype, {
    bind: function() {
        var method = this, _this = arguments[0], args = [];
        for (var i = 1, il = arguments.length; i < il; i++) {
            args.push(arguments[i]);
        }
        return function() {
            var thisArgs = args.concat();
            for (var i = 0, il = arguments.length; i < il; i++) {
                thisArgs.push(arguments[i]);
            }
            return method.apply(_this, thisArgs);
        };
    },
    bindEvent: function() {
        var method = this, _this = arguments[0], args = [];
        for (var i = 1, il = arguments.length; i < il; i++) {
            args.push(arguments[i]);
        }
        return function(e) {
            var thisArgs = args.concat();
            thisArgs.unshift(e || window.event);
            return method.apply(_this, thisArgs);
        };
    }
});

et.ns('et.log', function(){
    et.log = function(log) {
        if(!et.config.log)return false;
        if (typeof console != 'undefined' && typeof console.log == 'function'){
            console.log.apply(console, arguments);
        }
    };
});

et.ns("et.cookie", function() {
    et.cookie = {
        get: function(name) {
            var tmp, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)", "gi");
            if (tmp = reg.exec(unescape(document.cookie)))
                return(tmp[2]);
            return null;
        },
        set: function(name, value, expires, path, domain) {
            var str = name + "=" + escape(value);
            if (expires) {
                if (expires == 'never') {
                    expires = 100 * 365 * 24 * 60;
                }
                var exp = new Date();
                exp.setTime(exp.getTime() + expires * 60 * 1000);
                str += "; expires=" + exp.toGMTString();
            }
            if (path) {
                str += "; path=" + path;
            }
            if (domain) {
                str += "; domain=" + domain;
            }
            document.cookie = str;
        },
        del: function(name, path, domain) {
            document.cookie = name + "=" +
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
        }
    };
});

et.ns('et.utils', function(){
    et.utils = {
        bookmark: function(url, title, fallback_dom) {
            if (window.sidebar) {
                window.sidebar.addPanel(title, url, "");
            }
            else if (document.all) {
                window.external.AddFavorite(url, title);
            }
            else {
                if(fallback_dom)
                {
                    $(fallback_dom).html('把<a href="' + url + '" title="请把此链接拖动到书签栏收藏">' + title + '</a>放入收藏夹');
                }
            }
        }
    };
});


et.ns("et.tool.CountDown", function() {
    et.tool.CountDown = Class.create({
        initialize: function(options) {
            var time = new Date().getTime();
            this.opt = Object.extend({
                delay: 60000,
                startTime: time,
                endTime: time + 1000 * 60 * 60 * 48,
                onCount: this.onCount,
                onCountDone: function() {}
            },
            options);
            this.currentTime = this.opt.startTime;
        },
        start: function() {
            this.opt.onCount(this._getTimeInfo(this.currentTime));
            this.iTimer = setInterval(function() {
                var mtime = Math.floor((this.opt.endTime - this.currentTime) / 1000);
                if (mtime <= 0) {
                    this.end();
                    return;
                }
                this.currentTime += this.opt.delay;
                this.opt.onCount(this._getTimeInfo(this.currentTime));
            }
            .bind(this), this.opt.delay);
            return this;
        },
        end: function() {
            clearTimeout(this.iTimer);
            this.opt.onCountDone();
            return this;
        },
        onCount: function(time) {
        },
        _getTimeInfo: function(time) {
            var ltime = Math.floor((this.opt.endTime - this.currentTime) / 1000);
            return {
                second: ltime % 60,
                minute: Math.floor(ltime / 60) % 60,
                hour: Math.floor(ltime / 3600) % 24,
                day: Math.floor(ltime / (60 * 60 * 24))
            }
        }
    });
    et.tool.CountDown.init = function(wrapper_selector, display_selector, opt) {
        $(wrapper_selector).each(function() {
            var lt = $(this);
            var counter = $(display_selector, lt);
            if (!counter) {
                return;
            }
            options = {
                delay: opt.delay * 1000,
                startTime: opt.startTime * 1000,
                endTime: counter.attr('data-end') * 1000,
                onCount: function(counter, time) {
                    var html = [
                        time.day > 0 ? '<em class="day">' + time.day + '</em>天' : '', 
                        '<em class="hour">' + time.hour + '</em>小时', 
                        '<em class="minute">' + time.minute + '</em>分',
                        '<em class="second">' + time.second + '</em>秒'
                        ];
                    counter.html(html.join(''));
                }
                .bind(null, counter), onCountDone: function(lt, counter) {
                    lt.remove();
                }
                .bind(null, counter);
            };
            if (options.startTime >= options.endTime) {
                lt.remove();
            }
            else {
                new et.tool.CountDown(options).start();
            }
            counter.show();
        });
    };
});


(function($){
    /*class=<textarea "auto_height" data-max-height="200px"></textarea>*/
    $.fn.autoTextarea = function(options) {
        var defaults={
            maxHeight:null,//文本框是否自动撑高，默认：null，不自动撑高；如果自动撑高必须输入数值，该值作为文本框自动撑高的最大高度
            minHeight:$(this).height() //默认最小高度，也就是文本框最初的高度，当内容高度小于这个高度的时候，文本以这个高度显示
        };
        var opts = $.extend({},defaults,options);
        return $(this).each(function() {
            $(this).bind("paste cut keydown keyup focus blur",function(){
                var height,style=this.style;
                var maxHeight = parseInt(opts.maxHeight?opts.maxHeight:$(this).data('max-height'), 10);
                this.style.height =  opts.minHeight + 'px';
                if (this.scrollHeight > opts.minHeight) {
                    if (opts.maxHeight && this.scrollHeight > opts.maxHeight) {
                        height = opts.maxHeight;
                        style.overflowY = 'scroll';
                    } else {
                        height = this.scrollHeight;
                        style.overflowY = 'hidden';
                    }
                    style.height = height  + 'px';
                }
            });
        });
    };
    $.fn.selection = function(){
        var s,e,range,stored_range;
        if(this[0].selectionStart == undefined){
            var selection=document.selection;
            if (this[0].tagName.toLowerCase() != "textarea") {
                var val = this.val();
                range = selection.createRange().duplicate();
                range.moveEnd("character", val.length);
                s = (range.text == "" ? val.length:val.lastIndexOf(range.text));
                range = selection.createRange().duplicate();
                range.moveStart("character", -val.length);
                e = range.text.length;
            }else {
                range = selection.createRange(),
                stored_range = range.duplicate();
                stored_range.moveToElementText(this[0]);
                stored_range.setEndPoint('EndToEnd', range);
                s = stored_range.text.length - range.text.length;
                e = s + range.text.length;
            }
        }else{
            s=this[0].selectionStart,
            e=this[0].selectionEnd;
        }
        var te=this[0].value.substring(s,e);
        return {begin:s,end:e,content:te}
    };
    $('textarea.auto_height').autoTextarea();
})(jQuery);


//et.ns("et.ui.MsgBox", function() {
//    et.ui.MsgBox = {
//    };
//});