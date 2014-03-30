function PresidosEditor(texyEditor, presentation, themes, config) {
	this.id = presentation.id;
	this.config = config;

	this.texyEditor = texyEditor;

	this.name = ko.observable(presentation.name);
	this.editorContent = ko.observable(
		this.texyEditor.document.getValue()
	);
	this.previewHtml = ko.observable();

	this.texyEditor.editor.on('change', function (e) {
		this.editorContent(this.texyEditor.document.getValue());
	}.bind(this));

	this.themes = themes;

	var selectedTheme = null;
	for (var i = 0; i < themes.length; i++) {
		if (themes[i].id === presentation.theme.id) {
			selectedTheme = themes[i];
			break;
		}
	}

	this.selectedTheme = ko.observable(selectedTheme);
	this.previewTheme = ko.observable(selectedTheme);

	var self = this;
	ko.computed(function () {
		$.post(self.config.previewUrl, {
			id: self.id,
			text: self.editorContent()
		}, function (data) {
			self.name(data.name);
			self.previewHtml(data.html);
		});
	}).extend({
		rateLimit: 1500
	});
}

PresidosEditor.prototype.selectTheme = function (theme) {
	this.selectedTheme(theme);
	this.previewTheme(theme);
	$.post(this.config.saveThemeUrl, {
		id: this.id,
		theme: theme.id
	});
};

PresidosEditor.prototype.showPreviewTheme = function (theme) {
	this.previewTheme(theme);
};

PresidosEditor.prototype.resetPreviewTheme = function () {
	this.previewTheme(this.selectedTheme());
};
