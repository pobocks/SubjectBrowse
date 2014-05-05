<fieldset id="fieldset-subject-browse-form"><legend><?php echo __('Subject Browse'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <label for="subject_browse_headers"><?php echo __('Print headings'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox('subject_browse_headers', true,
                array('checked'=>(boolean) get_option('subject_browse_headers'))); ?>
            <p class="explanation">
                <?php echo __('Print headers for each section (#0-9 and symbols, A, B, etc.)'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="subject_browse_alphabetical_skiplinks"><?php echo __('Print skip links'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox('subject_browse_alphabetical_skiplinks', true,
                array('checked'=>(boolean) get_option('subject_browse_alphabetical_skiplinks'))); ?>
            <p class="explanation">
                <?php echo __('Print skip links at the top and bottom of each page, which link to the alphabetical headers.'); ?>
                <?php echo __('Note that if headers are turned off, skiplinks do not work.'); ?>
            </p>
        </div>
    </div>
    <div class="field">
        <div class="two columns alpha">
            <label for="subject_browse_item_links"><?php echo __('Add Links in Item pages'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox('subject_browse_item_links', true,
                array('checked'=>(boolean) get_option('subject_browse_item_links'))); ?>
            <p class="explanation">
                <?php echo __('Makes subject links in item display pages clickable links.'); ?>
            </p>
        </div>
    </div>
</fieldset>
