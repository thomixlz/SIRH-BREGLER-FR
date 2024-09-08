
 CKEDITOR.editorConfig = function( config ) {

	config.toolbarGroups = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools','source' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		'/',
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		{ name: 'about', groups: [ 'about' ] }
	];

	config.removeButtons = 'CopyFormatting,JustifyLeft,JustifyCenter,JustifyRight,Font,FontSize,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Save,Templates,NewPage,ExportPdf,Preview,Print,Find,Replace,CreateDiv,BidiLtr,BidiRtl,Language,Image,Table,Smiley,SpecialChar,PageBreak,Iframe,Maximize,ShowBlocks,About,Cut,Undo,Redo,Copy,Paste,PasteText,PasteFromWord';
    config.colorButton_colors = '000087CC,00ffff';
    config.font_names =
		"Figtree, sans-serif;"
		+ "Bodoni Moda;"
		
    config.stylesSet = [
	{ name: 'Title - H1', element: 'h1', styles: { 'font-weight': '700', 'font-size': '36px', 'line-height': '42px', 'color' : '000087CC' }},
	{ name: 'Title - H2', element: 'h2', styles: { 'font-weight': '700', 'font-size': '24px', 'line-height': '32px', 'color' : '000087CC' }},
    { name: 'Title - H3', element: 'h3', styles: { 'font-weight': '700', 'font-size': '18px', 'line-height': '24px', 'color' : '000087CC' }},
	{ name: 'Title - H4', element: 'h4', styles: { 'font-weight': '300', 'font-size': '16px', 'line-height': '24px', 'color' : '000087CC' }},
	{ name: 'Title - H5', element: 'h5', styles: { 'font-weight': '300', 'font-size': '12px', 'line-height': '24x', 'color' : '000087CC' }},
	{ name: 'Body-Text', element: 'p', styles: { 'font-weight': '300', 'font-size': '16px', 'line-height': '24px', 'color' : '000087CC' }},
	{ name: 'Quote-Text', element: 'p', styles: { 'font-weight': '300', 'font-size': '18px', 'line-height': '30px', 'color' : '000087CC' }},
    ];

	config.removePlugins = 'format,exportpdf'; 
	config.colorButton_enableMore = false;
	config.colorButton_enableAutomatic = false;

	

	

	config.plugins = config.plugins.replace(/(.*\s|^)widget(.*\s|$)/, '');



	
};