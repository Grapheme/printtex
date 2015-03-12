$(function(){
	$('textarea.tinymce').tinymce({
		script_url: '/k2/admin/tinymce/tiny_mce.js',
		theme: "advanced",

		plugins: "component,safari,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

		fullscreen_settings: {
			theme_advanced_buttons1: "save,newdocument,|,undo,redo,|,cut,copy,paste,pastetext,pasteword,|,add_image,image,media,emotions,|,hr,removeformat,|,tablecontrols,|,iespell,cleanup,code,fullscreen",
			theme_advanced_buttons2: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,|,forecolor,backcolor,styleprops,link,unlink,anchor,bullist,numlist,|,outdent,indent,|,ltr,rtl,|,sub,sup",
			theme_advanced_buttons3: "",
			theme_advanced_buttons4: ""
		},

		theme_advanced_buttons1: "undo,redo,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,link,unlink,|,add_image,image,component,|,code,fullscreen",
		theme_advanced_buttons2: "",
		theme_advanced_buttons3: "",
		theme_advanced_buttons4: "",

		theme_advanced_toolbar_location: "top",
		theme_advanced_toolbar_align: "left",
		theme_advanced_statusbar_location: "bottom",
		theme_advanced_resizing: false,

		extended_valid_elements: "img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|component|style]",

		content_css: "/k2/tinymce.css",
		language:"ru",
           relative_urls:false,
		remove_script_host:true,
           accessibility_warnings:false,
           theme_advanced_resizing:true,
           theme_advanced_resize_horizontal:false,

		setup:function(ed){
			ed.addButton('add_image',{
				title:'Вставить файл',
				image:'/k2/admin/tinymce/themes/advanced/img/insertimage.gif',
				onclick:function(){
					$.layer({'get':'/k2/admin/system/file-manager/?field='+ed.id, 'title':'Файловая библиотека', w:800});
				}
			});
		}
	});
});