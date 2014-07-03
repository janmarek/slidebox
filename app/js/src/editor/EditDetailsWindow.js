/**
 * @param {Object} config
 * @param {SlideBoxEditor} slideboxEditor
 * @constructor
 */
function EditDetailsWindow(config, slideboxEditor) {
	this.config = config;
	this.slideboxEditor = slideboxEditor;

	this.opened = ko.observable(false);
	this.saving = ko.observable(false);
	this.name = ko.observable();
	this.description = ko.observable();
}

EditDetailsWindow.prototype.open = function () {
	this.opened(true);
	this.name(this.slideboxEditor.name());
	this.description(this.slideboxEditor.description());
};

EditDetailsWindow.prototype.save = function () {
	if (this.saving()) {
		return;
	}

	this.saving(true);

	var self = this;
	$.post(this.config.detailsUrl, {
		id: this.slideboxEditor.id,
		name: this.name(),
		description: this.description()
	}, function (data) {
		self.slideboxEditor.name(data.presentation.name);
		self.slideboxEditor.nameLocked(data.presentation.nameLocked);
		self.slideboxEditor.description(data.presentation.description);
		self.opened(false);
		self.saving(false);
	});
};

