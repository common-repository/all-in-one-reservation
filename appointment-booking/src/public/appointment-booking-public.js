;
'use strict';

var airoAppointment = {
	init: function() {
		airoAppointment.book_apppointment();
	},
	book_apppointment: function() {
		$( document ).on(
			'keypress',
			'#rf_phone_no',
			function(ev) {
				if (ev.which != 43 && ev.which != 8 && ev.which != 0 && (ev.which < 48 || ev.which > 57)) {
					return false;
				}
			}
		);
		$( '.month-wrapper' ).on(
			'click',
			'.book-apppointment-now',
			function(e) {
				$( 'header' ).css( 'z-index', '-1' );
				booking_type    = $( '#aior_booking_type' ).val();
				aior_login_page = $( '#aior_login_page' ).val();
				user_count      = $( '#user_count' ).val();
				is_user_login   = $( '#is_user_login' ).val();
				if ('registered' == booking_type && user_count == 0 && is_user_login == 0) {
					window.location = aior_login_page;
					return;
				}
				pr        = $( this ).parents( '.solrow' );
				title     = pr.find( '.stitle' ).html();
				stime     = pr.find( '.stime' ).html();
				etime     = pr.find( '.etime' ).html();
				time      = stime + '-' + etime;
				sid       = $( this ).data( 'sid' );
				tid       = $( this ).data( 'taskid' );
				tno       = $( this ).data( 'taskno' );
				tdt       = $( this ).data( 'taskdate' );
				aid       = $( this ).data( 'abcid' );
				sps       = $( this ).data( 'space' );
				price     = $( this ).data( 'price' );
				app_limit = $( '#appointment_limit' ).val();
				nonce     = $( '#aior_admin_global_nonce' ).val();
				stp       = $( '#aior_slot_type' ).val();
				fd        = { sid: sid, stp: stp, aid: aid, tid: tid, tno: tno, tdt: tdt, sps: sps, title: title, time: time, price: price, app_limit: app_limit };
				$.ajax(
					{
						type: 'POST',
						dataType: 'html',
						url: aior_obj.ajaxurl,
						data: {
							action: 'aoir_reservation_global_ajax',
							act: 'aior_get_appointment_form',
							abcid: aid,
							nonce: nonce
						},
						beforeSend: function() { aior_public.loader() },
						success: function(fhtml) {
							$( 'header' ).css( 'z-index', '3' )
							swal.close();
							Swal.fire(
								{
									title: title + ' <small>' + time + '</small>',
									html: fhtml,
									showCancelButton: false,
									showConfirmButton: false,
									focusConfirm: false,
									preConfirm: () => {},
									onAfterClose: () => {},
									didRender: () => {
										sid       = fd.sid;
										tid       = fd.tid;
										tno       = fd.tno;
										tdt       = fd.tdt;
										aid       = fd.aid;
										sps       = fd.sps;
										stp       = fd.stp;
										pri       = fd.price;
										app_limit = fd.app_limit;
										fm        = $( '#aior_reservation_form_' + aid );
										if (parseInt( app_limit ) < parseInt( user_count ) && parseInt( app_limit ) != 0) {
											Swal.fire( { icon: 'warning', text: "Sorry, but you've hit the appointment limit. Each user may only book " + app_limit + " appointments at a time." } );
										}
										if (sps == 0 || sps < 0) {
											Swal.fire( { icon: 'error', text: 'No space available!' } ) } else {
																h  = '<input type="hidden" name="sid" value="' + sid + '">';
																h += '<input type="hidden" name="fid" value="' + aid + '">'; // form_id
																h += '<input type="hidden" name="tid" value="' + tid + '">'; // task_id
																h += '<input type="hidden" name="tno" value="' + tno + '">'; // task_no
																h += '<input type="hidden" name="tdt" value="' + tdt + '">'; // task_date
																h += '<input type="hidden" name="stp" value="' + stp + '">'; // slot type
																h += '<input type="hidden" name="pri" value="' + pri + '">'; // slot price
																h += '<input type="hidden" name="aior_post_type" value="sol_appointment_list">';
																h += '<input type="hidden" name="response_in" value="lb">';
																rs = '#rf_slot';
																fm.find( rs ).attr( 'value', '1' );
																fm.find( rs ).attr( 'min', '1' );
																fm.find( rs ).attr( 'max', sps );
																fm.append( h );
																$( '.sol_ap_s_date' ).remove();
											}
											if (typeof grecaptcha === undefined) {
												grecaptcha.render( 'aior_reservation_captcha', { 'sitekey': aior_recaptcha.sitekey, 'theme': aior_recaptcha.theme, 'hl': aior_recaptcha.hl } );
											}

									}
								},
								(fd)
							);

						}
					}
				);

			}
		);

	}
};
jQuery( document ).ready(
	function() {
		(function($) {
			airoAppointment.init();
		}(jQuery))
	}
);
