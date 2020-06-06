/* globals Chart:false, feather:false */

// Replace all feather icon definitions with actual icon
$(function () {
	'use strict';

	feather.replace();
})();

// Enable tooltips
$(function () {
	$('[data-toggle="tooltip"]').tooltip();
});
