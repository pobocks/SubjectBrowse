jQuery(document).delegate('.expander', 'click', function() {
    jQuery(this).toggleClass('expanded')
        .nextAll('ul:first').toggleClass('expanded');
    return true;
});
