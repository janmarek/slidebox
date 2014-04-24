function ChooseImageWindow(insertWindow, config) {
	this.opened = ko.observable(false);

	this.tabs = [
		new ImageFromUploadTab(this, insertWindow, config),
		new ImageFromUrlTab(this, insertWindow)
	];

	this.selectedTab = ko.observable(this.tabs[0]);
}

ChooseImageWindow.prototype.open = function () {
	this.opened(true);
	this.selectedTab().init();
};

ChooseImageWindow.prototype.selectTab = function (tab) {
	this.selectedTab(tab);
	tab.init();
};
