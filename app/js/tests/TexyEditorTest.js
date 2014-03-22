var expect = require('chai').expect;

describe('TexyEditor', function () {
	var document, ace, texyEditor;

	function mockCursor(row, column) {
		ace.getCursorPosition = function () {
			return {row: row, column: column};
		};
	}

	beforeEach(function () {
		document = {};
		ace = {
			session: {
				getDocument: function () {
					return document;
				}
			},
			commands: {
				addCommand: function () {}
			},
			focus: function () {}
		};
		texyEditor = new TexyEditor(ace);
	});

	describe('list handler', function () {
		var inserts;

		beforeEach(function () {
			inserts = [];
			ace.insert = function (insert) {
				inserts.push(insert);
			};
			mockCursor(0, 0);
		});

		it('inserts enter on empty line', function () {
			document.getLine = function () {
				return '';
			};
			texyEditor.listHandler();
			expect(inserts).to.eql(["\n"]);
		});

		it('inserts -', function () {
			document.getLine = function () {
				return '- asdf';
			};
			texyEditor.listHandler();
			expect(inserts).to.eql(["\n", '- ']);
		});

		it('inserts *', function () {
			document.getLine = function () {
				return '- asdf';
			};
			texyEditor.listHandler();
			expect(inserts).to.eql(["\n", '- ']);
		});

		it('inserts next number', function () {
			document.getLine = function () {
				return '2) asdf';
			};
			texyEditor.listHandler();
			expect(inserts).to.eql(["\n", '3) ']);
		});

		it('inserts next small letter', function () {
			document.getLine = function () {
				return 'a) asdf';
			};
			texyEditor.listHandler();
			expect(inserts).to.eql(["\n", 'b) ']);
		});

		it('after z inserts z again', function () {
			document.getLine = function () {
				return 'z) asdf';
			};
			texyEditor.listHandler();
			expect(inserts).to.eql(["\n", 'z) ']);
		});

		it('inserts next upper letter', function () {
			document.getLine = function () {
				return 'B) asdf';
			};
			texyEditor.listHandler();
			expect(inserts).to.eql(["\n", 'C) ']);
		});

		it('after Z inserts Z again', function () {
			document.getLine = function () {
				return 'Z) asdf';
			};
			texyEditor.listHandler();
			expect(inserts).to.eql(["\n", 'Z) ']);
		});

		it('works with indentation', function () {
			document.getLine = function () {
				return '- asdf';
			};
			texyEditor.listHandler();
			expect(inserts).to.eql(["\n", '- ']);
		});
	});

	describe('headings', function () {
		var selectionRange;

		beforeEach(function () {
			selectionRange = null;

			texyEditor.select = function () {
				selectionRange = arguments;
			};
		});

		it('prompts for text on empty line', function () {
			mockCursor(1, 0);
			document.getLine = function () {
				return '';
			};
			prompt = function () {
				return 'asdf';
			};

			var line, lines;
			document.insertLines = function (a, b) {
				line = a;
				lines = b;
			};

			texyEditor.heading('-');

			expect(line).to.equal(1);
			expect(lines).to.eql(['asdf', '----']);

			expect(selectionRange[0]).to.eql(1);
			expect(selectionRange[1]).to.eql(0);
			expect(selectionRange[2]).to.eql(1);
			expect(selectionRange[3]).to.eql(4);
		});

		it('adds underline', function () {
			mockCursor(1, 0);
			document.getLine = function () {
				return 'asdf';
			};

			var line, lines;
			document.insertLines = function (a, b) {
				line = a;
				lines = b;
			};

			texyEditor.heading('*');

			expect(line).to.equal(2);
			expect(lines).to.eql(['****']);

			expect(selectionRange[0]).to.eql(1);
			expect(selectionRange[1]).to.eql(0);
			expect(selectionRange[2]).to.eql(1);
			expect(selectionRange[3]).to.eql(4);
		});

		it('changes underline to other type', function () {
			mockCursor(1, 0);
			document.getLine = function (line) {
				if (line === 1) {
					return 'asdf';
				}
				if (line === 2) {
					return '===';
				}
				return '';
			};

			var line, lines;
			document.insertLines = function (a, b) {
				line = a;
				lines = b;
			};

			var startLine, endLine;
			document.removeLines = function (a, b) {
				startLine = a;
				endLine = b;
			};

			texyEditor.heading('*');

			expect(startLine).to.equal(2);
			expect(endLine).to.equal(2);
			expect(line).to.equal(2);
			expect(lines).to.eql(['****']);

			expect(selectionRange[0]).to.eql(1);
			expect(selectionRange[1]).to.eql(0);
			expect(selectionRange[2]).to.eql(1);
			expect(selectionRange[3]).to.eql(4);
		});

		it('removes underline if text is heading', function () {
			mockCursor(1, 0);
			document.getLine = function (line) {
				if (line === 1) {
					return 'asdf';
				}
				if (line === 2) {
					return '***';
				}
				return '';
			};

			var called = true;
			document.insertLines = function (a, b) {
				called = true;
			};

			var startLine, endLine;
			document.removeLines = function (a, b) {
				startLine = a;
				endLine = b;
			};

			texyEditor.heading('*');

			expect(startLine).to.equal(2);
			expect(endLine).to.equal(2);
			expect(called).to.be.true;
		});
	});
});