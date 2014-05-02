function PresentView(maxPage, exitLink) {
	var self = this;
	this.maxPage = maxPage;
	this.exitLink = exitLink;
	this.internalPage = ko.observable(1);
	this.barOpened = ko.observable(true);
	this.fullscreenSupported = Fullscreen.isSupported();
	this.fullscreenActive = ko.observable(false);

	this.page = ko.computed({
		read: function () {
			return self.internalPage();
		},
		write: function (value) {
			var intVal = parseInt(value, 10);

			if (intVal !== Number.NaN && intVal > 0 && intVal <= self.maxPage) {
				self.internalPage(intVal);
			}
		}
	});
}

PresentView.prototype = {
	prev: function () {
		this.page(this.page() - 1);
	},
	next: function () {
		this.page(this.page() + 1);
	},
	registerHashHandling: function (location) {
		if (/^#\d+$/.test(location.hash)) {
			this.page(location.hash.substr(1));
		}

		this.page.subscribe(function (value) {
			location.hash = value;
		});
	},
	registerKeys: function (window) {
		var self = this;
		$(window).keyup(function (e) {
			if (e.keyCode === 37) {
				self.prev();
			}

			if (e.keyCode === 39) {
				self.next();
			}

			if (e.keyCode === 27 && self.exitLink) {
				window.location.href = self.exitLink;
			}
		});
	},
	toggleFullscreen: function () {
		var newState = !this.fullscreenActive();
		if (newState) {
			Fullscreen.launch();
		} else {
			Fullscreen.exit();
		}
		this.fullscreenActive(newState);
	}
};
