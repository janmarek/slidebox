function InsertImageWindow(texyEditor) {
	this.texyEditor = texyEditor;
	this.opened = ko.observable(false);
	this.url = ko.observable();
	this.alt = ko.observable();
	this.align = ko.observable();
}

InsertImageWindow.prototype.open = function () {
	this.opened(true);
	this.url('');
	this.alt('');
	this.align('center');
};

InsertImageWindow.prototype.insert = function () {
	this.opened(false);

	var aligns = {
		center: null,
		left: '<',
		right: '>'
	};

	this.texyEditor.image(this.url(), this.alt(), aligns[this.align()]);
};

