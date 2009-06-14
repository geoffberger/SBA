$(document).ready(function(){
  $('#search').AutoPopulateInput({attr:'title'});

  $('.main-nav li a').TitleToolTip();

  $('#cart-form-buttons #edit-checkout').Buttonizer({attributes:{'class':'sup-alt'}});
  $('#cart-form-buttons #edit-update').Buttonizer();

  $('#checkout-form-bottom #edit-cancel').Buttonizer();
  $('#checkout-form-bottom #edit-continue').Buttonizer({attributes:{'class':'sup-alt'}});

  $('#uc-cart-checkout-review-form #edit-back').Buttonizer();
  $('#uc-cart-checkout-review-form #edit-submit').Buttonizer({attributes:{'class':'sup-alt'}});

  $('#comments #edit-submit').Buttonizer({attributes:{'class':'sup-alt'}});
  $('#comments #edit-preview').Buttonizer();

  $('#user-pass #edit-submit').Buttonizer({attributes:{'class':'sup-alt'}});
  $('#user-login #edit-submit').Buttonizer({attributes:{'class':'sup-alt'}});
  $('#user-register #edit-submit').Buttonizer({attributes:{'class':'sup-alt'}});

  if($('.slider').length > 0) {
    $('.slider').wrap('<div class="slider-wrap"></div>');
    $('.slider-wrap').easySlider();
  }

  if($('.node-add-to-cart').length > 0) {
    $('.node-add-to-cart').hide();
    $('.node-add-to-cart').after('<button class="explicit" type="submit" name="op">' + $('.node-add-to-cart').attr('value') + '</button>');
  }

  if($('.product-info').length > 0) {
    var i = 0;
    $('.product-info a[href="#toggle-1"]').addClass('current-toggle');
    $('.product-info').children('div').hide();
    $('.product-info div#toggle-1').show();

    $('.product-info li a').click(function() {
      //Apply correct toggle class
      $('.product-info li a').removeClass('current-toggle');
      $(this).addClass('current-toggle');

      //Return correct div
      $('.product-info').children('div').hide();
      var id = $(this).attr('href');
      $('.product-info').find(id).show();

      return false;
    });
  }

});

(function($) {
  $.fn.Buttonizer = function(options) {
    var defaults = {
      attributes:{},
    };

    var options = $.extend(defaults, options);

    return this.each(function() {
      var attrs = '';

      $.each(options.attributes, function(key, value) {
        attrs += key + '="' + value + '" ';
      });

      $(this).hide();
      $(this).after('<button id="' + $(this).attr('id') + '" value="' + $(this).attr('value') + '" type="submit" name="op" ' + attrs + '>' + $(this).attr('value') + '</button>');
      $('button[value="' + $(this).attr('value') + '"]').addClass('supporting');
    });
  };
})(jQuery);

(function($) {
  $.fn.AutoPopulateInput = function(options) {
    var defaults = {
      attr: 'value'
    };

    var options = $.extend(defaults, options);

    return this.each(function() {
      var text_val = $(this).attr(options.attr);
      $(this).attr('value', text_val);

      $(this).focus(function(){
        var input_val = $(this).attr('value');
        var focus_text = (text_val != input_val) ? input_val : '';
        $(this).attr('value', focus_text);
      });

      $(this).blur(function(){
        var input_val = $(this).attr('value');
        var blur_text = (input_val) ? input_val : text_val;
        $(this).attr('value', blur_text);
      });
    });
  }

})(jQuery);

(function($) {
  $.fn.TitleToolTip = function(options) {
    var defaults = '';

    var options = $.extend(defaults, options);

    return this.each(function() {
      //Check to see if tooltip-hover div exists
      if($('.tooltip-hover').length == 0)
        $('body').append('<div class="tooltip-hover disable"></div>');

      //Set title attribute outside the intended scope
      var title_attr = $(this).attr('title');
      $(this).mouseover(function() {
        //Set the measurements of the Tool Tip
        var offset = $(this).offset();
        var offset_top = offset.top + $(this).height() + 15;
        var offset_left = offset.left + ($(this).width() / 2);

        $(this).attr('title', '').parent().addClass('title-hover');
        $('.tooltip-hover').fadeIn('def').text(title_attr).css({'top': + offset_top + 'px', 'left': + offset_left + 'px'}).addClass('enable').removeClass('disable');
      }).mouseout(function() {
        $(this).attr('title', '').parent().removeClass('title-hover');
        $('.tooltip-hover').fadeOut('fast').text('').addClass('disable').removeClass('enable');
      });

    });

  }

})(jQuery);
