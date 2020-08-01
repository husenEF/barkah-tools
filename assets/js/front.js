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
    console.log({ name, val });
  });

  $(".variations_form").on("woocommerce_variation_select_change", function (e) {
    console.log({ e });
  });

  const getQty = async () => {
    const qtys = $(".qty").toArray();
    // console.log(qtys.toArray());
    let total = 0;
    let ret = [];

    qtys.length > 0
      ? await qtys.map((e) => {
          let id = $(e).attr("name");
          id = id.match(/\d+/g);
          id = id[0];
          const val = $(e).val();

          $.ajax({
            type: "post",
            url: bt_ajax.ajax_url,
            data: {
              action: "bt_order_wa",
              prod_id: id,
            },
            success: function (results) {
              // const payLoad = { ...payloadFb, ...results, value: variant[0].price };
              // console.log("bt-ajax", { results, payLoad });
              // fbq(
              //   "trackCustom",
              //   pixelName,
              //   // begin parameter object data
              //   payLoad
              //   // end parameter object data
              // );
              const ttotal = parseFloat(results.price * val);
              ret.push({
                id,
                price: results.price,
                total: ttotal,
              });
              total += ttotal;
            },
            error: function (error) {
              console.log("bt-ajax-error", { error });
            },
          });
        })
      : null;
    let red = 0;
    if (ret.length > 0) {
      red = ret.reduce((e, b) => console.log({ e, b }));
    }
    console.log({ ret, red, total });
    // return ret;
  };

  $(".barkah-tools.add-to-cart").on("click", function (e) {
    const isDisabled = $(this).children("button").hasClass("disabled");
    const prodTitle = $(this).data("product-name");
    const currency = $(this).data("curency");
    const prodId = $(this).data("prod-id");
    const pixelName = $(this).data("pixel");
    const productType = $(this).data("type");
    let href = $(this).attr("href");


    if (productType === "grouped") {
      // getQty();
    }

    if (variant.length > 0) {
      total = variant.reduce((e, v) => e.price + v);
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
