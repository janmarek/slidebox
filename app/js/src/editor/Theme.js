function Theme(data) {
	var self = this;

	this.id = data.id;
	this.name = data.name;
	this.className = data.className;
	this.variants = data.variants.map(function (variant) {
		return new ThemeVariant(self, variant);
	});
		
	this.defaultVariant = ko.observable(this.variants[0]);
}