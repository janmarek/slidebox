function NewSlideWindow(editor) {
	this.editor = editor;
	this.opened = ko.observable(false);
	this.name = ko.observable();
	this.slideTypes = [
		{id: '', name: 'normal'},
		{id: 'main', name: 'main slide (usually used for presentation name and author)'},
		{id: 'last', name: 'last slide (usually used for thank you slide)'}
	];
	this.type = ko.observable();
	this.nameFocused = ko.observable(false);
}

NewSlideWindow.prototype.open = function () {
	this.opened(true);
	this.name('');
	this.type('');

	this.nameFocused(true);
};

NewSlideWindow.prototype.insert = function () {
	this.opened(false);

	var texy = "\n\n";
	var name = this.name();
	var type = this.type();
	var modifier = type ? ' .[' + type + ']' : '';

	if (name) {
		var underlineLength = Math.max(TexyEditor.UNDERLINE_MIN, Math.min(TexyEditor.UNDERLINE_MAX, name.length));
		var underline = new Array(underlineLength + 1).join('*');

		texy += name + modifier + "\n" + underline;
	} else {
		texy += '----------------' + modifier;
	}

	texy += "\n\n";

	var selection = this.editor.getSelectionRange();
	this.editor.session.getDocument().replace(selection, texy);
	this.editor.focus();
};

