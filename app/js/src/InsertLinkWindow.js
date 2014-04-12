function InsertLinkWindow(texyEditor) {
	this.texyEditor = texyEditor;
	this.opened = ko.observable(false);
	this.text = ko.observable();
	this.url = ko.observable();
	this.textFocused = ko.observable(false);
	this.urlFocused = ko.observable(false);
}

InsertLinkWindow.prototype.open = function () {
	this.opened(true);
	this.text(this.texyEditor.getSelectedText());
	this.url('');

	if (this.text()) {
		this.urlFocused(true);
	} else {
		this.textFocused(true);
	}
};

InsertLinkWindow.prototype.insert = function () {
	this.opened(false);
	this.texyEditor.insertLink(this.text(), this.url());
};

