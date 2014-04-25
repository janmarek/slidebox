describe('TexyEditor', function () {
	var texyEditor;

	beforeEach(function () {
		texyEditor = new TexyEditor(editor);
	});

	function setValue(value) {
		editor.setValue(value);
		editor.selection.clearSelection();
		editor.selection.moveCursorTo(editor.session.getLength(), 0);
		editor.selection.moveCursorLineEnd();
	}

	function expectSelection(startRow, startColumn, endRow, endColumn) {
		var range = editor.getSelectionRange();
		expect(range.start.row).to.eql(startRow);
		expect(range.start.column).to.eql(startColumn);
		expect(range.end.row).to.eql(endRow);
		expect(range.end.column).to.eql(endColumn);
	}

	describe('list handler', function () {
		it('inserts enter on empty line', function () {
			setValue('asdf');
			texyEditor.listHandler();
			expect(editor.getValue()).to.eql("asdf\n");
		});

		it('inserts -', function () {
			setValue('- asdf');
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("- asdf\n- ");
		});

		it('inserts *', function () {
			setValue('* asdf');
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("* asdf\n* ");
		});

		it('inserts >', function () {
			setValue('> asdf');
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("> asdf\n> ");
		});

		it('inserts next number', function () {
			setValue('2) asdf');
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("2) asdf\n3) ");
		});

		it('inserts next number (dot syntax)', function () {
			setValue('2. asdf');
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("2. asdf\n3. ");
		});

		it('inserts next small letter', function () {
			setValue('a) asdf');
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("a) asdf\nb) ");
		});

		it('after z inserts z again', function () {
			setValue('z) asdf');
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("z) asdf\nz) ");
		});

		it('inserts next upper letter', function () {
			setValue('B) asdf');
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("B) asdf\nC) ");
		});

		it('after Z inserts Z again', function () {
			setValue('Z) asdf');
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("Z) asdf\nZ) ");
		});

		it('works with indentation', function () {
			setValue('  - asdf');
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("  - asdf\n  - ");
		});

		it('finishes lists after two enter pressed', function () {
			setValue("- asdf\n- ");
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("- asdf\n\n");
		});

		it('finishes blockquote after two enter pressed', function () {
			setValue("> asdf\n> ");
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("> asdf\n\n");
		});

		it('finishes numbered lists after two enter pressed', function () {
			setValue("1) asdf\n2) ");
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("1) asdf\n\n");
		});

		it('finishes letter lists after two enter pressed', function () {
			setValue("A) asdf\nB) ");
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("A) asdf\n\n");
		});
	});

	describe('headings', function () {
		it('prompts for text on empty line', function () {
			setValue("x\n\n\nx");
			editor.selection.moveCursorTo(2, 0);
			prompt = function () {
				return 'asdf';
			};

			texyEditor.heading('-');

			expect(editor.getValue()).to.eql("x\n\nasdf\n----\n\nx");
			expectSelection(2, 0, 2, 4);
		});

		it('adds underline', function () {
			setValue("\nabcd");

			texyEditor.heading('*');

			expect(editor.getValue()).to.eql("\nabcd\n****");
			expectSelection(1, 0, 1, 4);
		});

		it('changes underline to other type', function () {
			setValue("x\n\nasdf\n----\n\nx");
			editor.selection.moveCursorTo(2, 0);

			texyEditor.heading('=');

			expect(editor.getValue()).to.eql("x\n\nasdf\n====\n\nx");
			expectSelection(2, 0, 2, 4);
		});

		it('removes underline if text is heading', function () {
			setValue("x\n\nasdf\n----\n\nx");
			editor.selection.moveCursorTo(2, 1);

			texyEditor.heading('-');

			expect(editor.getValue()).to.eql("x\n\nasdf\n\nx");
			expectSelection(2, 1, 2, 1);
		});
	});

	describe('bold', function () {
		it('adds stars', function () {
			setValue('asdf');
			texyEditor.select(0, 1, 0, 3);
			texyEditor.bold();
			expect(editor.getValue()).to.eql('a**sd**f');
			expectSelection(0, 1, 0, 7);
		});

		it('removes stars', function () {
			setValue('a**sd**f');
			texyEditor.select(0, 1, 0, 7);
			texyEditor.bold();
			expect(editor.getValue()).to.eql('asdf');
			expectSelection(0, 1, 0, 3);
		});

		it('adds stars with italics', function () {
			setValue('a*sd*f');
			texyEditor.select(0, 1, 0, 5);
			texyEditor.bold();
			expect(editor.getValue()).to.eql('a***sd***f');
			expectSelection(0, 1, 0, 9);
		});

		it('removes stars with italics', function () {
			setValue('a***sd***f');
			texyEditor.select(0, 1, 0, 9);
			texyEditor.bold();
			expect(editor.getValue()).to.eql('a*sd*f');
			expectSelection(0, 1, 0, 5);
		});
	});

	describe('italics', function () {
		it('adds stars', function () {
			setValue('asdf');
			texyEditor.select(0, 1, 0, 3);
			texyEditor.italics();
			expect(editor.getValue()).to.eql('a*sd*f');
			expectSelection(0, 1, 0, 5);
		});

		it('removes stars', function () {
			setValue('a*sd*f');
			texyEditor.select(0, 1, 0, 5);
			texyEditor.italics();
			expect(editor.getValue()).to.eql('asdf');
			expectSelection(0, 1, 0, 3);
		});

		it('adds stars with bold', function () {
			setValue('a**sd**f');
			texyEditor.select(0, 1, 0, 7);
			texyEditor.italics();
			expect(editor.getValue()).to.eql('a***sd***f');
			expectSelection(0, 1, 0, 9);
		});

		it('removes stars with bold', function () {
			setValue('a***sd***f');
			texyEditor.select(0, 1, 0, 9);
			texyEditor.italics();
			expect(editor.getValue()).to.eql('a**sd**f');
			expectSelection(0, 1, 0, 7);
		});
	});

	describe('unordered lists', function () {
		it('can add list', function () {
			setValue("lorem\nipsum\ndolor\nsit\named");
			texyEditor.select(1, 3, 3, 2);
			texyEditor.unorderedList();
			expect(editor.getValue()).to.eql("lorem\n- ipsum\n- dolor\n- sit\named");
			expectSelection(1, 0, 3, 5);
		});

		it('can add list which is already partially list', function () {
			setValue("lorem\n- ipsum\n- dolor\nsit\named");
			texyEditor.select(1, 5, 3, 2);
			texyEditor.unorderedList();
			expect(editor.getValue()).to.eql("lorem\n- ipsum\n- dolor\n- sit\named");
			expectSelection(1, 0, 3, 5);
		});

		it('can remove list', function () {
			setValue("lorem\n- ipsum\n- dolor\n- sit\named");
			texyEditor.select(1, 0, 3, 4);
			texyEditor.unorderedList();
			expect(editor.getValue()).to.eql("lorem\nipsum\ndolor\nsit\named");
			expectSelection(1, 0, 3, 3);
		});

		it('can switch list type', function () {
			setValue("lorem\n123) ipsum\n- dolor\na) sit\named");
			texyEditor.select(1, 7, 3, 3);
			texyEditor.unorderedList();
			expect(editor.getValue()).to.eql("lorem\n- ipsum\n- dolor\n- sit\named");
			expectSelection(1, 0, 3, 5);
		});

		it('insert empty list', function () {
			setValue("lorem\n\n");
			texyEditor.select(2, 0, 2, 0);
			texyEditor.unorderedList();
			expect(editor.getValue()).to.eql("lorem\n\n- ");
			expectSelection(2, 2, 2, 2);
		});

		// TODO switch type with spaces at line beginning
	});

	describe('ordered lists', function () {
		it('can add list', function () {
			setValue("lorem\nipsum\ndolor\nsit\named");
			texyEditor.select(1, 3, 3, 2);
			texyEditor.orderedList();
			expect(editor.getValue()).to.eql("lorem\n1) ipsum\n2) dolor\n3) sit\named");
			expectSelection(1, 0, 3, 6);
		});

		it('can add list which is already partially list', function () {
			setValue("lorem\n1) ipsum\n2) dolor\nsit\named");
			texyEditor.select(1, 5, 3, 2);
			texyEditor.orderedList();
			expect(editor.getValue()).to.eql("lorem\n1) ipsum\n2) dolor\n3) sit\named");
			expectSelection(1, 0, 3, 6);
		});

		it('can remove list', function () {
			setValue("lorem\n1) ipsum\n2) dolor\n3) sit\named");
			texyEditor.select(1, 0, 3, 4);
			texyEditor.orderedList();
			expect(editor.getValue()).to.eql("lorem\nipsum\ndolor\nsit\named");
			expectSelection(1, 0, 3, 3);
		});

		it('can switch list type', function () {
			setValue("lorem\n123) ipsum\n- dolor\na) sit\named");
			texyEditor.select(1, 7, 3, 3);
			texyEditor.orderedList();
			expect(editor.getValue()).to.eql("lorem\n1) ipsum\n2) dolor\n3) sit\named");
			expectSelection(1, 0, 3, 6);
		});

		it('insert empty list', function () {
			setValue("lorem\n\n");
			texyEditor.select(2, 0, 2, 0);
			texyEditor.orderedList();
			expect(editor.getValue()).to.eql("lorem\n\n1) ");
			expectSelection(2, 3, 2, 3);
		});

		// TODO switch type with spaces at line beginning
	});

	describe('insert link', function () {
		it('replaces text', function () {
			setValue('lorem ipsum dolor');
			texyEditor.select(0, 6, 0, 11);
			texyEditor.insertLink('sit', 'www.amed.com');
			expect(editor.getValue()).to.eql('lorem "sit":www.amed.com dolor');
			expectSelection(0, 6, 0, 24);
		});

		it('does not select link when text is empty', function () {
			setValue('lorem ipsum dolor');
			texyEditor.select(0, 6, 0, 11);
			texyEditor.insertLink('', 'www.amed.com');
			expect(editor.getValue()).to.eql('lorem "":www.amed.com dolor');
			expectSelection(0, 7, 0, 7);
		});

		it('does not do anything when url is not set', function () {
			setValue('lorem ipsum dolor');
			texyEditor.select(0, 6, 0, 11);
			texyEditor.insertLink('amed', '');
			expect(editor.getValue()).to.eql('lorem ipsum dolor');
			expectSelection(0, 6, 0, 11);
		});
	});

	// TODO ordered list
	// TODO table
	// TODO image
});