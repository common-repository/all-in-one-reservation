; 'use strict';
var aior_admin = {
	init: function () {
		this.tabs();
		this.time_picker();
		this.add_holiday();
	},

	tabs: function () {
		$ = jQuery;
		$( '.aior-tabs li:first' ).addClass( 'active' );
		$( '.aior-tabs li:not(:first)' ).addClass( 'inactive' );
		$( '.aior-tab-content' ).hide();
		$( '.aior-tab-content:first' ).show();
		$( '.aior-tabs li a' ).on(
			'click',
			function () {
				if ($( this ).parents( 'li' ).hasClass( 'inactive' )) { // this is the start of our condition
					$( '.aior-tabs li a' ).parents( 'li' ).addClass( 'inactive' ).removeClass( 'active' );
					$( this ).parents( 'li' ).removeClass( 'inactive' ).addClass( 'active' );
					$( '.aior-tab-content' ).hide();
					$( '#' + $( this ).attr( 'id' ) + '-content' ).fadeIn( 'slow' );
				}
			}
		);
		/* Get Last Tab content */
		$( document ).on(
			'click',
			'.aior-tabs .aior-tab a',
			function(){
				localStorage.setItem( 'aior_tab',$( this ).attr( 'id' ) );
			}
		);
		$( '#' + localStorage.getItem( 'aior_tab' ) ).trigger( 'click' );

	},
	time_interval:function(){
		a = 30;
		b = $( '.aior_time_slot' );
		b.on(
			'change',
			function(){
				var a = b.val();$( '.timepicker' ).timepicker( 'option','interval',a )
			}
		);
		return a
	},
	time_format:function(){
		b = $( '.aior_clock_hours' );
		c = b.val();
		b.on(
			'change',
			function(){
				c = $( this ).val();
				$( '.timepicker' ).timepicker( 'option','timeFormat',c );
			}
		);
		return c
	},
	time_picker:function(){
		opening_time   = $( '.aior_opening_time' );
		closing_time   = $( '.aior_closing_time' );
		open_close_t   = $( '.aior_opening_time,.aior_closing_time' );
		h_opening_t    = $( '.h_opening_time' );
		h_closing_t    = $( '.h_closing_time' );
		h_open_close_t = $( '.h_opening_time,.h_closing_time' );
		clock_hours    = $( '.aior_clock_hours' );
		time_slot      = $( '.aior_time_slot' );
		time_format    = clock_hours.val();
		time_interval  = 30;
		/* Set time picker */
		$( '.timepicker' ).timepicker( 'option','timeFormat',time_format );
		/* If Clock Hours Changed */
		clock_hours.on(
			'change',
			function(){
				time_format = $( this ).val();
				open_close_t.timepicker( 'option','timeFormat',time_format );
				h_open_close_t.timepicker( 'option','timeFormat',time_format );
			}
		);
		/* If Time Slot (Interval) Change */
		time_slot.on(
			'change',
			function () {
				var time_interval = time_slot.val();
				$( '.timepicker' ).timepicker( 'option','interval',time_interval );
			}
		);
		/* Set Opening Time */
		opening_time.timepicker(
			{
				timeFormat: time_format,interval: time_interval,
				change:function(time){
					closing_time.val( '' );
					closing_time.timepicker( 'option','minTime',$( this ).val() );
				}
			}
		);
		/* Set Closing Time */
		closing_time.timepicker(
			{
				timeFormat: time_format,interval: time_interval
			}
		);
		/* Holiday Setting */
		h_opening_t.timepicker(
			{
				timeFormat: time_format,interval: time_interval,
				change: function (time) {
					$( this ).parent( 'div' ).siblings( 'div' ).children( '.h_closing_time' ).val( '' );
					$( this ).parent( 'div' ).siblings( 'div' ).children( '.h_closing_time' ).timepicker( {'minTime':$( this ).val()} );
				}
			}
		);
		h_closing_t.timepicker( {timeFormat: time_format,interval: time_interval} );
		$( '.datepick' ).datepicker( "option","dateFormat",'dd/mm/yy' );
		$( '.aior_date_format' ).change(
			function () {
				if ($( this ).val() != '') {
					$( '.datepick' ).datepicker( "option","dateFormat",$( this ).val() );
				}
			}
		);
	},
	autoChangeWorkingTime:function() {
		$                    = jQuery;
		var cur_opening_time = $( '.aior_opening_time' ).val();
		var cur_closing_time = $( '.aior_closing_time' ).val();
		var rest_r_closedays       = JSON.parse( restConfig.rest_r_closedays );
		var work_rest_opening_time = '';
		var work_rest_closing_time = '';
		var arr                    = '';
		if (rest_r_closedays != '') {
			arr = jQuery.map(
				rest_r_closedays,
				function (el) {
					return parseInt( el )
				}
			);
		}
		var i = 1;
		$( '.r_closed_days' ).each(
			function () {
				if (i == 7) {
					i = 0;
				}
				if ((work_rest_opening_time == old_opening_time || work_rest_opening_time == '') && $.inArray( i, arr ) == -1) {
					$( this ).parent( 'label' ).next( '.rest_row' ).find( '.h_opening_time' ).val( cur_opening_time );
				}
				if ((work_rest_closing_time == old_closing_time || work_rest_closing_time == '') && $.inArray( i, arr ) == -1) {
					$( this ).parent( 'label' ).next( '.rest_row' ).find( '.h_closing_time' ).val( cur_closing_time );
				}
				i++;
			}
		);
	},
	add_holiday:function(){
		$ = jQuery;
		$( '.rest_add_holiday_dt' ).click(
			function (e) {
				e.preventDefault();
				$( ".datepick" ).datepicker( "destroy" );
				var dtFormat = 'dd/mm/yy';
				if ($( '.rest_date_format' ).val() != '') {
					dtFormat = $( '.rest_date_format' ).val();
				}
				var field_table = $( this ).attr( 'data-id' );
				var div         = $( '.' + field_table ),
					row_count   = div.children( 'tbody' ).find( 'tr.rest_row' ).length,
					new_field   = div.children( 'tbody' ).children( 'tr.rest_clone' ).clone( false );
				new_field.attr( 'class', 'rest_row' );
				$( '.rest_tbl_holiday_head' ).show();
				var count = parseInt( row_count );
				count++;
				new_field.html( new_field.html().replace( /replace_this/g, count ) );
				new_field.insertBefore( div.children( 'tbody' ).find( '.rest_clone' ) );
				row_count++;
				$( ".datepick" ).datepicker(
					{
						minDate: new Date(),dateFormat: dtFormat,
						onSelect: function (date){
							var toid = $( this ).closest( 'td' ).next().find( 'input' ).attr( 'id' );
							$( "#" + toid ).datepicker( "option", "minDate", date );
							var fromid = $( this ).closest( 'td' ).prev().find( 'input' ).attr( 'id' );
							if ($( '#' + fromid ).val() == '') {
								$( '#' + fromid ).val( date );
							}
						}
					}
				);
				return false;
			}
		);
		$( document ).on( 'click','.rest-remove-button',function(){$( this ).parents( '.rest_row' ).remove()} );

	},
	toast:function(i,m){
		const Toast = Swal.mixin( {toast:true,position:'bottom-right',showConfirmButton:false,timer:3000,timerProgressBar:true,didOpen:(toast) => {toast.addEventListener( 'mouseenter',Swal.stopTimer );toast.addEventListener( 'mouseleave',Swal.resumeTimer )}} );
		Toast.fire( {icon: i,title:m} )
	},
};

; (function ($) {
	'use strict';
	jQuery.fn.extend(
		{
			main:function(){
			},
			check: function () {
				return this.each(
					function () {
						this.checked = true;
					}
				);
			},
			uncheck: function () {
				return this.each(
					function () {
						this.checked = false;
					}
				);
			}
		}
	);
	var AIOR_ADMIN = jQuery.fn.main;
	jQuery.extend(
		true,
		AIOR_ADMIN,
		{
			hello:function(){

			}
		}
	);
})( jQuery );
/* Run when document ready */
jQuery( document ).ready(
	function(){
		(function($){
			var urlSearchParams = new URLSearchParams(window.location.search);
			var myParam = urlSearchParams.get('post_type');
			if( 'sol_reservation_form' != myParam ) {
				var customMediaLibrary = window.wp.media({
					frame: 'select',
					title: "Select JSON File",
					multiple: false,
					library: {
						order: 'DESC',
						orderby: 'date',
						type: 'text/plain',
						search: null, 
						uploadedTo: null
					},
					button: {text: 'Done'}
				});
				
				$(document).on('click','input#aior_select_form_for_import',function(e) {
					e.preventDefault();
					customMediaLibrary.open();
				});
				customMediaLibrary.on( 'select', function() {
					var selectedImages = customMediaLibrary.state().get( 'selection' );
					var attachment = customMediaLibrary.state().get('selection').first().toJSON();
					aurl =  attachment.url;
					aid = attachment.id;
					if( aid ){
						var nonce = $('#aior_admin_global_nonce').val();
						$.ajax({
							type:'POST',cache:false,url:ajaxurl,data:{'action':'aoir_reservation_global_ajax','act':'import_json_reservation_from_ajax','aid':aid,'nonce':nonce
							},
							beforeSend:function(){airoAppointment.loader()},
							success:function(re){
								swal.close();
								if(re){
									Swal.fire(re);
								} else {
									Swal.fire('Something went wrong!!');
								}
							}
						});
					}
				});
			}
			aior_admin.init();
			Hooks.add_action(
				'sol_form_builder_prop',
				function( f,r ) {
					return '<div class="col-md-12"><div class="form-check"><label class="form-check-label"><input data-field="' + f + '" type="checkbox" class="form-check-input form_input_req" ' + r + '>Required</label></div></div>';
				}
			);
		}(jQuery))
		jQuery( '#enable_social_share' ).on(
			'change',
			function() {
				if ( this.value == parseInt( 1 )) {
					jQuery( '.social_share_icons' ).show();
				} else {
					jQuery( '.social_share_icons' ).hide();
				}
			}
		);
		jQuery( '#woo_cat_product' ).on(
			'change',
			function() {
				if ( this.value == 'woo_cat') {
					jQuery( '.woo_cat' ).show();
					jQuery( '.woo_product' ).hide();
				} else {
					jQuery( '.woo_cat' ).hide();
					jQuery( '.woo_product' ).show();
				}
			}
		);
		jQuery( '#enable_payment' ).on(
			'change',
			function() {
				if ( this.value == parseInt( 1 )) {
					jQuery( '.payment-gateways' ).show();
				} else {
					jQuery( '.payment-gateways' ).hide();
				}
			}
		);
		var payment = jQuery( '#enable_payment' ).find(':selected').val();
		if ( payment == parseInt( 1 )) {
			jQuery( '.payment-gateways' ).show();
		} else {
			jQuery( '.payment-gateways' ).hide();
		}
		if( jQuery('.aior_success_msg').text().length > 0 ) {
			aior_admin.toast('success', jQuery('.aior_success_msg').text().trim());
		}
	}
);
