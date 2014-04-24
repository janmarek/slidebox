ko.bindingHandlers.files = {
	init: function (el, valueAccessor) {
		var value = valueAccessor();
		el.addEventListener('change', function () {
			value(el.files);
		});
	},
	update: function (el, valueAccessor) {
		var value = valueAccessor();
		if (value() === null) {
			el.value = null;
		}
	}
};