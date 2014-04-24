function ImageFromUrlTab(chooseImageWindow, insertWindow) {
	this.chooseImageWindow = chooseImageWindow;
	this.insertWindow = insertWindow;

	this.name = 'From URL';
	this.key = 'url';

	this.url = ko.observable();
	this.urlFocused = ko.observable(false);
}

ImageFromUrlTab.prototype.init = function () {
	this.url('');
	this.urlFocused(true);
};

ImageFromUrlTab.prototype.choose = function () {
	this.chooseImageWindow.opened(false);
	this.insertWindow.open(this.url());
};