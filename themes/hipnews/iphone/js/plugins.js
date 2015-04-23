/* ImagesLoaded
 * David Desandro
 */
(function(c,n){var l="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";c.fn.imagesLoaded=function(f){function m(){var b=c(i),a=c(h);d&&(h.length?d.reject(e,b,a):d.resolve(e));c.isFunction(f)&&f.call(g,e,b,a)}function j(b,a){b.src===l||-1!==c.inArray(b,k)||(k.push(b),a?h.push(b):i.push(b),c.data(b,"imagesLoaded",{isBroken:a,src:b.src}),o&&d.notifyWith(c(b),[a,e,c(i),c(h)]),e.length===k.length&&(setTimeout(m),e.unbind(".imagesLoaded")))}var g=this,d=c.isFunction(c.Deferred)?c.Deferred():
0,o=c.isFunction(d.notify),e=g.find("img").add(g.filter("img")),k=[],i=[],h=[];c.isPlainObject(f)&&c.each(f,function(b,a){if("callback"===b)f=a;else if(d)d[b](a)});e.length?e.bind("load.imagesLoaded error.imagesLoaded",function(b){j(b.target,"error"===b.type)}).each(function(b,a){var d=a.src,e=c.data(a,"imagesLoaded");if(e&&e.src===d)j(a,e.isBroken);else if(a.complete&&a.naturalWidth!==n)j(a,0===a.naturalWidth||0===a.naturalHeight);else if(a.readyState||a.complete)a.src=l,a.src=d}):m();return d?d.promise(g):
g}})(jQuery);


/*
 * jQuery FlexSlider v2.1
 * Copyright 2012 WooThemes
 * Contributing Author: Tyler Smith
 */
;  (function(d){d.flexslider=function(i,k){var a=d(i),c=d.extend({},d.flexslider.defaults,k),e=c.namespace,r="ontouchstart"in window||window.DocumentTouch&&document instanceof DocumentTouch,s=r?"touchend":"click",l="vertical"===c.direction,m=c.reverse,h=0<c.itemWidth,q="fade"===c.animation,p=""!==c.asNavFor,f={};d.data(i,"flexslider",a);f={init:function(){a.animating=!1;a.currentSlide=c.startAt;a.animatingTo=a.currentSlide;a.atEnd=0===a.currentSlide||a.currentSlide===a.last;a.containerSelector=c.selector.substr(0,
 c.selector.search(" "));a.slides=d(c.selector,a);a.container=d(a.containerSelector,a);a.count=a.slides.length;a.syncExists=0<d(c.sync).length;"slide"===c.animation&&(c.animation="swing");a.prop=l?"top":"marginLeft";a.args={};a.manualPause=!1;var b=a,g;if(g=!c.video)if(g=!q)if(g=c.useCSS)a:{g=document.createElement("div");var n=["perspectiveProperty","WebkitPerspective","MozPerspective","OPerspective","msPerspective"],e;for(e in n)if(void 0!==g.style[n[e]]){a.pfx=n[e].replace("Perspective","").toLowerCase();
 a.prop="-"+a.pfx+"-transform";g=!0;break a}g=!1}b.transitions=g;""!==c.controlsContainer&&(a.controlsContainer=0<d(c.controlsContainer).length&&d(c.controlsContainer));""!==c.manualControls&&(a.manualControls=0<d(c.manualControls).length&&d(c.manualControls));c.randomize&&(a.slides.sort(function(){return Math.round(Math.random())-0.5}),a.container.empty().append(a.slides));a.doMath();p&&f.asNav.setup();a.setup("init");c.controlNav&&f.controlNav.setup();c.directionNav&&f.directionNav.setup();c.keyboard&&
 (1===d(a.containerSelector).length||c.multipleKeyboard)&&d(document).bind("keyup",function(b){b=b.keyCode;if(!a.animating&&(b===39||b===37)){b=b===39?a.getTarget("next"):b===37?a.getTarget("prev"):false;a.flexAnimate(b,c.pauseOnAction)}});c.mousewheel&&a.bind("mousewheel",function(b,g){b.preventDefault();var d=g<0?a.getTarget("next"):a.getTarget("prev");a.flexAnimate(d,c.pauseOnAction)});c.pausePlay&&f.pausePlay.setup();c.slideshow&&(c.pauseOnHover&&a.hover(function(){!a.manualPlay&&!a.manualPause&&
 a.pause()},function(){!a.manualPause&&!a.manualPlay&&a.play()}),0<c.initDelay?setTimeout(a.play,c.initDelay):a.play());r&&c.touch&&f.touch();(!q||q&&c.smoothHeight)&&d(window).bind("resize focus",f.resize);setTimeout(function(){c.start(a)},200)},asNav:{setup:function(){a.asNav=!0;a.animatingTo=Math.floor(a.currentSlide/a.move);a.currentItem=a.currentSlide;a.slides.removeClass(e+"active-slide").eq(a.currentItem).addClass(e+"active-slide");a.slides.click(function(b){b.preventDefault();var b=d(this),
 g=b.index();!d(c.asNavFor).data("flexslider").animating&&!b.hasClass("active")&&(a.direction=a.currentItem<g?"next":"prev",a.flexAnimate(g,c.pauseOnAction,!1,!0,!0))})}},controlNav:{setup:function(){a.manualControls?f.controlNav.setupManual():f.controlNav.setupPaging()},setupPaging:function(){var b=1,g;a.controlNavScaffold=d('<ol class="'+e+"control-nav "+e+("thumbnails"===c.controlNav?"control-thumbs":"control-paging")+'"></ol>');if(1<a.pagingCount)for(var n=0;n<a.pagingCount;n++)g="thumbnails"===
 c.controlNav?'<img src="'+a.slides.eq(n).attr("data-thumb")+'"/>':"<a>"+b+"</a>",a.controlNavScaffold.append("<li>"+g+"</li>"),b++;a.controlsContainer?d(a.controlsContainer).append(a.controlNavScaffold):a.append(a.controlNavScaffold);f.controlNav.set();f.controlNav.active();a.controlNavScaffold.delegate("a, img",s,function(b){b.preventDefault();var b=d(this),g=a.controlNav.index(b);b.hasClass(e+"active")||(a.direction=g>a.currentSlide?"next":"prev",a.flexAnimate(g,c.pauseOnAction))});r&&a.controlNavScaffold.delegate("a",
 "click touchstart",function(a){a.preventDefault()})},setupManual:function(){a.controlNav=a.manualControls;f.controlNav.active();a.controlNav.live(s,function(b){b.preventDefault();var b=d(this),g=a.controlNav.index(b);b.hasClass(e+"active")||(g>a.currentSlide?a.direction="next":a.direction="prev",a.flexAnimate(g,c.pauseOnAction))});r&&a.controlNav.live("click touchstart",function(a){a.preventDefault()})},set:function(){a.controlNav=d("."+e+"control-nav li "+("thumbnails"===c.controlNav?"img":"a"),
 a.controlsContainer?a.controlsContainer:a)},active:function(){a.controlNav.removeClass(e+"active").eq(a.animatingTo).addClass(e+"active")},update:function(b,c){1<a.pagingCount&&"add"===b?a.controlNavScaffold.append(d("<li><a>"+a.count+"</a></li>")):1===a.pagingCount?a.controlNavScaffold.find("li").remove():a.controlNav.eq(c).closest("li").remove();f.controlNav.set();1<a.pagingCount&&a.pagingCount!==a.controlNav.length?a.update(c,b):f.controlNav.active()}},directionNav:{setup:function(){var b=d('<ul class="'+
 e+'direction-nav"><li><a class="'+e+'prev" href="#">'+c.prevText+'</a></li><li><a class="'+e+'next" href="#">'+c.nextText+"</a></li></ul>");a.controlsContainer?(d(a.controlsContainer).append(b),a.directionNav=d("."+e+"direction-nav li a",a.controlsContainer)):(a.append(b),a.directionNav=d("."+e+"direction-nav li a",a));f.directionNav.update();a.directionNav.bind(s,function(b){b.preventDefault();b=d(this).hasClass(e+"next")?a.getTarget("next"):a.getTarget("prev");a.flexAnimate(b,c.pauseOnAction)});
 r&&a.directionNav.bind("click touchstart",function(a){a.preventDefault()})},update:function(){var b=e+"disabled";1===a.pagingCount?a.directionNav.addClass(b):c.animationLoop?a.directionNav.removeClass(b):0===a.animatingTo?a.directionNav.removeClass(b).filter("."+e+"prev").addClass(b):a.animatingTo===a.last?a.directionNav.removeClass(b).filter("."+e+"next").addClass(b):a.directionNav.removeClass(b)}},pausePlay:{setup:function(){var b=d('<div class="'+e+'pauseplay"><a></a></div>');a.controlsContainer?
 (a.controlsContainer.append(b),a.pausePlay=d("."+e+"pauseplay a",a.controlsContainer)):(a.append(b),a.pausePlay=d("."+e+"pauseplay a",a));f.pausePlay.update(c.slideshow?e+"pause":e+"play");a.pausePlay.bind(s,function(b){b.preventDefault();if(d(this).hasClass(e+"pause")){a.manualPause=true;a.manualPlay=false;a.pause()}else{a.manualPause=false;a.manualPlay=true;a.play()}});r&&a.pausePlay.bind("click touchstart",function(a){a.preventDefault()})},update:function(b){"play"===b?a.pausePlay.removeClass(e+
 "pause").addClass(e+"play").text(c.playText):a.pausePlay.removeClass(e+"play").addClass(e+"pause").text(c.pauseText)}},touch:function(){function b(b){j=l?d-b.touches[0].pageY:d-b.touches[0].pageX;p=l?Math.abs(j)<Math.abs(b.touches[0].pageX-e):Math.abs(j)<Math.abs(b.touches[0].pageY-e);if(!p||500<Number(new Date)-k)b.preventDefault(),!q&&a.transitions&&(c.animationLoop||(j/=0===a.currentSlide&&0>j||a.currentSlide===a.last&&0<j?Math.abs(j)/o+2:1),a.setProps(f+j,"setTouch"))}function g(){if(a.animatingTo===
 a.currentSlide&&!p&&null!==j){var h=m?-j:j,l=0<h?a.getTarget("next"):a.getTarget("prev");a.canAdvance(l)&&(550>Number(new Date)-k&&50<Math.abs(h)||Math.abs(h)>o/2)?a.flexAnimate(l,c.pauseOnAction):a.flexAnimate(a.currentSlide,c.pauseOnAction,!0)}i.removeEventListener("touchmove",b,!1);i.removeEventListener("touchend",g,!1);f=j=e=d=null}var d,e,f,o,j,k,p=!1;i.addEventListener("touchstart",function(j){a.animating?j.preventDefault():1===j.touches.length&&(a.pause(),o=l?a.h:a.w,k=Number(new Date),f=h&&
 m&&a.animatingTo===a.last?0:h&&m?a.limit-(a.itemW+c.itemMargin)*a.move*a.animatingTo:h&&a.currentSlide===a.last?a.limit:h?(a.itemW+c.itemMargin)*a.move*a.currentSlide:m?(a.last-a.currentSlide+a.cloneOffset)*o:(a.currentSlide+a.cloneOffset)*o,d=l?j.touches[0].pageY:j.touches[0].pageX,e=l?j.touches[0].pageX:j.touches[0].pageY,i.addEventListener("touchmove",b,!1),i.addEventListener("touchend",g,!1))},!1)},resize:function(){!a.animating&&a.is(":visible")&&(h||a.doMath(),q?f.smoothHeight():h?(a.slides.width(a.computedW),
 a.update(a.pagingCount),a.setProps()):l?(a.viewport.height(a.h),a.setProps(a.h,"setTotal")):(c.smoothHeight&&f.smoothHeight(),a.newSlides.width(a.computedW),a.setProps(a.computedW,"setTotal")))},smoothHeight:function(b){if(!l||q){var c=q?a:a.viewport;b?c.animate({height:a.slides.eq(a.animatingTo).height()},b):c.height(a.slides.eq(a.animatingTo).height())}},sync:function(b){var g=d(c.sync).data("flexslider"),e=a.animatingTo;switch(b){case "animate":g.flexAnimate(e,c.pauseOnAction,!1,!0);break;case "play":!g.playing&&
 !g.asNav&&g.play();break;case "pause":g.pause()}}};a.flexAnimate=function(b,g,n,i,k){p&&1===a.pagingCount&&(a.direction=a.currentItem<b?"next":"prev");if(!a.animating&&(a.canAdvance(b,k)||n)&&a.is(":visible")){if(p&&i)if(n=d(c.asNavFor).data("flexslider"),a.atEnd=0===b||b===a.count-1,n.flexAnimate(b,!0,!1,!0,k),a.direction=a.currentItem<b?"next":"prev",n.direction=a.direction,Math.ceil((b+1)/a.visible)-1!==a.currentSlide&&0!==b)a.currentItem=b,a.slides.removeClass(e+"active-slide").eq(b).addClass(e+
 "active-slide"),b=Math.floor(b/a.visible);else return a.currentItem=b,a.slides.removeClass(e+"active-slide").eq(b).addClass(e+"active-slide"),!1;a.animating=!0;a.animatingTo=b;c.before(a);g&&a.pause();a.syncExists&&!k&&f.sync("animate");c.controlNav&&f.controlNav.active();h||a.slides.removeClass(e+"active-slide").eq(b).addClass(e+"active-slide");a.atEnd=0===b||b===a.last;c.directionNav&&f.directionNav.update();b===a.last&&(c.end(a),c.animationLoop||a.pause());if(q)a.slides.eq(a.currentSlide).fadeOut(c.animationSpeed,
 c.easing),a.slides.eq(b).fadeIn(c.animationSpeed,c.easing,a.wrapup);else{var o=l?a.slides.filter(":first").height():a.computedW;h?(b=c.itemWidth>a.w?2*c.itemMargin:c.itemMargin,b=(a.itemW+b)*a.move*a.animatingTo,b=b>a.limit&&1!==a.visible?a.limit:b):b=0===a.currentSlide&&b===a.count-1&&c.animationLoop&&"next"!==a.direction?m?(a.count+a.cloneOffset)*o:0:a.currentSlide===a.last&&0===b&&c.animationLoop&&"prev"!==a.direction?m?0:(a.count+1)*o:m?(a.count-1-b+a.cloneOffset)*o:(b+a.cloneOffset)*o;a.setProps(b,
 "",c.animationSpeed);if(a.transitions){if(!c.animationLoop||!a.atEnd)a.animating=!1,a.currentSlide=a.animatingTo;a.container.unbind("webkitTransitionEnd transitionend");a.container.bind("webkitTransitionEnd transitionend",function(){a.wrapup(o)})}else a.container.animate(a.args,c.animationSpeed,c.easing,function(){a.wrapup(o)})}c.smoothHeight&&f.smoothHeight(c.animationSpeed)}};a.wrapup=function(b){!q&&!h&&(0===a.currentSlide&&a.animatingTo===a.last&&c.animationLoop?a.setProps(b,"jumpEnd"):a.currentSlide===
 a.last&&(0===a.animatingTo&&c.animationLoop)&&a.setProps(b,"jumpStart"));a.animating=!1;a.currentSlide=a.animatingTo;c.after(a)};a.animateSlides=function(){a.animating||a.flexAnimate(a.getTarget("next"))};a.pause=function(){clearInterval(a.animatedSlides);a.playing=!1;c.pausePlay&&f.pausePlay.update("play");a.syncExists&&f.sync("pause")};a.play=function(){a.animatedSlides=setInterval(a.animateSlides,c.slideshowSpeed);a.playing=!0;c.pausePlay&&f.pausePlay.update("pause");a.syncExists&&f.sync("play")};
 a.canAdvance=function(b,g){var d=p?a.pagingCount-1:a.last;return g?!0:p&&a.currentItem===a.count-1&&0===b&&"prev"===a.direction?!0:p&&0===a.currentItem&&b===a.pagingCount-1&&"next"!==a.direction?!1:b===a.currentSlide&&!p?!1:c.animationLoop?!0:a.atEnd&&0===a.currentSlide&&b===d&&"next"!==a.direction?!1:a.atEnd&&a.currentSlide===d&&0===b&&"next"===a.direction?!1:!0};a.getTarget=function(b){a.direction=b;return"next"===b?a.currentSlide===a.last?0:a.currentSlide+1:0===a.currentSlide?a.last:a.currentSlide-
 1};a.setProps=function(b,g,d){var e,f=b?b:(a.itemW+c.itemMargin)*a.move*a.animatingTo;e=-1*function(){if(h)return"setTouch"===g?b:m&&a.animatingTo===a.last?0:m?a.limit-(a.itemW+c.itemMargin)*a.move*a.animatingTo:a.animatingTo===a.last?a.limit:f;switch(g){case "setTotal":return m?(a.count-1-a.currentSlide+a.cloneOffset)*b:(a.currentSlide+a.cloneOffset)*b;case "setTouch":return b;case "jumpEnd":return m?b:a.count*b;case "jumpStart":return m?a.count*b:b;default:return b}}()+"px";a.transitions&&(e=l?
 "translate3d(0,"+e+",0)":"translate3d("+e+",0,0)",d=void 0!==d?d/1E3+"s":"0s",a.container.css("-"+a.pfx+"-transition-duration",d));a.args[a.prop]=e;(a.transitions||void 0===d)&&a.container.css(a.args)};a.setup=function(b){if(q)a.slides.css({width:"100%","float":"left",marginRight:"-100%",position:"relative"}),"init"===b&&a.slides.eq(a.currentSlide).fadeIn(c.animationSpeed,c.easing),c.smoothHeight&&f.smoothHeight();else{var g,n;"init"===b&&(a.viewport=d('<div class="'+e+'viewport"></div>').css({overflow:"hidden",
 position:"relative"}).appendTo(a).append(a.container),a.cloneCount=0,a.cloneOffset=0,m&&(n=d.makeArray(a.slides).reverse(),a.slides=d(n),a.container.empty().append(a.slides)));c.animationLoop&&!h&&(a.cloneCount=2,a.cloneOffset=1,"init"!==b&&a.container.find(".clone").remove(),a.container.append(a.slides.first().clone().addClass("clone")).prepend(a.slides.last().clone().addClass("clone")));a.newSlides=d(c.selector,a);g=m?a.count-1-a.currentSlide+a.cloneOffset:a.currentSlide+a.cloneOffset;l&&!h?(a.container.height(200*
 (a.count+a.cloneCount)+"%").css("position","absolute").width("100%"),setTimeout(function(){a.newSlides.css({display:"block"});a.doMath();a.viewport.height(a.h);a.setProps(g*a.h,"init")},"init"===b?100:0)):(a.container.width(200*(a.count+a.cloneCount)+"%"),a.setProps(g*a.computedW,"init"),setTimeout(function(){a.doMath();a.newSlides.css({width:a.computedW,"float":"left",display:"block"});c.smoothHeight&&f.smoothHeight()},"init"===b?100:0))}h||a.slides.removeClass(e+"active-slide").eq(a.currentSlide).addClass(e+
 "active-slide")};a.doMath=function(){var b=a.slides.first(),d=c.itemMargin,e=c.minItems,f=c.maxItems;a.w=a.width();a.h=b.height();a.boxPadding=b.outerWidth()-b.width();h?(a.itemT=c.itemWidth+d,a.minW=e?e*a.itemT:a.w,a.maxW=f?f*a.itemT:a.w,a.itemW=a.minW>a.w?(a.w-d*e)/e:a.maxW<a.w?(a.w-d*f)/f:c.itemWidth>a.w?a.w:c.itemWidth,a.visible=Math.floor(a.w/(a.itemW+d)),a.move=0<c.move&&c.move<a.visible?c.move:a.visible,a.pagingCount=Math.ceil((a.count-a.visible)/a.move+1),a.last=a.pagingCount-1,a.limit=1===
 a.pagingCount?0:c.itemWidth>a.w?(a.itemW+2*d)*a.count-a.w-d:(a.itemW+d)*a.count-a.w-d):(a.itemW=a.w,a.pagingCount=a.count,a.last=a.count-1);a.computedW=a.itemW-a.boxPadding};a.update=function(b,d){a.doMath();h||(b<a.currentSlide?a.currentSlide+=1:b<=a.currentSlide&&0!==b&&(a.currentSlide-=1),a.animatingTo=a.currentSlide);if(c.controlNav&&!a.manualControls)if("add"===d&&!h||a.pagingCount>a.controlNav.length)f.controlNav.update("add");else if("remove"===d&&!h||a.pagingCount<a.controlNav.length)h&&a.currentSlide>
 a.last&&(a.currentSlide-=1,a.animatingTo-=1),f.controlNav.update("remove",a.last);c.directionNav&&f.directionNav.update()};a.addSlide=function(b,e){var f=d(b);a.count+=1;a.last=a.count-1;l&&m?void 0!==e?a.slides.eq(a.count-e).after(f):a.container.prepend(f):void 0!==e?a.slides.eq(e).before(f):a.container.append(f);a.update(e,"add");a.slides=d(c.selector+":not(.clone)",a);a.setup();c.added(a)};a.removeSlide=function(b){var e=isNaN(b)?a.slides.index(d(b)):b;a.count-=1;a.last=a.count-1;isNaN(b)?d(b,
 a.slides).remove():l&&m?a.slides.eq(a.last).remove():a.slides.eq(b).remove();a.doMath();a.update(e,"remove");a.slides=d(c.selector+":not(.clone)",a);a.setup();c.removed(a)};f.init()};d.flexslider.defaults={namespace:"flex-",selector:".slides > li",animation:"fade",easing:"swing",direction:"horizontal",reverse:!1,animationLoop:!0,smoothHeight:!1,startAt:0,slideshow:!0,slideshowSpeed:7E3,animationSpeed:600,initDelay:0,randomize:!1,pauseOnAction:!0,pauseOnHover:!1,useCSS:!0,touch:!0,video:!1,controlNav:!0,
 directionNav:!0,prevText:"Previous",nextText:"Next",keyboard:!0,multipleKeyboard:!1,mousewheel:!1,pausePlay:!1,pauseText:"Pause",playText:"Play",controlsContainer:"",manualControls:"",sync:"",asNavFor:"",itemWidth:0,itemMargin:0,minItems:0,maxItems:0,move:0,start:function(){},before:function(){},after:function(){},end:function(){},added:function(){},removed:function(){}};d.fn.flexslider=function(i){void 0===i&&(i={});if("object"===typeof i)return this.each(function(){var a=d(this),c=a.find(i.selector?
 i.selector:".slides > li");1===c.length?(c.fadeIn(400),i.start&&i.start(a)):void 0===a.data("flexslider")&&new d.flexslider(this,i)});var k=d(this).data("flexslider");switch(i){case "play":k.play();break;case "pause":k.pause();break;case "next":k.flexAnimate(k.getTarget("next"),!0);break;case "prev":case "previous":k.flexAnimate(k.getTarget("prev"),!0);break;default:"number"===typeof i&&k.flexAnimate(i,!0)}}})(jQuery);
 
 
 /*	
 *	jQuery mmenu 3.0.6
 *	
 *	Copyright (c) 2013 Fred Heusschen
 *	www.frebsite.nl
 *
 *	Dual licensed under the MIT and GPL licenses.
 *	http://en.wikipedia.org/wiki/MIT_License
 *	http://en.wikipedia.org/wiki/GNU_General_Public_License
 */


!function(a){function n(b){if("string"==typeof b)switch(b){case"top":case"right":case"bottom":case"left":b={position:b}}if("object"!=typeof b&&(b={}),"undefined"!=typeof b.addCounters&&(a.fn.mmenu.deprecated("addCounters-option","counters.add-option"),b.counters={add:b.addCounters}),"undefined"!=typeof b.closeOnClick&&(a.fn.mmenu.deprecated("closeOnClick-option","onClick.close-option"),b.onClick={close:b.closeOnClick}),"undefined"!=typeof b.onClick&&("undefined"!=typeof b.onClick.delayPageload&&(a.fn.mmenu.deprecated("onClick.delayPageload-option","onClick.delayLocationHref-option"),b.onClick.delayLocationHref=b.onClick.delayPageload),"number"==typeof b.onClick.delayLocationHref&&(a.fn.mmenu.deprecated("a number for the onClick.delayLocationHref-option","true/false"),b.onClick.delayLocationHref=b.onClick.delayLocationHref>0?!0:!1)),"undefined"!=typeof b.configuration&&"undefined"!=typeof b.configuration.slideDuration&&(a.fn.mmenu.deprecated("configuration.slideDuration-option","configuration.transitionDuration-option"),b.configuration.transitionDuration=b.configuration.slideDuration),"boolean"==typeof b.onClick?b.onClick={close:b.onClick}:"object"!=typeof b.onClick&&(b.onClick={}),b=a.extend(!0,{},a.fn.mmenu.defaults,b),"string"!=typeof b.configuration.pageSelector&&(b.configuration.pageSelector="> "+b.configuration.pageNodetype),a.fn.mmenu.useOverflowScrollingFallback())switch(b.position){case"top":case"bottom":a.fn.mmenu.debug('position: "'+b.position+'" not possible when using the overflowScrolling-fallback.'),b.position="left"}return b}function o(){b=a(window),c=a("html"),d=a("body"),g=a(),i={page:H("page"),blocker:H("blocker"),blocking:H("blocking"),opened:H("opened"),opening:H("opening"),submenu:H("submenu"),subopen:H("subopen"),fullsubopen:H("fullsubopen"),subclose:H("subclose"),subopened:H("subopened"),subopening:H("subopening"),subtitle:H("subtitle"),selected:H("selected"),label:H("label"),noresult:H("noresult"),noresults:H("noresults"),nosubresult:H("nosubresult"),search:H("search"),counter:H("counter"),accelerated:H("accelerated"),dragging:H("dragging"),nooverflowscrolling:H("no-overflowscrolling")},j={toggle:J("toggle"),open:J("open"),close:J("close"),search:J("search"),reset:J("reset"),keyup:J("keyup"),change:J("change"),keydown:J("keydown"),count:J("count"),resize:J("resize"),opening:J("opening"),opened:J("opened"),closing:J("closing"),closed:J("closed"),setPage:J("setPage"),setSelected:J("setSelected"),transitionend:J("transitionend"),touchstart:J("touchstart"),mousedown:J("mousedown"),click:J("click"),dragleft:J("dragleft"),dragright:J("dragright"),dragup:J("dragup"),dragdown:J("dragdown"),dragend:J("dragend")},k={opened:K("opened"),options:K("options"),parent:K("parent"),sub:K("sub"),style:K("style"),scrollTop:K("scrollTop"),offetLeft:K("offetLeft")},a.fn.mmenu.useOverflowScrollingFallback(m)}function p(b,c){return b||(b=a(c.pageSelector,d),b.length>1&&(b=b.wrapAll("<"+c.pageNodetype+" />").parent())),b.addClass(i.page),b}function q(b,c,d){return b.contents().each(function(){3==a(this)[0].nodeType&&a(this).remove()}),b.is(d.menuNodetype)||(b=a("<"+d.menuNodetype+" />").append(b)),d.clone&&(b=b.clone(!0),b.add(b.find("*")).filter("[id]").each(function(){a(this).attr("id",H(a(this).attr("id")))})),b.prependTo("body").addClass(H("menu")).addClass(H(c)),a("li."+d.selectedClass,b).removeClass(d.selectedClass).addClass(i.selected),a("li."+d.labelClass,b).removeClass(d.labelClass).addClass(i.label),b}function r(b,c,d){if(b.addClass(H(c)),a("ul ul",b).addClass(i.submenu).each(function(b){var e=a(this),f=e.parent(),g=f.find("> a, > span"),h=f.parent(),j=e.attr("id")||H("s"+d+"-"+b);e.data(k.parent,f),f.data(k.sub,e),e.attr("id",j);var l=a('<a class="'+i.subopen+'" href="#'+j+'" />').insertBefore(g);if(g.is("a")||l.addClass(i.fullsubopen),"horizontal"==c){var j=h.attr("id")||H("p"+d+"-"+b);h.attr("id",j),e.prepend('<li class="'+i.subtitle+'"><a class="'+i.subclose+'" href="#'+j+'">'+g.text()+"</a></li>")}}),"horizontal"==c){var e=a("li."+i.selected,b);e.add(e.parents("li")).parents("li").removeClass(i.selected).end().each(function(){var b=a(this),c=b.find("> ul");c.length&&(b.parent().addClass(i.subopened).addClass(i.subopening),c.addClass(i.opened))}).parent().addClass(i.opened).parents("ul").addClass(i.subopened).addClass(i.subopening),a("ul."+i.opened,b).length||a("ul",b).not("."+i.submenu).addClass(i.opened),a("ul ul",b).appendTo(b)}else a("li."+i.selected,b).addClass(i.opened).parents("."+i.selected).removeClass(i.selected)}function s(b,c){return b||(b=a('<div id="'+i.blocker+'" />').appendTo(d)),G(b,function(){c.trigger(j.close)},!0,!0),b}function t(b,d,f,g){var h=a("li",b).off(j.setSelected).on(j.setSelected,function(){h.removeClass(i.selected),a(this).addClass(i.selected)}),l=a("a",b).not("."+i.subopen).not("."+i.subclose).not('[target="_blank"]');G(l,function(){var g=a(this),h=g.attr("href");F(d.setSelected,g)&&g.parent().trigger(j.setSelected),F(d.blockUI,g,"#"!=h.slice(0,1))&&c.addClass(i.blocking);var k="function"==typeof d.callback,l=function(){d.callback.call(g[0])};close=F(d.close,g),delayLocationHref=F(d.delayLocationHref,g),setLocationHref=F(d.setLocationHref,g,"#"!=h),setLocationHrefFn=function(){window.location.href=h};var m=!1;close&&(setLocationHref&&(delayLocationHref?D(e,setLocationHrefFn,f.transitionDuration):setLocationHrefFn()),k&&D(e,l,f.transitionDuration),m=b.triggerHandler(j.close)),close&&m||(setLocationHref&&setLocationHrefFn(),k&&l())}),g?(G(a("a."+i.subopen,b),function(){var b=a(this).parent().data(k.sub);b&&b.trigger(j.open)}),G(a("a."+i.subclose,b),function(){a(this).parent().parent().trigger(j.close)})):G(a("a."+i.subopen,b),function(){var b=a(this).parent().data(k.sub);b&&b.trigger(j.toggle)})}function u(b,c,d){var e=b.attr("id");e&&e.length&&(d.clone&&(e=I(e)),G(a('a[href="#'+e+'"]',c),function(){b.trigger(j.toggle)}));var e=c.attr("id");e&&e.length&&G(a('a[href="#'+e+'"]',c),function(){b.trigger(j.close)})}function v(a,b,c){return w(a,b,c),setTimeout(function(){x(a,b,c)},10),"open"}function w(a,d,f){var h=C();g.not(a).trigger(j.close),e.data(k.style,e.attr("style")||"").data(k.scrollTop,h).data(k.offetLeft,e.offset().left);var l=0;b.off(j.resize).on(j.resize,function(){var c=b.width();c!=l&&(l=c,e.width(c-e.data(k.offetLeft)))}).trigger(j.resize),f.preventTabbing&&b.off(j.keydown).on(j.keydown,function(a){return 9==a.keyCode?(a.preventDefault(),!1):void 0}),a.addClass(i.opened),f.hardwareAcceleration&&c.addClass(i.accelerated),c.addClass(i.opened).addClass(H(d.position)),e.scrollTop(h)}function x(a,b,d){D(e,function(){a.trigger(j.opened)},d.transitionDuration),c.addClass(i.opening),a.trigger(j.opening)}function y(a,d,f){return D(e,function(){a.removeClass(i.opened),c.removeClass(i.opened).removeClass(H(d.position)).removeClass(i.accelerated),e.attr("style",e.data(k.style)),b.off(j.resize),h&&h.scrollTop(e.data(k.scrollTop)),a.trigger(j.closed)},f.transitionDuration),c.removeClass(i.opening),b.off(j.keydown),a.trigger(j.closing),"close"}function z(a){if(a.hasClass(i.opened))return!1;d.scrollTop(0),c.scrollTop(0),a.removeClass(i.subopening).addClass(i.opened);var e=a.data(k.parent);return e&&e.parent().addClass(i.subopening),"open"}function A(a,b,c,d){if(!a.hasClass(i.opened))return!1;var e=a.data(k.parent);return e&&(D(b,function(){a.removeClass(i.opened)},d.transitionDuration),e.parent().removeClass(i.subopening)),"close"}function B(a){switch(a){case 9:case 16:case 17:case 18:case 37:case 38:case 39:case 40:return!0}return!1}function C(){return h||(0!=c.scrollTop()?h=c:0!=d.scrollTop()&&(h=d)),h?h.scrollTop():0}function D(b,c,d){a.fn.mmenu.support.transition?b.one(j.transitionend,c):setTimeout(c,d)}function E(a,b,c){return b>a&&(a=b),a>c&&(a=c),a}function F(a,b,c){return"function"==typeof a?a.call(b):"undefined"==typeof a&&"undefined"!=typeof c?c:a}function G(b,c,d,e){"string"==typeof b&&(b=a(b));var f=d?a.fn.mmenu.support.touch?j.touchstart:j.mousedown:j.click;e||b.off(f),b.on(f,function(a){a.preventDefault(),a.stopPropagation(),c.call(this,a)})}function H(a){return"mm-"+a}function I(a){return"mm-"==a.slice(0,3)&&(a=a.slice(3)),a}function J(a){return a+".mm"}function K(a){return"mm-"+a}var i,j,k,b=null,c=null,d=null,e=null,f=null,g=null,h=null;a.fn.mmenu=function(c){return b||o(),c=n(c),this.each(function(){var b=a(this),d=c.slidingSubmenus?"horizontal":"vertical";g=g.add(b),b.data(k.options,c).data(k.opened,!1),l++,e=p(e,c.configuration),b=q(b,c.position,c.configuration),f=s(f,b,c.configuration),r(b,d,l),t(b,c.onClick,c.configuration,c.slidingSubmenus),u(b,e,c.configuration),a.fn.mmenu.counters(b,c.counters,c.configuration),a.fn.mmenu.search(b,c.searchfield,c.configuration),a.fn.mmenu.dragOpen(b,c.dragOpen,c.configuration);var h=b.find("ul");b.add(h).off(j.toggle+" "+j.open+" "+j.close).on(j.toggle+" "+j.open+" "+j.close,function(a){a.preventDefault(),a.stopPropagation()}),b.on(j.toggle,function(){return b.triggerHandler(b.data(k.opened)?j.close:j.open)}).on(j.open,function(a){return b.data(k.opened)?(a.stopImmediatePropagation(),!1):(b.data(k.opened,!0),v(b,c,c.configuration))}).on(j.close,function(a){return b.data(k.opened)?(b.data(k.opened,!1),y(b,c,c.configuration)):(a.stopImmediatePropagation(),!1)}).off(j.setPage).on(j.setPage,function(a,d){e=p(d,c.configuration),u(b,e,c.configuration)}),"horizontal"==d?h.on(j.toggle,function(){return a(this).triggerHandler(j.open)}).on(j.open,function(){return z(a(this),c)}).on(j.close,function(){return A(a(this),b,c,c.configuration)}):h.on(j.toggle,function(){var c=a(this);return c.triggerHandler(c.parent().hasClass(i.opened)?j.close:j.open)}).on(j.open,function(){return a(this).parent().addClass(i.opened),"open"}).on(j.close,function(){return a(this).parent().removeClass(i.opened),"close"})})},a.fn.mmenu.defaults={position:"left",slidingSubmenus:!0,onClick:{close:!0,setSelected:!0,delayLocationHref:!0},configuration:{preventTabbing:!0,hardwareAcceleration:!0,selectedClass:"Selected",labelClass:"Label",counterClass:"Counter",pageNodetype:"div",menuNodetype:"nav",transitionDuration:400,dragOpen:{pageMaxDistance:500,pageMinVisible:65}}},a.fn.mmenu.search=function(b,c){if("boolean"==typeof c?c={add:c,search:c}:"string"==typeof search&&(c={add:!0,search:!0,placeholder:c}),"object"!=typeof c&&(c={}),c=a.extend(!0,{},a.fn.mmenu.search.defaults,c),c.add){var d=a('<div class="'+i.search+'" />').prependTo(b);d.append('<input placeholder="'+c.placeholder+'" type="text" autocomplete="off" />'),c.noResults&&a("ul",b).not("."+i.submenu).append('<li class="'+i.noresults+'">'+c.noResults+"</li>")}if(c.search){var d=a("div."+i.search,b),e=a("input",d),f=a("li."+i.label,b),g=a("em."+i.counter,b),h=a("li",b).not("."+i.subtitle).not("."+i.label).not("."+i.noresults),l="> a";c.showLinksOnly||(l+=", > span"),e.off(j.keyup+" "+j.change).on(j.keyup,function(a){B(a.keyCode)||e.trigger(j.search)}).on(j.change,function(){e.trigger(j.search)}),b.off(j.reset+" "+j.search).on(j.reset+" "+j.search,function(a){a.preventDefault(),a.stopPropagation()}).on(j.reset,function(){e.val(""),b.trigger(j.search)}).on(j.search,function(c,d){"string"==typeof d?e.val(d):d=e.val().toLowerCase(),h.add(f).addClass(i.noresult),h.each(function(){var b=a(this);a(l,b).text().toLowerCase().indexOf(d)>-1&&b.add(b.prevAll("."+i.label).first()).removeClass(i.noresult)}),a(a("ul."+i.submenu,b).get().reverse()).each(function(){var b=a(this),c=b.data(k.parent),e=(b.attr("id"),b.find("li").not("."+i.subtitle).not("."+i.label).not("."+i.noresult));e.length?c&&c.removeClass(i.noresult).removeClass(i.nosubresult).prevAll("."+i.label).first().removeClass(i.noresult):(b.trigger(j.close),c&&c.addClass(i.nosubresult))}),b[h.not("."+i.noresult).length?"removeClass":"addClass"](i.noresults),g.trigger(j.count)})}},a.fn.mmenu.search.defaults={add:!1,search:!0,showLinksOnly:!0,placeholder:"Search",noResults:"No results found."},a.fn.mmenu.counters=function(b,c,d){"boolean"==typeof c&&(c={add:c,count:c}),"object"!=typeof c&&(c={}),c=a.extend(!0,{},a.fn.mmenu.counters.defaults,c),a("em."+d.counterClass,b).removeClass(d.counterClass).addClass(i.counter),c.add&&a("."+i.submenu,b).each(function(){var c=a(this),d=c.attr("id");if(d&&d.length){var e=a('<em class="'+i.counter+'" />'),f=a("a."+i.subopen,b).filter('[href="#'+d+'"]');f.parent().find("em."+i.counter).length||f.before(e)}}),c.count&&a("em."+i.counter,b).each(function(){var c=a(this),d=a("ul"+c.next().attr("href"),b);c.off(j.count).on(j.count,function(a){a.preventDefault(),a.stopPropagation();var b=d.children().not("."+i.label).not("."+i.subtitle).not("."+i.noresult).not("."+i.noresults);c.html(b.length)})}).trigger(j.count)},a.fn.mmenu.counters.defaults={add:!1,count:!0},a.fn.mmenu.dragOpen=function(b,d,f){if(!a.fn.hammer)return!1;if("boolean"==typeof d&&(d={open:d}),"object"!=typeof d&&(d={}),d=a.extend(!0,{},a.fn.mmenu.dragOpen.defaults,d),d.open){var g=!1,h=!1,l=0,m=0,n=b.data(k.options);switch(n.position){case"left":var o={events:j.dragleft+" "+j.dragright,open_dir:"right",close_dir:"left",delta:"deltaX",negative:!1};break;case"right":var o={events:j.dragleft+" "+j.dragright,open_dir:"left",close_dir:"right",delta:"deltaX",negative:!0};break;case"top":var o={events:j.dragup+" "+j.dragdown,open_dir:"down",close_dir:"up",delta:"deltaY",negative:!1};break;case"bottom":var o={events:j.dragup+" "+j.dragdown,open_dir:"up",close_dir:"down",delta:"deltaY",negative:!0}}e.hammer().on(o.events+" "+j.dragend,function(a){a.gesture.preventDefault(),a.stopPropagation()}).on(o.events,function(j){var p=o.negative?-j.gesture[o.delta]:j.gesture[o.delta];if(h=p>l?o.open_dir:o.close_dir,l=p,l>d.threshold){if(!g){if(c.hasClass(i.opened))return;switch(g=!0,b.data(k.opened,!0),w(b,n,f),c.addClass(i.dragging),n.position){case"left":case"right":m=E(a(window).width(),0,f.dragOpen.pageMaxDistance)-f.dragOpen.pageMinVisible;break;default:m=a(window).height()-f.dragOpen.pageMinVisible}}g&&e.css("margin-"+n.position,E(l,0,m))}}).on(j.dragend,function(){g&&(g=!1,e.css("margin-"+n.position,""),c.removeClass(i.dragging),h==o.open_dir?x(b,n,f):(b.data(k.opened,!1),y(b,n,f)))})}},a.fn.mmenu.dragOpen.defaults={open:!1,threshold:50},a.fn.mmenu.useOverflowScrollingFallback=function(a){return c?("boolean"==typeof a&&c[a?"addClass":"removeClass"](i.nooverflowscrolling),c.hasClass(i.nooverflowscrolling)):(m=a,a)},a.fn.mmenu.support={touch:function(){return"ontouchstart"in window.document}(),overflowscrolling:function(){return"WebkitOverflowScrolling"in window.document.documentElement.style}(),oldAndroid:function(){var a=navigator.userAgent;return a.indexOf("Android")>=0?2.4>parseFloat(a.slice(a.indexOf("Android")+8)):!1}(),transition:function(){return"transition"in document.createElement("div").style}()},a.fn.mmenu.debug=function(a){"undefined"!=typeof console&&"undefined"!=typeof console.log&&console.log("MMENU: "+a)},a.fn.mmenu.deprecated=function(a,b){"undefined"!=typeof console&&"undefined"!=typeof console.warn&&console.warn("MMENU: "+a+" is deprecated, use "+b+" instead.")};var l=0,m=a.fn.mmenu.support.touch&&!a.fn.mmenu.support.overflowscrolling}(jQuery);