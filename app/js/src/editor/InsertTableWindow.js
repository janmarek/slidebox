function InsertTableWindow(texyEditor) {
	this.texyEditor = texyEditor;
	this.opened = ko.observable(false);
	this.rows = ko.observable();
	this.cols = ko.observable();
	this.header = ko.observable();
}

InsertTableWindow.prototype.open = function () {
	this.opened(true);
	this.rows(4);
	this.cols(4);
	this.header(true);
};

InsertTableWindow.prototype.insert = function () {
	this.opened(false);
	this.texyEditor.table(this.rows(), this.cols(), this.header());
};

