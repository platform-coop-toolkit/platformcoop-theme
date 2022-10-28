(function ($) {
  // Verification to jQuery works only in event template
  if ($('#pcc-event-template-unique-id').length == 0) {
    return;
  }
  // Take values from url
  let windowLocation = window.location;
  let url = new URL(windowLocation.href);
  let initialCategory = url.searchParams.get("cat") ?? 'all';
  let initialPage = url.searchParams.get("pg") ?? 1;

  // Apply url params and call ajax
  window.history.pushState(window.location.href, null, `?cat=${initialCategory}&pg=${initialPage}`);
  filterEvents(initialCategory, initialPage);

  // Add tab active class
  $(`[data-value="${initialCategory}"]`).addClass("active");

  // Push the data to url and call ajax to apply new filter and handle with tab active
  $(".events-category li").click(function () {
    $(".events-category li").removeClass("active");
    $(this).addClass("active");
    let category = $(this).data("value");
    window.history.pushState(window.location.href, null, `?cat=${category}&pg=1`);
    filterEvents(category, 1);
  });
  
  // Handle with pagination
  function navigationPage() {
    let newUrl = new URL(windowLocation.href);
    
    // Add params to next button
    $('.navigation .next').click(function (event) {
      event.preventDefault();
      filterEvents(newUrl.searchParams.get("cat"), parseInt(newUrl.searchParams.get("pg"), 10) + 1)
      window.history.pushState(window.location.href, null, `?cat=${newUrl.searchParams.get("cat")}&pg=${parseInt(newUrl.searchParams.get("pg"), 10) + 1}`);
      $("html, body").animate({ scrollTop: 200 }, "smooth");
    });

    // Add params to previous button
    $('.navigation .prev').click(function (event) {
      event.preventDefault();
      filterEvents(newUrl.searchParams.get("cat"), parseInt(newUrl.searchParams.get("pg"), 10) - 1)
      window.history.pushState(window.location.href, null, `?cat=${newUrl.searchParams.get("cat")}&pg=${parseInt(newUrl.searchParams.get("pg"), 10) - 1}`);
      $("html, body").animate({ scrollTop: 200 }, "smooth");
    });

    // Add pagination ajax call
    $('.navigation .page-numbers:not(.dots, .next, .prev)').click(function (event) {
      event.preventDefault();
      let pageValue = $(this).text();
      let params = newUrl.searchParams.get("cat") ?? 'all';
      window.history.pushState(window.location.href, null, `?cat=${params}&pg=${pageValue}`);
      filterEvents(params, pageValue)
      $("html, body").animate({ scrollTop: 200 }, "smooth");
    }) 
  }

  // Ajax function to filter events
  function filterEvents(category, currentPage) {
    $.ajax({
      url: filter_events_script_ajax.ajax_url,
      type: "POST",
      data: {
        category: category,
        currentPage: parseInt(currentPage, 10) ?? 1,
        action: "filter_events",
        nonce: filter_events_script_ajax.nonce,
      },
      beforeSend: function () {
        $(".events-container").html("Loading...");
      },
      success: function (response) {
        let posts = response.data.posts;
        let pagination = response.data.pagination;
        console.log(response.data.posts)
        if (!posts.length) {
          $(".events-container").html("No results");
        } else {
          $(".events-container").html("");
        }
        posts.forEach((post) => {
          $(".events-container")
            .append(`
              <article class="blog card post-2286 pcc-event type-pcc-event status-publish hentry content" style="cursor: pointer;">
                <figure class="blog__image">
                ${!post.img ? `
                  <div class="placeholder-wrap placeholder-wrap-event">
                      <svg class="placeholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78.76 78.76">
                        <defs>
                        <style>
                          .stroke{fill:none;stroke:currentColor;stroke-miterlimit:10;stroke-width:2px}.fill{fill:currentColor;}
                        </style>
                        </defs>
                        <path class="stroke" d="M39.38 55.13l-18-51.75-18 51.75h12.37v20.25H27V55.13z"></path>
                        <path class="stroke" d="M75.38 55.13l-18-51.75-18 51.75h12.37v20.25H63V55.13z"></path>
                        <circle class="fill" cx="21.38" cy="3.38" r="3.38"></circle><circle class="fill" cx="57.38" cy="3.38" r="3.38"></circle>
                        <circle class="fill" cx="39.38" cy="55.13" r="3.38"></circle><circle class="fill" cx="51.75" cy="55.13" r="3.38"></circle>
                        <circle class="fill" cx="63" cy="55.13" r="3.38"></circle><circle class="fill" cx="3.38" cy="55.13" r="3.38"></circle>
                        <circle class="fill" cx="15.75" cy="55.13" r="3.38"></circle><circle class="fill" cx="27" cy="55.13" r="3.38"></circle>
                        <circle class="fill" cx="51.75" cy="75.38" r="3.38"></circle><circle class="fill" cx="63" cy="75.38" r="3.38"></circle>
                        <circle class="fill" cx="15.75" cy="75.38" r="3.38"></circle><circle class="fill" cx="27" cy="75.38" r="3.38"></circle>
                        <circle class="fill" cx="75.38" cy="55.13" r="3.38"></circle>
                      </svg>      
                  </div>`
                : 
                `<img src="${post.img}" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="">`
                }
                </figure>
                <div class="blog__details">
                  <header class="text">
                    <h2 class="title"><a href="${post.link ?? "#"}">${ post.post_title ?? "No title"}</a></h2>
                  </header>
                  <time class="updated" datetime="2020-03-13T13:38:04+00:00">${post.start ?? ''}</time>
                </div>
              </article>`);
        });

        if (!pagination) {
          $(".pagination").html("");
        } else {
          $(".pagination").html(pagination);
          navigationPage();
        }
      },
      error: function (error) {
        console.log(error);
      },
    });
  }
})(jQuery);
