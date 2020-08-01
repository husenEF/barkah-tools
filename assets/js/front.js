let variant = [];
let total = 0;

function fbqcheck() {
  if (typeof fbq === "undefined") {
    console.log("notfound");
    setTimeout(fbqcheck, 500);
  } else {
    alert("Facebook pixel loaded");
  }
}

// fbqcheck();
jQuery(document).on("found_variation.first", function (e, v) {
  //   console.log("found_variation.first", { e, v });
  const attributsKey = Object.keys(v.attributes);

  variant.push({
    price: v.display_price,
    id: v.variation_id,
    sku: v.sku,
    name: v.attributes[attributsKey[0]],
  });
});
jQuery(document).ready(function ($) {
  let qty = $(".input-text.qty.text").val();
  let producTitle = $(".product_title.entry-title").text();
  $(".variations_form").on("woocommerce_variation_select_change", function (e) {
    console.log({ e });
  });

  $(".barkah-tools.add-to-cart").on("click", function (e) {
    const isDisabled = $(this).children("button").hasClass("disabled");
    const prodTitle = $(this).data("product-name");
    const currency = $(this).data("curency");
    const prodId = $(this).data("prod-id");
    const pixelName = $(this).data("pixel");
    let href = $(this).attr("href");

    total = variant.reduce((e, v) => e.price + v);
    if (variant.length > 0) {
      const varName = variant[0].name;
      href = href.replace("var:", `*Varian*: ${varName.toUpperCase()}%0D%0A`);
    } else {
      href = href.replace("var:", ``);
    }

    if (!isDisabled) {
      const payloadFb = {
        source: "barkah-tools",
        pluginVersion: "1.0.0",
      };

      $.ajax({
        type: "post",
        url: bt_ajax.ajax_url,
        data: {
          action: "bt_order_wa",
          prod_id: prodId,
        },
        success: function (results) {
          // console.log("bt-ajax", { results });
          const payLoad = { ...payloadFb, ...results, value: variant[0].price };
          // console.log("bt-ajax", { results, payLoad });
          fbq(
            "trackCustom",
            pixelName,
            // begin parameter object data
            payLoad
            // end parameter object data
          );
        },
        error: function (error) {
          console.log("bt-ajax-error", { error });
        },
      });

      setTimeout(function () {
        window.open(href, "_blank");
      }, 800);
      e.preventDefault();
    }
  });
});
