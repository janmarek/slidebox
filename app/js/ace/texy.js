define(function (require, exports, module) {
	var oop = require("../lib/oop");
	var TextMode = require("./text").Mode;
	var JavaScriptMode = require("./javascript").Mode;
	var XmlMode = require("./xml").Mode;
	var HtmlMode = require("./html").Mode;
	var TexyHighlightRules = require("./texy_highlight_rules").TexyHighlightRules;
	var TextHighlightRules = require("./text_highlight_rules").TextHighlightRules;

	var Mode = function () {
		this.HighlightRules = TexyHighlightRules;

		this.createModeDelegates({
			"js-": JavaScriptMode,
			"xml-": XmlMode,
			"html-": HtmlMode,
			"text-": TextMode
		});
	};
	oop.inherits(Mode, TextMode);

	exports.Mode = Mode;
});