$(document).ready(function (e) {
	$('.sh_nmr').click(function () {
		if ($('.num_hide').is(':hidden')) {
			$('.num_hide').show();
			$('.sh_nmr').hide();
		} else {
			$('.num_hide').hide();
		}
	});
	$('.sh_nmr_01').click(function () {
		if ($('.num_hide_01').is(':hidden')) {
			$('.num_hide_01').show();
			$('.sh_nmr_01').hide();
		} else {
			$('.num_hide_01').hide();
		}
	});
	$('.sh_nmr_02').click(function () {
		if ($('.num_hide_02').is(':hidden')) {
			$('.num_hide_02').show();
			$('.sh_nmr_02').hide();
		} else {
			$('.num_hide_02').hide();
		}
	});
	$('.btn-menu, .hidden-menu ul a').click(function () {
		if ($('.hidden-menu').is(':hidden')) {
			$('.hidden-menu').show();
		} else {
			$('.hidden-menu').hide();
		}
	});
	$("a[rel='m_PageScroll2id']").mPageScroll2id({
		offset: 120
	});
	$('.phone').inputmask("+7(999) 999-99-99");
	$("#owl-demo").owlCarousel({
		autoPlay: 10000,
		items: 1,
		itemsDesktop: [1199, 1],
		itemsDesktopSmall: [979, 1],
		itemsTablet: [768, 1],
		itemsMobile: [479, 1],
		loop: true,
		autoWidth: true,
		navigation: true,
		navigationText: ['<i class="fa fa-angle-left fa-2x" aria-hidden="true"></i>', '<i class="fa fa-angle-right fa-2x" aria-hidden="true"></i>'],
		rewindNav: true,
		pagination: true,
		rewindSpeed: 100
	});
	$("#owl-demo-galery").owlCarousel({
		autoPlay: 10000,
		items: 3,
		itemsDesktop: [1199, 3],
		itemsDesktopSmall: [979, 2],
		loop: true,
		navigation: true,
		navigationText: ['<i class="fa fa-angle-left fa-2x" aria-hidden="true"></i>', '<i class="fa fa-angle-right fa-2x" aria-hidden="true"></i>'],
		rewindNav: true,
		pagination: true,
		rewindSpeed: 100
	});
	//	calculator start
	$('#input_number').on('keydown', function (e) {
		if (e.key.length == 1 && e.key.match(/[^0-9'".,]/)) {
			return false;
		};
		$('.calc input, .calc select').change(function () {
			$dlinna = $('#input_number').val();
			$shirina = $('#input_number_2').val();
			$tkan = $('select#select_calc option:selected').val();
			$result = $dlinna * $shirina * $tkan;
			if ($('#input_number_3').is(':checked')) {
				$result += parseFloat($('#input_number_3').val()) * $dlinna;
			};
			if ($('#input_number_4').is(':checked')) {
				$result += parseFloat($('#input_number_4').val()) * ($dlinna / 2);
			};
			$('#itog').text(Math.round($result));
			console.log($tkan);
		});
	});
	$('.input-number').focusin(function () {
		$('#long').animate({
			'opacity': 'show'
		}, 300);
		$('#car').animate({
			'opacity': '0.3'
		}, 300);
	});
	$('.input-number-2').focusin(function () {
		$('#width').animate({
			'opacity': 'show'
		}, 300);
		$('#car').animate({
			'opacity': '0.3'
		}, 300);
	});
	$('.input-number-3').on('click', function () {
		if ($(this).is(':checked')) {
			$('#truba').animate({
				'opacity': 'show'
			}, 300);
			$('#car').animate({
				'opacity': '0.3'
			}, 300);
		} else {
			$('#truba').hide();
		};
	});
	$('.input-number-4').on('click', function () {
		if ($(this).is(':checked')) {
			$('#treshetki').animate({
				'opacity': 'show'
			}, 300);
			$('#car').animate({
				'opacity': '0.3'
			}, 300);
		} else {
			$('#treshetki').hide();
		};
	});
	$('.openButton, .close, #calc_buttom').click(function () {
		if (!$('.openButton').hasClass('openDone')) {
			$('.openButton').addClass('openDone');
			$('.calc').css("left", "0px");
			$('.blur').fadeIn(300);
		} else {
			$('.openButton').removeClass('openDone');
			$('.calc').css("left", "-999px");
			$('.blur').fadeOut(300);
		}
	});
	//	calculator end
});
