jQuery(document).ready(function($) {
  // AJAX Begin //
  // function show_slide(myPostID) {
  //   jQuery.post(
  //     slide_ova_ajax_script.ajaxurl,
  //     {
  //       action: 'the_ajax_hook',
  //       postID : myPostID
  //     } ,

  //     // call the data
  //     function(data) {
  //       // set the video data to an html element
  //       var slide_url = data.image
  //         , slide
  //         // , video_link = data.video
  //         // , slide_video
  //         ;
  //         if (slide != null) {
  //           slide = '<img src="' + slide_url + '" class="img-responsive" />';
  //         } else {
  //           slide = "<p>Sorry, No image Available.</p>";
  //         }
  //       // if (video_link != null) {
  //       //   youtube_video = video_link.indexOf('youtu');
  //       //   vimeo_video = video_link.indexOf('vimeo');
  //       //   if (youtube_video != null) {
  //       //     video_link = video_link.split('.be/');
  //       //     slide_video = '<iframe src="http://www.youtube.com/embed/' + video_link[1] + '?rel=0" frameborder="0" width="560" height="315" allowfullscreen></iframe>';
  //       //   } else if (vimeo_vid != null) {
  //       //     video_link = video_link.split('.com/');
  //       //     slide_video = '<iframe src="http://player.vimeo.com/video/' + video_link[1] + '" frameborder="0" width="560" height="315" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
  //       //   } else {
  //       //     slide_video = "<p>Sorry, No Video Available.</p>";
  //       //   }
  //       // }
  //       // populate content
  //       jQuery("#slide-ova .slide").html(slide);
  //       jQuery("#slide-ova .slide-desc h2").html(data.title);
  //       jQuery("#slide-ova .slide-desc p").html(data.content);
  //     }
  //   );
  // }
  // // call the ajax function on click
  // $('#slide-ova-containa .slides-holder a').click( function(e) {
  //   e.preventDefault();
  //   slide_ID = $(this).attr('id');
  //   slide_ID = slide_ID.replace('slide-', '');
  //   $(this).find('img').addClass('active');
  //   $(this).siblings().find('img').removeClass('active');
  //   show_slide(slide_ID);
  // });
  // AJAX END //

    ////////////////////////////////////////////////////////////////////////////
   //                                                      Galleries Rotator //
  ////////////////////////////////////////////////////////////////////////////

  var $galleries_container     = $('#slide-ova-galleries')
    , $galleries_wrapper       = $galleries_container.find('.galleries-wrapper')
    , $galleries_holder        = $galleries_container.find('.galleries-holder')
    , $gallery_preview         = $galleries_container.find('.galleries-holder .gallery-preview')
    , $galleries_set_selectors = $galleries_container.find('.galleries-set-selectors')
    , total_galleries          = $gallery_preview.length
    , gallery_preview_width    = $gallery_preview.outerWidth(true)
    , galleries_set_width      = gallery_preview_width * 4
    , galleries_holder_width   = 0
    ;

    ///////////////////////////
   // Gallery Rotator Width //
  ///////////////////////////

  $gallery_preview.each(function() {
     galleries_holder_width += gallery_preview_width;
  });
  $('.galleries-holder').width(galleries_holder_width);


    ////////////////////////
   // Gallery Pagination //
  ////////////////////////

  // for every eight add one dot and number them appropriately
  for(var i = 0; i < total_galleries; i+=8) {
    var n =  i / 8
      , s = n + 1
      ;
    $galleries_set_selectors.append('<li class="set" data-target="' + n + '">' + s + '</li>');
  }
  // set the active default
  $galleries_container.find('.set:nth-child(1)').addClass('active');
  // animate on click
  $('#slide-ova-galleries .set').click( function(){
    var galleries_set_number = $(this).attr('data-target');
    // go right
    $galleries_holder.clearQueue().animate({
      left: (galleries_set_width * galleries_set_number) * -1
    });
    // make what was clicked active and reset the others
    $(this).addClass('active').siblings().removeClass('active');
  });

    ////////////////////////////////////////////////////////////////////////////
   //                                                  Modal Gallery Rotator //
  ////////////////////////////////////////////////////////////////////////////

  var $slides_container     = $('.modal .slide-container')
    , $slides_wrapper       = $('.slides-wrapper')
    , $slides_holder        = $('.slides-holder')
    , $slide_preview        = $('.slide-preview')
    , $slides_set_selector  = $('.slides-set-selector')
    , total_slides          = $slide_preview.length
    , slide_preview_width   = $slide_preview.outerWidth(true)
    , slides_set_width      = slide_preview_width * 3
    , slides_holder_width   = 0
    ;

  // set the width of the slides wrapper
  $slide_preview.each(function() {
     slides_holder_width += slide_preview_width;
  });
  $('.slides-holder').width(slides_holder_width);

  // for set the selector width
  for(var i = 0; i < total_slides; i+=6) {
    var n =  i / 6
      , s = n + 1
      , w = (slides_holder_width / s)
      ;

    $slides_set_selector.append( s ).width(w);
  }
  // click and drag
  // $slides_set_selector.on('mousedown' function(){

  // });
});
