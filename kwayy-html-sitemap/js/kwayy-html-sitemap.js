function kwayyhs_encodeHTML(dirtyString) {
	var container = document.createElement('div');
	var text = document.createTextNode(dirtyString);
	container.appendChild(text);
	return container.innerHTML;
}

jQuery(document).ready(function($){
	$(document).on('click', '.kwayyhs-welcome-notice .notice-dismiss', function() {
		var $notice = $(this).closest('.kwayyhs-welcome-notice');
		var nonce = $notice.data('nonce') || (typeof kwayyhs_vars !== 'undefined' ? kwayyhs_vars.nonce : '');
		var ajax_url = (typeof kwayyhs_vars !== 'undefined' && kwayyhs_vars.ajax_url) ? kwayyhs_vars.ajax_url : (typeof ajaxurl !== 'undefined' ? ajaxurl : '');
		if (ajax_url) {
			$.post(ajax_url, {
				action: 'kwayyhs_dismiss_welcome_notice',
				nonce: nonce
			});
		}
	});

	var $excludeSelect = $('#kwayyhs-exclude');

	function formatSelect2Item(option) {
		if (!option.id) {
			return option.text;
		}
		var $element = $(option.element);
		var iconClass = $element.data('icon');
		if (!iconClass || !/^dashicons-[a-z0-9-]+$/.test(iconClass)) {
			iconClass = (option.id && option.id.toString().indexOf('term_') === 0) ? 'dashicons-tag' : 'dashicons-admin-post';
		}
		var $span = $('<span></span>');
		var $icon = $('<span></span>').addClass('dashicons ' + iconClass + ' kwayyhs-select2-icon');
		$span.append($icon).append(document.createTextNode(' ' + option.text));
		return $span;
	}

	// Initialize Select2 on Exclude Post select box
	if ($excludeSelect.length && typeof $.fn.select2 !== 'undefined') {
		$excludeSelect.select2({
			placeholder: $excludeSelect.attr('data-placeholder') || 'Search and select posts or terms to exclude...',
			allowClear: true,
			width: '100%',
			closeOnSelect: false,
			templateResult: formatSelect2Item,
			templateSelection: formatSelect2Item,
			escapeMarkup: function(m) { return m; }
		});
	}

	// Function to filter Select2 options based on checked CPT checkboxes
	function updateExcludeOptions() {
		if (!$excludeSelect.length) return;

		// Get array of active (checked) CPT names
		var activeCPTs = [];
		$('#kwayyhs-sortable input[type="checkbox"]:checked').each(function() {
			var name = $(this).attr('name') || '';
			if (name.indexOf('kwayyhs_active_') === 0) {
				var key = name.replace('kwayyhs_active_', '');
				if (key) {
					activeCPTs.push(key);
					if (key.indexOf('cpt_') === 0) {
						activeCPTs.push(key.replace('cpt_', ''));
					}
				}
			}
		});

		// Loop through optgroups and options
		$excludeSelect.find('optgroup').each(function() {
			var $group = $(this);
			var cpt = $group.attr('data-cpt');
			var isActive = activeCPTs.indexOf(cpt) !== -1;

			if (isActive) {
				$group.prop('disabled', false);
				$group.find('option').each(function() {
					$(this).prop('disabled', false);
				});
			} else {
				$group.prop('disabled', true);
				$group.find('option').each(function() {
					$(this).prop('disabled', true);
					$(this).prop('selected', false);
				});
			}
		});

		// Refresh Select2
		if (typeof $.fn.select2 !== 'undefined') {
			$excludeSelect.trigger('change.select2');
		}
	}

	// Initial update on page load
	updateExcludeOptions();

	// Trigger update when CPT checkbox changes
	$(document).on('change', '#kwayyhs-sortable input[type="checkbox"]', function() {
		updateExcludeOptions();
	});

	// jQuery UI Sortable setup
	if ($('#kwayyhs-sortable').length) {
		$('#kwayyhs-sortable').sortable({
			handle: '.kwayyhs-dragable-handler',
			placeholder: 'kwayyhs-ui-state-highlight',
			update: function(event, ui) {
				var fruitOrder = $('#kwayyhs-sortable').sortable('toArray').toString();
				$('#kwayyhs-sortorder').val(fruitOrder);
			}
		});
	}

	// Change title toggle
	$(document).on('click', '.kwayyhs_changename, .kwayyhs_changename a', function(e){
		e.preventDefault();
		e.stopPropagation();
		$('.kwayyhs-newname').fadeOut(100);
		var $popover = $(this).closest('.kwayyhs-cpt-name').find('.kwayyhs-newname');
		$popover.fadeIn(150, function() {
			$popover.find('input').focus().select();
		});
		return false;
	});

	// Prevent popover clicks from bubbling to document
	$(document).on('click', '.kwayyhs-newname', function(e) {
		e.stopPropagation();
	});

	// Hide popovers when clicking anywhere outside
	$(document).on('click', function() {
		$('.kwayyhs-newname').fadeOut(100);
	});

	$(document).on('click', 'button.kwayy-save-newname, a.kwayy-save-newname', function(e){
		e.preventDefault();
		var $popover = $(this).closest('.kwayyhs-newname');
		var $input = $popover.find('input');
		var val = $.trim($input.val());
		var $cptRow = $(this).closest('.kwayyhs-cpt-row, .kwayyhs-cpt');
		var origName = $cptRow.find('.kwayyhs-originalname').text();

		if( val === '' ){
			val = origName;
			$input.val(val);
		}

		$cptRow.find('.kwayyhs-cpt-name-title').text( val );
		$popover.fadeOut(150);
		return false;
	});

	$(document).on('click', 'button.kwayy-cancel-newname, a.kwayy-cancel-newname', function(e){
		e.preventDefault();
		var $popover = $(this).closest('.kwayyhs-newname');
		var $cptRow = $(this).closest('.kwayyhs-cpt-row, .kwayyhs-cpt');
		var currentTitle = $cptRow.find('.kwayyhs-cpt-name-title').text();
		$popover.find('input').val( currentTitle );
		$popover.fadeOut(150);
		return false;
	});
});
