$(document).ready(function(){

	// globals
	var $terms, $return, $menu, $searchbar, $description, count, $complet, $partiel;
	AutoComplete.init();

});

var AutoComplete = {
	init:function(){

		// init
		$terms = [];
		$return = [];
		$menu = $('#show-hide-menu');
		$searchbar = $('section.edition').find('#search-bar');
		$description = $('#search-bar').closest('form').find('textarea[name="description"]');
		$complet = $('section.edition').find('#autosearch .output-complet');
		$partiel = $('section.edition').find('#autosearch .output-partiel');

		count = 0;		
		$complet.find('li.item').each(function(){
			var titre = $(this).find('.item-titre').text(), 
				texte = $(this).find('.item-texte').text(),
				target = $(this).find('label').attr('for');
			$terms.push([titre,texte,target]);
			count += 1;
		});
		$terms.sort();

		// controls
		var allowed = true, $container = $('section.edition');
		$container.on('click', '.show-hide.bouton', function(){
			$partiel.html('');
		});

		$container.off().on('click', '.autosearch-results li.item>label', function(e){
			AutoComplete.selectItem($(this));
			e.stopPropagation();
		});

		$container.off('keydown').on('keydown', '#search-bar', function(e){
			if (e.repeat != undefined) { allowed = !e.repeat; }
			if (!allowed) return;
			allowed = false;
			$key = e.keyCode;
			if ( $key == 38 || $key == 40 || $key == 13) {
				e.preventDefault();
				e.stopPropagation();
				AutoComplete.controlKey($key);
				return;
			}
			setTimeout(function() { AutoComplete.rechercher(openMenu=true); }, 50);
		});

		$container.on('keyup', '#search-bar', function(e) { 
			allowed = true;
		});

		$container.on('focus', '#search-bar', function(e){
			allowed = true;
			if ( $partiel.find('.item').length > 0 ) {
				AutoComplete.rechercher(openMenu=true);
			}
			$('#searchform').submit(function(e){
				e.preventDefault();
				AutoComplete.selectItem($(this));
				$('input').blur();
			});		
		});

	},
	filtrer: function(str, dict) {
		var research = new RegExp(str,"gi");
		for (var j=0; j<dict.length; j++) {
			var $titre = dict[j][0], $texte = dict[j][1], $target = dict[j][2];
			if ($titre.match(research)) {
				var regex = new RegExp( '(' + str + ')', 'gi' );
    			var $titre = $titre.replace( regex, "<b>$1</b>" );
				$return.push('<li class="item"><label for="'+$target+'"><span class="item-titre">' + $titre + '</span><span class="item-texte">' + $texte + '</span></label></li>');
			}
		}
	},
	rechercher: function(openMenu){
		$complet.find('input:radio').prop("checked", false);
		$description.val('');

		var $search = $searchbar.val();
		$return = [];
		AutoComplete.filtrer($search, $terms);
		if ( $search == '' || ! $('input').val ) {
			$partiel.html('');
			$menu.prop('checked', false);
		} else {
			if ($return.length) {
				$partiel.html($return);
				if(openMenu) $menu.prop('checked', true);						
			} else {
				$menu.prop('checked', false);						
			}
		}
		var $container = AutoComplete.getContainer();
		$container.find('.item:first-child').addClass('focus');
	},
	selectItem: function($item){
		var $titre = $item.find('.item-titre').text(),
			$texte = $item.find('.item-texte').text();
		$menu.prop('checked', false);
		$searchbar.val($titre);
		$description.val($texte);//.prop( "disabled", true );
		var $target = $item.attr('for'),
			$checkbox = $('#'+$target);
		if ($checkbox.is(':checked') == false) {
			$checkbox.prop('checked', true);
		}
	},
	getContainer: function(){
		if ( $partiel.find('.item').length > 0 ) {
			var $container = $partiel;
		} else {
			var $container = $complet;
		}
		return $container;
	},
	controlKey: function($key) {
		var $container = AutoComplete.getContainer();
		$focus = $container.find('.focus');

		if ( $focus.length > 0 ) {
			var $next = $focus.next(),
				$prev = $focus.prev();
		} else {			
			var $next = $container.find('.item:first-child'),
				$prev = $container.find('.item:last-child');
		}
		if ( $key == 38 ) { // Up
			if ( $focus.is(':first-child') ) {
				$prev = $container.find('.item:last-child');
			}
			$container.find('.focus').removeClass('focus');
			$prev.addClass('focus');
		} else if ( $key == 40 ) { // Down
			if ( $focus.is(':last-child') ) {
				$next = $container.find('.item:first-child');
			}
			$container.find('.focus').removeClass('focus');
			$next.addClass('focus');
		}
		if ( $key == 13 ) { // Enter
			if ( $focus.length > 0 ) { AutoComplete.selectItem($focus.find('label')); }
		}		
	}
}

