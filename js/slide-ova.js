jQuery(document).ready(function($) {
  // AJAX Begin //
  function show_video(myPostID) {
    jQuery.post(
      the_ajax_script.ajaxurl,
      {
        action: 'the_ajax_hook',
        postID : myPostID
      } ,

      // call the data
      function(data) {
        // set the video data to an html element
        var video_link = data.video,
            slide_video;
        if (video_link != null) {
          youtube_video = video_link.indexOf('youtu');
          vimeo_video = video_link.indexOf('vimeo');
          if (youtube_video != null) {
            video_link = vid_link.split('.be/');
            slide_video = '<iframe src="http://www.youtube.com/embed/' + video_link[1] + '?rel=0" frameborder="0" width="560" height="315" allowfullscreen></iframe>';
          } else if (vimeo_vid != null) {
            vid_link = video_link.split('.com/');
            slide_video = '<iframe src="http://player.vimeo.com/video/' + video_link[1] + '" frameborder="0" width="560" height="315" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
          } else {
            slide_video = "<p>Sorry, No Video Available.</p>";
          }
        }
        // populate content
        jQuery("#slide-ova .slide").html(slide_video);
        jQuery("#slide-ova .slide-desc h2").html(data.title);
        jQuery("#slide-ova .slide-desc p").html(data.content);
      }
    );
  }
  // call the ajax function on click
  $('#slide-ova-containa .slides-holder a').click( function(e) {
    e.preventDefault();
    video_ID = $(this).attr('id');
    video_ID = video_ID.replace('slide-', '');
    $(this).find('img').addClass('active');
    $(this).siblings().find('img').removeClass('active');
    show_video(video_ID);
  });
  // AJAX END //

  // VIDEO IMAGE ROTATOR BEGIN //

  var $slides_wrapper       = $('#slide-ova-containa .slides-wrapper')
    , $slides_holder        = $('#slide-ova-containa .slides-holder')
    , $slide_preview        = $('#slide-ova-containa .slides-holder a')
    , $slides               = $slides_holder.find('.slide-preview')
    , total_slides          = $slides.length
    , slide_preview_margins = parseInt($slide_preview.css("marginLeft").replace('px', '')) + parseInt($slide_preview.css("marginRight").replace('px', ''))
    , slide_preview_width   = $slide_preview.width() + slide_preview_Margins
    , slides_set_width      = slide_preview_width * 3
    , $slides_set_selectors = $slides_wrapper.find('.slides-set-selectors')
    , slides_holder_width   = 0
    ;

  // set the width of the slides wrapper
  $slide_preview.each(function() {
     slides_holder_width += slide_preview_width;
  });
  $('.slides-holder').width(slides_holder_width);

  // create the dots
  // for every three add one dot and number them appropriately
  for(var i = 0; i < total_slides; i+=3) {
    var n =  i / 3;
    $slides_set_selectors.append('<li class="set" data-target="' + n + '">' + n + '</li>');
  }
  // set the first dot as the active devault
  $('#slide-ova-containa .set:nth-child(1)').addClass('active');

  // animate the rotator
  $('#slide-ova-containa .set').click( function(){
    var slides_set_number = $(this).attr('data-target');
    // animate right
    $slides_holder.clearQueue().animate({
      left: (slides_set_width * slides_set_number) * -1
    });
    // make what was clicked active
    $(this).addClass('active').siblings().removeClass('active');
  });
});
