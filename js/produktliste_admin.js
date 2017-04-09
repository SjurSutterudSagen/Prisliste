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
            };
        }

        Element.prototype.remove = function() {
            this.parentElement.removeChild(this);
        };

        NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
            for(var i = this.length - 1; i >= 0; i--) {
                if(this[i] && this[i].parentElement) {
                    this[i].parentElement.removeChild(this[i]);
                }
            }
        };

        //ingredients buttons functionality
        var count = 0;
        if ( $( ".productlist_ingredient" ).length !== 0 ) {
            count = $( ".productlist_ingredient" ).length;
        }
        $( '#new_ingredient' ).click(function(){
            count++;
            //inserting the new inputs before the new ingredients button
            $(
                "<tr>" +
                "<th><label for='ingredient[" + count + "]'>Ingrediens</label></th>" +
                "<td><input name='ingredient[" + count + "][" + 'ingredient_name' + "]' type='text' value='' class='regular-text' /></td>" +
                "<td><p class='allergen-titel-mobile'>Allergen?</p><div class='allergen-checkbox-div'><input name='ingredient[" + count + "][" + 'allergen' + "]' type='checkbox' value='1' class='regular-text' /></div></td>" +
                "<td><input type='hidden' name='ingredient[" + count + "][" + 'remove' + "]' value='0'/><button class='ingredient-delete-button button'>Slett</button></td>" +
                "</tr>"
            ).insertBefore( '#ingredients_wrapper' );
        });

        //delete ingredient button
        $( '.ingredients-div-container' ).on('click', '.ingredient-delete-button', function(e){
            e.preventDefault();
            this.previousSibling.value = 1;

            if ( $( this ).closest('td').next().val() == null ) {
                $( this ).closest('tr').hide('slow', function (){
                    $( this ).remove();
                });
            } else {
                //hide the parent tr
                $( this ).closest('tr').hide('slow');
            }
        });

          $( 'input[name=delete_product_submit]' ).click(function(e){
            e.preventDefault();
            toastr.options = {
              "closeButton": true,
              "debug": false,
              "newestOnTop": false,
              "progressBar": false,
              "positionClass": "toast-top-center",
              "preventDuplicates": false,
              "onclick": null,
              "showDuration": "300",
              "hideDuration": "1000",
              "timeOut": "0",
              "extendedTimeOut": "0",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut",
              "tapToDismiss": false
            };
            toastr.error('<br /><br />Er du sikker på at du vil slette dette produktet?<br /><br /><br /><button id="delete_product_yes" type="button" class="button button-primary" value=true>Ja</button><button id="delete_product_no" type="button" class="button button-warning" value="false">Avbryt</button>');

            $( '#delete_product_yes' ).click(function(e){
              $('#delete_product_form').submit();
            });
            $( '#delete_product_no' ).click(function(e){
              toastr.remove();
            });
          });


          var preventOk = false;
          $( 'input[name=delete_category_submit]' ).click(function(e){
            e.preventDefault();
            toastr.options = {
              "closeButton": true,
              "debug": false,
              "newestOnTop": false,
              "progressBar": false,
              "positionClass": "toast-top-center",
              "preventDuplicates": false,
              "onclick": null,
              "showDuration": "300",
              "hideDuration": "1000",
              "timeOut": "0",
              "extendedTimeOut": "0",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut",
              "tapToDismiss": false
            };
            toastr.error('<br /><br />Er du sikker på at du vil slette denne kategorien?<br /><br /><br /><button id="delete_category_yes" type="button" class="button button-primary" value=true>Ja</button><button id="delete_category_no" type="button" class="button button-warning" value="false">Avbryt</button>');

            $( '#delete_category_yes' ).click(function(e){
              preventOk = true;

              if (preventOk) {
                $("<input name='delete_category_submit' type='hidden' value='Slett kategori''>").insertBefore( 'input[name=delete_category_submit] ' );
                $( '#delete_category_form' ).submit();
              }
            });

            $( '#delete_category_no' ).click(function(e){toastr.remove();});
          });
    });
})(jQuery);
