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
  $(".woocommerce").on("change", "input.qty", function (e) {
    // $("[name='update_cart']").trigger("click");
    const name = $(this).attr("name");
    const val = $(this).val();
    // console.log({ name, val });
    total += parseFloat(val);
  });

  $(".variations_form").on("woocommerce_variation_select_change", function (e) {
    console.log({ e });
  });

  $(".barkah-tools.add-to-cart").on("click", function (e) {
    const isDisabled = $(this).children("button").hasClass("disabled");
    const prodTitle = $(this).data("product-name");
    const currency = $(this).data("curency");
    const prodId = $(this).data("prod-id");
    const pixelName = $(this).data("pixel");
    const productType = $(this).data("type");
    let href = $(this).attr("href");

    if (productType === "grouped") {
      const qty = $(".qty").toArray();

      console.log({ total });
    }

    if (variant.length > 0) {
      total = variant.reduce((e, v) => e.price + v);
      const varName = variant[0].name;
      let varText = `*Varian*: ${varName.toUpperCase()}%0D%0A`;
      if (total > 0) varText = `${varText}, *Total*: ${total}%0D%0A`;
      href = href.replace("var:", varText);
    } else {
      let varText = `*Varian*: -`;
      if (total > 0) varText = `${varText}, *Total*: ${total}%0D%0A`;
      href = href.replace("var:", varText);
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
          let value = 0;
          if (variant.length > 0) value = variant[0].price;

          if (productType === "grouped") value = parseFloat(results.price);
          const payLoad = { ...payloadFb, ...results, value };
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
