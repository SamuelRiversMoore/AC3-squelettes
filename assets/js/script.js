
L.mapbox.accessToken = 'pk.eyJ1IjoicGllcnJlcGllcnJlcGllcnJlIiwiYSI6IkdXdE5CRFEifQ.3zLbKVYfHituW8BVU-bl5g';

var mapBounds = [[8,-70],[-53,-50]]; // Andes
//var mapBounds = [[15,-70],[-57,-50]]; // Andes

$(function(){
	$.tablesorter.addParser({
		id: 'dates',
		is: function(s, table, cell, $cell) { return false; },
		format: function(s, table, cell, cellIndex) {
			var $cell = $(cell);
			var date = $cell.attr('data-date');
			if( date ){
				date = date.split(' ')[0];
				date = parseInt(date.split('-').join(''));
				return date || s;
			}
			else {
				return s;
			}
		},
		parsed: false,
		type: 'numeric'
	});

	$.tablesorter.addParser({
		id: 'statuts',
		is: function(s, table, cell, $cell) { return false; },
		format: function(s, table, cell, cellIndex) {
			var $cell = $(cell);
			return $cell.attr('data-statut') || s;
		},
		parsed: false,
		type: 'text'
	});

	$.tablesorter.addParser({
		id: 'personnes',
		is: function(s, table, cell, $cell) { return false; },
		format: function(s, table, cell, cellIndex) {
			var $cell = $(cell);
			return $cell.attr('data-personne') || s;
		},
		parsed: false,
		type: 'text'
	});
});

$( document ).ready(function() {

	var myModal = $('body').find('.modal');
	if(myModal.length && myModal.is(':visible')){
		$('body').addClass('hasModal'); // pour désactiver la scrollbar de l'arrière plan si le modal et visible
	}

	$('#show-menu, #close-menu').on('click', function(){
		if ($('#menu').hasClass('shown')) {
			$('body').addClass('menu-opened');
			$('#menu').addClass('shown');
		} else {
			$('body').removeClass('menu-opened');
			$('#menu').removeClass('shown');			
		}
	});


	/* SELECT MENU */
	var openMenu = false;
	function closeMenu(){
		if(openMenu != false){
			openMenu.removeClass('open');
			openMenu = false;
		}
	}

	$(document).on('click', function(e) {
		// Pour supprimer les messages d'avertissement des formulaires au clique.
		if($(document).find('.message.ok').length && !okremoved) $(document).find('.message.ok').remove(); var okremoved = true;


		if(openMenu != false){
			if(!$(e.target).closest('.select-menu').length){
				closeMenu();
			} else {
				if ($(e.target).is('li.status')) {
					var $target = $(e.target);
					var $menu = $target.closest('.select-menu');
					var $old = $target.parent().find('.selected');
					var newValue = $target.attr('data-value').replace(' ', '');
					var oldValue = $old.attr('data-value');
					var original = $menu.attr('data-original').replace(' ', '');

					$target.addClass('selected');
					$old.removeClass('selected');

					$menu.find('.select-title .status').removeClass(oldValue).addClass(newValue);
					$menu.find('select').val(newValue);
					$menu.closest('form').submit();
					closeMenu();
				}
			}
		}
	}).on('click', '.select-title', function(e){
		if ($(this).hasClass('open')){
			closeMenu();
		} else {
			$('.select-title.open').removeClass('open');
			$(this).addClass('open');
			openMenu = $(this);
		}
	});

	// responsive tabs pas fini
	$('.tabs-wrapper').on('click', '#tabs-menu-button', function(){
		$(this).closest('.tabs-wrapper').toggleClass('openmenu');
	})

	// surmodal
	$('.modal').on('click', '.bouton.montrer', function(){
		var target = $(this).attr('data-target');
		target = document.getElementById(target);
		if($(target).length){
			$(target).removeClass('hidden');
			// Reinitialiser autocomplete.js & FileUpload.js
			AutoComplete.init();
			FileUpload.init();

		}
	});
	$('.modal').on('click', '.surmodal', function(e){
		$(this).addClass('hidden');
	}).on('click', '.surmodal>.edition', function(e){
		e.stopPropagation();
	}).on('click', '.surmodal .bouton.annuler', function(e){
		e.preventDefault();
		$(this).closest('.surmodal').addClass('hidden');
	})

	// pour le futur
	$('.modal').on('click', '.diapositive .supprimer-button', function(){
		$(this).closest('.diapositive').addClass('deletemode');
	}).on('click', '.diapositive .bouton.annuler', function(){
		$(this).closest('.diapositive').removeClass('deletemode');
	})

	$('.modal').on('click', '.diapositive.fake .bouton.ajouter', function(){
		$(this).closest('.diapositive').removeClass('fake').addClass('ajout');
	}).on('click', '.diapositive.ajout .bouton.annuler', function(){
		$(this).closest('.diapositive').removeClass('ajout').addClass('fake');
	});

	Ps.initialize(document.getElementById('menu'),{
		suppressScrollX: true
	});

	// lexique interception
	var lexique = {
		ouvre:function($mot){
			$mot.addClass('ouvert').siblings('.glomot-contenu').addClass('ouvert');
		},
		ferme:function($mot, ajuste){
			if(ajuste == true){
				$('html, body').animate({ scrollTop: $(document).scrollTop() - $mot.parent().height()}, 0);
			}
			$mot.removeClass('ouvert').siblings('.glomot-contenu').removeClass('ouvert');
		},
		montre:function($mot){
			$('html, body').animate({ scrollTop: $mot.offset().top - 200}, 0);
		}
	}
	$('#lexique-lettres').on('click', 'a', function(e){
		e.preventDefault();
		$('html, body').animate({
			scrollTop: $( $.attr(this, 'href') ).offset().top - 64
		}, 250);
	});

	if($('#lexique-mots').length){
		if ( $('#lexique-mots').find('.lexique-titre.ouvert').length ){
			var $mot_ouvert = $('#lexique-mots').find('.lexique-titre.ouvert');
			setTimeout(function() {
				lexique.montre($mot_ouvert);
			}, 100);
		} else {
			var $mot_ouvert;
		}
		$('#lexique-mots').on('click', 'a.lexique-titre', function(e){
			e.preventDefault();
			history.pushState({ path: this.path }, '', this.href);
			if ($(this).hasClass('ouvert')) {
				lexique.ferme($(this), ajuste = false);
			} else {
				if (typeof $mot_ouvert != 'undefined' && $mot_ouvert.length > 0) {
					if(($mot_ouvert.parent().index() - $(this).parent().index()) < 0){
						var ajuste = true;
					} else {
						var ajuste = false;
					}
					lexique.ferme($mot_ouvert, ajuste);
				}
				$mot_ouvert = $(this);
				lexique.ouvre($mot_ouvert);
			}
		});
	}





	/* slickers */
	$(".slider").slick({
		dots: true,
		infinite: true,
		speed: 300,
		//slidesToShow: 1,
		//centerMode: true,
		//variableWidth: true,
		prevArrow: '<img class="slider-arrow-left" src="squelettes/assets/images/arrow-left.png">',
		nextArrow: '<img class="slider-arrow-right" src="squelettes/assets/images/arrow-right.png">',
		accessibility: true,

	});
	$(".slick-zoom").on('click', function(e) {
		$(".slider").addClass("slick-fullscreen");
		$(".slider").slick('setPosition');
	});
	$(".slider").on('click', function(e) {
		if( !$(e.target).hasClass('slick-arrow') ) {
			if($(e.target).hasClass('slick-slide') || $(e.target).parent().hasClass('slick-slide')) {
				$(".slider").slick('slickNext');
			} else {
				$(this).removeClass("slick-fullscreen");
				$(".slider").slick('setPosition');
			}
		}
	})
	$(document).keyup(function(e) {
		if (e.keyCode == 27) { // escape key maps to keycode `27`
			$(".slider").removeClass("slick-fullscreen")
			$(".slider").slick('setPosition');
		}
	});


	/* Ancres dans les tabs */
	$(".thematique-sommaire a").click(function(e) {
		closeMenu();
		e.preventDefault();
		var dest = $(this).attr('href');
		$('html,body').animate({ scrollTop: $(dest).offset().top }, 'slow');
	})

	$("#searchbar-button").on('change', function() {
	    if($(this).is(':checked')) {
	    	$('#menu-searchbar input').focus();
	    }
	});

	$('#contenu.compte article.tab table.sortable').each(function(){
		$(this).tablesorter({
			headers: {
				0 : { sorter: 'statuts' },
				3 : { sorter: 'dates' }
			},
		});
	});

	$('section.personnes table.sortable').each(function(){
		$(this).tablesorter({
			headers: {
				0 : { sorter: 'personnes' },
			},
		});
	});

	/* ----- gestion des onglets (tab) ---- */

	// les tabs du compte fonctionnent sur un système d'ancres et pas de liens en dur
	if (!$('body').hasClass('logged') || $('#contenu').hasClass('compte')) {
		$("#nav-tabs li a").click(function(e) {
			closeMenu();
			e.preventDefault();
			history.pushState({ path: this.path }, '', this.href);
			var $inventaireMenu = $(this).closest('#nav-tabs');
			if (!$(this).hasClass('bouton')) {
				if($(this).hasClass('chapitre') && $(this).closest('.chapitres').is(':hidden')){
					$(this).closest('.chapitres').slideDown(300, function(){
						$(document.body).trigger("sticky_kit:recalc");
					});
				} else {
					if (!$(this).hasClass('chapitre') && !$(this).hasClass('strong') && $(this).siblings('ul.chapitres').find('a.strong').length <= 0) {
						$inventaireMenu.find('.chapitres').slideUp(300);
						if ($(this).next('.chapitres').children().length >= 1) {
							$(this).next('.chapitres').slideDown(300, function(){
								$(document.body).trigger("sticky_kit:recalc");
							});
						}
					}
				}
			}
			$(".tabs-wrapper article.tab").hide();
			if (document.getElementById( this.getAttribute('data-cible') )){
				document.getElementById( this.getAttribute('data-cible') ).style.display = 'block';
			}
			$inventaireMenu.find("li a").removeClass('strong');
			if($(this).hasClass('titre')){
				$(this).next('.chapitres').find('li a').first().addClass('strong');
			} else {
				$(this).addClass('strong');
			}

			if ($('#inventaire-thematiques').length){
				var topPosition = $('#inventaire-thematiques').offset().top;
				if ( ($(document).scrollTop() - topPosition) >= 100 ) {
					$('html,body').animate({ scrollTop: topPosition }, 200);
				}
			}

			$inventaireMenu.closest('.tabs-wrapper').removeClass('openmenu')
			return false;
		});
	}


	$(window).on('resize', function(){
		set_offset();
	});

	var stick_offset = 0;
	function set_offset(){
		var wwidth = $(window).width();
		if(wwidth <= 1100){
			stick_offset = 65;
		} else {
			stick_offset = 0;
		}
		if(typeof sticky != 'undefined'){
			sticky.trigger("sticky_kit:detach");
			sticky.stick_in_parent({offset_top : stick_offset});
		}
	}

	// pour afficher le premier article si aucun n'est ouvert. J'ai pas réussi à le faire en SPIP...
	if ($('#nav-tabs').length > 0) {
		if (window.location.hash) {
			// interception d'url
			var target = $('#nav-tabs').find('a[href="'+window.location.hash+'"]');
			setTimeout(function() {
				target.trigger('click');
			}, 200);
		} else {
			if($('#nav-tabs').find('a.strong').length <= 0){
				$('#nav-tabs').find('li a').first().trigger('click');
			}
		}
		set_offset();
		var sticky = $('#nav-tabs').stick_in_parent({offset_top : stick_offset});
		$('#nav-tabs').on("sticky_kit:bottom", function(e) {
			$(this).addClass('is_bottom');
		}).on("sticky_kit:unbottom", function(e) {
			$(this).removeClass('is_bottom');
		});

		var $inventaireMenu = $('#nav-tabs');
		var minHeight = $inventaireMenu.find('header h5').outerHeight(true);  // hauteur du header
		var itemHeight = $inventaireMenu.find('ul.thematiques>li').last().outerHeight(true); // hauteur d'une entrée


		$inventaireMenu.find('ul.thematiques>li').each(function(){
			minHeight += itemHeight; // hauteur du premier niveau
		});
		var maxlength = 0;
		$inventaireMenu.find('ul.chapitres').each(function(){
			var thislength = $(this).find('li').length;
			if(thislength > maxlength) maxlength = thislength; // hauteur du menu chapitres le plus grand
		});
		minHeight += itemHeight * maxlength;
		minHeight += $inventaireMenu.find('#topButton').outerHeight(true);
		minHeight += 40; //par sécurité

		if($('#nav-tabs').height() > minHeight){
			minHeight = $('#nav-tabs').height() + 40;
		}

		$('#inventaire-tabs').css('min-height', minHeight).find('article.tab').each(function(){
			$(this).css('min-height', minHeight);
		});

	}

	/* ----- / fin gestion des onglets (tab) ---- */





	if($('#contenu.compte').length > 0 && $('#contenu').find('.dashboard .tabs-wrapper')){
		var dashboard = $('section.dashboard .tabs-wrapper');
		var nav = dashboard.find('#nav-tabs');
		dashboard.find('article.tab').each(function(){
			var target = $(this).attr('id').replace('#', '');
			var props = $(this).find('tr>td.statut .select-menu div.status.prop').length;
			if (props>0) {
				var prop = $('<span class="somme status prop" data-count="'+props+'"><span class="count">'+props+'</span></span>')
				nav.find('.item:not(titre)[data-cible='+target+']').append(prop);
			}
		});
		nav.find('li.section').each(function(){
			var section = $(this);
			var somme = 0;
			section.find('ul.chapitres span.somme').each(function(){
				somme += parseInt($(this).attr('data-count'));
			});
			if(somme > 0){
				var total = $('<span class="somme status prop" data-count="'+somme+'"><span class="count">'+somme+'</span></span>');
				section.find('.item.titre').append(total);
			}
		});
	}





	/* Init Mapbox */
	if($("#welcome-map").length > 0) {
		var map = L.mapbox.map('welcome-map', 'mapbox.streets', { 
			zoomControl: false,
			attributionControl: false,
		});
		
        map.dragging.disable();
        map.touchZoom.disable();
        map.doubleClickZoom.disable();
        map.scrollWheelZoom.disable();
        map.boxZoom.disable();
        map.keyboard.disable();
        if (map.tap) map.tap.disable();
        

        var zoom = ($(window).width() >= 700) ? 4 : 3;
		map.setView([ -20, -60 ], zoom)

		/*
		map.fitBounds(L.latLngBounds(mapBounds).getCenter());
		if ($(window).width() >= 700) {
			var bounds = map.getBounds();
			var mapWidth = bounds.getEast() - bounds.getWest();
			var center = map.getCenter();
			map.panTo([center.lat, center.lng + mapWidth/4], {animate: false})			
		}
		*/

	}

	if($('#administration-map').length){
    	var wrapper = $('#administration-map').closest('section.row'),
    		latInput = wrapper.find('input[name="latitude"]'),
    		lonInput = wrapper.find('input[name="longitude"]');

		var adminMap = L.mapbox.map('administration-map', 'mapbox.streets', { 
			attributionControl: false,
		}).fitBounds(mapBounds);

    	var geocoderControl = L.mapbox.geocoderControl('mapbox.places', {keepOpen: false, autocomplete: true});
    		geocoderControl.addTo(adminMap);

		getInputVal();
	    geocoderControl.on('select', function(object){
	    	var coord = object.feature.geometry.coordinates;
	    		setMarker(coord[1], coord[0]);
	    	geocoderControl._toggle();
	    });
	    latInput.on('input', function(){ getInputVal(); });
	    lonInput.on('input', function(){ getInputVal(); });
	    function getInputVal(){
	    	if(latInput.val().length >= 1 && lonInput.val().length >= 1 ){
		    	var zoomVal = adminMap['_zoom'] ? adminMap['_zoom'] : 5;
	    		var lat = latInput.val(), lon = lonInput.val();
	    		setMarker(lat, lon);
	    		adminMap.setView([lat, lon], zoomVal);
	    	} else {
	 			adminMap.fitBounds(mapBounds);
	    	}
	    }
	    function displayCoords(){
	    	var m = marker.getLatLng();
	    	latInput.val(m.lat);
	    	lonInput.val(m.lng);
	    };

       	var marker;
	    function setMarker(lat, lon){
	    	if (undefined != marker) { adminMap.removeLayer(marker); }
	    	marker = L.marker([lat, lon], { icon: L.mapbox.marker.icon({ 'marker-color': '08c15f'}), draggable: true });
   			marker.addTo(adminMap);
			marker.on('dragend', displayCoords);
			displayCoords();
	    }
	}

	if($("#inventaire-mapbox").length > 0){

		var map = L.mapbox.map('inventaire-mapbox', 'mapbox.streets', { 
			attributionControl: false,
		});
		map.scrollWheelZoom.disable();
		map.fitBounds(mapBounds);
		
		if(typeof geojson !== 'undefined' && geojson.length > 0) {
			var markers = L.mapbox.featureLayer()
				.setGeoJSON(geojson)
				.addTo(map);
			map.fitBounds(markers.getBounds(), { padding: L.point(60, 60), maxZoom: maxZoom });
			markers.on('click', function(e) {
				if (!$("#inventaire-carte").hasClass('projet')) {
					window.location = e.layer.feature.properties['url'];
				};
			});
			markers.on('mouseover', function(e) {
				e.layer.openPopup();
			});
			markers.on('mouseout', function(e) {
				e.layer.closePopup();
			});

		} else {
			//$('#inventaire-carte').remove();
		}
	}


});



// pour afficher des marqueurs customs
/*
	"properties": {
		"title": "#NOM_FR",
		"url": "#URL_VILLE",
		"icon": {
			"className": "map-pointer", // class name to style
			"html": "#NOM_FR", // add content inside the marker
			"iconSize": null // size of icon, use null to set the size in CSS
		}
	}
	var markers = L.mapbox.featureLayer().addTo(map);
	markers.on('layeradd', function(e) {
		var marker = e.layer,
			feature = marker.feature;
		marker.setIcon(L.divIcon(feature.properties.icon));
	});
	markers.setGeoJSON(geojson);

*/
