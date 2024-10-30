jQuery(document).ready(function($) {
	
	$.fn.getTabHiddenDimensions = function (includeMargin) {
        var $item = this,
        props = { position: 'relative', visibility: 'hidden', display: 'block' },
        dim = { width: 0, height: 0, innerWidth: 0, innerHeight: 0, outerWidth: 0, outerHeight: 0 },
        $hiddenParents = $item.parents().andSelf().not(':visible'),
        includeMargin = (includeMargin == null) ? false : includeMargin;
        var oldProps = [];
        $hiddenParents.each(function () {
            var old = {};

            for (var name in props) {
                old[name] = this.style[name];
                this.style[name] = props[name];
            }

            oldProps.push(old);
        });

        dim.width = $item.width();
        dim.outerWidth = $item.outerWidth(includeMargin);
        dim.innerWidth = $item.innerWidth();
        dim.height = $item.height();
        dim.innerHeight = $item.innerHeight();
        dim.outerHeight = $item.outerHeight(includeMargin);

        $hiddenParents.each(function (i) {
            var old = oldProps[i];
            for (var name in props) {
                this.style[name] = old[name];
            }
        });

        return dim;
    }
	
	function mlptGetHighestTab($wrapper) {
		var $divs = $wrapper.find('.mlpt-inner-div');
		var height = 0, bHeight;
			bHeight = height;
		for(var i = 0; i < $divs.length; i++) {
			height = $($divs[i]).getTabHiddenDimensions().outerHeight;
			bHeight = (height > bHeight) ? height : bHeight;
		}
		return bHeight;
	}
	
	function mlptSetAllHeightsToHeighest($wrapper) {
		var heighest = mlptGetHighestTab($wrapper);
			$wrapper.find('.mlpt-inner-div').css({height: heighest});
	}
	
	var mlptAllSliderInstances = [];
	
	function mlptScrollbar($par, $sHeight, $limit) {
		var $scrolled = $par.find('.mlpt-inner-limit');
		var sbInit = $limit,
			plHeight = $sHeight,
			hDiff = plHeight - sbInit;
		var set = $par.attr('data-set');
		var num = $par.attr('data-num');
			console.log($sHeight, $limit);
			if(hDiff > 0) {
				var prop = hDiff / plHeight,
					handleHeight = Math.ceil((1 - prop) * sbInit);
					handleHeight -= handleHeight%2;
									
					if($scrolled.closest('.mlpt_wrap').length > 0) {} else {
						$scrolled.wrap('<div id="mlpt_'+set+'_wrap_'+num+'" class="mlpt_wrap"></div>');
					}
							
					$('#mlpt_'+set+'_wrap_'+num).css({'height':''+sbInit+'px','overflow':'hidden','position':'relative'});
					$wrap = $('#mlpt_'+set+'_wrap_'+num);
					mlptAllSliderInstances.push($wrap);
					
						if($wrap.find('#slider_'+set+'_vert_'+num).length == 0) {
							$wrap.append('<div id="slider_'+set+'_vert_'+num+'" class="mlpt-slider-vert"></div>');
						}
						
						$offset = $('.mlpt-slider-vert').getTabHiddenDimensions().width;
						$fWidth = $wrap.getTabHiddenDimensions().width;
						console.log($offset,$fWidth);
						$scrolled.css({position: 'absolute', width: ($fWidth - ($offset+20))+'px', padding: $offset+'px'});
						
						$('#slider_'+set+'_vert_'+num).slider({
							orientation:'vertical',
							range:'min',
							min:0,
							max:100,
							value:100,
							animate:true,
							slide: function(event, ui) {
								if($scrolled.css('display') != 'none') {
									var tVal = -((100-ui.value)*hDiff/100);
								}
								$scrolled.css({top:tVal});
								$('#slider_'+set+'_vert_'+num+' .ui-slider-range').height(ui.value+'%');
							},
							change: function(event, ui) {
								if($scrolled.css('display') != 'none') {	
									var tVal = -((100-ui.value)*hDiff/100);
								}
								if(!$scrolled.is(':animated')) {
									$scrolled.animate({top:tVal},300);
								}
								$('#slider_'+set+'_vert_'+num+' .ui-slider-range').height(ui.value+'%');
							}
						});

						var scrollable = sbInit - handleHeight,
							sliderMargin = (sbInit - scrollable)*0.5;
									
							$('#slider_'+set+'_vert_'+num+' .ui-slider-handle').css({height:handleHeight,'margin-bottom':-0.5*handleHeight});
							$('#slider_'+set+'_vert_'+num+'.ui-slider').css({height:scrollable,'margin-top':sliderMargin});
							$( '#slider_'+set+'_vert_'+num ).show();							
			}
			else {
				var slider = $( '#slider_'+set+'_vert_'+num );
					if(slider.length) {
						slider.hide();
						$scrolled.css({width: '96%'})
					}
			}
							
			$('#slider_'+set+'_vert_'+num+'.ui-slider').click(function(event) {
				event.stopPropagation();
			});
		

	} /* End mlptScrollbar */
	
	
	function mlptSetAllHeightsToLimit($wrapper, $limit) {
		var limit = $limit;
		var height;
		var	$divs = $wrapper.find('.mlpt-inner-limit');
			if($divs) {
				$divs.each(function() {
					$this = $(this);
					$par = $this.closest('.mlpt-inner-div');
					
					height = $this.getTabHiddenDimensions().outerHeight;
					$par.css({height: limit+'px'});	
						
					mlptScrollbar($par, height, limit);
				});
			}
			return false;
	}
	
	function mlptInit() {
		var mlptWrapper = $('ul.mlpt_shortcode_conc');
	
		mlptWrapper.imagesLoaded( function() {
			mlptWrapper.each(function() {
				var $this = $(this).closest('div.mlpt_shortcode_info');
				
				if($this.attr('data-highest') == '1') {
					mlptSetAllHeightsToHeighest($(this));
				}
				else if($this.attr('data-limit') != undefined && parseInt($this.attr('data-limit')) > 99) {
					mlptSetAllHeightsToLimit($(this), $this.attr('data-limit'));
				}
				else {}
			});
		});
	}
	
	$('ul.mlpt_shortcode_tabs li a').click(function(event) {
		event.preventDefault();
			$this = $(this);
			$instance = $this.parent().parent().attr('data-instance');
			var b = $this.parent().index();
			var a = $('div#mlpt_shortcode_info_divs_'+$instance).children().eq(b);
			$('ul#mlpt_shortcode_tabs_'+$instance+' a').removeClass('active');
			$('ul#mlpt_shortcode_tabs_'+$instance+' li').removeClass('li_active');
			$('div#mlpt_shortcode_info_divs_'+$instance+' div').removeClass('div_active');
			a.addClass('div_active');
			$this.parent('li').addClass('li_active');
			$this.addClass('active');
	});
	
	$('ul.mlpt_action_tabs li a').click(function(event) {
		event.preventDefault();
			$this = $(this);
			$instance = $this.parent().parent().attr('data-instance');
			var b = $this.parent().index();
			var a = $('div#mlpt_action_info_divs_'+$instance).children().eq(b);
			$('ul#mlpt_action_tabs_'+$instance+' a').removeClass('active');
			$('ul#mlpt_action_tabs_'+$instance+' li').removeClass('li_active');
			$('div#mlpt_action_info_divs_'+$instance+' div').removeClass('div_active');
			a.addClass('div_active');
			$this.parent('li').addClass('li_active');
			$this.addClass('active');
	});
	
	$("ul.mlpt_shortcode_conc li > a").click(function(event){
		event.preventDefault();
			var $this = $(this);
			var $par = $this.closest('ul.mlpt_shortcode_conc');
			var ex = $par.find('.mlpt-inner-div.conc-active');
			var sel = $this.next('.mlpt-inner-div');
			var as = $par.find('a.mlpt-header');
			var divs = $par.find('.mlpt-inner-div');
			var prev = $this.closest('div.mlpt_shortcode_info').attr('data-prev');
			
			if(!$this.hasClass('conc-active')) {
				ex.slideUp(350);
				divs.removeClass('conc-active');
				as.removeClass('conc-active');
				sel.slideDown(350);
				$this.addClass('conc-active');
				sel.addClass('conc-active');
			}
			else {
				if(parseInt(prev) != 1) {
					sel.slideUp(350);
					divs.removeClass('conc-active');
					as.removeClass('conc-active');
				}
			}
	});
	
	$("ul.mlpt_action_conc li > a").click(function(event){
		event.preventDefault();
			var $this = $(this);
			var $par = $this.closest('ul.mlpt_action_conc');
			var ex = $par.find('.mlpt-inner-div.conc-active');
			var sel = $this.next('.mlpt-inner-div');
			var as = $par.find('a.mlpt-header');
			var divs = $par.find('.mlpt-inner-div');
			var prev = $this.closest('div.mlpt_action_info').attr('data-prev');
			
			if(!$this.hasClass('conc-active')) {
				ex.slideUp(350);
				divs.removeClass('conc-active');
				as.removeClass('conc-active');
				sel.slideDown(350);
				$this.addClass('conc-active');
				sel.addClass('conc-active');
			}
			else {
				if(parseInt(prev) != 1) {
					sel.slideUp(350);
					divs.removeClass('conc-active');
					as.removeClass('conc-active');
				}
			}
	});
	
	$('.mlpt-inner-div.conc-active').slideDown(350);
	
	var $allMlptVideos = $('div.mlpt-inner-div iframe'),
		$fluidEl = $('div.mlpt-inner-div');
		$allMlptVideos.each(function() {
			$(this)
			.data('aspectRatio', this.height / this.width)
			.removeAttr('height')
			.removeAttr('width');
		});

		$(window).resize(function() {
			var newWidth = $fluidEl.width();
				$allMlptVideos.each(function() {
					var $el = $(this);
						$el.width(newWidth).height(newWidth * $el.data('aspectRatio'));
				});
				
				mlptInit();		
		}).resize();
	
});