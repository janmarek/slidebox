function InsertImageWindow(texyEditor) {
	this.texyEditor = texyEditor;
	this.opened = ko.observable(false);
	this.url = ko.observable();
	this.description = ko.observable();
	this.align = ko.observable();
}

InsertImageWindow.prototype.open = function (url) {
	this.opened(true);
	this.url(url);
	this.description('');
	this.align('center');
};

InsertImageWindow.prototype.insert = function () {
	this.opened(false);

	var aligns = {
		center: null,
		left: '<',
		right: '>'
	};

	this.texyEditor.image(this.url(), null, aligns[this.align()], this.description());
};

