;
'use strict';
var airoAppointment = {
	init: function() {
		this.switch_slot_builder();
		this.add_slot_weekly();
		this.add_slot_monthly();
		this.init_time_field();
		// this.init_date_field();
		this.booked_appointmetn_act();
	},
	uid: function() { return Math.floor( Math.random() * (100000 - 1 + 1) + 57 ) },
	loader: function() { Swal.fire( { allowOutsideClick: false, showConfirmButton: false, didOpen: () => { Swal.showLoading() }  } ) },
	switch_slot_builder: function() {
		t = 'input[name="slot_type"]';
		v = $( t + ':checked' ).val();
		w = $( '.slot_weekly_cnt' );
		m = $( '.slot_monthly_cnt' );
		w.hide();
		m.hide();
		if (v == 1) {
			w.show() } else {
			m.show() }
			$( document ).on(
				'change',
				t,
				function() {
					n = $( this ).val();
					w.hide();
					m.hide();
					if (n == 1) {
						w.show() } else {
									m.show() }
				}
			);
	},
	booked_appointmetn_act: function() {
		$( document ).on(
			'click',
			'.booke-appointment-act',
			function() {
				todo  = $( this ).data( 'act' );
				pid   = $( this ).data( 'pid' );
				nonce = $( '#aior_admin_global_nonce' ).val();
				td    = $( this ).parents( 'td' );
				$.ajax(
					{
						type: 'POST',
						cache: false,
						url: ajaxurl,
						data: { 'action': 'aoir_reservation_global_ajax', 'act': 'do_booked_appointment_act', 'todo': todo, 'pid': pid, 'nonce': nonce },
						beforeSend: function() { airoAppointment.loader() },
						success: function(r) {
							swal.close();
							if (r) {
								j = JSON.parse( r );
								if (j.act == 'approved') {
									td.find( '.approve-btn' ).hide();
									td.find( '.deny-btn' ).show();
									Swal.fire( { icon: 'success', text: j.message } )
								} else if (j.act == 'declined') {
									td.find( '.deny-btn' ).hide();
									td.find( '.approve-btn' ).show();
									Swal.fire( { icon: 'info', text: j.message } )
								}
							}
						}
					}
				);
			}
		)
	},
	slot_html: function(d) { return h },
	label: function(v) { return '<label>' + v + '</label>' },
	slot_field: function() {
		ls = '<label>';
		le = '</label>';
		h  = '';
		h += ls + aior_obj.lang.title + le + '<input class="slot_title" type="text" placeholder="' + aior_obj.lang.title + '">';
		h += ls + aior_obj.lang.desc + le + '<textarea class="slot_desc" type="text"  placeholder="' + aior_obj.lang.desc + '"></textarea>';
		h += ls + aior_obj.lang.start_time + le + '<input class="slot_start timepicker" type="text" placeholder="' + aior_obj.lang.start_time + '">';
		h += ls + aior_obj.lang.end_time + le + '<input class="slot_end timepicker" type="text" placeholder="' + aior_obj.lang.end_time + '">';
		h += ls + aior_obj.lang.duration + le + '<input class="slot_duration" type="number" placeholder="' + aior_obj.lang.duration + '" value="60" min="1">'
		h += ls + aior_obj.lang.interval + le + '<input class="slot_interval" type="number" placeholder="' + aior_obj.lang.interval + '" value="60" min="1">'
		h += ls + aior_obj.lang.space + le + '<input class="slot_space" type="number" placeholder="' + aior_obj.lang.space + '" value="1" min="1">';
		h += ls + aior_obj.lang.price + le + '<input class="slot_price" type="number" placeholder="' + aior_obj.lang.price + '">';
		return h;
	},
	add_slot_weekly: function() {
		$( document ).on(
			'click',
			'.aior_add_weekly_slot',
			function(a) {
				d  = $( this ).data( 'day' );
				h  = '<li class="temp_s_builder">';
				h += airoAppointment.slot_field();
				h += '<a class="button aior_slot_close">' + aior_obj.lang.close + '</a> <a class="button button-primary aior_slot_build_w" data-day="' + d + '">' + aior_obj.lang.build + '</a>';
				h += '</li>'
				$( this ).parents( '.aior_weekly_slot' ).find( 'ul' ).append( h );
				airoAppointment.init_time_field();
			}
		);
		/* Build Slot for Weekly */
		$( document ).on(
			'click',
			'.aior_slot_build_w',
			function() {
				uid      = airoAppointment.uid();
				t        = $( this );
				d        = $( this ).data( 'day' );
				title    = $( this ).parents( 'li' ).find( '.slot_title' ).val();
				desc     = $( this ).parents( 'li' ).find( '.slot_desc' ).val();
				start    = $( this ).parents( 'li' ).find( '.slot_start' ).val();
				end      = $( this ).parents( 'li' ).find( '.slot_end' ).val();
				duration = $( this ).parents( 'li' ).find( '.slot_duration' ).val();
				interval = $( this ).parents( 'li' ).find( '.slot_interval' ).val();
				space    = $( this ).parents( 'li' ).find( '.slot_space' ).val();
				price    = $( this ).parents( 'li' ).find( '.slot_price' ).val();
				nonce    = $( '#aior_admin_global_nonce' ).val();
				if ( ! title) {
					Swal.fire( { icon: 'error', text: 'Add task title!' } ); return false }
				if ( ! start) {
					Swal.fire( { icon: 'error', text: 'Add your start day!' } ); return false }
				if ( ! end) {
					Swal.fire( { icon: 'error', text: 'Add your end day!' } ); return false }
				$.ajax(
					{
						type: 'POST',
						cache: false,
						url: ajaxurl,
						data: { 'action': 'aoir_reservation_global_ajax', 'act': 'aior_generate_day_slot', 'day': d, 'title': title, 'desc': desc, 'start': start, 'end': end, 'duration': duration, 'interval': interval, 'space': space, 'price': price, 'nonce': nonce },
						beforeSend: function() { airoAppointment.loader() },
						success: function(re) {
							swal.close();
							if (re) {
								ar = JSON.parse( re );
								if (ar.length > 0) {
									for (i = 0; i < ar.length; i++) {
										from = ar[i][0];
										to   = ar[i][1];
										sid  = airoAppointment.uid();
										h    = '<li class="slot_weekly">';
										h   += '<b>' + title + '</b><br>';
										h   += '<small>' + desc + '</small><br>';
										h   += '<input class="slot_id" type="hidden" name="slot_weekly[' + d + '][' + uid + '][' + i + '][q]" value="' + sid + '">';
										h   += '<input class="slot_title" type="hidden" name="slot_weekly[' + d + '][' + uid + '][' + i + '][t]" value="' + title + '">';
										h   += '<input class="slot_desc" type="hidden" name="slot_weekly[' + d + '][' + uid + '][' + i + '][d]" value="' + desc + '">';
										h   += '<div class="solrow">';
										h   += '<div class="solcol"><label>' + aior_obj.lang.start_time + '</label><input class="slot_start timepicker" type="text" name="slot_weekly[' + d + '][' + uid + '][' + i + '][st]" placeholder="' + aior_obj.lang.start_time + '" value="' + from + '"></div>';
										h   += '<div class="solcol"><label>' + aior_obj.lang.end_time + '</label><input class="slot_end timepicker" type="text" name="slot_weekly[' + d + '][' + uid + '][' + i + '][et]" placeholder="' + aior_obj.lang.end_time + '"  value="' + to + '"></div>';
										h   += '</div>';
										h   += '<label>' + aior_obj.lang.space + '</label><input class="slot_space" type="number" name="slot_weekly[' + d + '][' + uid + '][' + i + '][s]" placeholder="' + aior_obj.lang.space + '" value="' + space + '" min="1">';
										h   += '<label>' + aior_obj.lang.price + '</label><input class="slot_price" type="number" name="slot_weekly[' + d + '][' + uid + '][' + i + '][p]" placeholder="' + aior_obj.lang.price + '" value="' + price + '">';

										h += '<a class="button aior_slot_close">' + aior_obj.lang.close + '</a>';
										h += '</li>';
										t.parents( '.aior_weekly_slot' ).find( 'ul' ).append( h );
										airoAppointment.init_time_field();
									}
									t.parents( '.aior_weekly_slot' ).find( '.temp_s_builder' ).remove();
								}
							}
						}
					}
				);
			}
		);
		$( document ).on( 'click', '.aior_slot_close', function() { $( this ).parents( 'li' ).remove() } );
	},

	add_slot_monthly: function() {
		$( document ).on(
			'click',
			'.aior_add_monthly_slot',
			function() {
				bid = airoAppointment.uid();
				ls  = '<label>';
				le  = '</label> ';
				h   = '<li class="temp_s_builder tsbid' + bid + '">';
				h  += ls + aior_obj.lang.start_date + le + '<input class="slot_start_date datepicker" type="text" placeholder="' + aior_obj.lang.start_date + '">';
				h  += ls + aior_obj.lang.end_date + le + '<input class="slot_end_date datepicker" type="text" placeholder="' + aior_obj.lang.end_date + '"><br>';
				h  += airoAppointment.slot_field();
				h  += '<a class="button aior_slot_close">' + aior_obj.lang.close + '</a> <a class="button button-primary aior_slot_build_m">' + aior_obj.lang.build + '</a>';
				h  += '</li>';
				$( this ).parents( '.slot_monthly' ).find( 'ul' ).append( h );
				airoAppointment.init_time_field();
				airoAppointment.init_date_field( '.tsbid' + bid );
			}
		);

		/* Build Custom Slot for Montly */
		$( document ).on(
			'click',
			'.aior_slot_build_m',
			function() {
				uid      = airoAppointment.uid();
				t        = $( this );
				start_d  = $( this ).parents( 'li' ).find( '.slot_start_date' ).val();
				end_d    = $( this ).parents( 'li' ).find( '.slot_end_date' ).val();
				title    = $( this ).parents( 'li' ).find( '.slot_title' ).val();
				desc     = $( this ).parents( 'li' ).find( '.slot_desc' ).val();
				start    = $( this ).parents( 'li' ).find( '.slot_start' ).val();
				end      = $( this ).parents( 'li' ).find( '.slot_end' ).val();
				duration = $( this ).parents( 'li' ).find( '.slot_duration' ).val();
				interval = $( this ).parents( 'li' ).find( '.slot_interval' ).val();
				space    = $( this ).parents( 'li' ).find( '.slot_space' ).val();
				price    = $( this ).parents( 'li' ).find( '.slot_price' ).val();
				nonce    = $( '#aior_admin_global_nonce' ).val();
				pid      = $( '#possible_post_id' ).val();
				if ( ! start_d) {
					Swal.fire( { icon: 'error', text: 'Add your start date!' } ); return false }
				if ( ! end_d) {
					Swal.fire( { icon: 'error', text: 'Add your end date!' } ); return false }
				if ( ! title) {
					Swal.fire( { icon: 'error', text: 'Add task title!' } ); return false }
				if ( ! start) {
					Swal.fire( { icon: 'error', text: 'Add your start time!' } ); return false }
				if ( ! end) {
					Swal.fire( { icon: 'error', text: 'Add your end time!' } ); return false }

				$.ajax(
					{
						type: 'POST',
						cache: false,
						url: ajaxurl,
						data: { 'action': 'aoir_reservation_global_ajax', 'act': 'aior_generate_montly_slot', 'start_d': start_d, 'end_d': end_d, 'title': title, 'desc': desc, 'start': start, 'end': end, 'duration': duration, 'interval': interval, 'space': space, 'price': price, 'nonce': nonce },
						beforeSend: function() { airoAppointment.loader() },
						success: function(re) {
							swal.close();
							if (re) {
								ar = JSON.parse( re );
								if (ar.length > 0) {
									h  = '<li class="slot_montly">';
									h += '<header>';
									h += '<h3>' + title + ' : <small>' + desc + '</small></h3>';
									h += start_d + ' to ' + end_d;
									h += '<a data-target="' + uid + '" data-pid="' + pid + '" href="javascript:void(0)" class="slot_m_remove"><span class="dashicons dashicons-trash"></span></a>';
									h += '</header>';

									for (i = 0; i < ar.length; i++) {
										from = ar[i][0];
										to   = ar[i][1];
										sid  = airoAppointment.uid();
										h   += '<div class="sm_slot sm_slot' + uid + '">';
										h   += '<div class="solrow">';
										h   += '<input class="slot_id" type="hidden" name="slot_monthly[' + uid + '][' + i + '][q]" value="' + sid + '">';
										h   += '<input class="slot_start timepicker" type="hidden" name="slot_monthly[' + uid + '][' + i + '][sd]" value="' + start_d + '">';
										h   += '<input class="slot_end timepicker" type="hidden" name="slot_monthly[' + uid + '][' + i + '][ed]" value="' + end_d + '">';
										h   += '<input class="slot_title" type="hidden" name="slot_monthly[' + uid + '][' + i + '][t]" value="' + title + '">';
										h   += '<input class="slot_desc" type="hidden" name="slot_monthly[' + uid + '][' + i + '][d]" value="' + desc + '">';

										h += '<div class="solcol"><label>' + aior_obj.lang.start_time + '</label><input class="slot_start timepicker" type="text" name="slot_monthly[' + uid + '][' + i + '][st]" placeholder="' + aior_obj.lang.start_time + '" value="' + from + '"></div>';
										h += '<div class="solcol"><label>' + aior_obj.lang.end_time + '</label><input class="slot_end timepicker" type="text" name="slot_monthly[' + uid + '][' + i + '][et]" placeholder="' + aior_obj.lang.end_time + '"  value="' + to + '"></div>';

										h += '<div class="solcol">';
										h += '<label>' + aior_obj.lang.space + '</label><input class="slot_space" type="number" name="slot_monthly[' + uid + '][' + i + '][s]" placeholder="' + aior_obj.lang.space + '" value="' + space + '" min="1">';
										h += '</div>';

										h += '<div class="solcol">';
										h += '<label>' + aior_obj.lang.price + '</label><input class="slot_price" type="number" name="slot_monthly[' + uid + '][' + i + '][p]" placeholder="' + aior_obj.lang.price + '" value="' + price + '">';
										h += '</div>';

										h += '<div class="solcol">';
										h += '<a class="button aior_slot_close_m">' + aior_obj.lang.close + '</a>';
										h += '</div>';

										h += '</div>';
										h += '</div>';

									}
									h += '</li>';
									t.parents( '.slot_monthly' ).find( 'ul' ).append( h );
									airoAppointment.init_time_field();
									t.parents( '.slot_monthly' ).find( '.temp_s_builder' ).remove();
								}
							}
						}
					}
				);
			}
		);
		$( document ).on(
			'click',
			'.slot_m_remove',
			function() {
				t     = $( this ).data( 'target' );
				i     = $( this ).data( 'pid' );
				nonce = $( '#aior_admin_global_nonce' ).val();
				g     = $( this );
				$.ajax(
					{
						type: 'POST',
						cache: false,
						url: ajaxurl,
						data: { 'action': 'aoir_reservation_global_ajax', 'act': 'aior_remove_montly_task', 'target': t, 'pid': i, 'nonce': nonce },
						beforeSend: function() { airoAppointment.loader() },
						success: function(re) {
							swal.close();
							if (re && re == 'removed') {
								g.parents( 'li' ).remove() }
						}
					}
				);
			}
		);
		$( document ).on( 'click', '.aior_slot_close_m', function() { $( this ).parents( '.sm_slot' ).remove() } );

	},

	init_time_field: function() {
		time_format   = aior_admin.time_format();
		time_interval = aior_admin.time_interval();
		$( '.slot_weekly li,.slot_monthly li' ).each(
			function(i) {
				st = $( this ).find( '.slot_start' );
				et = $( this ).find( '.slot_end' );
				st.timepicker(
					{
						timeFormat: time_format,
						interval: time_interval,
						change: function(time) {
							et.val( '' );
							et.timepicker( 'option', 'minTime', $( this ).val() )
						}
					}
				);
				et.timepicker( { timeFormat: time_format, interval: time_interval, change: function(time) {} } );
			}
		);
	},
	init_date_field: function(bid) {
		ssd    = '.slot_start_date';
		sed    = '.slot_end_date';
		var th = $( bid );
		th.find( ssd ).dateRangePicker(
			{
				autoClose: true,
				startDate: new Date(),
				separator: ' to ',
				getValue: function() {
					if (th.find( ssd ).val() && th.find( sed ).val()) {
						return th.find( ssd ).val() + ' to ' + th.find( sed ).val()
					} else {
						return '' }
				},
				setValue: function(s, s1, s2) {
					th.find( ssd ).val( s1 );
					th.find( sed ).val( s2 )
				}
			}
		);
	},

};

jQuery( document ).ready(
	function() {
		(function($) {
			airoAppointment.init();
		}(jQuery))
	}
);
