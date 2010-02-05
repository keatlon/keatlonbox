// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Basic set. Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {
	onShiftEnter:	{keepDefault:false, replaceWith:'<br />\n'},
	onCtrlEnter:	{keepDefault:false, openWith:'\n<p>', closeWith:'</p>\n'},
	onTab:			{keepDefault:false, openWith:'	 '},
	markupSet: [
		{name:'Heading 1',  className:'btnHead', key:'1', openWith:'<h1(!( class="[![Class]!]")!)>', closeWith:'</h1>', placeHolder:'Your title here...' },
		{name:'Bold',       className:'btnBold', key:'B', openWith:'(!(<strong>|!|<b>)!)', closeWith:'(!(</strong>|!|</b>)!)' },
		{name:'Italic',     className:'btnItalic', key:'I', openWith:'(!(<em>|!|<i>)!)', closeWith:'(!(</em>|!|</i>)!)' },
		{name:'Quote',      className:'btnQuote', key:'Q', openWith:'(!(<quote>|!|<i>)!)', closeWith:'(!(</quote>|!|</i>)!)' },
		{name:'Link',       className:'btnLink', key:'L', openWith:'<a href="[![Link:!:http://]!]"(!( title="[![Title]!]")!)>', closeWith:'</a>', placeHolder:'Your text to link...' },
 		{separator:'---------------' },
		{name:'Picture', className:'btnPicture', beforeInsert:showInsertImageDlg },
		{name:'Cut', className:'btnCut', openWith:'<cut/>'},
	]
}

/*
 *
		{name:'Video', key:'L', openWith:'<a href="[![Link:!:http://]!]"(!( title="[![Title]!]")!)>', closeWith:'</a>', placeHolder:'Your text to link...' },

 *
 *
 */

function showInsertImageDlg(obj)
{
    var selector = '1';
    dialog.load('navigator','image', {'selector' : selector});
};