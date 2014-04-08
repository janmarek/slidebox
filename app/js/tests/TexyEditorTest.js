describe('TexyEditor', function () {
	var document, ace, texyEditor;

	beforeEach(function () {
		texyEditor = new TexyEditor(editor);
	});

	function setValue(value) {
		editor.setValue(value);
		editor.selection.clearSelection();
		editor.selection.moveCursorTo(editor.session.getLength(), 0);
		editor.selection.moveCursorLineEnd();
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
		it('inserts next number', function () {
			setValue('2) asdf');
			texyEditor.listHandler();

			expect(editor.getValue()).to.eql("2) asdf\n3) ");
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

			var range = editor.getSelectionRange();
			expect(range.start.row).to.eql(2);
			expect(range.start.column).to.eql(0);
			expect(range.end.row).to.eql(2);
			expect(range.end.column).to.eql(4);
		});

		it('adds underline', function () {
			setValue("\nabcd");

			texyEditor.heading('*');

			expect(editor.getValue()).to.eql("\nabcd\n****");

			var range = editor.getSelectionRange();
			expect(range.start.row).to.eql(1);
			expect(range.start.column).to.eql(0);
			expect(range.end.row).to.eql(1);
			expect(range.end.column).to.eql(4);
		});

		it('changes underline to other type', function () {
			setValue("x\n\nasdf\n----\n\nx");
			editor.selection.moveCursorTo(2, 0);

			texyEditor.heading('=');

			expect(editor.getValue()).to.eql("x\n\nasdf\n====\n\nx");

			var range = editor.getSelectionRange();
			expect(range.start.row).to.eql(2);
			expect(range.start.column).to.eql(0);
			expect(range.end.row).to.eql(2);
			expect(range.end.column).to.eql(4);
		});

		it('removes underline if text is heading', function () {
			setValue("x\n\nasdf\n----\n\nx");
			editor.selection.moveCursorTo(2, 1);

			texyEditor.heading('-');

			expect(editor.getValue()).to.eql("x\n\nasdf\n\nx");

			var range = editor.getSelectionRange();
			expect(range.start.row).to.eql(2);
			expect(range.start.column).to.eql(1);
			expect(range.end.row).to.eql(2);
			expect(range.end.column).to.eql(1);
		});
	});
});