$(document).ready(function () {
	if (!$.cookie('smartCookies')) {

		$(document).one('mouseleave', function (e) {
			function getWindow() {
				$('.offer').arcticmodal({
					closeOnOverlayClick: true,
					closeOnEsc: true
				});
			};
			setTimeout(getWindow, 1);
			$.cookie('smartCookies', true, {
				expires: 7,
				path: '/'
			});
		});

	};
});
