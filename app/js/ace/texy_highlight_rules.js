define(function (require, exports, module) {
	var oop = require("../lib/oop");
	var lang = require("../lib/lang");
	var TextHighlightRules = require("./text_highlight_rules").TextHighlightRules;
	var JavaScriptHighlightRules = require("./javascript_highlight_rules").JavaScriptHighlightRules;
	var XmlHighlightRules = require("./xml_highlight_rules").XmlHighlightRules;
	var HtmlHighlightRules = require("./html_highlight_rules").HtmlHighlightRules;
	var CssHighlightRules = require("./css_highlight_rules").CssHighlightRules;

	function code(tag, prefix) {
		return {
			token: "support.function",
			regex: "^/--code " + tag + "\\s*$",
			push: prefix + "start"
		};
	}

	var TexyHighlightRules = function() {
		HtmlHighlightRules.call(this);

		this.$rules.start.unshift(
			{
				token: "markup.heading",
				regex: /^[#\*=-]{3,}$/
			},
			code("(?:javascript|js)", "jscode-"),
			code("xml", "xmlcode-"),
			code("html", "htmlcode-"),
			code("css", "csscode-"),
			{
				token: "support.function",
				regex: "^/--code(?: \\S+)?$",
				push: "text-start"
			},
			{
				token: "support.function",
				regex: "^/--text$",
				push: "text-start"
			},
			{
				token: "support.function",
				regex: "^/--html$",
				push: "htmlcode-start"
			},
			{ // block quote
				token: "string.blockquote",
				regex: "^>\\s*(?:[*+-]|\\d+\\.)?\\s+"
			},
			{
				include: "basic"
			}
		);

		this.addRules({
			"basic": [
				{
					token: "constant.language.escape",
					regex: /\\[\\`*_{}\[\]()#+\-.!]/
				}, { // strong ***
					token: "emphasis.strong",
					regex: "(\\*\\*\\*)(\\S.*?\\S)(\\*\\*\\*)"
				}, { // strong **
					token: "strong",
					regex: "([*]{2}(?=\\S))(.*?\\S[*_]*)(\\1)"
				}, { // emphasis * _
					token: "emphasis",
					regex: "([*](?=\\S))(.*?\\S[*_]*)(\\1)"
				}, { //
					token: "url",
					regex: "(?:https?|ftp|dict):[^'\"\\s]+"
				},
				{ //
					token: "url",
					regex: "www\\.[^'\"\\s]+"
				},
				{ //
					token: "url",
					regex: "\\\"" + 	// "
						"[^\\\"]+" + 	// text
						"\\\":" +		// ":
						"[^'\"\\s]+"	// url
				},
				{ // ul - *
					token: "markup.list",
					regex: /^\s*[-\*]/
				},
				{ // ol 1) 1.
					token: "markup.list",
					regex: /^\s*\d+[\)\.]/
				},
				{ // ol a) A)
					token: "markup.list",
					regex: /^\s*[a-zA-Z]\)/
				},
				{
					token: "markup.blockquote",
					regex: /^>/
				},
				{
					token: "markup.modifier",
					regex: /\.\[[a-zA-Z0-9 \-]+\]$/
				}
			]
		});

		var self = this;
		function endCode(rules, prefix) {
			self.embedRules(rules, prefix, [{
				token: "support.function",
				regex: "^\\\\--",
				next: "pop"
			}]);
		}

		endCode(JavaScriptHighlightRules, "jscode-");
		endCode(TextHighlightRules, "text-");
		endCode(HtmlHighlightRules, "htmlcode-");
		endCode(CssHighlightRules, "csscode-");
		endCode(XmlHighlightRules, "xmlcode-");

		this.normalizeRules();
	};
	oop.inherits(TexyHighlightRules, TextHighlightRules);

	exports.TexyHighlightRules = TexyHighlightRules;
});