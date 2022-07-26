/* global Assert, googletag */
function Doubleclick() {
    "use strict";
    var module = Doubleclick;

    var doubleclick_id = "3948140";
    var service_started = false;
    var banner_ad_min_width = 890;

    var slot_map = {
        "any": [],
        "large": [],
        "small": []
    };

    function execute_doubleclick_later(func) {
        googletag.cmd.push(function() {
            try {
                func();
            } catch(err) {
                console.info(
                    "A error was thrown in doubleclick it tried to hide:"
                );

                console.info("" + err);

                throw err;
            }
        });
    }

    function init_service() {
        if (service_started) {
            return;
        }

        execute_doubleclick_later(function() {
            // Believe it or not, calling enableSingleRequest is required
            // for letting us make multiple requests for endless pagination.
            if (!googletag.pubads().enableSingleRequest()) {
                throw new Error(
                    "enableSingleRequest failed for doubleclick!"
                );
            }

            // We must disable the initial ad load so we have complete control
            // over when ads load, so we can cause ads to load when they
            // are inserted into the page with endless pagination.
            googletag.pubads().disableInitialLoad();

            googletag.enableServices();
        });

        service_started = true;
    }

    function show_ads_in_list(slot_list) {
        for (var i = 0; i < slot_list.length; ++i) {
            var slot = slot_list[i];

            if (!slot.show_manually && !slot.show_called) {
                slot.show();
            }
        }
    }

    function show_ads_for_display_size(display_size) {
        show_ads_in_list(slot_map[display_size]);
    }

    function current_display_size() {
        if ($(window).width() >= banner_ad_min_width) {
            return "large";
        }
        else {
            return "any";
        }
    }

    module.create_slot = function(args) {
        var ad_unit = args.ad_unit;
        var id = args.id;
        var display_size = args.display_size;
        var ad_size = args.ad_size;
        var targets = args.targets;
        var show_manually = args.show_manually;

        if (Assert.enabled) {
            Assert.is_string("ad_unit", ad_unit);
            Assert.is_string("id", id);
            Assert.is_string("display_size", display_size);
            Assert.is_array("ad_size", ad_size);
            Assert.is_number("ad_size[0]", ad_size[0]);
            Assert.is_number("ad_size[1]", ad_size[1]);
            Assert.is_nullable_object("targets", targets);
            Assert.is_nullable_boolean("show_manually", show_manually);
        }

        if (slot_map[display_size] == null) {
            throw new Error("`display_size` must be one of: "
                + Object.keys(slot_map)
            );
        }

        // Start the service if needed.
        init_service();

        var gpt_slot;

        execute_doubleclick_later(function() {
            gpt_slot = googletag.defineSlot(
                "/" + doubleclick_id + "/" + ad_unit,
                ad_size,
                id
            );

            gpt_slot.addService(googletag.pubads());

            for (var key in targets) {
                if (targets.hasOwnProperty(key)) {
                    var value = targets[key];

                    if (value != null && value !== "") {
                        gpt_slot.setTargeting(key, value);
                    }
                }
            }
        });

        var slot_handler = {
            ad_unit: ad_unit,
            show_called: false,
            show_manually: show_manually === true,
            show: function() {
                slot_handler.show_called = true;

                execute_doubleclick_later(function() {
                    // Stop if the element just isn't there.
                    if (document.getElementById(id) == null) {
                        throw new Error(
                            "The div for the ad banner with id "
                            + "'" + id + "' "
                            + "Could not be found!"
                        );
                    }

                    googletag.display(id);
                    googletag.pubads().refresh([gpt_slot]);
                });
            }
        };

        if (current_display_size() === display_size) {
            slot_handler.show();
        }

        slot_map[display_size].push(slot_handler);

        return slot_handler;
    };

    // Set up code for executing slots when ready.
    $(function() {
        var last_display_size = current_display_size();

        $(window).resize(function() {
            var new_display_size = current_display_size();

            if (new_display_size !== last_display_size) {
                // Display different ads when the display size changes.
                last_display_size = new_display_size;

                show_ads_for_display_size(last_display_size);
            }
        });

        show_ads_for_display_size(last_display_size);

        // Show the ads appropriate for any display size.
        // This line doesnt do anything!!!!!!!!!!!
        show_ads_for_display_size("any");

    });

}

Doubleclick();