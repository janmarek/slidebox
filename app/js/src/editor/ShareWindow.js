function ShareWindow() {
	this.opened = ko.observable(false);
}

ShareWindow.prototype.open = function () {
	this.opened(true);
};
