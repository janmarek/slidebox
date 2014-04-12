function PresidosEditor(texyEditor, presentation, themes, config) {
	this.id = presentation.id;
	this.config = config;

	this.texyEditor = texyEditor;

	this.insertLinkWindow = new InsertLinkWindow(texyEditor);

	this.name = ko.observable(presentation.name);
	this.nameLocked = ko.observable(presentation.nameLocked);
	this.updated = ko.observable(presentation.updated);
	this.created = presentation.created;
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
			if (!self.nameLocked()) {
				self.name(data.name);
			}
			self.previewHtml(data.html);
			self.updated(data.updated);
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

PresidosEditor.prototype.rename = function () {
	var newName = prompt('New presentation name:', this.name());

	if (!newName) {
		return;
	}

	this.nameLocked(true);
	this.name(newName);

	$.post(this.config.renameUrl, {
		id: this.id,
		name: newName
	});
};
