$(function(){

	jQuery.fn.tableDraw = function() {
		selectedTd = $('.b-prices table tr td.select')[0].cellIndex
		selectedTr = $('.b-prices table tr td.select').parent('tr')[0].rowIndex
		$('.b-prices table tr:lt(' + (selectedTr + 1) + ')').each(function(){
			$(this).children('th:eq(' + selectedTd + '),td:eq(' + selectedTd + ')').css({'background': '#ffffff'});
		})
		$('.b-prices table tr:eq(' + selectedTr + ') td:lt(' + (selectedTd + 1) + ')').css({'background': '#ffffff'});
	};

	$('.b-prices table tr td').live('hover', function(){
		if ($(this)[0].cellIndex < 1)
			return
		$('.b-prices table tr td, .b-prices table tr th').removeClass('select').css({'background': 'none'});
		$(this).addClass('select').tableDraw()
	})
	$('.b-prices table tr td.select').click()

    $('.b-tarifs select').styler();

    $('.b-services .item').hover(function(){
        $(this).find('a img.gray').fadeOut(400)
        $(this).find('a img.color').fadeIn(400)
    }, function(){
        $(this).find('a img.color').fadeOut(400)
        $(this).find('a img.gray').fadeIn(400)
    })

    $('.main-menu li ul').css({'opacity':'0.9'})
    $('.main-menu li').hover(function(){
        $(this).children('ul').show()
    }, function(){
        $(this).children('ul').hide()
    })

    if($.browser.msie && $.browser.version < 8){
	}else{
		$('body').css('overflow-x', 'hidden');
	}

    $("#slides").slides({
        preload: false,
        generateNextPrev: false
    })

	$('#dialog-form').dialog({
		autoOpen: false,
		height: 360,
		width: 600,
		modal: true,
		resizable: false,
		buttons: {
			"Отправить": function() {
				if(!$('#name').val() || !$('#name').val()){
				 	return false;
				}
				$('#par').val('тираж - '+$('.calcTiraz').val()+', цветов - '+$('.calcColor').val()+', формат - '+$('.calcFormat').val()+', эффект - '+$('.calcEffect').val()+', стоимость за ед. - '+$('.calcOne').val()+' стоимость тиража - '+$('.calcTotal').val()+'');
				$.post('/form.php', $('#userForm').serialize(), function(data){
		    		$('.form1, .ui-dialog-buttonpane').hide();
		    		$('.form2').show();
		    	});
				return false;
				$( this ).dialog( "close" );
			}
		}
	});

	$( "#button-order" ).button().click(function() {
		$( "#dialog-form" ).dialog( "open" );
	});

    $('.content').css('min-height', ($(document).height()-340)+'px');

    $('.b-equipment-list .link').click(function(){    	var item = $('div[num='+$(this).attr('num')+']');

    	var hbox = $('.text-box', item).height();
    	hbox -= 70;

    	if(hbox<70){    		return false;
    	}

    	if($('.text', item).height()+1 > 71){
    	 	hbox = 70;
    	}

    	$('.text', item).stop().animate({'height':''+hbox+'px'});
    	//.b-equipment-list .text
    	return false;
    });

    $('.calcForm select').change(function(){
    	$.post('/calc.php', $('.calcForm').serialize(), function(data){
    		var obj = eval(data);
    		$('.calcOne').val(obj[0]+' руб.');
    		$('.calcTotal').val(obj[1]+' руб.');
    	});
    });

    $('.calcForm').submit(function(){
    	return false;
    });

    $('.b-prices td').click(function(){    	if($(this)[0].cellIndex < 1){    		return;
    	}

    	$('.calcTiraz').val($(this).attr('t'));
    	$('.calcColor').val($(this).attr('c')).change();
        $('.b-tarifs select').trigger('refresh');
    });
})