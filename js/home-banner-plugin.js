/**
 * Home Banner Plugin - Vivero Los Cocos
 *
 * Banner temporal para mostrar en la Home mientras se terminan los productos.
 * Para desactivar: eliminar el <script> de home.html o setear
 *   localStorage.setItem('hideBanner', 'true')
 *
 * Para remover definitivamente:
 *   1. Borrar este archivo (js/home-banner-plugin.js)
 *   2. Borrar el <script src="js/home-banner-plugin.js"> de home.html
 *   3. Borrar el bloque CSS con id "home-banner-styles" de home.html
 */
(function () {
  'use strict';

  // --- Configuracion ---------------------------------------------------
  var CONFIG = {
    // Texto principal del banner
    title: 'Estamos preparando nuestra tienda online',
    // Texto secundario
    subtitle: 'Muy pronto vas a poder comprar todas nuestras plantas y productos desde la comodidad de tu casa.',
    // Llamado a accion (WhatsApp)
    ctaText: 'Mientras tanto, consultanos por WhatsApp',
    // Numero de WhatsApp (sin +, sin espacios)
    whatsappNumber: '5492615000000',
    // Mensaje predeterminado de WhatsApp
    whatsappMessage: 'Hola! Vi la pagina de Los Cocos y me gustaria consultar por productos.',
    // Mostrar boton de cerrar
    dismissible: true,
    // Recordar cierre en esta sesion (sessionStorage)
    rememberDismiss: true
  };

  // --- Verificar si fue cerrado -----------------------------------------
  if (CONFIG.rememberDismiss && sessionStorage.getItem('homeBannerDismissed')) {
    return;
  }
  if (localStorage.getItem('hideBanner') === 'true') {
    return;
  }

  // --- Crear el banner --------------------------------------------------
  function createBanner() {
    var banner = document.createElement('div');
    banner.id = 'home-wip-banner';
    banner.setAttribute('role', 'alert');

    var whatsappURL = 'https://wa.me/' + CONFIG.whatsappNumber +
      '?text=' + encodeURIComponent(CONFIG.whatsappMessage);

    banner.innerHTML =
      '<div class="wip-banner-inner">' +
        '<div class="wip-banner-icon">🌱</div>' +
        '<div class="wip-banner-content">' +
          '<p class="wip-banner-title">' + CONFIG.title + '</p>' +
          '<p class="wip-banner-subtitle">' + CONFIG.subtitle + '</p>' +
          '<a href="' + whatsappURL + '" target="_blank" rel="noopener noreferrer" class="wip-banner-cta">' +
            '<svg class="wip-banner-wa-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>' +
            CONFIG.ctaText +
          '</a>' +
        '</div>' +
        (CONFIG.dismissible
          ? '<button class="wip-banner-close" aria-label="Cerrar banner">&times;</button>'
          : '') +
      '</div>';

    // Cerrar banner
    if (CONFIG.dismissible) {
      var closeBtn = banner.querySelector('.wip-banner-close');
      closeBtn.addEventListener('click', function () {
        banner.classList.add('wip-banner-hiding');
        if (CONFIG.rememberDismiss) {
          sessionStorage.setItem('homeBannerDismissed', '1');
        }
        setTimeout(function () {
          banner.remove();
        }, 400);
      });
    }

    return banner;
  }

  // --- Insertar en el DOM -----------------------------------------------
  function insertBanner() {
    var banner = createBanner();
    var main = document.querySelector('main');
    if (main) {
      main.parentNode.insertBefore(banner, main);
    } else {
      var header = document.querySelector('header');
      if (header && header.nextSibling) {
        header.parentNode.insertBefore(banner, header.nextSibling);
      } else {
        document.body.appendChild(banner);
      }
    }
  }

  // --- Inicializar ------------------------------------------------------
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', insertBanner);
  } else {
    insertBanner();
  }
})();
