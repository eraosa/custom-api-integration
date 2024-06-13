jQuery(document).ready(function ($) {
	$('#custom-api-settings-form').on('submit', function (e) {
		var elementsField = $('#custom_api_elements');
		var elementsValue = elementsField.val().trim();

		if (elementsValue === '') {
			alert('Please enter a list of elements.');
			elementsField.focus();
			e.preventDefault();
			return false;
		}

		return true;
	});
});
