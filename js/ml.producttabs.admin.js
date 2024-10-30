jQuery(document).ready(function($) {
        var count = parseInt($('#mlpt_add_here').attr('data-count'));
		
        $('div#mlpt_product_meta').on('click', '.mlpt-remove', function() {
			if( $(this).parent('div.mlpt_tab_setting').children('input, textarea').val() ) {
				var r = confirm("Confirm Remove Tab");
					if (r == true) {
						$(this).parent().parent().remove();
						count -= 1;
					} else {
						return false;
					}
			}
			else {
				$(this).parent().parent().remove();
				count -= 1;		
			}
        });
		
		function mlProductTabsAdminChangeSort() {
			var items = $('div.mlpt_tab_setting_div');
			var num, checkVal;
			items.each(function(i) {
				$this = $(this);
				num = $this.find('span.mlpt_tab_title span.mlpt_tab_num');
				num.html(i+1);
				checkVal = $this.find('input[name="_mlpt_custom_active"]');
				checkVal.val(i+1);
			});
		}
		
		$( 'div#mlpt_product_meta' ).sortable({
			axis: 'y',
			containment: '',
			items: 'div.mlpt_tab_setting_div',
			distance: 5,
			zIndex: 1000,
			cursor: 'move',
			stop: function( event, ui ) {
				mlProductTabsAdminChangeSort();
			}
		});
		
		$('div#mlpt_product_meta').on('input', '.mlpt_custom_active_title', function(event) {
			var $this = $(this);
			var input = $this.closest('div.mlpt_tab_setting_div').find('span.mlpt_tab_aTitle');
				input.html($this.val());
		});
		
        $(".mlpt_add_meta").click(function() {
            count += 1;

            $('#mlpt_add_here').append('<div class="mlpt_tab_setting_div"><h2 data-id="'+count+'"><input type="radio" name="_mlpt_custom_active" value="'+count+'" /> <span class="mlpt_tab_title">Tab <span class="mlpt_tab_num">'+count+'</span> - <span class="mlpt_tab_aTitle"></span></span><span class="dashicons dashicons-minus"></span></h2><div data-id="'+count+'" class="mlpt_tab_setting"><div class="mlpt_tab_setting_inner"><span class="mlpt_editor_title">Tab Title</span> <input type="text" class="mlpt_custom_active_title" name="_mlpt_custom['+count+'][title]" value="" /><span class="mlpt_editor_title">Tab Icon</span> <input type="text" name="_mlpt_custom['+count+'][icon]" value="" /><span class="mlpt_editor_title">Info</span> <textarea rows="5" cols="4" name="_mlpt_custom['+count+'][info]"></textarea></div><span class="mlpt-remove">Remove Tab</span></div></div>' );
			return false;
        });
		
		$('select.mlnp_icon_list').on('change', function (event) {
			var optionSelected = $("option:selected", this);
			var valueSelected = this.value;
			$('span#mlpt_icon_box i').attr('class',valueSelected);
			$('div#mlpt_product_meta input#mlpt_readonly').val(valueSelected);		
		});
		
		$('#mlpt_product_meta').on('click', 'h2', function(event) {
			if(event.target.type == 'radio') return;
			var $this = $(this);
				if($this.next('div.mlpt_tab_setting').css('display') != 'none') {
					$this.next('div.mlpt_tab_setting').slideUp();
					$this.children('span.dashicons').attr('class','dashicons dashicons-plus');
				}
				else {
					$this.next('div.mlpt_tab_setting').slideDown();
					$this.children('span.dashicons').attr('class','dashicons dashicons-minus');
				}
		});
		
		$('.mlpt_tab_setting_div h2 input[name="_mlpt_custom_active"]').on('change', function(event) {
			event.preventDefault();
			$('span.mlpt_tab_title em').remove();
			var $this = $(this);
			var $next = $this.next('span.mlpt_tab_title').append('<em style="color:#000; margin-left:6px;">active</em>');
		});
		
		$('.mlpt-color-picker').wpColorPicker();
		$('div.mlpt_tab_setting').slideUp();
		$('.mlpt_tab_setting_div h2 input[name="_mlpt_custom_active"]:checked').next('span.mlpt_tab_title').append('<em style="color:#000; margin-left:6px;">active</em>');
		
		$('.mlpt_check_concert.choose_mlpt input[name="_mlpt_action_type"]').on('change', function() {
			event.preventDefault();
			var $this = $(this);
			var $div = $('.mlpt_start_concert');
			
			if($this.val() == 'concertina') {
				$div.slideDown(500);
			}
			else {
				$div.slideUp(500);
			}
		});
		
		
		var mlptRadios = document.getElementsByName('_mlpt_props_start');
		var $mlptDiv = $('.mlpt_start_concert');
		
		if($('.mlpt_check_concert.choose_mlpt input[name="_mlpt_action_type"]:checked').val() == 'concertina') {
			$mlptDiv.slideDown(500);
		}
		else {
			$mlptDiv.slideUp(500);
		}
		
});