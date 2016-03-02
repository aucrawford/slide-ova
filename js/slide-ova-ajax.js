// blank
'use strict';
jQuery(document).ready(function($) {
  function show_slide(myPostID) {
    jQuery.post(
      slide_ova_ajax_script.ajaxurl,
      {
        action: 'the_ajax_hook',
        postID : myPostID
      } ,

        ///////////////////
       // Call the Data //
      ///////////////////

      function(data) {
        // set the image data to an html element
        var slide_url = data.image
          , slide
          // , video_link = data.video
          // , slide_video
          ;
          if (slide != null) {
            slide = '<img src="' + slide_url + '" class="img-responsive" />';
          } else {
            slide = "<p>Sorry, No image Available.</p>";
          }
        // if (video_link != null) {
        //   youtube_video = video_link.indexOf('youtu');
        //   vimeo_video = video_link.indexOf('vimeo');
        //   if (youtube_video != null) {
        //     video_link = video_link.split('.be/');
        //     slide_video = '<iframe src="http://www.youtube.com/embed/' + video_link[1] + '?rel=0" frameborder="0" width="560" height="315" allowfullscreen></iframe>';
        //   } else if (vimeo_vid != null) {
        //     video_link = video_link.split('.com/');
        //     slide_video = '<iframe src="http://player.vimeo.com/video/' + video_link[1] + '" frameborder="0" width="560" height="315" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        //   } else {
        //     slide_video = "<p>Sorry, No Video Available.</p>";
        //   }
        // }
        // populate content
        jQuery(".slide").html(slide);
        jQuery(".slide-desc h2").html(data.title);
        jQuery(".slide-desc p").html(data.content);
      }
    );
  }
    ////////////////////////////////
   // Call the function on click //
  ////////////////////////////////

  $('.slide-preview').click( function(e) {
    e.preventDefault();
    var $this = $(this)
      , slide_ID = $this.attr('id')
      , slide_ID = slide_ID.replace('slide-', '')
      , slide_URL = $this.find('img').attr('src')
      , $slide = $this.closest('.slide-container').find('.slide-image')
      ;
    $this.find('img').addClass('active');
    $this.siblings().find('img').removeClass('active');
    // console.log($slide);
    // $slide.html('Hello');
    $slide.html('<img src="' + slide_URL + '" class="img-responsive" />');
    //show_slide(slide_ID);
  });
});