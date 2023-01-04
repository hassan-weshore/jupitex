"use strict";

jQuery(window).on('elementor/frontend/init', function () {
	elementorFrontend.hooks.addAction('frontend/element_ready/thegem-countdown.default', function ($scope, $) {

		function countdown_init_time(timestamp) {

			var now = new Date();
			var currentTime = now.getTime();
			var eventTime = timestamp * 1000;

			if ((eventTime - currentTime) < 0) {
				eventTime = currentTime;
			}

			var remTime = eventTime - currentTime;
			var s = Math.floor(remTime / 1000);
			var m = Math.floor(s / 60);
			var h = Math.floor(m / 60);
			var d = Math.floor(h / 24);

			h %= 24;
			m %= 60;
			s %= 60;

			h = h + 100;
			m = m + 100;
			s = s + 100;

			return [d, h, m, s];

		}

		function countdown_init_circle_time(timestamp, startEventDate) {

			var now = new Date();
			var currentTime = now.getTime();
			var eventTime = timestamp * 1000;
			var dd = Math.floor(Math.abs(eventTime - startEventDate * 1000) / 1000 / 24 / 60 / 60);

			if ((eventTime - currentTime) < 0) {
				eventTime = currentTime;
			}

			var remTime = eventTime - currentTime;
			var s = Math.floor(remTime / 1000);
			var m = Math.floor(s / 60);
			var h = Math.floor(m / 60);
			var d = Math.floor(h / 24);

			h %= 24;
			m %= 60;
			s %= 60;

			h = h + 100;
			m = m + 100;
			s = s + 100;

			return [d, h, m, s, dd];

		}

		function circle_path(value, total, radius, weight) {
			radius = parseInt(radius);
			weight = parseInt(weight);

			var alpha = 360 / total * value,
				R = radius - weight / 2,
				a = (90 - alpha) * Math.PI / 180,
				x = radius + R * Math.cos(a),
				y = radius - R * Math.sin(a),
				path;

			if (total === value) {
				path = [["M", radius, radius - R], ["A", R, R, 0, 1, 1, radius - 0.01, radius - R]];
			} else {
				path = [["M", radius, radius - R], ["A", R, R, 0, +(alpha > 180), 1, x, y]];
			}
			return path;
		}

		function rdraw_days($rCanvas, elCD, elAllCD) {
			$rCanvas.layerDays.animate({arc: [(elCD), elAllCD, $rCanvas.colorDays]}, 1000, "linear");
		}

		function rdraw_hours($rCanvas, elCH) {
			$rCanvas.layerHours.animate({arc: [(elCH - 100), 24, $rCanvas.colorHours]}, 1000, "linear");
		}

		function rdraw_minutes($rCanvas, elCM) {
			$rCanvas.layerMinutes.animate({arc: [(elCM - 100), 60, $rCanvas.colorMinutes]}, 1000, "linear");
		}

		function rdraw_seconds($rCanvas, elCS) {
			if (elCS === 100)
				elCS = 161;
			$rCanvas.layerSeconds.animate({arc: [(elCS - 101), 60, $rCanvas.colorSeconds]}, 1000, "linear");
		}

		function countdown(elem, odometerDays, odometerHours, odometerMinutes, odometerSeconds, $rCanvas) {

			var elEventDate = elem.attr('data-eventdate'),
				elD = countdown_init_time(elEventDate)[0],
				elH = countdown_init_time(elEventDate)[1],
				elM = countdown_init_time(elEventDate)[2],
				elS = countdown_init_time(elEventDate)[3];

			if (elem.hasClass('countdown-style-5')) {
				var elStartEventDate = elem.attr('data-starteventdate'),
					elCD = countdown_init_circle_time(elEventDate, elStartEventDate)[0],
					elCH = countdown_init_circle_time(elEventDate, elStartEventDate)[1],
					elCM = countdown_init_circle_time(elEventDate, elStartEventDate)[2],
					elCS = countdown_init_circle_time(elEventDate, elStartEventDate)[3],
					elAllCD = countdown_init_circle_time(elEventDate, elStartEventDate)[4];
			}

			if ($('.countdown-days', elem) && odometerDays) {
				$('.countdown-days', elem).text(elD);
				odometerDays.value = elD;
			}
			if ($('.countdown-hours', elem) && odometerHours) {
				$('.countdown-hours', elem).text(elH);
				odometerHours.value = elH;
			}
			if ($('.countdown-minutes', elem) && odometerMinutes) {
				$('.countdown-minutes', elem).text(elM);
				odometerMinutes.value = elM;
			}
			if ($('.countdown-seconds', elem) && odometerSeconds) {
				$('.countdown-seconds', elem).text(elS);
				odometerSeconds.value = elS;
			}

			setTimeout(function () {
				if (elem.hasClass('countdown-style-5')) {
					rdraw_days($rCanvas, elCD, elAllCD);
					rdraw_hours($rCanvas, elCH);
					rdraw_minutes($rCanvas, elCM);
					rdraw_seconds($rCanvas, elCS);
				}
				countdown(elem, odometerDays, odometerHours, odometerMinutes, odometerSeconds, $rCanvas);
			}, 1000);
		}

		$scope.find('.countdown-container').each(function () {

			if ($(this).data('initialized')) {
				return;
			}
			$(this).data('initialized', true);

			var odometerDays = null,
				odometerHours = null,
				odometerMinutes = null,
				odometerSeconds = null, $el;
			var $glEvent = $(this);
			var glEventDate = $glEvent.attr('data-eventdate');

			if ($glEvent.hasClass('countdown-style-1') || $glEvent.hasClass('countdown-style-3') || $glEvent.hasClass('countdown-style-4') || $glEvent.hasClass('countdown-style-7')) {
				$el = $('.countdown-days', $glEvent).get(0);
				if ($el) {
					odometerDays = new Odometer({auto: false, el: $el, duration: 1000, theme: 'minimal'});
				}
				if ($glEvent.hasClass('countdown-style-1') || $glEvent.hasClass('countdown-style-3') || $glEvent.hasClass('countdown-style-4')) {
					$el = $('.countdown-hours', $glEvent).get(0);
					if ($el) odometerHours = new Odometer({auto: false, el: $el, duration: 1000, theme: 'minimal'});
					$el = $('.countdown-minutes', $glEvent).get(0);
					if ($el) odometerMinutes = new Odometer({auto: false, el: $el, duration: 1000, theme: 'minimal'});
					$el = $('.countdown-seconds', $glEvent).get(0);
					if ($el) odometerSeconds = new Odometer({auto: false, el: $el, duration: 1000, theme: 'minimal'});
				}
				countdown($glEvent, odometerDays, odometerHours, odometerMinutes, odometerSeconds, '');
			}

			if ($glEvent.hasClass('countdown-style-6')) {
				var glD = countdown_init_time(glEventDate)[0];
				var glH = countdown_init_time(glEventDate)[1];
				var glM = countdown_init_time(glEventDate)[2];
				var glS = countdown_init_time(glEventDate)[3];

				$el = $('.countdown-days', $glEvent).get(0);
				if ($el) odometerDays = new Odometer({auto: false, el: $el, value: glD, duration: 1000, theme: 'minimal'});
				$el = $('.countdown-hours', $glEvent).get(0);
				if ($el) odometerHours = new Odometer({auto: false, el: $el, value: glH, duration: 1000, theme: 'minimal'});
				$el = $('.countdown-minutes', $glEvent).get(0);
				if ($el) odometerMinutes = new Odometer({auto: false, el: $el, value: glM, duration: 1000, theme: 'minimal'});
				$el = $('.countdown-seconds', $glEvent).get(0);
				if ($el) odometerSeconds = new Odometer({auto: false, el: $el, value: glS, duration: 1000, theme: 'minimal'});
				countdown($glEvent, odometerDays, odometerHours, odometerMinutes, odometerSeconds, '');
			}

			if ($glEvent.hasClass('countdown-style-5')) {
				var glStartEventDate = $glEvent.attr('data-starteventdate'),
					glNumberWeight = $glEvent.attr('data-weightnumber'),
					glColorDays = $glEvent.attr('data-colordays'),
					glColorHours = $glEvent.attr('data-colorhours'),
					glColorMinutes = $glEvent.attr('data-colorminutes'),
					glColorSeconds = $glEvent.attr('data-colorseconds'),
					elCD = countdown_init_circle_time(glEventDate, glStartEventDate)[0],
					elCH = countdown_init_circle_time(glEventDate, glStartEventDate)[1],
					elCM = countdown_init_circle_time(glEventDate, glStartEventDate)[2],
					elCS = countdown_init_circle_time(glEventDate, glStartEventDate)[3],
					elAllCD = countdown_init_circle_time(glEventDate, glStartEventDate)[4];

				$el = $('.countdown-days', $glEvent).get(0);
				if ($el) odometerDays = new Odometer({auto: false, el: $el, duration: 1000, theme: 'minimal'});
				$el = $('.countdown-hours', $glEvent).get(0);
				if ($el) odometerHours = new Odometer({auto: false, el: $el, duration: 1000, theme: 'minimal'});
				$el = $('.countdown-minutes', $glEvent).get(0);
				if ($el) odometerMinutes = new Odometer({auto: false, el: $el, duration: 1000, theme: 'minimal'});
				$el = $('.countdown-seconds', $glEvent).get(0);
				if ($el) odometerSeconds = new Odometer({auto: false, el: $el, duration: 1000, theme: 'minimal'});

				var daysCirclesSize = $glEvent.attr('data-days-circles-size'),
					hoursCirclesSize = $glEvent.attr('data-hours-circles-size'),
					minutesCirclesSize = $glEvent.attr('data-minutes-circles-size'),
					secondsCirclesSize = $glEvent.attr('data-seconds-circles-size');

				var rCanvasDays = new Raphael($('.circle-raphael-days', $glEvent).get(0), daysCirclesSize * 2, daysCirclesSize * 2),
					rCanvasHours = new Raphael($('.circle-raphael-hours', $glEvent).get(0), hoursCirclesSize * 2, hoursCirclesSize * 2),
					rCanvasMinutes = new Raphael($('.circle-raphael-minutes', $glEvent).get(0), minutesCirclesSize * 2, minutesCirclesSize * 2),
					rCanvasSeconds = new Raphael($('.circle-raphael-seconds', $glEvent).get(0), secondsCirclesSize * 2, secondsCirclesSize * 2),
					paramDays = {stroke: glColorDays, "stroke-width": glNumberWeight},
					paramHours = {stroke: glColorHours, "stroke-width": glNumberWeight},
					paramMinutes = {stroke: glColorMinutes, "stroke-width": glNumberWeight},
					paramSeconds = {stroke: glColorSeconds, "stroke-width": glNumberWeight};

				var rCanvasDaysBase = rCanvasDays.circle(daysCirclesSize, daysCirclesSize, daysCirclesSize - glNumberWeight / 2);
				rCanvasDaysBase.attr({"stroke-width": glNumberWeight});
				rCanvasDaysBase.node.setAttribute('class', 'base-circle');
				var rCanvasDaysCircle = rCanvasDays.circle(daysCirclesSize, daysCirclesSize, daysCirclesSize - glNumberWeight);
				rCanvasDaysCircle.attr({stroke: 'transparent'});
				rCanvasDaysCircle.node.setAttribute('class', 'inner-circle');


				var rCanvasHoursBase = rCanvasHours.circle(hoursCirclesSize, hoursCirclesSize, hoursCirclesSize - glNumberWeight / 2);
				rCanvasHoursBase.attr({"stroke-width": glNumberWeight});
				rCanvasHoursBase.node.setAttribute('class', 'base-circle');
				var rCanvasHoursCircle = rCanvasHours.circle(hoursCirclesSize, hoursCirclesSize, hoursCirclesSize - glNumberWeight);
				rCanvasHoursCircle.attr({stroke: 'transparent'});
				rCanvasHoursCircle.node.setAttribute('class', 'inner-circle');

				var rCanvasMinutesBase = rCanvasMinutes.circle(minutesCirclesSize, minutesCirclesSize, minutesCirclesSize - glNumberWeight / 2);
				rCanvasMinutesBase.attr({"stroke-width": glNumberWeight});
				rCanvasMinutesBase.node.setAttribute('class', 'base-circle');
				var rCanvasMinutesCircle = rCanvasMinutes.circle(minutesCirclesSize, minutesCirclesSize, minutesCirclesSize - glNumberWeight);
				rCanvasMinutesCircle.attr({stroke: 'transparent'});
				rCanvasMinutesCircle.node.setAttribute('class', 'inner-circle');

				var rCanvasSecondsBase = rCanvasSeconds.circle(secondsCirclesSize, secondsCirclesSize, secondsCirclesSize - glNumberWeight / 2);
				rCanvasSecondsBase.attr({"stroke-width": glNumberWeight});
				rCanvasSecondsBase.node.setAttribute('class', 'base-circle');
				var rCanvasSecondsCircle = rCanvasSeconds.circle(secondsCirclesSize, secondsCirclesSize, secondsCirclesSize - glNumberWeight);
				rCanvasSecondsCircle.attr({stroke: 'transparent'});
				rCanvasSecondsCircle.node.setAttribute('class', 'inner-circle');

				rCanvasDays.customAttributes.arc = function (value, total) {
					return {path: circle_path(value, total, daysCirclesSize, glNumberWeight)};
				};
				rCanvasHours.customAttributes.arc = function (value, total) {
					return {path: circle_path(value, total, hoursCirclesSize, glNumberWeight)};
				};
				rCanvasMinutes.customAttributes.arc = function (value, total) {
					return {path: circle_path(value, total, minutesCirclesSize, glNumberWeight)};
				};
				rCanvasSeconds.customAttributes.arc = function (value, total) {
					return {path: circle_path(value, total, secondsCirclesSize, glNumberWeight)};
				};

				var $rCanvas = {
					rCanvasDays: rCanvasDays,
					rCanvasHours: rCanvasHours,
					rCanvasMinutes: rCanvasMinutes,
					rCanvasSeconds: rCanvasSeconds,
					layerDays: rCanvasDays.path().attr(paramDays).attr({arc: [0, elAllCD]}),
					layerHours: rCanvasHours.path().attr(paramHours).attr({arc: [0, 24]}),
					layerMinutes: rCanvasMinutes.path().attr(paramMinutes).attr({arc: [0, 60]}),
					layerSeconds: rCanvasSeconds.path().attr(paramSeconds).attr({arc: [0, 60]}),
					colorDays: glColorDays,
					colorHours: glColorHours,
					colorMinutes: glColorMinutes,
					colorSeconds: glColorSeconds
				};


				$rCanvas.layerDays.animate({arc: [elCD, elAllCD]}, 1000, "linear");
				$rCanvas.layerHours.animate({arc: [elCH - 100, 24]}, 1000, "linear");
				$rCanvas.layerMinutes.animate({arc: [elCM - 100, 60]}, 1000, "linear");
				$rCanvas.layerSeconds.animate({arc: [elCS - 100, 60]}, 1000, "linear");

				countdown($glEvent, odometerDays, odometerHours, odometerMinutes, odometerSeconds, $rCanvas);
			}

		});
	});
});