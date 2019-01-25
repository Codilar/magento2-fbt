<!--
/**
 * Codilar_Afbt extension
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
        var Afbt = {
            fetchUrl: config.afbtFetchUrl,
            parentProductId: config.product_id,
            cartAddUrl: config.addToCartUrl,
            form_key: config.form_key,
            init: function () {
                var self = this;
                self.getAssociatedProducts();
            },
            getAssociatedProducts: function () {
                var self = this;
                $.ajax({
                    url: self.fetchUrl,
                    method: "POST",
                    data: {product_id: self.parentProductId},
                    success: function (response) {
                        if (response.status) {
                            require([
                                "text!"+require.toUrl("Codilar_Afbt/template/afbt_cards.html")
                            ], function (template) {
                                template = _.template(template);
                                $(".afbt-container").html(template({
                                    productsData: response
                                }));
                                var owl = $("#fbt-carousel-blog");
                                owl.owlCarousel({
                                    loop:false,
                                    nav:true,
                                    margin:15,
                                    navText: ['<div class="left-arrow">', '<div class="right-arrow">'],
                                    responsive:{
                                        0:{
                                            items:1,
                                            margin:0
                                        },
                                        600:{
                                            items:1,
                                            margin:0
                                        },
                                        1000:{
                                            items:3
                                        }
                                    }
                                });
                                checkClasses();
                                owl.on('translated.owl.carousel', function(event) {
                                    checkClasses();
                                });

                                function checkClasses(){
                                    var total = $('.fbt-container .owl-stage .owl-item.active').length;

                                    $('.fbt-container .owl-stage .owl-item').removeClass('lastActiveItem');

                                    $('.fbt-container .owl-stage .owl-item.active').each(function(index){
                                        if (index === total - 1 && total>1) {
                                            // this is the last one
                                            $(this).addClass('lastActiveItem');
                                        }
                                    });
                                }
                            });
                            self.processAddCart();
                        } else {
                            $(".afbt-container").html("");
                        }
                    }
                });
            },
            processAddCart: function () {
                var self = this;
                $(document).on("click", ".afbt-container .action.tocart.primary.afbt", function () {
                    var parentProductId = $(this).closest("form").find("[name='product_parent']").val();
                    var associatedProductId = $(this).closest("form").find("[name='product_associated']").val();
                    var products = [];
                    products.push(parentProductId);
                    products.push(associatedProductId);
                    if (parentProductId && associatedProductId) {
                        $.ajax({
                            url: self.cartAddUrl+"product/"+parentProductId+"/",
                            method: "POST",
                            showLoader: true,
                            data: {products: products, from_afbt: 1},
                            success: function (response) {
                                console.log(response);
                            }
                        });
                    }
                });
            },
            getSimpleProductId: function () {
                var selected_options = {};
                jQuery('div.swatch-attribute').each(function(k,v){
                    var attribute_id    = jQuery(v).attr('attribute-id');
                    var option_selected = jQuery(v).attr('option-selected');
                    //console.log(attribute_id, option_selected);
                    if(!attribute_id || !option_selected){ return;}
                    selected_options[attribute_id] = option_selected;
                });

                var product_id_index = jQuery('[data-role=swatch-options]').data('mageSwatchRenderer').options.jsonConfig.index;
                var found_ids = [];
                //console.log(product_id_index);
                jQuery.each(product_id_index, function(product_id,attributes){
                    //console.log(product_id);
                    var productIsSelected = function(attributes, selected_options){
                        return _.isEqual(attributes, selected_options);
                    }
                    if(productIsSelected(attributes, selected_options)){
                        found_ids.push(product_id);
                    }
                });

                //console.log(found_ids);

                if (found_ids.length) {
                    var selected_product_id = found_ids[0];
                    console.log(selected_product_id);
                }
            }
        };
        /** initialize the afbt widget */
        Afbt.init();
    };
});