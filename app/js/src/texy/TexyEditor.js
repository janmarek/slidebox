function TexyEditor(aceEditor) {
	this.editor = aceEditor;
	this.document = this.editor.session.getDocument();
	this.registerHandlers();
}

TexyEditor.UNDERLINE_MIN = 3;
TexyEditor.UNDERLINE_MAX = 20;
TexyEditor.REGEXP_UL = /^\s*([\-\*]) (.*)/;
TexyEditor.REGEXP_OL = /^\s*([\d]+)([\)\.]) (.*)/;
TexyEditor.REGEXP_LETTER_OL = /^\s*([a-zA-Z]+)\) (.*)/;
TexyEditor.REGEXP_BLOCKQUOTE = /^> (.*)/;

TexyEditor.prototype.registerHandlers = function () {
	this.editor.commands.addCommand({
		name: 'Bold',
		bindKey: {mac: 'Cmd-B', win: 'Ctrl-B'},
		exec: this.bold.bind(this)
	});
	this.editor.commands.addCommand({
		name: 'Italics',
		bindKey: {mac: 'Cmd-I', win: 'Ctrl-I'},
		exec: this.italics.bind(this)
	});
	this.editor.commands.addCommand({
		name: 'List',
		bindKey: 'Enter',
		exec: this.listHandler.bind(this)
	});
};

TexyEditor.prototype.listHandler = function () {
	var line = this.editor.getCursorPosition().row;
	var lineContent = this.document.getLine(line);

	this.editor.insert("\n");

	var endList = function () {
		this.document.removeLines(line, line);
		this.editor.insert("\n");
	}.bind(this);

	var ulMatches = lineContent.match(TexyEditor.REGEXP_UL);
	if (ulMatches) {
		if (ulMatches[2] === '') {
			endList();
		} else {
			this.editor.insert(ulMatches[1] + ' ');
		}
		return;
	}

	var olMatches = lineContent.match(TexyEditor.REGEXP_OL);
	if (olMatches) {
		if (olMatches[3] === '') {
			endList();
		} else {
			var nextVal = parseInt(olMatches[1], 10) + 1;
			this.editor.insert(nextVal + olMatches[2] + ' ');
		}
		return;
	}

	var letterOlMatches = lineContent.match(TexyEditor.REGEXP_LETTER_OL);
	if (letterOlMatches) {
		if (letterOlMatches[2] === '') {
			endList();
		} else {
			var letter = letterOlMatches[1];
			var nextLetter;
			if (letter.toLowerCase() === 'z') {
				nextLetter = letter;
			} else {
				nextLetter = String.fromCharCode(letter.charCodeAt(0) + 1);
			}

			this.editor.insert(nextLetter + ') ');
		}
		return;
	}

	var bqMatches = lineContent.match(TexyEditor.REGEXP_BLOCKQUOTE);
	if (bqMatches) {
		if (bqMatches[1] === '') {
			endList();
		} else {
			this.editor.insert('> ');
		}
	}
};

TexyEditor.prototype.getSelectedText = function () {
	return this.editor.getSelectedText();
};

TexyEditor.prototype.tag = function (startText, endText) {
	// todo check if start row == end row

	var text = this.editor.getSelectedText();
	var selectionRange = this.editor.getSelectionRange();
	var newText = startText + text + endText;

	this.document.replace(selectionRange, newText);

	if (text) {
		selectionRange.setEnd(selectionRange.end.row, selectionRange.end.column + startText.length + endText.length);
		this.editor.selection.setSelectionRange(selectionRange);
	} else {
		this.editor.selection.clearSelection();
		this.editor.moveCursorTo(selectionRange.start.row, selectionRange.start.column + startText.length);
	}

	this.editor.focus();
};

TexyEditor.prototype.phrase = function (text) {
	this.tag(text, text);
};

TexyEditor.prototype.bold = function () {
	// todo check if start row == end row

	var text = this.editor.getSelectedText();

	var matches = text.match(/^\*\*(.*)\*\*$/);

	if (matches) {
		var selectionRange = this.editor.getSelectionRange();
		this.document.replace(selectionRange, matches[1]);
		selectionRange.setEnd(selectionRange.end.row, selectionRange.end.column - 4);
		this.editor.selection.setSelectionRange(selectionRange);
		this.editor.focus();
	} else {
		this.phrase('**');
	}
};

TexyEditor.prototype.italics = function () {
	// todo check if start row == end row

	var text = this.editor.getSelectedText();

	if (text.match(/^\*\*\*.*\*\*\*$/) || text.match(/^\*[^*]+\*$/)) {
		var selectionRange = this.editor.getSelectionRange();
		this.document.replace(selectionRange, text.substring(1, text.length - 1));
		selectionRange.setEnd(selectionRange.end.row, selectionRange.end.column - 2);
		this.editor.selection.setSelectionRange(selectionRange);
		this.editor.focus();
	} else {
		this.phrase('*');
	}
};

TexyEditor.prototype.insertLink = function (text, url) {
	if (!url) {
		return;
	}

	var selection = this.editor.getSelectionRange();
	var linkText = '"' + text + '":' + url;
	this.document.replace(selection, linkText);

	if (text) {
		this.select(
			selection.start.row,
			selection.start.column,
			selection.start.row,
			selection.start.column + linkText.length
		);
	} else {
		this.editor.moveCursorTo(selection.start.row, selection.start.column + 1);
		this.editor.clearSelection();
	}

	this.editor.focus();
};

TexyEditor.prototype.heading = function (level) {
	var line = this.editor.getCursorPosition().row;
	var headingText = this.document.getLine(line);
	var emptyLine = headingText === "";

	// is already heading
	if (headingText) {
		var nextLine = this.document.getLine(line + 1);
		var matches = nextLine.match(/^([\*=\-]){3,}$/);
		if (matches) {
			this.document.removeLines(line + 1, line + 1);
		}
		if (matches && matches[1] === level) {
			return;
		}
	}

	var insertLines = [];

	if (emptyLine) {
		headingText = prompt('Enter heading text', '');
		insertLines.push(headingText);

		if (!headingText) {
			return;
		}
	}

	var underlineLength = Math.max(TexyEditor.UNDERLINE_MIN, Math.min(TexyEditor.UNDERLINE_MAX, headingText.length));
	var underline = new Array(underlineLength + 1).join(level);
	insertLines.push(underline);

	this.document.insertLines(line + (emptyLine ? 0 : 1), insertLines);

	this.select(line, 0, line, headingText.length);
	this.editor.focus();
};

TexyEditor.prototype.select = function (startRow, startColumn, endRow, endColumn) {
	var Range = ace.require("ace/range").Range;

	this.editor.selection.setSelectionRange(new Range(startRow, startColumn, endRow, endColumn));
};

TexyEditor.prototype.unorderedList = function () {
	var range = this.editor.getSelectionRange();
	var lines = [];
	var startRow = range.start.row;
	var startColumn = range.start.column;
	var endRow = range.end.row;
	var endColumn = range.end.column;
	var i, matches;

	// remove list if every line is part of list
	var removeMode = true;
	for (i = startRow; i <= endRow; i++) {
		if (!this.document.getLine(i).match(TexyEditor.REGEXP_UL)) {
			removeMode = false;
			break;
		}
	}

	for (i = startRow; i <= endRow; i++) {
		var line = this.document.getLine(i);

		// remove list
		if (removeMode) {
			lines.push(line.substring(2));
			continue;
		}

		// partial list - line ok
		if (line.match(TexyEditor.REGEXP_UL)) {
			lines.push(line);
			continue;
		}

		// change list type
		var lineContent = null;

		matches = line.match(TexyEditor.REGEXP_OL);
		if (matches) {
			lineContent = matches[3];
		} else {
			matches = line.match(TexyEditor.REGEXP_LETTER_OL);
			lineContent = matches ? matches[2] : null;
		}

		if (matches) {
			lines.push('- ' + lineContent);

			// {123}) {text} -> - {text}
			if (i === startRow) {
				startColumn = startColumn - matches[1].length;
			}
			if (i === endRow) {
				endColumn = endColumn - matches[1].length;
			}
			continue;
		}

		// add list or change partial list to complete list
		lines.push('- ' + line);
		if (i === startRow) {
			startColumn += 2;
		}
		if (i === endRow) {
			endColumn += 2;
		}
	}

	// move selection left when removing list
	if (removeMode) {
		startColumn = Math.max(0, startColumn - 2);
		endColumn = Math.max(0, endColumn - 2);
	}

	this.document.removeLines(startRow, endRow);
	this.document.insertLines(startRow, lines);
	this.select(startRow, startColumn, endRow, endColumn);
	this.editor.focus();
};

TexyEditor.prototype.table = function (cols, rows, header) {
	var texy = "\n";
	var i, j;

	for (i = 0; i < rows; i++) {
		// header
		if (header && i < 2) {
			texy += '|';
			for (j = 0; j < cols; ++j) {
				texy += '--------';
			}
			texy += "\n";
		}

		// cells
		for (j = 0; j < cols; j++) {
			texy += "|       ";
		}
		texy += "|\n";
	}
	texy += "\n";

	var selection = this.editor.getSelectionRange();
	this.document.replace(selection, texy);
	this.editor.focus();
};

TexyEditor.prototype.image = function (src, alt, align) {
	var texy = '[* ' + src + ' ' + (alt ? '.(' + alt + ') ' : '') + (align ? align : '*') + ']';

	var selection = this.editor.getSelectionRange();
	this.document.replace(selection, texy);
	this.select(
		selection.start.row,
		selection.start.column,
		selection.start.row,
		selection.start.column + texy.length
	);
	this.editor.focus();
};