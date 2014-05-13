function initSourceCode(lang, themeName) {
	var aliases = {
		javascript: ['js'],
		c_cpp: ['c', 'cpp']
	};

	var selectors = [lang];

	if (aliases[lang]) {
		selectors = selectors.concat(aliases[lang]);
	}

	var cssSelectors = selectors.map(function (sel) {
		return 'pre.' + sel + ' code';
	}).join(',');

	$(cssSelectors).each(function () {
		var el = this;
		ace.require(["ace/ext/static_highlight", "ace/mode/" + lang, "ace/theme/" + themeName, "ace/lib/dom"], function() {
			var highlighter = ace.require("ace/ext/static_highlight");
			var mode = ace.require("ace/mode/" + lang).Mode;
			var theme = ace.require("ace/theme/" + themeName);
			var dom = ace.require("ace/lib/dom");

			var data = el.innerText;

			var highlighted = highlighter.render(data, new mode(), theme);

			dom.importCssString(highlighted.css, "ace_highlight");
			el.innerHTML = highlighted.html;
		});
	});
}