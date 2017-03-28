/**
 * Created by Sjur on 17.03.2017.
 * Copied from what Jonas had done earlier.
 */
(function( $ ) {
    "use strict";
    $(document).ready(function(){
        //accordian functionality
        var acc = document.getElementsByClassName("accordion");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].onclick = function() {
                this.classList.toggle("active");

                var panel = this.nextElementSibling;
                if (panel.style.maxHeight){
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }

                var img = this.firstElementChild.firstElementChild;
                img.classList.toggle("thumbnail-hide");

                var hv_arrow = this.getElementsByClassName("fa");
                hv_arrow[0].classList.toggle("fa-chevron-down");
                hv_arrow[0].classList.toggle("fa-chevron-up");
            }
        }

        Element.prototype.remove = function() {
            this.parentElement.removeChild(this);
        }

        NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
            for(var i = this.length - 1; i >= 0; i--) {
                if(this[i] && this[i].parentElement) {
                    this[i].parentElement.removeChild(this[i]);
                }
            }
        }

        //ingredients buttons functionality
        var count = 0;
        $( '#new_ingredient' ).click(function(){
            count++;
            $( '#ingredients_wrapper' ).append(
                "<tr>" +
                    "<th><label for='ingredient[" + count + "]'>Ingrediens " + count + "</label></th>" +
                    "<td><input name='ingredient[" + count + "][" + 'ingredient_name' + "]' type='text' value='' class='regular-text' /></td>" +
                    "<td><input name='ingredient[" + count + "][" + 'ingredient_allergen' + "]' type='checkbox' value='true' class='regular-text' /></td>" +
                "</tr>"
            );
            console.log('Clicked');
            //TODO: test prepending to the last child
        });
    });
})(jQuery);