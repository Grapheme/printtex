/**
 *
 * @author K2cms.ru
 * @copyright Copyright © 2009-2010 K2cms.ru, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.Component', {
		init:function(ed, url) {
			ed.addButton('component', {
				title:'Вставить компонент',
				image:url+'/img/button.gif',
				onclick:function(){
					id = ed.id;
					fid = id.replace(/\D+/, '');
					$.layer({'get':'/k2/admin/system/component/component.php?field='+fid, 'title':'Вставить компонент', 'w':397});
				}
			});

			ed.onBeforeSetContent.add(function(ed, o) {
				o.content = o.content.replace(/\<!-- component(\d+):(\d+) -->/g, "<img src=\"/k2/dev/component/$1/static/icon.gif?"+(Math.random()*1)+"\" class=\"mceItemNoResize\" component=\"$1:$2\"\" />");
			});
			ed.onPostProcess.add(function(ed, o){
				if(o.get){
					o.content = o.content.replace(/<img[^>]+>/gi, function(im){
						com = im.match(/component\="(\d+):(\d+)"/);
						if(com !== null){
						}
						return im;
					});
				}
			});

			ed.onDblClick.add(function(ed, e) {
				if(e.target.nodeName == 'IMG'){
			    	if((im = e.target.getAttribute('component')) !== null && (com = im.match(/(\d+):(\d+)/))){
						fid = id.replace(/\D+/, '');
						if(id == 'mce_fullscreen'){
						}else{
						}
			    	}
				}
			});
		},

		getInfo:function(){
			return {
				longname : 'Component',
				author : 'K2CMS',
				authorurl : 'http://www.k2cms.ru',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});
	tinymce.PluginManager.add('component', tinymce.plugins.Component);
})();