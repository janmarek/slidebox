ko.bindingHandlers.showNth = {
	init: function (el, valueAccessor) {
		$(el).find('> *').hide();
	},
	update: function (el, valueAccessor) {
		var index = valueAccessor();
		var $el = $(el);
		$el.find('> :visible').hide();
		$el.find('> *').eq(index).show();
	}
};