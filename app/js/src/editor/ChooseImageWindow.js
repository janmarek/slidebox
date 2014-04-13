function ChooseImageWindow(insertWindow) {
	this.insertWindow = insertWindow;
	this.opened = ko.observable(false);
	this.url = ko.observable();
	this.urlFocused = ko.observable(false);
}

ChooseImageWindow.prototype.open = function () {
	this.opened(true);
	this.url('');
	this.urlFocused(true);
};

ChooseImageWindow.prototype.choose = function () {
	this.opened(false);
	this.insertWindow.open(this.url());
};

