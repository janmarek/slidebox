describe('PresentView', function () {
	var presentView;

	beforeEach(function () {
		presentView = new PresentView(5);
	});

	describe('page', function () {
		it('can be set', function () {
			presentView.page(3);
			expect(presentView.page()).to.eql(3);
		});
		it('cannot be smaller than 1', function () {
			presentView.page(0);
			expect(presentView.page()).to.eql(1);
		});
		it('cannot be bigger than max page', function () {
			presentView.page(8);
			expect(presentView.page()).to.eql(1);
		});
		it('is converted to int', function () {
			presentView.page(3.54);
			expect(presentView.page()).to.eql(3);
		});
		it('can be set to number only', function () {
			presentView.page("lorem ipsum");
			expect(presentView.page()).to.eql(1);
		});
	});
});