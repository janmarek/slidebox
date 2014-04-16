/**
 * @param {Object} config
 * @param {PresidosEditor} presidosEditor
 * @constructor
 */
function EditDetailsWindow(config, presidosEditor) {
	this.config = config;
	this.presidosEditor = presidosEditor;

	this.opened = ko.observable(false);
	this.saving = ko.observable(false);
	this.name = ko.observable();
	this.description = ko.observable();
}

EditDetailsWindow.prototype.open = function () {
	this.opened(true);
	this.name(this.presidosEditor.name());
	this.description(this.presidosEditor.description());
};

EditDetailsWindow.prototype.save = function () {
	if (this.saving()) {
		return;
	}

	this.saving(true);

	var self = this;
	$.post(this.config.detailsUrl, {
		id: this.presidosEditor.id,
		name: this.name(),
		description: this.description()
	}, function (data) {
		self.presidosEditor.name(data.presentation.name);
		self.presidosEditor.nameLocked(data.presentation.nameLocked);
		self.presidosEditor.description(data.presentation.description);
		self.opened(false);
		self.saving(false);
	});
};

