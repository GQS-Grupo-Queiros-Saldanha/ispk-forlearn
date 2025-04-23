

$(document).ready(function(){
     $('.owl-carousel').owlCarousel({
     items: 1,
     nav: false,
     dots: true,
     dotsContainer: '.owl-dots',
     autoplay: true,
     autoplayTimeout: 3000,
     loop: true
     });
});

var color = new Array("#ff8600", " #d833cb", "#ff4141","#3ec4f7");
var i=0;							
setInterval(function() {
     $( "#Topbar").css("background-color", color[i]);
     i+=1;if(i>3){ i=0; }
},3000);
;