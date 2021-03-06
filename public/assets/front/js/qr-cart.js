var pprice = 0.00;
// lazy load init
var lazyLoadInstance = new LazyLoad();

function totalPrice(qty) {
    qty = qty.toString().length > 0 ? qty : 0;
    let $variant = $("input[name='variant']:checked");
    let $addons = $("input[name='addons']:checked");


    let vprice = $variant.length > 0 ? $variant.data('price') : 0.00;
    let total = parseFloat(pprice) + parseFloat(vprice);

    if ($addons.length > 0) {
        $addons.each(function() {
            total += parseFloat($(this).data('price'));
        });
    }

    total = total.toFixed(2) * parseInt(qty);

    if ($("#productPrice").length > 0) {
        $("#productPrice").html(total.toFixed(2));
    }

    return total.toFixed(2);
}

function addToCart(url, variant, qty, addons , notes) {
    let cartUrl = url;

    // button disabled & loader activate (only for modal add to cart button)
    $(".modal-cart-link").addClass('disabled');
    $(".modal-cart-link span").removeClass('d-block');
    $(".modal-cart-link span").addClass('d-none');
    $(".modal-cart-link i").removeClass('d-none');
    $(".modal-cart-link i").addClass('d-inline-block');
    $("textarea[name='notes']").val("");

    console.log(cartUrl + ',,,' + qty + ',,,' + totalPrice(qty) + ',,,' + JSON.stringify(variant) + ',,,' + JSON.stringify(addons));


    $.get(cartUrl + ',,,' + qty + ',,,' + totalPrice(qty) + ',,,' + JSON.stringify(variant) + ',,,' + JSON.stringify(addons)  + ',,,' + notes, function(res) {

        $(".request-loader").removeClass("show");

        // button enabled & loader deactivate (only for modal add to cart button)
        $(".modal-cart-link").removeClass('disabled');
        $(".modal-cart-link span").removeClass('d-none');
        $(".modal-cart-link span").addClass('d-block');
        $(".modal-cart-link i").removeClass('d-inline-block');
        $(".modal-cart-link i").addClass('d-none');

        if (res.message) {
            toastr["success"](res.message);
            $("#cartQuantity").load(location.href + " #cartQuantity");
        } else {
            toastr["error"](res.error);
        }
    });
}

(function($) {
    "use strict";

    // ============== add to cart js start =======================//

    $(".cart-link").on('click', function(e) {
        e.preventDefault();
        let product = $(this).data('product');
        let variations = JSON.parse(product.variations);
        let addons = JSON.parse(product.addons);
        // set product current price
        pprice = product.current_price;

        // clear all previously loaded variations & addon input radio & checkboxes 
        $(".variation-label").addClass("d-none");
        $("#variants").html("");
        $(".addon-label").addClass("d-none");
        $("#addons").html("");

        // load variants & addons in modal if variations or addons available for this item
        if ($('#variationModal')) {
            $("#variationModal").modal('show');

            // set modal title & quantity
            $("#variationModal .modal-title > span").html(product.title);
            $("input[name='cart-amount']").val(1);

            if (variations != null) {
                $(".variation-label").removeClass("d-none");

                // load variations radio button input fields
                let variants = ``;
                for (let i = 0; i < variations.length; i++) {
                    variants += `<div class="form-check d-flex justify-content-between">
                        <div>
                            <input class="form-check-input" type="radio" name="variant" id="variant${i}" value="${variations[i].name}" data-price="${variations[i].price.toFixed(2)}" ${i == 0 ? 'checked' : ''}>
                            <label class="form-check-label" for="variant${i}">
                            ${variations[i].name}
                            </label>
                        </div>
                        <span>
                            + ${textPosition == 'left' ? currText : ''} ${variations[i].price} ${textPosition == 'right' ? currText : ''}
                        </span>
                    </div>`
                }
                $("#variants").html(variants);


                // add margin top if variations available
                $(".addon-label").addClass('mt-3');
            } else {
                $(".addon-label").removeClass('mt-3');
            }

            if (addons != null) {
                $(".addon-label").removeClass("d-none");

                // load addons checkbox input fields
                let addonHtml = ``;
                for (let i = 0; i < addons.length; i++) {
                    addonHtml += `<div class="form-check d-flex justify-content-between">
                        <div>
                            <input class="form-check-input" type="checkbox" name="addons" id="addon${i}" value="${addons[i].name}" data-price="${addons[i].price.toFixed(2)}">
                            <label class="form-check-label" for="addon${i}">
                            ${addons[i].name}
                            </label>
                        </div>
                        <span>
                            + ${textPosition == 'left' ? currText : ''} ${addons[i].price} ${textPosition == 'right' ? currText : ''}
                        </span>
                    </div>`
                }
                $("#addons").html(addonHtml);
            }
            if (addons == null && variations == null) {
                $('.modal-body').hide();
            } else {
                $('.modal-body').show();
            }

            // set modal price
            totalPrice(1)

            $(".modal-cart-link").attr('data-product_id', product.id);

        } else {
            $(".request-loader").addClass("show");

            let $this = $(this);
            let url = $this.attr('data-href');
            let qty = $("#detailsQuantity").length > 0 ? $("#detailsQuantity").val() : 1;
            let notes = $("textarea[name='notes']").val();

            addToCart(url, "", qty, "" , notes);
        }



    });

    // ============== add to cart js end =======================//


    // ============== variation modal add to cart start =======================//
    $(document).on('click', '.modal-cart-link', function() {
        let variantName = $("input[name='variant']:checked").val();
        let variantPrice = $("input[name='variant']:checked").data('price');
        let qty = $("input[name='cart-amount']").val();
        let notes = $("textarea[name='notes']").val();
        let pid = $(this).attr('data-product_id');
        let url = mainurl + "/add-to-cart/" + pid;

        let variant = { name: variantName, price: variantPrice };

        let $addons = $("input[name='addons']:checked");
        let addons = [];
        if ($addons.length > 0) {
            $addons.each(function() {
                addons.push({ name: $(this).val(), price: $(this).data('price') });
            });
        }

        addons = addons.length > 0 ? addons : "";
        qty = qty.length > 0 ? parseInt(qty) : 0;

        addToCart(url, variant, qty, addons , notes);
        $("#variationModal").modal('hide');
    });
    // ============== variation modal add to cart end =======================//


    // ============== modal quantity add / substruct =======================//
    $(document).on("click", ".modal-quantity .plus", function() {
        let $input = $(".modal-quantity input");
        let currval = parseInt($input.val());

        let newval = currval + 1;
        $input.val(newval);
        totalPrice(newval);
    });
    $(document).on("click", ".modal-quantity .minus", function() {
        let $input = $(".modal-quantity input");
        let currval = parseInt($input.val());

        if (currval > 1) {
            let newval = currval - 1;
            $input.val(newval);
            totalPrice(newval);
        }
    });
    // ============== modal quantity add / substruct =======================// 


    // ============== variant change js start =======================//
    $(document).on('change', '#variants input', function() {
        totalPrice($("input[name='cart-amount']").val());
    });
    // ============== variant change js end =======================//


    // ============== addon change js start =======================//
    $(document).on('change', '#addons input', function() {
        totalPrice($("input[name='cart-amount']").val());
    });
    // ============== addon change js end =======================//


    // ============== addon change js start =======================//
    $(document).on('input', "input[name='cart-amount']", function() {
        totalPrice($("input[name='cart-amount']").val());
    });
    // ============== addon change js end =======================//


    //=============== cart update js start ==========================//

    $(document).on('click', '#cartUpdate', function() {
        $(".request-loader").addClass("show");
        let cartqty = [];
        let cartprice = [];
        let cartproduct = [];
        let cartUpdateUrl = $(this).attr('data-href');

        $(".cart_qty").each(function() {
            cartqty.push($(this).val());
        })

        let formData = new FormData();
        let i = 0;
        for (i = 0; i < cartqty.length; i++) {
            formData.append('qty[]', cartqty[i]);
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: cartUpdateUrl,
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                $(".request-loader").removeClass("show");
                if (data.message) {
                    $("#cartQuantity").load(location.href + " #cartQuantity");
                    toastr["success"](data.message);
                } else {
                    toastr["error"](data.error);
                }

            }
        });
    })


    //================= cart update js end ==========================//

    // ================ cart item remove js start =======================//

    $(document).on('click', '.item-remove', function() {
        $(".request-loader").addClass("show");
        let removeItemUrl = $(this).attr('data-href');
        $.get(removeItemUrl, function(res) {
            $(".request-loader").removeClass("show");
            if (res.message) {
                $("#refreshDiv").load(location.href + " #refreshDiv");
                $("#cartQuantity").load(location.href + " #cartQuantity");
                toastr["success"](res.message);
            } else {
                toastr["error"](res.error);
            }

        });

    });


    // ================ cart item remove js start =======================//


    $('.addclick').on('click', function() {
        let orderamount = $('#detailsQuantity').val();
        $('#order_click_with_qty').val(orderamount);
    });
    $('.subclick').on('click', function() {
        let orderamount = $('#detailsQuantity').val();
        $('#order_click_with_qty').val(orderamount);
    });

    // ============== cart sidebar toggle start =======================//
    $(document).on('click', ".cart-icon", function() {
        $(".cart-sidebar").addClass('show');
        $(".cart-sidebar-overlay").addClass('show');
        $("#refreshDiv").load(location.href + " #refreshDiv", function() {
            console.log('loaded');
            setTimeout(function() {
                $(".cart-sidebar-loader-container").removeClass('show');
            }, 100);
            lazyLoadInstance.update();
        });
    });
    $(document).on('click', ".cart-header .close, .cart-sidebar-overlay", function() {
        $(".cart-sidebar").removeClass('show');
        $(".cart-sidebar-overlay").removeClass('show');
        $(".cart-sidebar-loader-container").addClass('show');
    });
    // ============== cart sidebar toggle end =======================// 


}(jQuery));