/**
 * Customizer Communicator
 */
( function ( api, $ ) {
    "use strict";

    api.bind("ready", function() {

        function getSectionResponsiveFields(section) {

            return XTFW_CUSTOMIZER_CTRL.responsive_fields.filter(function (item) {
                return item.section === section;
            });
        }

        function injectDeviceSwitcher(fields) {

            fields.forEach(function (item) {

                var container = api.control(item.config_id+'['+item.id+']').container;
                if(container.find('.xirki-devices-wrapper').length === 0) {
                    container.prepend(XTFW_CUSTOMIZER_CTRL.device_switcher);

                    item.hidden_screens.forEach(function(hidden) {

                        container.find('.xirki-devices-wrapper').find('.preview-'+hidden).hide();
                    });
                }
            });
        }

        api.section.each( function ( section ) {

            section.expanded.bind( function( isExpanding ) {

                if(isExpanding){

                    var fields = getSectionResponsiveFields(section.id);
                    injectDeviceSwitcher(fields);
                }

            });

        });

        $(document).on('click', '.xirki-devices button', function() {

            var device = $(this).data('device');

            api.previewedDevice.set(device);
        });
    });

} )( wp.customize, jQuery );