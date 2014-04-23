function CollaboratorsWindow(config, presidosEditor) {
	this.config = config;
	this.presidosEditor = presidosEditor;

	this.opened = ko.observable(false);
	this.owner = presidosEditor.user;
	this.collaborators = ko.observableArray();
	this.autocompletedUsers = ko.observableArray();
	this.autocompleteString = ko.observable('');

	var self = this;
	this.autocompleteString.subscribe(function (val) {
		if (!val) {
			self.autocompletedUsers([]);
		}

		if (val.length < 3) {
			return;
		}

		var params = {
			name: val,
			collaboratorIds: self.getIds()
		};
		$.getJSON(config.autocompleteCollaboratorsUrl, params, function (data) {
			self.autocompletedUsers(data.users);
		});
	});
}

CollaboratorsWindow.prototype.getIds = function () {
	return this.collaborators().map(function (item) {
		return item.id;
	});
};

CollaboratorsWindow.prototype.open = function () {
	this.opened(true);
	this.autocompleteString('');
	this.collaborators(this.presidosEditor.collaborators());
};

CollaboratorsWindow.prototype.add = function (user) {
	this.collaborators.push(user);
	this.autocompletedUsers.remove(user);
};

CollaboratorsWindow.prototype.remove = function (user) {
	this.collaborators.remove(user);
};

CollaboratorsWindow.prototype.save = function () {
	this.opened(false);
	$.post(this.config.saveCollaboratorsUrl, {
		id: this.presidosEditor.id,
		collaborators: this.getIds()
	}, function () {
		this.presidosEditor.collaborators(this.collaborators());
	}.bind(this));
};