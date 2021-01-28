
define(['jquery'], function($) {
	var load_advancedreminders_class = new advancedreminders_class();

	//===============================================================
	// !Class advancedreminders_class
	//===============================================================
	function advancedreminders_class () {
		this.function_load_editor = null;
		this.langs = [];
		this.keylangs = [];
	}

	advancedreminders_class.prototype.start = function(function_load_editor, langs) {
		this.load_editor = function_load_editor;
		this.langs = langs;                 //{en: "English (en)", pt_br: "Português - Brasil (pt_br)"}
		this.keylangs = Object.keys(langs); //Object.keys(obj) = ['en', 'es', 'fr', 'pt_br'];
		var thisclass = this;
		var arr = ['textinactivity', 'textactivities', 'textnocompletion'];
		for(var i=0; i<arr.length; i++) {
			this.load_div_all(arr[i], i);
			try {
				var obj = JSON.parse($('#fitem_id_'+ arr[i] +' textarea').val());
			}catch(err){
				var obj = {};
			}
			for(var j=0; j<this.keylangs.length; j++) {
				this.addtab(i, j, obj[this.keylangs[j]]);
			}
		}
		$('.advrmd_all .lilang').on('click', function(event) {
			event.preventDefault();
			$(this).closest('.dropdown').find('.tab1').html($(this).html());
		});
		$('.advrmd_all .lidelete').on('click', function(event) {
			event.preventDefault();
			var r = confirm("Deseja realmente deletar esta mensagem?");
			if(r == true) $('.dropdown-'+ $(this).attr('data-divid')).remove();
		});
		$('.tab1').on('click', function(event) {
			var thisclass = this;
			setTimeout(function(){
				$($(thisclass).attr('href').replace('div-', '') +'editable').focus();
			}, 50);
		});
		$('#region-main form').on('submit', function(event) {
			var obj = {};
			$('.advrmd_all .tab-content textarea').each(function() {
				if(typeof obj[$(this).attr('data-elem')*1] != 'object') obj[$(this).attr('data-elem')*1] = {};
				obj[$(this).attr('data-elem')*1][$(this).attr('data-lang')] = {title:escape($(this).siblings('input').val()), body:escape($(this).val())};
			});
			for(i=0; i<arr.length; i++) {
				$('#id_'+ arr[i]).val(JSON.stringify(obj[i]));
			}
		});
		$('.advrmd_all textarea').each(function() {
			thisclass.load_editor($(this).attr('id'));
		});
		$('input[type="text"]').css('width', '70px').attr('type', 'number');
	}

	advancedreminders_class.prototype.addtab = function(elemid, numid, content) {
		var objcontent = content;
		if(typeof objcontent == 'string') objcontent = {title:'', body:objcontent};
		if(typeof objcontent != 'object') objcontent = {title:'', body:''};
		if(typeof objcontent.title == 'string') objcontent.title = unescape(objcontent.title);
		if(typeof objcontent.body == 'string') objcontent.body = unescape(objcontent.body);

		var active = (numid == 0) ? 'active' : '';
		var activefade = (numid == 0) ? 'active' : 'fade';
		var strhtml1 = "<li class='dropdown dropdown-"+ elemid +"-"+ numid +"'>\
			<a class='tab1 "+ active +"' data-toggle='tab' href='#div-textarea-"+ elemid +"-"+ numid +"'>"+ this.langs[this.keylangs[numid]] +"</a> <div class='dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></div>\
			<ul class='dropdown-menu' role='menu'>";
		for(var i=0; i<this.keylangs.length; i++) {
			strhtml1 += "<li class='lilang' role='presentation'><a role='menuitem' tabindex='-1' href='#'>"+ this.langs[this.keylangs[i]] +"</a></li>";
		}
		strhtml1 += "<li role='presentation' class='divider'></li>\
			  <li class='lidelete' role='presentation' data-divid='"+ elemid +"-"+ numid +"'><a role='menuitem' tabindex='-1' href='#'>"+ M.str.moodle.delete +"</a></li>\
			</ul>\
		  </li>";
		var strhtml2 = "<div class='tab-pane dropdown-"+ elemid +"-"+ numid +" "+ activefade +"' style='min-height:300px;' id='div-textarea-"+ elemid +"-"+ numid +"' >\
							<div style='min-height:300px;'>\
								<input id='input-"+ elemid +"-"+ numid +"' placeholder='"+ M.str.message['privacy:metadata:messages:subject'] +"' style='border:1px solid #bbb !important;width:calc(100% - 1px);padding:10px;border-bottom:0 !important;' value='"+ objcontent.title +"' />\
								<textarea id='textarea-"+ elemid +"-"+ numid +"' rows='15' data-elem='"+ elemid +"' data-lang='"+ this.keylangs[numid] +"' style='min-height:300px;'>"+ objcontent.body +"</textarea>\
							</div>\
						</div>";
		$('#advrmd_all'+ elemid +' .nav-tabs').append(strhtml1);
		$('#advrmd_all'+ elemid +' .tab-content').append(strhtml2);
	}

	advancedreminders_class.prototype.load_div_all = function(elemid, numid) {
		var strhtml = "<div id='advrmd_all"+ numid +"' class='advrmd_all'>\
			<style>\
				.advrmd_all {width:100%;}\
				.advrmd_all .tab-pane {margin-bottom:40px;}\
				.advrmd_all .tab-pane > div {min-height:300px;}\
				.advrmd_all textarea {width:calc(100% - 14px);}\
				.advrmd_all li.dropdown {position:relative;display:inline-flex;}\
				.advrmd_all li.dropdown a {background-color:transparent;}\
				.advrmd_all li.dropdown a.active {background-color:#f2f2f2;}\
				.advrmd_all .tab1 {padding:8px 30px 8px 12px;}\
				.advrmd_all .nav.nav-tabs {display:flex;}\
				.advrmd_all .dropdown-toggle {width:20px;height:20px;position:absolute;top:9px;right:3px;}\
				#fgroup_id_allowedroles label, #fgroup_id_allowedroles span {display:inline-block;width:calc(100% - 30px);}\
				#fgroup_id_allowedroles label ~ span {display: contents;}\
				.nav.nav-tabs {margin-bottom:0;}\
			</style>\
			<ul class='nav nav-tabs'></ul>\
			<div class='tab-content'></div>\
		</div>";
		$('#id_'+ elemid).css('display', 'none').before(strhtml);
	}

	return {
		'init': function(str_map, str_call) {
			load_advancedreminders_class.start(str_map, str_call);
		}
	};
});
