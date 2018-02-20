jQuery(document).ready(function(){
    jQuery('#filter-bar button[type=button]').click(function() {
        jQuery('#filter_search').val('');
        jQuery('#adminForm').submit();
    });
});
