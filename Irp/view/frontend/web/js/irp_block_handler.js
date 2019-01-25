<!--
/**
 * Codilar_Irp extension
 * NOTICE OF LICENSE
 *
 *
 * @category  Codilar
 * @package   Codilar_Afbt
 * @copyright Copyright (c) 2019
 */
-->
define(['jquery', 'underscore', 'owlCarousel'], function ($, _, owlCarousel) {
    return function (config) {
        var Irp = {
            fetchUrl: config.irpFetchUrl,
            parentProductId: config.product_id,
            init: function () {
                var self = this;
                self.getRelatedProducts();
            },
            getRelatedProducts: function () {
                var self = this;
                $.ajax({
                    url: self.fetchUrl,
                    method: "POST",
                    data: {product_id: self.parentProductId},
                    success: function (response) {
                        if (response.status) {
                            require([
                                "text!"+require.toUrl("Codilar_Irp/template/irp_cards.html")
                            ], function (template) {
                                template = _.template(template);
                                $(".irp-container").html(template({
                                    productsData: response.products
                                }));
                                var owl = $("#related-carousel-blog");
                                owl.owlCarousel({
                                    loop: false,
                                    nav: true,
                                    margin: 15,
                                    navText: ['<div class="left-arrow">', '<div class="right-arrow">'],
                                    responsive: {
                                        0: {
                                            items: 2,
                                            margin: 0
                                        },
                                        600: {
                                            items: 2,
                                            margin: 0
                                        },
                                        1000: {
                                            items: 5
                                        }
                                    }
                                });
                                checkClasses();
                                owl.on('translated.owl.carousel', function (event) {
                                    checkClasses();
                                });

                                function checkClasses() {
                                    var total = $('.fbt-container .owl-stage .owl-item.active').length;

                                    $('.fbt-container .owl-stage .owl-item').removeClass('lastActiveItem');

                                    $('.fbt-container .owl-stage .owl-item.active').each(function (index) {
                                        if (index === total - 1 && total > 1) {
                                            // this is the last one
                                            $(this).addClass('lastActiveItem');
                                        }
                                    });
                                }
                            });
                        } else {
                            $(".irp-container").html("");
                        }
                    }
                });
            }
        };
        /** initialize the irp widget */
        Irp.init();
    };
});