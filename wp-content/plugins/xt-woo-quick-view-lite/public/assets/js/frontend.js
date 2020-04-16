(function( $ ) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     */

    $(function() {

        //final width --> this is the quick view image slider width
        //maxQuickWidth --> this is the max-width of the quick-view panel

        var customizer = false,
            quickView = $('.xt-woo-quick-view'),
            woofcPremiumEnabled = ($('.xt_woofc-premium').length > 0),
            isTouchDevice = touchSupport(),
            addTimeoutId,
            resizeTimeout,
            currentSlider,
            mobileSliderWidth = 350,
            mobileSliderHeight = 350,
            desktopSliderWidth = 400,
            desktopSliderHeight = 400,
            defaultMaxQuickWidth = 900,
            defaultMaxQuickHeight = 755,
            defaultSliderWidth,
            defaultSliderHeight,
            sliderFinalWidth,
            sliderFinalHeight,
            maxQuickWidth = defaultMaxQuickWidth,
            maxQuickHeight = defaultMaxQuickHeight,
            closeOnOverlayClick = true,
            isVisible = false,
            animationComplete = false,
            recentProduct = null,
            recentVariation = null,
            mobileScreen = false,
            tabletScreen = false,
            winWidth,
            winHeight,
            mobileBrowserFooterBarHeight = 0,
            productSelector = '.products .product, .jet-woo-builder-product',
            firstProductSelector = '.products .product:first-child, .jet-woo-builder-product:first-child',
            lastProductSelector = '.products .product:last-child, .jet-woo-builder-product:last-child';

        function initVars() {

            customizer = (typeof(wp) !== 'undefined' && typeof(wp.customize) !== 'undefined');

            mobileSliderWidth = getOption('xt_wooqv-mobile-slider-width', 350, true);
            mobileSliderHeight = getOption('xt_wooqv-mobile-slider-height', 350, true);
            desktopSliderWidth = getOption('xt_wooqv-desktop-slider-width', 400, true);
            desktopSliderHeight = getOption('xt_wooqv-desktop-slider-height', 400, true);

            if(XT_WOOQV.is_fullscreen) {
                desktopSliderWidth = getOption('xt_wooqv-desktop-slider-width-fullscreen', 40, true);
                mobileSliderHeight = getOption('xt_wooqv-mobile-slider-height-fullscreen', 55, true);
            }
        }
        
        function updateResponsiveVars() {

            winWidth = $(window).width(),
            winHeight = $(window).height(),
            tabletScreen = winWidth <= XT_WOOQV.layouts.M,
            mobileScreen = winWidth <= XT_WOOQV.layouts.S,
            defaultSliderWidth = tabletScreen ? parseInt(mobileSliderWidth) : parseInt(desktopSliderWidth);
            defaultSliderHeight = tabletScreen ? parseInt(mobileSliderHeight) : parseInt(desktopSliderHeight);
        }

        function getSelectedImage(productId, productElem) {

            var selectedImage = productElem.find('img.attachment-shop_catalog');
            if (selectedImage.length === 0) {

                selectedImage = productElem.find('.woocommerce-LoopProduct-link > img');

                if (selectedImage.length === 0) {
                    selectedImage = productElem.find('.woocommerce-LoopProduct-link img').first();

                    if (selectedImage.length === 0) {
                        selectedImage = productElem.find('.attachment-woocommerce_thumbnail').first();

                        if (selectedImage.length === 0) {
                            selectedImage = productElem.find('.woocommerce-LoopProduct-link').first();

                            if (selectedImage.length === 0) {
                                selectedImage = productElem.find('.wp-post-image').first();
                            }
                        }
                    }
                }
            }


            if (selectedImage.length === 0) {

                if($('.empty-box').length) {
                    selectedImage = $('.empty-box').find('img');
                }else{
                    selectedImage = $('<img>');
                }
            }

            return selectedImage;
        }

        function throttle (callback, limit) {
            var wait = false;                  // Initially, we're not waiting
            return function () {               // We return a throttled function
                if (!wait) {                   // If we're not waiting
                    callback.call(this);       // Execute users function
                    wait = true;               // Prevent future invocations
                    setTimeout(function () {   // After a period of time
                        wait = false;          // And allow future invocations
                    }, limit);
                }
            }
        }

        function initEvents() {

            var handler;
            var bodyEvents = $._data(document.body).events;

            if(bodyEvents && typeof(bodyEvents.click) !== 'undefined') {

                var bodyClickEvents = bodyEvents.click;

                for (var i = 0; i < bodyClickEvents.length; i++) {

                    if (bodyClickEvents[i].namespace === 'preview') {
                        handler = bodyClickEvents[i].handler;
                        break;
                    }
                }

                if (handler) {
                    $(document.body).off('click.preview', 'a');
                    $(document.body).on('click.preview', 'a', function (e) {

                        if (!$(e.target).hasClass('xt_wooqv-trigger') && !$(e.target).hasClass('xt_wooqv-trigger-icon')) {
                            handler(e);
                        }

                    });
                }
            }

            if(customizer && XT_WOOQV.can_use_premium_code) {

                if(typeof(wp.customize) !== 'undefined' && typeof(wp.customize.preview) !== 'undefined') {

                    quickView.attrchange({
                        trackValues: true, /* Default to false, if set to true the event object is
					                updated with old and new value.*/
                        callback: function (e) {
                            //event               - event object
                            //event.attributeName - Name of the attribute modified
                            //event.oldValue      - Previous value of the modified attribute
                            //event.newValue      - New value of the modified attribute
                            //Triggered when the selected elements attribute is added/updated/removed

                            if(e.attributeName.search('xt_wooqv-') !== -1) {

                                initVars();

                                setTimeout(function() {

                                    triggerQuickViewResize();
                                    triggerQuickViewResize();

                                },1);

                            }
                        }
                    });

                    var requireWindowResize = [
                        'modal_slider_width_desktop',
                        'modal_slider_width_desktop_fullscreen',
                        'modal_slider_height_desktop',
                        'modal_slider_height_mobile',
                        'modal_slider_height_mobile_fullscreen'
                    ];

                    requireWindowResize.forEach(function (setting) {

                        wp.customize.value('xt_wooqv[' + setting + ']').bind(function () {

                            triggerQuickViewResize();
                        });
                    });

                }
            }

            var initQuickViewAnimation = function(productId, productElem) {

                var selectedImage = getSelectedImage(productId, productElem);

                preloadImage(selectedImage.attr('src'), function() {

                    animateQuickView(productId, productElem, selectedImage, sliderFinalWidth, maxQuickWidth, 'open');
                });
            };

            //open / close the quick view panel
            $('body').on('click', function(evt){

                var trigger, product, productId;

                if( $(evt.target).is('.xt_wooqv-shortcode-trigger') || $(evt.target).closest('.xt_wooqv-shortcode-trigger').length ) {

                    evt.preventDefault();
                    evt.stopPropagation();

                    if(XT_WOOQV.can_use_premium_code) {

                        trigger = $(evt.target).hasClass('.xt_wooqv-shortcode-trigger') ? $(evt.target) : $(evt.target).closest('.xt_wooqv-shortcode-trigger');
                        var target = trigger.attr('target');

                        product = $('#' + target).find(productSelector).first();
                        productId = trigger.data('id');

                        if(!isVisible) {

                            initQuickViewAnimation(productId, product);

                        }else{

                            triggerProductQuickView(productId);
                        }
                    }

                }else if( $(evt.target).is('.xt_wooqv-product-overlay')) {

                    evt.preventDefault();
                    evt.stopPropagation();

                    $(evt.target).next().trigger('click');

                }else if( $(evt.target).is('.xt_wooqv-trigger') || $(evt.target).closest('.xt_wooqv-trigger').length) {

                    evt.preventDefault();
                    evt.stopPropagation();

                    trigger = $(evt.target).hasClass('.xt_wooqv-trigger') ? $(evt.target) : $(evt.target).closest('.xt_wooqv-trigger');
                    product = trigger.closest(productSelector).first();
                    productId = trigger.data('id');

                    if(!isVisible) {

                        initQuickViewAnimation(productId, product);

                    }else{

                        triggerProductQuickView(productId);
                    }

                }else if(
                    $(evt.target).is('.xt_wooqv-close-icon') ||
                    $(evt.target).is('html.xt_wooqv-active') ||
                    (($(evt.target).is('.xt_wooqv-overlay') || $(evt.target).is('.xt_wooqv-nav')) && closeOnOverlayClick)
                ) {

                    // only close modal on overlay click if animation is complete
                    if(animationComplete && isVisible) {

                        closeQuickView(null, sliderFinalWidth, maxQuickWidth);
                    }

                }else if($(evt.target).is('.xt_wooqv-prev') || $(evt.target).closest('.xt_wooqv-prev').length) {

                    previousProduct();

                }else if($(evt.target).is('.xt_wooqv-next') || $(evt.target).closest('.xt_wooqv-next').length) {

                    nextProduct();
                }
            });

            if (customizer) {

                $('body').on('mouseover', 'a .xt_wooqv-trigger, a .xt_wooqv-product-overlay', function() {

                    var $link = $(this).closest('a');
                    $link.attr('data-href', $link.attr('href')).attr('href', '#');

                }).on('mouseout', 'a .xt_wooqv-trigger, a .xt_wooqv-product-overlay', function() {

                    var $link = $(this).closest('a');
                    $link.attr('href', $link.attr('data-href'));
                });
            }

            if(XT_WOOQV.can_use_premium_code) {

                document.addEventListener('keyup', function (event) {
                    if (event.defaultPrevented) {
                        return;
                    }

                    var key = event.key || event.keyCode;

                    if (key === 'Escape' || key === 'Esc' || key === 27) {
                        closeQuickView(null, sliderFinalWidth, maxQuickWidth);
                    }
                });
            }

            // Resize Event
            $(window).on('resize', function() {
                resizeQuickView();
                resizeQuickView();
            });

            $(document.body).on('xt_wooqv-animation-end', function() {

                triggerQuickViewResize();

                if(XT_WOOQV.can_use_premium_code) {
                    checkNavigation();
                }

                setTimeout(function() {

                    if(isVisible) {
                        $('html').addClass('xt_wooqv-ready');
                    }else{
                        $('html').removeClass('xt_wooqv-ready');
                    }

                }, 10);

                animationComplete = true;

            });


            // Woo Floating Cart Integration
            if(!woofcPremiumEnabled) {

                //single add product to cart
                $(document).on('click', '.xt-woo-quick-view form .single_add_to_cart_button', function(evt){

                    var btn = $(this);

                    if(btn.hasClass('disabled')) {
                        return false;
                    }

                    if(skipAddToCart(btn)) {
                        return true;
                    }

                    evt.preventDefault();
                    evt.stopPropagation();

                    if(validateAddToCart(btn)) {
                        addToCart(btn);
                    }

                });

            }

            if(!!XT_WOOQV.close_on_added) {

                var closeModal = function() {

                    if(isVisible) {
                        closeQuickView(null, sliderFinalWidth, maxQuickWidth);
                    }
                };

                $( document.body ).on( 'xt_woofc_added_to_cart', closeModal);
                $( document.body ).on( 'xt_wooqv_added_to_cart', closeModal);
            }
        }


        function addToCart(trigger) {

            if(addTimeoutId){
                clearInterval(addTimeoutId);
            }

            if(trigger.data('loading')) {
                return false;
            }

            trigger.removeClass('added');

            var form = trigger.closest('form');
            var args = form.serializeJSON();

            if(typeof args === 'string') {
                args = $.parseJSON(args);
            }

            if(typeof args === 'object') {
                args['add-to-cart'] = form.find('[name="add-to-cart"]').val();
            }

            trigger.data('loading', true);
            trigger.addClass('loading');

            //update cart product list
            request(args, function() {

                trigger.removeClass('loading').addClass('added');
                trigger.removeData('loading');

                addTimeoutId = setTimeout(function() {
                    trigger.removeClass('added');
                }, 3000);

                setTimeout(function() {

                    $( document.body ).trigger('added_to_cart', [null, null, trigger]);
                    $( document.body ).trigger( 'xt_wooqv_added_to_cart' );

                },200);
            });

        }


        function request(args, callback) {

            $('html').addClass('xt_wooqv-loading');

            var type = 'single-add';

            var params = {
                action: 'xt_wooqv_update_cart',
                type: type
            };

            params = $.extend(params, args);

            $.ajax({
                url: location.href,
                data: params,
                type: 'post',
                success: function() {

                    $('html').removeClass('xt_wooqv-loading');

                    if(typeof(callback) !== 'undefined') {
                        callback();
                    }
                }
            });
        }

        function skipAddToCart(btn) {

            if(btn.closest('.wc-product-table').length) {

                return true;
            }

            return false;
        }

        function validateAddToCart(btn) {

            // validate required options from multiple plugins

            var form = btn.closest('form');
            var errors = 0;

            // Check if has quantity
            var $qty = form.find('.quantity .qty:visible');

            if($qty.length) {

                $qty.closest('.quantity').removeClass('xt_wooqv-error');

                if (parseInt($qty.val()) === 0) {
                    $qty.closest('.quantity').addClass('xt_wooqv-error');
                    errors++;
                }
            }

            // https://woocommerce.com/products/product-add-ons/
            var $elements = form.find('.wc-pao-required-addon, .required-product-addon');

            // https://codecanyon.net/item/woocommerce-extra-product-options/7908619
            $elements = $.merge(
                $elements,
                form.find('.tm-has-required + div.tm-extra-product-options-container').not('.tc-hidden div.tm-extra-product-options-container')
            );

            // https://wordpress.org/plugins/woocommerce-product-addon/
            $elements = $.merge(
                $elements,
                form.find('.ppom-field-wrapper .show_required').closest('.form-group')
            );

            // https://woocommerce.com/products/gravity-forms-add-ons/
            $elements = $.merge(
                $elements,
                form.find('.gfield_contains_required')
            );

            $elements.each(function() {

                var $row = $(this);

                if($row.is(':visible')) {
                    var $input = $row.find(':input');

                    if ($input.attr('type') === 'checkbox' || $input.attr('type') === 'radio') {
                        $row.removeClass('xt_wooqv-error');
                        if (!$input.is(':checked')) {
                            errors++;
                            $row.addClass('xt_wooqv-error');
                        }
                    } else {
                        $row.removeClass('xt_wooqv-error');
                        if ($input.val() === '') {
                            errors++;
                            $row.addClass('xt_wooqv-error');
                        }
                    }
                }else{
                    $row.removeClass('xt_wooqv-error');
                }
            });

            if(errors > 0) {
                var $firstError = form.find('.xt_wooqv-error').first();
                var inQuickView = $firstError.closest('.xt-woo-quick-view').length > 0;
                var scroll_selector = inQuickView ? '.xt_wooqv-item-info' : 'html,body';

                if($firstError.length) {
                    $(scroll_selector).animate({scrollTop: $firstError.offset().top - 100}, 500);
                }
            }

            return (errors === 0);
        }

        function getOption(key, defaultVal, isInt) {

            var val;
            isInt = isInt ? isInt : false;

            if(quickView.attr(key)) {

                val = quickView.attr(key);

            }else{

                val = defaultVal;
            }

            if(isInt) {
                val = parseInt(val);
            }

            return val;
        }

        function customizerValuesChanged() {

            // DESKTOP

            if(!tabletScreen) {

                var width_units = XT_WOOQV.is_fullscreen ? 'vw' : 'px';
                var height_units = XT_WOOQV.is_fullscreen ? 'vh' : 'px';

                $('.xt-woo-quick-view').css('width', '' );

                if(XT_WOOQV.is_fullscreen) {

                    $('.xt_wooqv-slider-wrapper, .xt_wooqv-slider li').css('width', desktopSliderWidth+width_units);
                    $('.xt_wooqv-item-info').css('width', (100 - parseInt(desktopSliderWidth)) + width_units);

                }else{

                    $('.xt_wooqv-slider-wrapper, .xt_wooqv-slider li').css({width: desktopSliderWidth+width_units, height: desktopSliderHeight+height_units});
                    $('.xt-woo-quick-view').css('height', desktopSliderHeight+height_units);
                }

            }else{

                $('.xt-woo-quick-view, .xt_wooqv-slider-wrapper, .xt_wooqv-slider, .xt_wooqv-slider li').css('height', '');

            }

            resetSlider();
        }

        function resizeQuickView() {

            if(!$('html').hasClass('xt_wooqv-resizing')) {

                $('html').addClass('xt_wooqv-resizing');

                if(resizeTimeout) {
                    clearTimeout(resizeTimeout);
                }

                resizeTimeout = setTimeout(function() {
                    $('html').removeClass('xt_wooqv-resizing');
                }, 500);
            }

            window.requestAnimationFrame(function() {

                updateResponsiveVars();

                if (customizer && XT_WOOQV.can_use_premium_code) {
                    customizerValuesChanged();
                }

                //SET VARS FOR MOBILE

                if (winWidth <= defaultSliderWidth) {

                    sliderFinalWidth = winWidth;
                    maxQuickWidth = sliderFinalWidth;

                } else {

                    sliderFinalWidth = defaultSliderWidth;
                    maxQuickWidth = defaultMaxQuickWidth;
                }

                if (winHeight <= defaultSliderHeight) {

                    sliderFinalHeight = winHeight;
                    maxQuickHeight = sliderFinalHeight;

                } else {

                    sliderFinalHeight = defaultSliderHeight;
                    maxQuickHeight = defaultMaxQuickHeight;
                }

                var quickViewLeft = (winWidth - quickView.width()) / 2,
                    quickViewTop = (winHeight - quickView.height()) / 2,
                    quickViewWidth = (winWidth * 0.8 < maxQuickWidth) ? winWidth * 0.8 : maxQuickWidth,
                    quickViewInfoWidth = parseInt(quickViewWidth - desktopSliderWidth);

                quickView.css({
                    'top': quickViewTop > 0 ? quickViewTop : 0,
                    'left': quickViewLeft > 0 ? quickViewLeft : 0,
                    'width': quickViewWidth
                });

                resetSlider();

                if (!XT_WOOQV.is_fullscreen && !tabletScreen) {
                    quickView.find('.xt_wooqv-item-info').css('width', quickViewInfoWidth);
                }

                resizeInfoBoxHeight();

            });

        }

        function resizeInfoBoxHeight() {

            if (tabletScreen) {

                var height = quickView.find('.xt_wooqv-item-info .xt_wooqv-item-info-inner').outerHeight(true) + mobileBrowserFooterBarHeight;
                quickView.find('.xt_wooqv-item-info').css('height', height);

            } else {
                quickView.find('.xt_wooqv-item-info').css('height', '100%');
            }
        }

        function triggerQuickViewResize() {

            $(window).trigger('resize');
        }

        function closeQuickView(productId, finalWidth, maxQuickWidth, noAnimation, callback) {

            if(!isVisible) {
                return false;
            }

            productId = typeof(productId) !== 'undefined' ? productId : getRecentProductId();
            noAnimation = typeof(noAnimation) !== 'undefined' ? noAnimation : false;

            var productElem = getProductById(productId);
            var selectedImage = getSelectedImage(productId, productElem);

            //update the image in the gallery
            if(!noAnimation && !quickView.hasClass('velocity-animating')) {
                animateQuickView(productId, productElem, selectedImage, finalWidth, maxQuickWidth, 'close', callback);
            } else {
                closeNoAnimation(selectedImage, finalWidth, maxQuickWidth, callback);
            }
        }

        function animateQuickView(productId, productElem, image, finalWidth, maxQuickWidth, animationType, callback) {

            //store some image data (width, top position, ...)
            //store window data to calculate quick view panel position

            var topSelected = image.offset().top - $(window).scrollTop(),
                leftSelected = image.offset().left,
                widthSelected = image.width(),

                finalLeft = (winWidth - finalWidth)/2,
                finalTop = (winHeight - sliderFinalHeight)/2,
                quickViewWidth = ( winWidth * 0.8 < maxQuickWidth ) ? winWidth * 0.8 : maxQuickWidth,
                quickViewLeft = (winWidth - quickViewWidth)/2,
                quickViewTop = finalTop;

            animationComplete = false;

            var initialStyles,
                animationStyles,
                animationEasing,
                animationDuration,
                finalStyles;

            var triggerOpen = function() {

                $('html').addClass('xt_wooqv-active');

                loadProductInfo(productId, null, function() {

                    updateResponsiveVars();

                    productElem.addClass('empty-box');

                    if(!!XT_WOOQV.is_fullscreen) {

                        if(XT_WOOQV.fullscreen_animation === 'none') {

                            initialStyles = {
                                'opacity': 1
                            };

                            animationStyles = {
                                'opacity': 1
                            };

                        }else if(XT_WOOQV.fullscreen_animation === 'fade') {

                            initialStyles = {
                                'opacity': 0
                            };

                            animationStyles = {
                                'opacity': 1
                            };

                        }else if(XT_WOOQV.fullscreen_animation === 'slide-top') {

                            initialStyles = {
                                'translateY': -winHeight,
                                'opacity': 1
                            };

                            animationStyles = {
                                'translateY': 0,
                                'opacity': 1
                            };

                        }else if(XT_WOOQV.fullscreen_animation === 'slide-bottom') {

                            initialStyles = {
                                'translateY': winHeight,
                                'opacity': 1
                            };

                            animationStyles = {
                                'translateY': 0,
                                'opacity': 1
                            };

                        }else if(XT_WOOQV.fullscreen_animation === 'slide-left') {

                            initialStyles = {
                                'translateX': -winWidth,
                                'opacity': 1
                            };

                            animationStyles = {
                                'translateX': 0,
                                'opacity': 1
                            };

                        }else if(XT_WOOQV.fullscreen_animation === 'slide-right') {

                            initialStyles = {
                                'translateX': winWidth,
                                'opacity': 1
                            };

                            animationStyles = {
                                'translateX': 0,
                                'opacity': 1
                            };
                        }

                        animationEasing = 'easeInOut';
                        animationDuration = 250;

                    }else{

                        //place the quick view over the image gallery and give it the dimension of the gallery image
                        initialStyles = {
                            'width': widthSelected,
                            'top': topSelected > 0 ? topSelected : 0,
                            'left': leftSelected > 0 ? leftSelected : 0,
                            'scaleX': tabletScreen ? '1' : '0.5',
                            'scaleY': tabletScreen ? '1' : '0.5',
                            'opacity': 0
                        };

                        //animate the quick view: animate its width and center it in the viewport
                        //during this animation, only the slider image is visible
                        animationStyles = {
                            'width': finalWidth,
                            'top': finalTop > 0 ? finalTop : 0,
                            'left': finalLeft > 0 ? finalLeft : 0,
                            'scaleX': '1',
                            'scaleY': '1',
                            'opacity': 1
                        };

                        animationEasing = tabletScreen ? 'easeInOut' : [400, 20];
                        animationDuration = tabletScreen ? 250 : 800;

                    }

                    quickView.velocity(initialStyles, 0).velocity(animationStyles, animationDuration, animationEasing, function(){

                        quickView.addClass('xt_wooqv-animate-width');

                        if(tabletScreen) {
                            quickView.addClass('xt_wooqv-add-content');
                        }

                        finalStyles = {
                            'top': quickViewTop,
                            'left': quickViewLeft,
                            'width': quickViewWidth
                        };

                        //animate the quick view: animate its width to the final value
                        quickView.velocity(finalStyles, ((XT_WOOQV.is_fullscreen || tabletScreen) ? 100 : 300), 'ease', function(){
                            //show quick view content

                            resetSlider();
                            triggerQuickViewResize();

                            quickView.addClass('xt_wooqv-add-content');

                            setTimeout(function() {
                                quickView.addClass('xt_wooqv-preview-gallery');
                            }, 50);

                            setTimeout(function() {
                                quickView.removeClass('xt_wooqv-preview-gallery');
                            }, 2000);

                            isVisible = true;
                            $(document.body).trigger('xt_wooqv-animation-end');

                            if(typeof(callback) !== 'undefined') {
                                callback();
                            }

                        });

                    }).addClass('xt_wooqv-is-visible');


                });
            };

            var triggerClose = function() {

                $('html').removeClass('xt_wooqv-ready');
                resetSlider(true);

                if(tabletScreen) {

                    quickView.removeClass('xt_wooqv-add-content xt_wooqv-animate-width');
                }

                if(!!XT_WOOQV.is_fullscreen) {

                    if(XT_WOOQV.fullscreen_animation === 'none') {

                        initialStyles = {
                            'opacity': 0
                        };

                        animationStyles = {
                            'opacity': 0
                        };

                    }else if(XT_WOOQV.fullscreen_animation === 'fade') {

                        initialStyles = {
                            'opacity': 1
                        };

                        animationStyles = {
                            'opacity': 0
                        };

                    }else if(XT_WOOQV.fullscreen_animation === 'slide-top') {

                        initialStyles = {
                            'translateY': 0,
                            'opacity': 1
                        };

                        animationStyles = {
                            'translateY': -winHeight,
                            'opacity': 1
                        };

                    }else if(XT_WOOQV.fullscreen_animation === 'slide-bottom') {

                        initialStyles = {
                            'translateY': 0,
                            'opacity': 1
                        };

                        animationStyles = {
                            'translateY': winHeight,
                            'opacity': 1
                        };

                    }else if(XT_WOOQV.fullscreen_animation === 'slide-left') {

                        initialStyles = {
                            'translateX': 0,
                            'opacity': 1
                        };

                        animationStyles = {
                            'translateX': -winWidth,
                            'opacity': 1
                        };

                    }else if(XT_WOOQV.fullscreen_animation === 'slide-right') {

                        initialStyles = {
                            'translateX': 0,
                            'opacity': 1
                        };

                        animationStyles = {
                            'translateX': winWidth,
                            'opacity': 1
                        };
                    }

                }else {

                    var left = (tabletScreen ? quickView.position().left : finalLeft);

                    //close the quick view reverting the animation
                    initialStyles = {
                        'width': finalWidth,
                        'top': finalTop > 0 ? finalTop : 0,
                        'left': left > 0 ? left : 0,
                    };

                    //animate the quick view: animate its width and center it in the viewport
                    //during this animation, only the slider image is visible
                    animationStyles = {
                        'width': widthSelected,
                        'top': topSelected > 0 ? topSelected : 0,
                        'left': leftSelected > 0 ? leftSelected : 0,
                        'scaleX': tabletScreen ? '1' : '0.5',
                        'scaleY': tabletScreen ? '1' : '0.5',
                        'opacity': 0
                    };
                }

                quickView.removeClass('xt_wooqv-add-content').velocity(initialStyles, (XT_WOOQV.is_fullscreen || tabletScreen ? 0 : 300), 'ease', function(){

                    $('html').removeClass('xt_wooqv-active');

                    quickView.removeClass('xt_wooqv-animate-width').velocity(animationStyles, (XT_WOOQV.is_fullscreen || tabletScreen ? 500 : 500), 'ease', function(){

                        isVisible = false;
                        quickView.removeClass('xt_wooqv-no-transitions xt_wooqv-is-visible');
                        productElem.removeClass('empty-box');

                        triggerQuickViewResize();

                        $(document.body).trigger('xt_wooqv-animation-end');

                        if(typeof(callback) !== 'undefined') {
                            callback();
                        }

                    });
                });

                recentProduct = null;
            };


            if( animationType === 'open' && !isVisible) {

                triggerOpen();

            } else if(isVisible || animationType === 'close') {

                triggerClose();
            }
        }

        function closeNoAnimation(image, finalWidth, maxQuickWidth, callback) {

            resetSlider(true);

            image = image.length ? image : $('.empty-box');

            var topSelected = image.offset().top - $(window).scrollTop(),
                leftSelected = image.offset().left,
                widthSelected = image.width();

            //close the quick view reverting the animation
            $('html').removeClass('xt_wooqv-active xt_wooqv-ready');

            $('.empty-box').removeClass('.empty-box');

            quickView.velocity('stop').removeClass('xt_wooqv-add-content xt_wooqv-no-transitions xt_wooqv-animate-width xt_wooqv-is-visible').css({
                'top': topSelected,
                'left': leftSelected,
                'width': widthSelected,
            });
            isVisible = false;

            triggerQuickViewResize();

            if(typeof(callback) !== 'undefined') {
                callback();
            }
        }

        function loadProductInfo(id, variation_id, callback) {

            if(typeof(xt_woofc_is_cart_open) !== 'undefined' && xt_woofc_is_cart_open()) {
                xt_woofc_close_cart();
            }

            var slider_only = (variation_id || isVisible) ? 1 : 0;

            variation_id = variation_id ? variation_id : 0;
            variation_id = variation_id === -1 ? 0 : variation_id;

            recentProduct = recentProduct ? recentProduct : 0;
            recentVariation = recentVariation ? recentVariation : 0;

            if(recentProduct === id && recentVariation === variation_id){
                return;
            }

            if(animationComplete) {

                if(slider_only) {

                    quickView.find('.xt_wooqv-slider-wrapper').block({
                        message: null
                    });

                }else{

                    $('html').addClass('xt_wooqv-loading');

                    quickView.block({
                        message: null
                    });
                }
            }

            recentProduct = id;

            if(slider_only) {

                recentVariation = variation_id;

                var params = {
                    action: 'xt_wooqv_quick_view',
                    id: id,
                    variation_id: variation_id,
                    slider_only: slider_only
                };

                $.ajax({
                    url: XT_WOOQV.wc_ajax_url.toString().replace('%%endpoint%%', 'xt_wooqv_quick_view'),
                    data: params,
                    type: 'post',
                    success: function (data) {

                        quickView.find('.xt_wooqv-slider-wrapper').replaceWith($(data.quickview));

                        onProductLoaded(id, variation_id, data, slider_only, callback);
                    }
                });

            }else{

                var $product = getProductContentById(id);

                if($product.length) {
                    setTimeout(function () {

                        quickView.find('.xt_wooqv-product').replaceWith($product);

                        // Support WooCommerce Quick View Event
                        $('body').trigger('quick-view-displayed');

                        // Allow third party plugins hook actions once the quick view has loaded and displayed.
                        $('body').trigger('xt-woo-quick-view-displayed');

                        window.dispatchEvent(new Event('load', {bubbles: true}));

                        onProductLoaded(id, variation_id, null, slider_only, callback);

                    }, animationComplete ? 300 : 0);
                }
            }

        }

        function onProductLoaded(id, variation_id, data, slider_only, callback) {

            data = data ? data : null;

            recentProduct = id;
            recentVariation = variation_id;

            if(customizer) {

                customizerValuesChanged();
            }

            if(slider_only) {

                resetSlider();

            }else{

                initProductVariationsEvents();
                initProductBundleEvents();
                initProductCompositeEvents();
            }

            triggerQuickViewResize();


            // Scroll Event
            if(isTouchDevice) {
                quickView.find('.xt_wooqv-product').off('scroll', throttle(checkMobileOverflowBar, 100));
                quickView.find('.xt_wooqv-product').on('scroll', throttle(checkMobileOverflowBar, 100));
            }


            if(XT_WOOQV.can_use_premium_code) {

                initLightSlider(data, callback);

                if(!slider_only) {
                    checkNavigation();
                }

            }else{

                if(typeof(callback) !== 'undefined') {
                    callback(data);
                }
            }

            if(!slider_only) {

                $(document.body).trigger('xt_wooqv-product-loaded');
            }

            if(animationComplete) {

                if(slider_only) {
                    quickView.find('.xt_wooqv-slider-wrapper').unblock();
                }else {
                    quickView.unblock();
                }

                setTimeout(function () {

                    if(!slider_only) {
                        $('html').removeClass('xt_wooqv-loading');
                    }

                }, slider_only ? 0 : 300);
            }

        }

        function checkNavigation() {

            if(!!XT_WOOQV.modal_nav_enabled) {

                if (moreProductsAvailable()) {

                    $('html').removeClass('xt_wooqv-hide-nav');

                    if (isFirstProduct()) {
                        $('html').addClass('xt_wooqv-first-product');
                    } else {
                        $('html').removeClass('xt_wooqv-first-product');
                    }

                    if (isLastProduct()) {
                        $('html').addClass('xt_wooqv-last-product');
                    } else {
                        $('html').removeClass('xt_wooqv-last-product');
                    }

                } else {

                    $('html').addClass('xt_wooqv-hide-nav');
                }
            }
        }

        function initProductVariationsEvents() {

            if ( typeof($.fn.wc_variation_form) === 'function' ) {

                quickView.find( '.variations_form' ).each( function() {

                    $( this ).wc_variation_form();

                    if(!!XT_WOOQV.can_use_premium_code) {
                        $(this).off('found_variation', onFoundVariation);
                        $(this).find('.reset_variations').off('click', onResetVariation);
                        $(this).on('found_variation', onFoundVariation);
                        $(this).find('.reset_variations').on('click', onResetVariation);
                    }
                });
            }
        }

        function initProductBundleEvents() {

            if ( typeof($.fn.wc_pb_bundle_form) === 'function' ) {

                quickView.find( '.bundle_form' ).each( function() {

                    $( this ).wc_pb_bundle_form();

                    var $bundle_button = $( this ).find('.bundle_button');
                    var $more_info_button = $( this ).find('.xt_wooqv-more-info');

                    if($bundle_button.length && $more_info_button.length) {
                        $more_info_button.appendTo($bundle_button);
                    }
                });
            }
        }

        function initProductCompositeEvents() {

            if ( typeof($.fn.wc_composite_form) === 'function' ) {

                quickView.find( '.composite_form' ).each( function() {

                    $( this ).wc_composite_form();
                });
            }
        }

        function onFoundVariation ( event, variation ) {

            resizeInfoBoxHeight();

            loadVariation(variation);
        }

        function onResetVariation() {

            resizeInfoBoxHeight();

            loadVariation();
        }

        function loadVariation(variation) {

            var id = getRecentProductId();
            var variation_id = variation ? variation.variation_id : -1;

            loadProductInfo(id, variation_id, function() {

                resizeInfoBoxHeight();
            });
        }

        function initLightSlider(data, callback) {

            var attachments = parseInt(quickView.find('.xt_wooqv-slider-wrapper').attr('data-attachments'));
            if(attachments <= 1) {
                if(typeof(callback) !== 'undefined') {
                    callback(data);
                }
                return false;
            }

            currentSlider = quickView.find('.xt_wooqv-slider').lightSlider({
                mode: XT_WOOQV.slider_animation,
                auto: !!XT_WOOQV.slider_autoplay,
                pauseOnHover: true,
                pause: 3000,
                item: !!XT_WOOQV.slider_vertical ? 1 : XT_WOOQV.slider_items_desktop,
                loop: true,
                gallery: !!XT_WOOQV.slider_gallery,
                thumbItem: 7,
                thumbMargin: 0,
                slideMargin:0,
                vertical: !!XT_WOOQV.slider_vertical,
                vThumbWidth: 60,
                verticalHeight: sliderFinalHeight * 0.7,
                enableDrag: isTouchDevice,
                currentPagerPosition: 'left',
                controls: !!XT_WOOQV.slider_arrows_enabled,
                prevHtml: '<span class="xt_wooqv-arrow-icon '+XT_WOOQV.slider_arrow+'"></span>',
                nextHtml: '<span class="xt_wooqv-arrow-icon '+XT_WOOQV.slider_arrow+'"></span>',
                responsive : [
                    {
                        breakpoint:XT_WOOQV.layouts.M,
                        settings: {
                            thumbItem: 11,
                            item: 1,
                        }
                    },
                    {
                        breakpoint:XT_WOOQV.layouts.S,
                        settings: {
                            thumbItem: 9,
                            item: 1,
                        }
                    }
                ],
                onSliderLoad: function(el) {

                    repositionSliderGalleryImages();

                    if(!!XT_WOOQV.slider_lightbox) {

                        el.lightGallery({
                            selector: '.xt_wooqv-slider .lslide',
                            mode: 'lg-'+XT_WOOQV.slider_animation,
                            prevHtml: '<span class="xt_wooqv-arrow-icon '+XT_WOOQV.slider_arrow+'"></span>',
                            nextHtml: '<span class="xt_wooqv-arrow-icon '+XT_WOOQV.slider_arrow+'"></span>',
                            showAfterLoad: false,
                            enableDrag: isTouchDevice
                        });

                        el.on('onAfterOpen.lg',function(){

                            var slide = el.find('.lslide.active').index();
                            el.data('lightGallery').slide(slide - 1);
                        });

                    }

                    if(typeof(callback) !== 'undefined') {
                        callback(data);
                    }
                }
            });
        }

        function resetSlider(destroyGallery) {

            if(!!XT_WOOQV.can_use_premium_code) {

                if(currentSlider) {

                    currentSlider.refresh();
                    currentSlider.goToSlide(1);

                    repositionSliderGalleryImages();

                    if(typeof(destroyGallery) !== 'undefined' && destroyGallery && !!XT_WOOQV.slider_lightbox && currentSlider.data('lightGallery')) {

                        currentSlider.data('lightGallery').destroy();
                    }
                }
            }
        }

        function repositionSliderGalleryImages() {

            if(!!XT_WOOQV.slider_gallery && quickView.find('.lSGallery').length) {

                var $slider = quickView.find('.xt_wooqv-slider-wrapper');
                var $gallery = $slider.find('.lSGallery');

                if(!!XT_WOOQV.slider_vertical) {

                    var height = $gallery.height();

                    var top = ($slider.height() - height) / 2;
                    top = top < 0 ? 0 : top;

                    $gallery.css({height: height, top: top});

                }else{

                    var width = $gallery.width();

                    var left = ($slider.width() - width) / 2;
                    left = left < 0 ? 0 : left;

                    $gallery.css({width: width, left: left});
                }

            }

        }

        function _open(id) {

            id = (typeof(id) !== 'undefined') ? id : null;

            if(isVisible) {
                return false;
            }

            var $product = (id !== null) ? getProductById(id) : getFirstProduct();

            $product.find('.xt_wooqv-trigger').trigger('click');

        }

        function _close() {

            closeQuickView(null, sliderFinalWidth, maxQuickWidth);
        }

        function previousProduct() {

            if(!isVisible || !recentProduct) {
                return false;
            }

            var product = getRecentProduct().prev();

            triggerProductQuickView(product);
        }

        function nextProduct() {

            if(!isVisible || !recentProduct) {
                return false;
            }

            var product = getRecentProduct().next();

            triggerProductQuickView(product);
        }

        function moreProductsAvailable() {

            return getTotalProducts() > 1;
        }

        function isFirstProduct() {

            if(!isVisible || !recentProduct) {
                return false;
            }

            return recentProduct === getFirstProductId();
        }

        function isLastProduct() {

            if(!isVisible || !recentProduct) {
                return false;
            }

            return recentProduct === getLastProductId();
        }

        function getRecentProduct() {

            return getProductById(getRecentProductId());
        }

        function getRecentProductId() {

            if(recentProduct === null || getProductById(recentProduct).length === 0) {
                recentProduct = getFirstProductId();
            }

            return recentProduct;
        }

        function getTotalProducts() {

            return $(productSelector).find('.xt_wooqv-trigger').length;
        }

        function getFirstProduct() {

            return $(firstProductSelector).find('.xt_wooqv-trigger').closest(productSelector).first();
        }

        function getLastProduct() {

            return $(lastProductSelector).find('.xt_wooqv-trigger').closest(productSelector).first();
        }

        function getFirstProductId() {

            return getProductId(getFirstProduct());
        }

        function getLastProductId() {

            return getProductId(getLastProduct());
        }

        function getProductById(id) {

            return getProductTriggerById(id).closest(productSelector).first();
        }

        function getProductTriggerById(id) {

            return $('.xt_wooqv-trigger[data-id='+id+']');
        }

        function getProductContentById(id) {

            var trigger = getProductTriggerById(id);
            if(trigger.length) {
                return $($.parseJSON(trigger.data('quickview')));
            }

            return null;
        }

        function getProductId(product) {

            return product.length ? product.find('.xt_wooqv-trigger').data('id') : null;
        }

        function triggerProductQuickView(product) {

            var id;
            if(typeof(product) === "number") {
                id = product;
            }else{
                var trigger = product.find('.xt_wooqv-trigger');
                if(trigger.length) {
                    id = trigger.data('id');
                }
            }

            if(id) {

                quickView.velocity('stop');

                isVisible = false;
                recentProduct = null;
                loadProductInfo(id, null, function() {
                    isVisible = true;

                    if(!!XT_WOOQV.can_use_premium_code) {
                        checkNavigation();
                    }
                });
            }
        }

        function preloadImage(src, callback)
        {
            if(src) {
                var img=new Image();
                img.src=src;
                img.onload = callback(true);
                img.onerror = callback(false);
            }else{
                callback(false);
            }
        }

        function touchSupport() {

            if ('ontouchstart' in document.documentElement) {
                $('html').addClass('xt_wooqv-touchevents');
                return true;
            }

            $('html').addClass('xt_wooqv-no-touchevents');

            return false;
        }

        /**
         * When bottom > window.innerHeight, we know for a fact that the mobile browser footer bar is visible
         */
        function checkMobileOverflowBar() {

            var elementHeight = Math.floor($(this).height());

            if ((window.innerHeight < elementHeight)) {

                mobileBrowserFooterBarHeight = elementHeight - window.innerHeight;

                resizeInfoBoxHeight();

                $('html').addClass('xt_wooqv-mobile-bar-visible');


            } else {

                mobileBrowserFooterBarHeight = 0;

                $('html').removeClass('xt_wooqv-mobile-bar-visible');

                resizeInfoBoxHeight();
            }
        }

        $(function() {

            initVars();
            updateResponsiveVars();
            initEvents();
            triggerQuickViewResize();

        });

        window.xt_wooqv_resize = triggerQuickViewResize;
        window.xt_wooqv_resize_info = resizeInfoBoxHeight;
        window.xt_wooqv_open = _open;
        window.xt_wooqv_close = _close;
        window.xt_wooqv_is_modal_open = function() {
            return isVisible;
        };

        if(!!XT_WOOQV.can_use_premium_code) {
            window.xt_wooqv_previous = previousProduct;
            window.xt_wooqv_is_first = isFirstProduct;
            window.xt_wooqv_is_last = isLastProduct;
            window.xt_wooqv_next = nextProduct;
        }
    });


})( jQuery );
