function PresidosEditor(texyEditor, presentation, isOwner, themes, config) {
	this.id = presentation.id;
	this.config = config;

	this.texyEditor = texyEditor;
	this.isOwner = isOwner;

	this.name = ko.observable(presentation.name);
	this.nameLocked = ko.observable(presentation.nameLocked);
	this.description = ko.observable(presentation.description);
	this.published = ko.observable(presentation.published);
	this.collaborators = ko.observableArray(presentation.collaborators);
	this.updated = ko.observable(presentation.updated);
	this.created = presentation.created;
	this.user = presentation.user;
	this.editorContent = ko.observable(
		this.texyEditor.document.getValue()
	);
	this.previewHtml = ko.observable();

	this.insertLinkWindow = new InsertLinkWindow(texyEditor);
	this.insertTableWindow = new InsertTableWindow(texyEditor);
	this.insertImageWindow = new InsertImageWindow(texyEditor);
	this.chooseImageWindow = new ChooseImageWindow(this.insertImageWindow, config);
	this.editDetailsWindow = new EditDetailsWindow(config, this);
	this.shareWindow = new ShareWindow();
	this.collaboratorsWindow = new CollaboratorsWindow(config, this);

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

PresidosEditor.prototype.publish = function () {
	$.post(this.config.publishUrl, {
		id: this.id
	}, function () {
		this.published(true);
	}.bind(this));
};