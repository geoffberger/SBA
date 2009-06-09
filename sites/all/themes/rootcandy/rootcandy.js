$(function()
 {
  $("table tr").hover(
   function()
   {
    $(this).addClass("highlight");
   },
   function()
   {
    $(this).removeClass("highlight");
   }
  )
 }
)

$(document).ready(function() {
  $("a[rel=external]").click(function() {
    this.target = "_blank";
  });
});
