jQuery(document).ready(function($){

  $.fn.randomize = function(childElem) {
    return this.each(function() {
        var $this = $(this);
        var elems = $this.children(childElem);

        elems.sort(function() { return (Math.round(Math.random())-0.5); });

        $this.remove(childElem);

        for(var i=0; i < elems.length; i++)
          $this.append(elems[i]);

    });
  }

  $slideshow = $('#video-slideshow');

  $slideshow.find('#video-slideshow-nav a').click(function(e){
    e.preventDefault();
    var video_url = $(this).attr('href');
    var re = /\d+/;
    var vimeo_id = re.exec(video_url);

    var $embed = $slideshow.find('.embed iframe');
    var embed_src = 'http://player.vimeo.com/video/' + vimeo_id[0]  + '?title=0&byline=0&portrait=0';

    if( embed_src != $embed.attr('src') ) {
      $embed.attr('src', embed_src );
      $slideshow.find('li').removeClass('active');
      $(this).parents('li').addClass('active');
    }
    return false;
  });

  $slideshow.find('#video-slideshow-nav').randomize("li");
  $slideshow.find('#video-slideshow-nav li:first-child a').click();

});

