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
	this.youtubeWindow = new YoutubeWindow(texyEditor);
	this.chooseImageWindow = new ChooseImageWindow(this.insertImageWindow, config);
	this.editDetailsWindow = new EditDetailsWindow(config, this);
	this.shareWindow = new ShareWindow();
	this.collaboratorsWindow = new CollaboratorsWindow(config, this);
	this.newSlideWindow = new NewSlideWindow(texyEditor.editor);

	this.texyEditor.editor.on('change', function (e) {
		this.editorContent(this.texyEditor.document.getValue());
	}.bind(this));

	this.themes = themes.map(function (theme) {
		return new Theme(theme);
	});

	var selectedTheme = null;
	var selectedVariant = null;
	for (var i = 0; i < this.themes.length; i++) {
		for (var j = 0; j < this.themes[i].variants.length; j++) {
			if (this.themes[i].variants[j].id === presentation.themeVariant.id) {
				selectedVariant = this.themes[i].variants[j];
				selectedTheme = selectedVariant.theme;
				selectedTheme.defaultVariant(selectedVariant);
				break;
			}
		}
	}

	this.selectedTheme = ko.observable(selectedTheme);
	this.previewTheme = ko.observable(selectedTheme);
	this.selectedThemeVariant = ko.observable(selectedVariant);
	this.previewThemeVariant = ko.observable(selectedVariant);

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
	var variant = theme.defaultVariant();
	this.selectedTheme(theme);
	this.selectedThemeVariant(variant);
	this.previewTheme(theme);
	this.previewThemeVariant(variant);
	$.post(this.config.saveThemeUrl, {
		id: this.id,
		themeVariant: variant.id
	});
};

PresidosEditor.prototype.selectThemeVariant = function (themeVariant) {
	themeVariant.theme.defaultVariant(themeVariant);
	this.selectTheme(themeVariant.theme);
};

PresidosEditor.prototype.showPreviewTheme = function (theme) {
	this.previewTheme(theme);
	this.previewThemeVariant(theme.defaultVariant());
};

PresidosEditor.prototype.resetPreviewTheme = function () {
	this.previewTheme(this.selectedTheme());
	this.previewThemeVariant(this.selectedThemeVariant());
};

PresidosEditor.prototype.showPreviewThemeVariant = function (themeVariant) {
	this.previewTheme(themeVariant.theme);
	this.previewThemeVariant(themeVariant);
};

PresidosEditor.prototype.resetPreviewThemeVariant = function () {
	this.previewThemeVariant(this.selectedThemeVariant());
};

PresidosEditor.prototype.publish = function () {
	$.post(this.config.publishUrl, {
		id: this.id
	}, function () {
		this.published(true);
	}.bind(this));
};