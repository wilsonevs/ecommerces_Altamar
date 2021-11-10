const openNav = document.querySelector(".open-btn");
const closeNav = document.querySelector(".close-btn");
const menu = document.querySelector(".nav-list");

openNav.addEventListener("click", () => {
  menu.classList.add("show");
});

closeNav.addEventListener("click", () => {
  menu.classList.remove("show");
});

// Fixed Nav
const navBar = document.querySelector(".nav");
const navHeight = navBar.getBoundingClientRect().height;
window.addEventListener("scroll", () => {
  const scrollHeight = window.pageYOffset;
  if (scrollHeight > navHeight) {
    navBar.classList.add("fix-nav");
  } else {
    navBar.classList.remove("fix-nav");
  }
});

// Scroll To
const links = [...document.querySelectorAll(".scroll-link")];
links.map((link) => {
  if (!link) return;
  link.addEventListener("click", (e) => {
    e.preventDefault();

    const id = e.target.getAttribute("href").slice(1);

    const element = document.getElementById(id);
    const fixNav = navBar.classList.contains("fix-nav");
    let position = element.offsetTop - navHeight;

    window.scrollTo({
      top: position,
      left: 0,
    });

    navBar.classList.remove("show");
    menu.classList.remove("show");
    document.body.classList.remove("show");
  });
});


// PproductosDetalles cambio de imagen

(function ($) {
	"use strict";

    jQuery(document).ready(function ($) {
        
        $(".thumbnails").find('img').bind("click", function() {
            var src = $(this).attr("src");
            // Check the beginning of the src attribute  
            var state = (src.indexOf("bw_") === 0) ? 'bw' : 'clr';
            // Modify the src attribute based upon the state var we just set
            (state === 'bw') ? src = src.replace('bw_', 'clr_') : src = src.replace('clr_', 'bw_');
            // Apply the new src attribute value  
            $(this).attr("src", src);

            // This is just for demo visibility
            $('.main img').attr("src", src);
            
            $('.thumbnails .thumbnail.active').removeClass('active');
            
            $(this).parent().parent().addClass('active');
            
            

          return false;
        });
        
        var spins = document.getElementsByClassName("qt-area");
        for (var i = 0, len = spins.length; i < len; i++) {
            var spin = spins[i],
                span = spin.getElementsByTagName("i"),
                input = spin.getElementsByTagName("input")[0];

            input.onchange = function() { input.value = +input.value || 0; };
            span[0].onclick = function() { input.value = Math.max(0, input.value - 1); };
            span[1].onclick = function() { input.value -= -1; };
        }



    });


    jQuery(window).load(function(){

        
    });


}(jQuery));